<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Club;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $clubId = null;
        $clubHashId = null;
        $studentId = null;
        $studentHashId = null;

        if ($user->role === 'student') {
            $student = Student::where('user_id', $user->id)->first();

            if ($student) {
                $studentId = $student->id;
                $studentHashId = Hashids::encode($studentId);

                $firstClub = $student->clubs()->first();
                if ($firstClub) {
                    $clubId = $firstClub->id;
                    $clubHashId = Hashids::encode($clubId);
                }
            }
        } else if ($user->role === 'club_pengurus') {
            $club = Club::where('user_id', $user->id)->first();

            if ($club) {
                $clubId = $club->id;
                $clubHashId = Hashids::encode($clubId);
            }
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'club_id' => $clubId,
                'club_hash_id' => $clubHashId,
                'student_id' => $studentId,
                'student_hash_id' => $studentHashId,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->query('all') == 'true') {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logout dari semua device berhasil']);
        }

        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }


}
