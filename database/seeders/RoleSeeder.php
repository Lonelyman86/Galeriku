<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Administrator
        Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Super user with full access',
            ]
        );

        // 2. User Biasa
        Role::firstOrCreate(
            ['id' => 2],
            [
                'name' => 'User Biasa',
                'slug' => 'user',
                'description' => 'Regular user with standard access',
            ]
        );
    }
}
