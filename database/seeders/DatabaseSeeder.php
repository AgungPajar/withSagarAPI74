<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ClubSeeder::class,
            UserSeeder::class,
            JurusanSeeder::class,
            StudentSeeder::class,
            \Database\Seeders\AdminSeeder::class,
            \Database\Seeders\UserGroupSeeder::class,
        ]);
    }
}
