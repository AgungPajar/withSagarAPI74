<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Club;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|url',
            'password' => 'nullable|string|min:6',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // ✅ Jangan set name lagi karena kolom name udah ga ada di tabel users
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ✅ Update club berdasarkan user_id
        $club = Club::where('user_id', $user->id)->first();

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

                $destinationPath = public_path('storage/logos');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                $file->move($destinationPath, $filename);
                $club->logo_path = 'logos/' . $filename;
            }

            $club->save();
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'club' => $club ? $club->fresh()->makeHidden(['created_at', 'updated_at']) : null,
        ]);
    }
}
