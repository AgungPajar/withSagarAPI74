<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Club;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if ($user->role === 'student' || $user->role === 'club_pengurus') {
            $student = Student::where('user_id', $user->id)->first();

            if ($student) {
                $studentId = $student->id;
                $studentHashId = $studentId;

                // Cek apakah dia admin/pengurus di sebuah club
                $club = Club::where('student_id', $student->id)->first();
                if ($club) {
                    $user->role = 'club_pengurus'; // Timpa role secara dinamis untuk response
                    $clubId = $club->id;
                    $clubHashId = $clubId;
                } else {
                    $user->role = 'student';
                    $firstClub = $student->clubs()->first();
                    if ($firstClub) {
                        $clubId = $firstClub->id;
                        $clubHashId = $clubId;
                    }
                }
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
