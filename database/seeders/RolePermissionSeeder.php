<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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

        // Delete all existing permissions first
        Permission::query()->delete();

    // Create permissions (idempotent)
    $permissions = [
            // User Management
            'users.view',
            'users.add',
            'users.edit',
            'users.delete',

            // Role Management
            'roles.view',
            'roles.add',
            'roles.edit',
            'roles.delete',

            // Permission Management
            'permissions.view',
            'permissions.add',
            'permissions.edit',
            'permissions.delete',

            // Dashboard
            'dashboard.view',

            // Settings
            'settings.view',
            'settings.edit',

            // Master Data Management
            'master_data.view',
            'master_data.add',
            'master_data.edit',
            'master_data.delete',

            // Peminjaman Management
            'peminjaman_dana.view',
            'peminjaman_dana.add',
            'peminjaman_dana.edit',
            'peminjaman_dana.active/non_active',
            'peminjaman_dana.pengajuan_peminjaman',
            'peminjaman_dana.validasi_dokumen',
            'peminjaman_dana.persetujuan_debitur',
            'peminjaman_dana.validasi_ceo_ski',
            'peminjaman_dana.validasi_direktur',
            'peminjaman_dana.generate_kontrak',
            'peminjaman_dana.konfirmasi_debitur',
            'peminjaman_dana.upload_dokumen_transfer',

            // Restrukturisasi Management
            'pengajuan_restrukturisasi.view',
            'pengajuan_restrukturisasi.add',
            'pengajuan_restrukturisasi.edit',
            'pengajuan_restrukturisasi.ajukan_restrukturisasi',
            'pengajuan_restrukturisasi.validasi_dokumen',
            'pengajuan_restrukturisasi.persetujuan_ceo_ski',
            'pengajuan_restrukturisasi.persetujuan_direktur',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions (idempotent)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        // Remove all previous permissions before syncing
        $superAdminRole->permissions()->detach();
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // Remove all previous permissions before syncing
        $adminRole->permissions()->detach();
        $adminRole->syncPermissions([
            'users.view',
            'users.add',
            'users.edit',
            'roles.view',
            'permissions.view',
            'dashboard.view',
            'settings.view',
        ]);

        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        // Remove all previous permissions before syncing
        $moderatorRole->permissions()->detach();
        $moderatorRole->syncPermissions([
            'users.view',
            'users.edit',
            'dashboard.view',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        // Remove all previous permissions before syncing
        $userRole->permissions()->detach();
        $userRole->syncPermissions([
            'dashboard.view',
        ]);

        $debiturRole = Role::firstOrCreate(['name' => 'Debitur', 'restriction' => 0]);

        $debiturRole->permissions()->detach();
        $debiturRole->syncPermissions([
            'peminjaman_dana.view',
            'peminjaman_dana.add',
            'peminjaman_dana.edit',
            'peminjaman_dana.pengajuan_peminjaman',
            'peminjaman_dana.persetujuan_debitur',
            'peminjaman_dana.konfirmasi_debitur',
            
            'pengajuan_restrukturisasi.view',
            'pengajuan_restrukturisasi.add',
            'pengajuan_restrukturisasi.edit',
            'pengajuan_restrukturisasi.ajukan_restrukturisasi',
        ]);

        $financeRole = Role::firstOrCreate(['name' => 'Finance SKI', 'restriction' => 0]);
        $financeRole->permissions()->detach();
        $financeRole->syncPermissions([
            'peminjaman_dana.view',
            'peminjaman_dana.validasi_dokumen',
            'peminjaman_dana.upload_dokumen_transfer',

            'pengajuan_restrukturisasi.validasi_dokumen',
        ]);

        $ceoRole = Role::firstOrCreate(['name' => 'CEO SKI', 'restriction' => 0]);
        $ceoRole->permissions()->detach();
        $ceoRole->syncPermissions([
            'peminjaman_dana.view',
            'peminjaman_dana.validasi_ceo_ski',

            'pengajuan_restrukturisasi.persetujuan_ceo_ski',
        ]);

        $direkturRole = Role::firstOrCreate(['name' => 'Direktur SKI', 'restriction' => 0]);
        $direkturRole->permissions()->detach();
        $direkturRole->syncPermissions([
            'peminjaman_dana.view',
            'peminjaman_dana.validasi_direktur',

            'pengajuan_restrukturisasi.persetujuan_direktur',
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

        $debitur = User::firstOrCreate([
            'email' => 'debitur@example.com',
        ], [
            'name' => 'Debitur User',
            'password' => bcrypt('password'),
        ]);
        if (! $debitur->hasRole('Debitur')) {
            $debitur->assignRole('Debitur');
        }

        $finance = User::firstOrCreate([
            'email' => 'finance@example.com',
        ], [
            'name' => 'Finance User',
            'password' => bcrypt('password'),
        ]);
        if (! $finance->hasRole('Finance SKI')) {
            $finance->assignRole('Finance SKI');
        }

        $ceo = User::firstOrCreate([
            'email' => 'ceo@example.com',
        ], [
            'name' => 'CEO User',
            'password' => bcrypt('password'),
        ]);
        if (! $ceo->hasRole('CEO SKI')) {
            $ceo->assignRole('CEO SKI');
        }

        $direktur = User::firstOrCreate([
            'email' => 'direktur@example.com',
        ], [
            'name' => 'Direktur User',
            'password' => bcrypt('password'),
        ]);
        if (! $direktur->hasRole('Direktur SKI')) {
            $direktur->assignRole('Direktur SKI');
        }

        // Restore the original cache driver
        config(['cache.default' => $originalCacheDriver]);
    }
}
