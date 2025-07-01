<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Buat user dulu
        $user = User::create([
            'name' => 'Agung Pajar',
            'username' => '00723441322',
            'password' => Hash::make('password12345'),
            'role' => 'student',
        ]);

        // Baru buat student dan assign user_id
        $student = Student::create([
            'name' => 'Agung Pajar',
            'nisn' => '00723441322',
            'class' => 'XI',
            'id_jurusan' => 6,
            'phone' => '08132323842',
            'tanggal_lahir' => '2007-03-15',
            'user_id' => $user->id,
        ]);
    }
}

