<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::updateOrCreate([
            'email' => 'dev@gncs.dev',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('programmer123'),
        ]);
    }
}
