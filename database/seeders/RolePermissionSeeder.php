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
            // Menu Sfinance
            'sfinance.menu.dashboard_pembiayaan',
            'sfinance.menu.dashboard_pembiayaan_investasi',
            'sfinance.menu.pengajuan_peminjaman',
            'sfinance.menu.ar_perbulan',
            'sfinance.menu.ar_performance',
            'sfinance.menu.pengajuan_restukturisasi',
            'sfinance.menu.program_restukturisasi',
            'sfinance.menu.pengembalian_dana',
            'sfinance.menu.debitur_piutang',
            'sfinance.menu.report_pengembalian',
            'sfinance.menu.pengajuan_investasi',
            'sfinance.menu.report_penyaluran_dana',
            'sfinance.menu.penyaluran_deposito',
            'sfinance.menu.kertas_kerja_sfinance',
            'sfinance.menu.pengembalian_investasi',
            // Menu SFinlog
            'sfinlog.menu.dashboard_pembiayaan',
            'sfinlog.menu.dashboard_investasi_deposito',
            'sfinlog.menu.peminjaman_dana',
            'sfinlog.menu.ar_perbulan',
            'sfinlog.menu.ar_performance',
            'sfinlog.menu.pengembalian_dana',
            'sfinlog.menu.debitur_piutang',
            'sfinlog.menu.report_pengembalian',
            'sfinlog.menu.pengajuan_investasi',
            'sfinlog.menu.penyaluran_deposito',
            'sfinlog.menu.report_penyaluran_dana',
            'sfinlog.menu.kertas_kerja_investor',
            'sfinlog.menu.pengembalian_investasi',
            'peminjaman_finlog.view',
            'peminjaman_finlog.add',
            'peminjaman_finlog.edit',
            'peminjaman_finlog.delete',
            'peminjaman_finlog.validasi_io',              // Step 2
            'peminjaman_finlog.persetujuan_debitur',      // Step 3
            'peminjaman_finlog.persetujuan_finance_ski',  // Step 4
            'peminjaman_finlog.persetujuan_ceo_finlog',   // Step 5
            'peminjaman_finlog.generate_kontrak',         // Step 6
            'peminjaman_finlog.upload_bukti',             // Step 7
            // Settings
            'settings.view',
            'settings.edit',
            // Master Data Management
            'master_data.view',
            'master_data.add',
            'master_data.edit',
            'master_data.delete',
            // Peminjaman Management
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
            // Pengembalian Pinjaman Management
            'pengembalian_pinjaman.view',
            'pengembalian_pinjaman.add',
            'pengembalian_pinjaman.edit',
            // Investasi Management
            'investasi.view',
            'investasi.add',
            'investasi.edit',
            'investasi.submit_pengajuan',
            'investasi.delete',
            'investasi.validasi_bagi_hasil',
            'investasi.validasi_ceo_ski',
            'investasi.upload_bukti_transfer',
            'investasi.generate_kontrak',
            // Pengajuan Investasi Finlog Management
            'pengajuan_investasi_finlog.view',
            'pengajuan_investasi_finlog.add',
            'pengajuan_investasi_finlog.edit',
            'pengajuan_investasi_finlog.delete',
            'pengajuan_investasi_finlog.submit',
            'pengajuan_investasi_finlog.validasi_finance_ski',
            'pengajuan_investasi_finlog.validasi_ceo_finlog',
            'pengajuan_investasi_finlog.upload_bukti',
            'pengajuan_investasi_finlog.generate_kontrak',
            // Penyaluran Deposito
            'penyaluran_deposito.view',
            'penyaluran_deposito.add',
            'penyaluran_deposito.edit',
            'penyaluran_deposito.upload_bukti',
            // Program Restrukturisasi
            'program_restrukturisasi.view',
            'program_restrukturisasi.add',
            'program_restrukturisasi.edit',
            // Pengembalian Investasi
            'pengembalian_investasi.view',
            'pengembalian_investasi.add',
            'pengembalian_investasi.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions (idempotent)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        // Remove all previous permissions before syncing
        $superAdminRole->permissions()->detach();
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            ['restriction' => 1]
        );
        // Remove all previous permissions before syncing
        $adminRole->permissions()->detach();
        $adminRole->syncPermissions([
            'users.view',
            'users.add',
            'users.edit',
            'roles.view',
            'permissions.view',
            'settings.view',
        ]);

        $moderatorRole = Role::updateOrCreate(
            ['name' => 'moderator'],
            ['restriction' => 1]
        );
        // Remove all previous permissions before syncing
        $moderatorRole->permissions()->detach();
        $moderatorRole->syncPermissions([
            'users.view',
            'users.edit',
        ]);

        $userRole = Role::updateOrCreate(
            ['name' => 'user'],
            ['restriction' => 0]
        );
        // Remove all previous permissions before syncing
        $userRole->permissions()->detach();
        $userRole->syncPermissions([]);

        $debiturRole = Role::updateOrCreate(
            ['name' => 'Debitur'],
            ['restriction' => 0]
        );

        $debiturRole->permissions()->detach();
        $debiturRole->syncPermissions([
            // Menu Access - Peminjaman & Deposito
            'sfinance.menu.pengajuan_peminjaman',
            'sfinance.menu.pengajuan_restukturisasi',
            'sfinance.menu.pengembalian_dana',
            'sfinance.menu.program_restukturisasi',
            'sfinance.menu.penyaluran_deposito',

            'sfinlog.menu.peminjaman_dana',
            'sfinlog.menu.pengembalian_dana',
            'sfinlog.menu.penyaluran_deposito',

            // SFinance Peminjaman
            'peminjaman_dana.add',
            'peminjaman_dana.edit',
            'peminjaman_dana.active/non_active',
            'peminjaman_dana.pengajuan_peminjaman',
            'peminjaman_dana.persetujuan_debitur',
            'peminjaman_dana.konfirmasi_debitur',

            // SFinlog Peminjaman
            'peminjaman_finlog.view',
            'peminjaman_finlog.add',
            'peminjaman_finlog.edit',
            'peminjaman_finlog.persetujuan_debitur',  // Step 3 approval

            // Restrukturisasi
            'pengajuan_restrukturisasi.view',
            'pengajuan_restrukturisasi.add',
            'pengajuan_restrukturisasi.edit',
            'pengajuan_restrukturisasi.ajukan_restrukturisasi',
            'program_restrukturisasi.view',
            'program_restrukturisasi.add',
            'program_restrukturisasi.edit',

            // Pengembalian Pinjaman
            'pengembalian_pinjaman.view',
            'pengembalian_pinjaman.add',
            'pengembalian_pinjaman.edit',

            // Penyaluran Deposito (Debitur can only view and upload bukti)
            'penyaluran_deposito.view',
            'penyaluran_deposito.upload_bukti',
        ]);

        $investorRole = Role::updateOrCreate(
            ['name' => 'Investor'],
            ['restriction' => 0]
        );
        $investorRole->permissions()->detach();
        $investorRole->syncPermissions([
            // Menu Access - Investasi Only
            'sfinance.menu.pengajuan_investasi',
            'sfinance.menu.report_penyaluran_dana',
            'sfinance.menu.pengembalian_investasi',

            'sfinlog.menu.pengajuan_investasi',
            'sfinlog.menu.report_penyaluran_dana',
            'sfinlog.menu.pengembalian_investasi',

            // SFinance Investasi (Pengajuan Investasi)
            'investasi.view',
            'investasi.add',
            'investasi.edit',
            'investasi.submit_pengajuan',           // Investor can submit pengajuan
            'investasi.upload_bukti_transfer',      // Investor can upload bukti transfer

            // SFinlog Investasi
            'pengajuan_investasi_finlog.view',
            'pengajuan_investasi_finlog.add',
            'pengajuan_investasi_finlog.edit',
            'pengajuan_investasi_finlog.delete',
        ]);

        $financeRole = Role::updateOrCreate(
            ['name' => 'Finance SKI'],
            ['restriction' => 1]
        );
        $financeRole->permissions()->detach();
        $financeRole->syncPermissions([
            // SFinance Menu
            'sfinance.menu.dashboard_pembiayaan',
            'sfinance.menu.dashboard_pembiayaan_investasi',
            'sfinance.menu.pengajuan_peminjaman',
            'sfinance.menu.ar_perbulan',
            'sfinance.menu.ar_performance',
            'sfinance.menu.pengajuan_restukturisasi',
            'sfinance.menu.program_restukturisasi',
            'sfinance.menu.pengembalian_dana',
            'sfinance.menu.debitur_piutang',
            'sfinance.menu.report_pengembalian',
            'sfinance.menu.pengajuan_investasi',
            'sfinance.menu.report_penyaluran_dana',
            'sfinance.menu.penyaluran_deposito',
            'sfinance.menu.kertas_kerja_sfinance',
            'sfinance.menu.pengembalian_investasi',

            // SFinlog Menu
            'sfinlog.menu.dashboard_pembiayaan',
            'sfinlog.menu.dashboard_investasi_deposito',
            'sfinlog.menu.peminjaman_dana',
            'sfinlog.menu.ar_perbulan',
            'sfinlog.menu.ar_performance',
            'sfinlog.menu.pengembalian_dana',
            'sfinlog.menu.debitur_piutang',
            'sfinlog.menu.report_pengembalian',
            'sfinlog.menu.pengajuan_investasi',
            'sfinlog.menu.penyaluran_deposito',
            'sfinlog.menu.report_penyaluran_dana',
            'sfinlog.menu.kertas_kerja_investor',
            'sfinlog.menu.pengembalian_investasi',

            // SFinance Permissions
            'peminjaman_dana.validasi_dokumen',
            'peminjaman_dana.generate_kontrak',  // Step 6 Generate Kontrak
            'peminjaman_dana.upload_dokumen_transfer',
            'pengajuan_restrukturisasi.validasi_dokumen',
            'investasi.validasi_bagi_hasil',
            'investasi.generate_kontrak',

            // Penyaluran Deposito (Finance SKI can create/edit)
            'penyaluran_deposito.view',
            'penyaluran_deposito.add',
            'penyaluran_deposito.edit',

            // Pengembalian Investasi (Finance SKI can create/edit)
            'pengembalian_investasi.view',
            'pengembalian_investasi.add',
            'pengembalian_investasi.edit',

            // SFinlog Peminjaman
            'peminjaman_finlog.view',
            'peminjaman_finlog.add',
            'peminjaman_finlog.persetujuan_finance_ski',  // Step 4
            'peminjaman_finlog.generate_kontrak',         // Step 6
            'peminjaman_finlog.upload_bukti',             // Step 7

            // SFinlog Investasi
            'pengajuan_investasi_finlog.view',
            'pengajuan_investasi_finlog.add',
            'pengajuan_investasi_finlog.validasi_finance_ski',  // Step 2
            'pengajuan_investasi_finlog.upload_bukti',          // Step 4
            'pengajuan_investasi_finlog.generate_kontrak',      // Step 5
        ]);

        $ceoRole = Role::updateOrCreate(
            ['name' => 'CEO SKI'],
            ['restriction' => 1]
        );
        $ceoRole->permissions()->detach();
        $ceoRole->syncPermissions([
            // Dashboard Access
            'sfinance.menu.dashboard_pembiayaan',
            'sfinance.menu.dashboard_pembiayaan_investasi',

            // Menu Access
            'sfinance.menu.pengembalian_dana',
            'sfinance.menu.report_pengembalian',
            'sfinance.menu.ar_performance',
            'sfinance.menu.ar_perbulan',
            'sfinance.menu.debitur_piutang',
            'sfinance.menu.pengajuan_investasi',
            'sfinance.menu.pengajuan_peminjaman',
            'sfinance.menu.pengajuan_restukturisasi',
            'sfinance.menu.program_restukturisasi',  // Program Restrukturisasi menu

            'peminjaman_dana.validasi_ceo_ski',
            'investasi.validasi_ceo_ski',  // CEO can approve investasi

            'pengajuan_restrukturisasi.persetujuan_ceo_ski',
        ]);

        $direkturRole = Role::updateOrCreate(
            ['name' => 'Direktur SKI'],
            ['restriction' => 1]
        );
        $direkturRole->permissions()->detach();
        $direkturRole->syncPermissions([
            // Dashboard Access
            'sfinance.menu.dashboard_pembiayaan',
            'sfinance.menu.dashboard_pembiayaan_investasi',

            // Menu Access
            'sfinance.menu.pengajuan_peminjaman',
            'sfinance.menu.pengajuan_restukturisasi',

            'peminjaman_dana.validasi_direktur',
            'pengajuan_restrukturisasi.persetujuan_direktur',
        ]);

        $ceoFinlogRole = Role::updateOrCreate(
            ['name' => 'CEO S-Finlog'],
            ['restriction' => 1]
        );
        $ceoFinlogRole->permissions()->detach();
        $ceoFinlogRole->syncPermissions([
            // Menu SFinlog
            'sfinlog.menu.dashboard_pembiayaan',
            'sfinlog.menu.dashboard_investasi_deposito',
            'sfinlog.menu.peminjaman_dana',
            'sfinlog.menu.pengajuan_investasi',

            // Peminjaman Finlog
            'peminjaman_finlog.view',
            'peminjaman_finlog.add',
            'peminjaman_finlog.persetujuan_ceo_finlog',  // Step 5 approval

            // Investasi Finlog
            'pengajuan_investasi_finlog.view',
            'pengajuan_investasi_finlog.add',
            'pengajuan_investasi_finlog.validasi_ceo_finlog',  // Step 3 approval
        ]);

        $ioRole = Role::updateOrCreate(
            ['name' => 'IO (Investment Officer)'],
            ['restriction' => 1]
        );
        $ioRole->permissions()->detach();
        $ioRole->syncPermissions([
            // Menu SFinlog
            'sfinlog.menu.dashboard_pembiayaan',
            'sfinlog.menu.peminjaman_dana',

            // Peminjaman Finlog
            'peminjaman_finlog.view',
            'peminjaman_finlog.add',
            'peminjaman_finlog.validasi_io',  // Step 2 validation
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

        $ceoFinlog = User::firstOrCreate([
            'email' => 'ceofinlog@example.com',
        ], [
            'name' => 'CEO S-Finlog',
            'password' => bcrypt('password'),
        ]);
        if (! $ceoFinlog->hasRole('CEO S-Finlog')) {
            $ceoFinlog->assignRole('CEO S-Finlog');
        }

        // Restore the original cache driver
        config(['cache.default' => $originalCacheDriver]);
    }
}
