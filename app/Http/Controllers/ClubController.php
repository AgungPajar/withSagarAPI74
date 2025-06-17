<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with('user')->get()->map(function ($club) {
            if (is_int($club->id)) {
                $club->hash_id = Hashids::encode($club->id);
            } else {
                $club->hash_id = null; // fallback jika id tidak valid
            }
            Log::info('Club ID type:', ['type' => gettype($club->id), 'value' => $club->id]);

            return $club;
        });
        return response()->json($clubs);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:clubs,name',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'status'   => 'nullable|string',
        ]);

        $club = Club::create([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? null,
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'club_id' => $club->id,
        ]);
        return response()->json([
            $club->load('user'),
        ], 201);
    }


    public function show(Request $request, $hashedId)
    {
        $decoded = Hashids::decode($hashedId);
        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Club not found',
            ], 404);
        };
        $id = $decoded[0];
        $user = $request->user();
        if ($user && $user->role === 'club_pengurus' && $user->club_id !=  $id) {
            return response()->json([
                'message' => 'Akses Ditolak'
            ], 403);
        }
        $club = Club::with('user')->find($id);
        if (!$club) {
            return response()->json([
                'message' => 'Ekskul tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'id' => $club->id,
            'name' => $club->name,
            'description' => $club->description,
            'logo_path' => $club->logo_path,
            'username' => $club->user->username ?? null,
        ]);
    }

    public function edit(Club $club)
    {
        //   
    }

    public function update(Request $request, Club $club)
    {
        //
    }

    public function destroy($id)
    {
        $club = Club::with('user')->findOrFail($id);

        if ($club->user) {
            $club->user->delete();
        }
        $club->delete();
        return response()->json(null, 204);
    }

    public function members($hashedId)
    {
        $decoded = Hashids::decode($hashedId);
        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Club not found',
            ], 404);
        }
        $id = $decoded[0];
        $club = Club::with('students')->find($id);
        if (!$club) {
            return response()->json([
                'message' => 'Club not found',
            ], 404);
        }

        return response()->json($club->students);
    }

    public function getStudents($hashedId)
    {
        return $this->members($hashedId);
    }
}
