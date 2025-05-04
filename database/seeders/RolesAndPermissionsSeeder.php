<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        $permissions = [
            // Ticket permissions
            'view tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'assign tickets',
            'change ticket status',
            
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            
            // Dashboard permissions
            'view dashboard',
            'view reports',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles and assign permissions
        
        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        // Technician role
        $technicianRole = Role::create(['name' => 'technician']);
        $technicianRole->givePermissionTo([
            'view tickets',
            'edit tickets',
            'change ticket status',
            'view dashboard',
            'view reports',
        ]);
        
        // User role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view tickets',
            'create tickets',
            'view dashboard',
        ]);
        
        // Assign admin role to user ID 1 (typically the first user/admin)
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
