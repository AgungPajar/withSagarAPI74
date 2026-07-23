<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with('user')->get()->map(function ($club) {
            if (is_int($club->id)) {
                $club->hash_id = $club->id;
            } else {
                $club->hash_id = null;
            }
            if ($club->logo_path && !str_starts_with($club->logo_path, 'logos/')) {
                $club->logo_path = 'logos/' . $club->logo_path;
            }
            return $club;
        });
        return response()->json($clubs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:clubs,name',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'status'   => 'nullable|string',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
        ]);
        $club = Club::create([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? null,
        ]);
        return response()->json([
            $club->load('user'),
        ], 201);
    }


    public function show(Request $request, $hashedId)
    {
        $decoded = [$hashedId];
        if (count($decoded) === 0) {
            return response()->json([
                'message' => 'Club not found',
            ], 404);
        };
        $id = $decoded[0];
        $user = $request->user();
        if ($user && $user->role === 'club_pengurus') {
            $student = Student::where('user_id', $user->id)->first();
            $clubCheck = $student ? Club::where('student_id', $student->id)->first() : null;
            if (!$clubCheck || $clubCheck->id != $id) {
                return response()->json(['message' => 'Akses Ditolak'], 403);
            }
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
            'logo_path' => $club->logo_path ? (str_starts_with($club->logo_path, 'logos/') ? $club->logo_path : 'logos/' . $club->logo_path) : null,
            'group_link' => $club->group_link,
            'username' => $club->user->username ?? null,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|url',
            'new_password' => 'nullable|string|min:6|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->username = $request->username ?? $user->username;
        $user->name = $request->name ?? $user->name;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        $student = Student::where('user_id', $user->id)->first();
        $club = $student ? Club::where('student_id', $student->id)->first() : null;

        if ($club) {
            $club->name = $request->name ?? $club->name;
            $club->description = $request->description ?? $club->description;
            $club->group_link = $request->group_link ?? $club->group_link;

            if ($request->hasFile('logo')) {
                $oldFile = str_replace('logos/', '', $club->logo_path);
                if ($club->logo_path && Storage::disk('public')->exists('logos/' . $oldFile)) {
                    Storage::disk('public')->delete('logos/' . $oldFile);
                }

                $file = $request->file('logo');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('logos', $filename, 'public');
                $club->logo_path = $filename;
            }

            $club->save();
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

    public function getByUser($userId)
    {
        $student = Student::where('user_id', $userId)->first();
        $club = $student ? Club::where('student_id', $student->id)->first() : null;

        if (!$club) {
            return response()->json(['message' => 'Club not found'], 404);
        }

        return response()->json([
            'id' => $club->id,
            'name' => $club->name,
            'hash_id' => $club->id,
            'logo_path' => $club->logo_path ? (str_starts_with($club->logo_path, 'logos/') ? $club->logo_path : 'logos/' . $club->logo_path) : null,
        ]);
    }
}
