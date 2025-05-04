<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\AdminUserSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the roles and permissions seeder first
        $this->call(RolesAndPermissionsSeeder::class);
        
        // Create admin user
        $this->call(AdminUserSeeder::class);
        
        // User::factory(10)->create();
    }
}
