<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGroup;
use App\Models\Permission;
use App\Models\Admin;
use App\Models\User;

class UserGroupSeeder extends Seeder
{
    public function run()
    {
        $adminGroup = UserGroup::updateOrCreate([
            'slug' => 'admin'
        ], [
            'name' => 'Administrator',
            'description' => 'Full access group',
        ]);

        $perm = Permission::updateOrCreate([
            'slug' => 'manage_system'
        ], [
            'name' => 'Manage System',
            'description' => 'Full system management'
        ]);

        $adminGroup->permissions()->syncWithoutDetaching([$perm->id]);

        // Attach to seeded admin if exists
        $admin = Admin::where('email','admin@example.com')->first();
        if ($admin) {
            $admin->groups()->syncWithoutDetaching([$adminGroup->id]);
        }

        // Optionally attach a normal user (first user) to admin group for testing
        $user = User::first();
        if ($user) {
            $user->groups()->syncWithoutDetaching([$adminGroup->id]);
        }
    }
}
