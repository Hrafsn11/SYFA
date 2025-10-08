<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Cache table might not exist yet during fresh migrations
            // This is safe to ignore as the cache will be cleared when needed
        }

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Dashboard
            'view dashboard',

            // Settings
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view permissions',
            'view dashboard',
            'view settings',
        ]);

        $moderatorRole = Role::create(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view users',
            'edit users',
            'view dashboard',
        ]);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view dashboard',
        ]);

        // Create a super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('super-admin');

        // Create a regular admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');
    }
}
