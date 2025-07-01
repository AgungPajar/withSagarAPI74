<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'group_link' => $club->group_link,
            'username' => $club->user->username ?? null,
        ]);
    }

    public function edit(Club $club)
    {
        //   
    }

     public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->username = $request->username;
        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $club = null;
        if ($user->club_id) {
            $club = Club::find($user->club_id);

            if ($club) {
                $club->name = $request->name;
                $club->description = $request->description ?? $club->description;
                $club->group_link = $request->group_link ?? $club->group_link;

                if ($request->hasFile('logo')) {
                    if ($club->logo_path && Storage::disk('public')->exists($club->logo_path)) {
                        Storage::disk('public')->delete($club->logo_path);
                    }

                    $file = $request->file('logo');
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    // Pastikan folder tujuan ada
                    $destinationPath = public_path('storage/logos');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0775, true);
                    }

                    // Pindahkan file ke public/storage/logos
                    $file->move($destinationPath, $filename);

                    // Simpan path untuk diakses publik
                    $club->logo_path = 'logos/' . $filename;
                }
                $club->save();
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'club' => $club ? $club->fresh()->makeHidden(['created_at', 'updated_at']) : null,
        ]);
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
}
