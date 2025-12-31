<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'projects.read',
            'projects.create',
            'projects.update',
            'projects.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign read permission to user
        $userRole->givePermissionTo('projects.read', 'projects.create');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ecoeats.local'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@ecoeats.local'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole('user');
    }
}
