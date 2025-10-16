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
        // Temporarily change cache driver to array to avoid database cache issues
        $originalCacheDriver = config('cache.default');
        config(['cache.default' => 'array']);

        // Reset cached roles and permissions
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Cache table might not exist yet during fresh migrations
            // This is safe to ignore as the cache will be cleared when needed
        }

    // Create permissions (idempotent)
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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions (idempotent)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view permissions',
            'view dashboard',
            'view settings',
        ]);

        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorRole->syncPermissions([
            'view users',
            'edit users',
            'view dashboard',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'view dashboard',
        ]);

        // Create a super admin user (idempotent)
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
        ]);
        if (! $superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        // Create a regular admin user (idempotent)
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Restore the original cache driver
        config(['cache.default' => $originalCacheDriver]);
    }
}
