<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Club;

class ProfileController extends Controller
{
    public function update(Request $request) {
        $user = Auth::user();

        $request ->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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
                
                if ($request->hasFile('logo')) {
                    if ($club->logo_path && Storage::disk('public')->exists($club->logo_path)) {
                        Storage::disk('public')->delete($club->logo_path);
                    }

                    $path = $request->file('logo')->store('logos', 'public');
                    $club->logo_path = $path;
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
}
