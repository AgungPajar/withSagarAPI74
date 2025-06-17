<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:users,username',
            'phone' => 'required|string|max:15|unique:users,phone',
            'email' => 'required|string|email|max:255|unique:users,email',
            'club_id' => 'required|exists:clubs,id',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'nisn' => $request->nisn,
            'class' => $request->class,
            'phone' => $request->phone,
            'club_id' => $request->club_id,
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
        ], 201);
    }
}
