<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $mpk = User::updateOrCreate([
            'username' => 'mpk'
        ], [
            'name' => 'MPK',
            'email' => 'mpk@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'mpk',
        ]);

        $osis = User::updateOrCreate([
            'username' => 'osis'
        ], [
            'name' => 'OSIS',
            'email' => 'osis@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'osis',
        ]);


    }
}
