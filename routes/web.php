<?php

use App\Http\Controllers\LaporanTagihanBulananController;
use App\Http\Controllers\MonitoringPembayaranController;
use App\Livewire\LaporanInvestasiSFinance;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TagihanPinjamanController;
use App\Http\Controllers\PengembalianTagihanPinjamanController;
use App\Http\Controllers\PenyaluranDanaInvestasiController; // SFinlog one?
use App\Http\Controllers\JenisInvestasiController;
use App\Livewire\LaporanTagihanBulanan;
use App\Livewire\ConfigMatrixScore;
use App\Livewire\HomeServices;
use App\Livewire\PermissionManagement;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Halaman landing setelah login (tanpa middleware checkPermission)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/home-services', HomeServices::class)->name('home.services');

// Authenticated routes dengan Jetstream dan Permission handling
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'checkPermission',
    'setActiveModule',
])->group(function () {

    require __DIR__ . '/livewire_route.php';

    // Module Entry Points - Redirect to first accessible route based on user permissions
    Route::get('sfinance', function () {
        return \App\Helpers\ModuleRedirectHelper::redirectToFirstAccessible('sfinance');
    })->name('sfinance.index');

    Route::get('sfinlog', function () {
        return \App\Helpers\ModuleRedirectHelper::redirectToFirstAccessible('sfinlog');
    })->name('sfinlog.index');

    // Module Routes: SFinance
    Route::prefix('sfinance')->name('sfinance.')->group(function () {
        require __DIR__ . '/module_routes.php';
    });

    // Module Routes: SFinlog  
    Route::prefix('sfinlog')->name('sfinlog.')->group(function () {
        require __DIR__ . '/sfinlog_routes.php';
    });

    // User Management Routes - Example with permission middleware
    Route::get('users', UserManagement::class)->name('users.index');
    Route::get('roles', RoleManagement::class)->name('roles.index');
    Route::get('permissions', PermissionManagement::class)->name('permissions.index');

    // Tagihan Pinjaman Routes
    Route::prefix('tagihan-pinjaman')->name('tagihan-pinjaman.')->controller(TagihanPinjamanController::class)->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('detail');
        // Route::get('/{id}/edit', 'edit')->name('edit');
        Route::post('/{id}', 'update')->name('update');

        Route::get('/{id}/preview-kontrak', 'previewKontrak')->name('preview-kontrak');
        Route::post('/{id}/generate-kontrak', 'generateKontrak')->name('generate-kontrak');
        Route::post('/{id}/approval', 'approval')->name('approval');
        Route::get('/history/{historyId}', 'getHistoryDetail')->name('history.detail');
        Route::patch('/{id}/toggle-active', 'toggleActive')->name('toggle-active');
    });

    Route::post('pengembalian-tagihan-pinjaman/store', [PengembalianTagihanPinjamanController::class, 'store'])->name('pengembalian-tagihan-pinjaman.store');
    
    // Tagihan Pinjaman Routes - Legacy support if needed or redundant
    
    // Route Controller yang masih diperlukan (fitur yang belum ada di Livewire)
    Route::get('tagihan-pinjaman/{id}', [TagihanPinjamanController::class, 'show'])->name('tagihan-pinjaman.detail'); // Detail page
    Route::put('tagihan-pinjaman/{id}', [TagihanPinjamanController::class, 'update'])->name('tagihan-pinjaman.update'); // Update endpoint
    Route::post('tagihan-pinjaman/{id}/preview-kontrak', [TagihanPinjamanController::class, 'previewKontrak'])->name('tagihan-pinjaman.preview-kontrak');
    Route::post('tagihan-pinjaman/{id}/download-kontrak', [TagihanPinjamanController::class, 'downloadKontrak'])->name('tagihan-pinjaman.download-kontrak');
    Route::post('tagihan-pinjaman', [TagihanPinjamanController::class, 'store'])->name('tagihan-pinjaman.store'); // Store endpoint untuk Livewire form
    Route::post('tagihan-pinjaman/{id}/approval', [TagihanPinjamanController::class, 'approval'])->name('tagihan-pinjaman.approval');
    Route::get('tagihan-pinjaman/history/{historyId}', [TagihanPinjamanController::class, 'getHistoryDetail'])->name('tagihan-pinjaman.history.detail');
    Route::patch('tagihan-pinjaman/{id}/toggle-active', [TagihanPinjamanController::class, 'toggleActive'])->name('tagihan-pinjaman.toggle-active');

    // Pengajuan Cicilan Routes (Restrukturisasi)
    Route::prefix('pengajuan-cicilan')->name('pengajuan-cicilan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PengajuanCicilanController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\PengajuanCicilanController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\PengajuanCicilanController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\PengajuanCicilanController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\PengajuanCicilanController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\PengajuanCicilanController::class, 'destroy'])->name('destroy');
        Route::get('tagihan-pinjaman/{idDebitur}', [\App\Http\Controllers\PengajuanCicilanController::class, 'getPeminjamanListApi'])->name('tagihan-pinjaman.list');
        Route::get('detail-pengajuan/{id}', [\App\Http\Controllers\PengajuanCicilanController::class, 'getPengajuanDetail'])->name('detail-pengajuan');
        // Evaluasi endpoints
        Route::post('{id}/evaluasi', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'save'])->name('evaluasi.save');
        Route::post('{id}/decision', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'decision'])->name('evaluasi.decision');
    });

    // Penyesuaian Cicilan Routes (Program Restrukturisasi)
    Route::prefix('penyesuaian-cicilan')->name('penyesuaian-cicilan.')->group(function () {
        Route::get('/', \App\Livewire\PenyesuaianCicilan\Index::class)->name('index');
        Route::get('create', \App\Livewire\PenyesuaianCicilan\Create::class)->name('create');
        Route::get('{id}', \App\Livewire\PenyesuaianCicilan\Show::class)->name('show');
        Route::get('{id}/edit', \App\Livewire\PenyesuaianCicilan\Edit::class)->name('edit');
        Route::get('{id}/generate-kontrak', \App\Livewire\PenyesuaianCicilan\GenerateKontrak::class)->name('generate-kontrak');
        Route::get('{id}/preview-kontrak', \App\Livewire\PenyesuaianCicilan\PreviewKontrak::class)->name('preview-kontrak');
        // Controller endpoints (if still needed)
        Route::post('/', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'store'])->name('store');
        Route::get('approved', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'getApprovedRestrukturisasi'])->name('approved');
        Route::get('detail/{id}', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'getRestrukturisasiDetail'])->name('detail');
    });

    // Detail cicilan menggunakan Controller dengan Pure AJAX (bukan Livewire component)
    Route::get('detail-cicilan/{id}', [\App\Http\Controllers\PengajuanCicilanController::class, 'show'])->name('detail-cicilan');

    // Pengembalian Tagihan Pinjaman
    Route::post('pengembalian-tagihan-pinjaman/store', [PengembalianTagihanPinjamanController::class, 'store'])->name('pengembalian-tagihan-pinjaman.store');
    Route::get('pengembalian-tagihan-pinjaman/export-pdf', [PengembalianTagihanPinjamanController::class, 'exportPdf'])->name('pengembalian-tagihan-pinjaman.export-pdf');

    // Riwayat Tagihan Aktif (Debitur Piutang)
    // AJAX endpoints
    Route::get('riwayat-tagihan-aktif/histori', [App\Http\Controllers\RiwayatTagihanAktifController::class, 'getHistoriPembayaran'])->name('riwayat-tagihan-aktif.histori');
    Route::get('riwayat-tagihan-aktif/summary', [App\Http\Controllers\RiwayatTagihanAktifController::class, 'getSummaryData'])->name('riwayat-tagihan-aktif.summary');

    // Laporan Tagihan Bulanan Routes
    Route::get('laporan-tagihan-bulanan', LaporanTagihanBulanan::class)->name('laporan-tagihan-bulanan.index');
    Route::post('laporan-tagihan-bulanan/update', [LaporanTagihanBulananController::class, 'updateLaporanTagihan'])->name('laporan-tagihan-bulanan.update');

    // Monitoring Pembayaran (AR Performance)
    // AJAX endpoints (still needed for modal)
    Route::get('monitoring-pembayaran/transactions', [MonitoringPembayaranController::class, 'getTransactions'])->name('monitoring-pembayaran.transactions');
    Route::get('monitoring-pembayaran/export-pdf', [MonitoringPembayaranController::class, 'exportPDF'])->name('monitoring-pembayaran.export-pdf');

    Route::get('laporan-pengembalian', \App\Livewire\LaporanPengembalian::class)->name('laporan-pengembalian.index');
    Route::get('laporan-pengembalian/export-pdf', [\App\Http\Controllers\LaporanPengembalianController::class, 'exportPdf'])->name('laporan-pengembalian.export-pdf');

    // This one seems to be SFinlog controller in root folder (if exists) or SFinlog folder
    Route::get('report-penyaluran-dana-investasi', [PenyaluranDanaInvestasiController::class, 'index'])->name('report-penyaluran-dana-investasi.index');

    Route::get('laporan-investasi-sfinance', LaporanInvestasiSFinance::class)->name('laporan-investasi-sfinance.index');

    Route::prefix('jenis-investasi')->name('jenis-investasi.')->group(function () {
        Route::post('/', [JenisInvestasiController::class, 'store'])->name('store');
        Route::get('{id}/edit', [JenisInvestasiController::class, 'edit'])->name('edit');
        Route::put('{id}', [JenisInvestasiController::class, 'update'])->name('update');
        Route::delete('{id}', [JenisInvestasiController::class, 'destroy'])->name('destroy');
        Route::post('{id}/upload-bukti', [JenisInvestasiController::class, 'uploadBukti'])->name('upload-bukti');
    });

    // Pengembalian Investasi Routes
    Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PengembalianInvestasiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\PengembalianInvestasiController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\PengembalianInvestasiController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\PengembalianInvestasiController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\PengembalianInvestasiController::class, 'destroy'])->name('destroy');
    });

    // Form Kerja Investor Routes (Legacy - redirect to pengajuan-investasi)
    Route::prefix('form-kerja-investor')->name('form-kerja-investor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PengajuanInvestasiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\PengajuanInvestasiController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\PengajuanInvestasiController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'destroy'])->name('destroy');
        Route::post('{id}/update-status', [\App\Http\Controllers\PengajuanInvestasiController::class, 'updateStatus'])->name('update-status');
        Route::post('{id}/upload-bukti', [\App\Http\Controllers\PengajuanInvestasiController::class, 'uploadBuktiTransfer'])->name('upload-bukti');
        Route::post('{id}/generate-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
    });

    // Pengajuan Investasi Routes
    Route::prefix('pengajuan-investasi')->name('pengajuan-investasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PengajuanInvestasiController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\PengajuanInvestasiController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PengajuanInvestasiController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\PengajuanInvestasiController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'destroy'])->name('destroy');
        Route::post('{id}/approval', [\App\Http\Controllers\PengajuanInvestasiController::class, 'approval'])->name('approval');
        Route::get('history/{historyId}', [\App\Http\Controllers\PengajuanInvestasiController::class, 'getHistoryDetail'])->name('history.detail');
        Route::post('{id}/update-status', [\App\Http\Controllers\PengajuanInvestasiController::class, 'updateStatus'])->name('update-status');
        Route::post('{id}/upload-bukti', [\App\Http\Controllers\PengajuanInvestasiController::class, 'uploadBuktiTransfer'])->name('upload-bukti');
        Route::get('{id}/preview-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'previewKontrak'])->name('preview-kontrak');
        Route::get('{id}/download-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'downloadKontrakPdf'])->name('download-kontrak');
        Route::post('{id}/generate-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
        Route::get('{id}/download-sertifikat', [\App\Http\Controllers\PengajuanInvestasiController::class, 'downloadSertifikat'])->name('download-sertifikat');
    });


    // config matrix pinjaman
    // Route::get('config-matrix-pinjaman', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'index'])->name('matrixpinjaman');

    Route::post('config-matrix-pinjaman', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'store'])->name('config-matrix-pinjaman.store');
    Route::get('config-matrix-pinjaman/{id}/edit', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'edit'])->name('config-matrix-pinjaman.edit');
    Route::put('config-matrix-pinjaman/{id}', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'update'])->name('config-matrix-pinjaman.update');
    Route::delete('config-matrix-pinjaman/{id}', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'destroy'])->name('config-matrix-pinjaman.destroy');
    Route::get('config-matrix-score', ConfigMatrixScore::class)->name('matrixscore');

    // Master Debitur dan Investor
    Route::prefix('master-data/debitur-investor')->name('master-data.debitur-investor.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'destroy'])->name('destroy');

        Route::patch('{id}/toggle-status', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('{id}/unlock', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'unlock'])->name('unlock');
        Route::delete('{id}/delete-signature', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'deleteSignature'])->name('delete-signature');
        Route::get('{id}/history-kol', \App\Livewire\KolHistoryIndex::class)->name('history-kol');
    });

    // Master KOL
    Route::prefix('master-data/kol')->name('master-data.kol.')->group(function () {
        // Route::get('/', [\App\Http\Controllers\Master\MasterKolController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Master\MasterKolController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\MasterKolController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'destroy'])->name('destroy');
    });

    // Master Sumber Pendanaan Eksternal
    Route::prefix('master-data/sumber-pendanaan-eksternal')->name('master-data.sumber-pendanaan-eksternal.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'destroy'])->name('destroy');
    });

    // Master Karyawan SKI
    Route::prefix('master-data/karyawan-ski')->name('master-data.karyawan-ski.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'index'])->name('index');
        Route::get('{id}', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'show'])->name('show');
        Route::post('/', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'store'])->name('store');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'destroy'])->name('destroy');
        Route::patch('{id}/toggle-status', [\App\Http\Controllers\Master\MasterKaryawanSkiController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Master Cells Project
    Route::prefix('master-data/cells-project')->name('master-data.cells-project.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Master\CellsProjectController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\CellsProjectController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\CellsProjectController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\CellsProjectController::class, 'destroy'])->name('destroy');
    });

    // Global Search
    Route::get('search', \App\Http\Controllers\GlobalSearchController::class)->name('search');
    Route::get('search/api', [\App\Http\Controllers\GlobalSearchController::class, 'api'])->name('search.api');
    Route::get('notif-read/{id}', [NotificationController::class, 'read_redirect']);
    Route::post('notif-hide/{id}', [NotificationController::class, 'hide_redirect']);
    Route::post('notif-read-all', [NotificationController::class, 'readall']);
    Route::get('/check-notifications', [NotificationController::class, 'checkNew']);
    Route::resource('notification', NotificationController::class);

    Route::prefix('portofolio')->name('portofolio.')->group(function () {
        Route::get('laporan-investasi/{id}', [\App\Http\Controllers\PortofolioController::class, 'getData'])->name('get-data');
        Route::post('store/{id}', [\App\Http\Controllers\PortofolioController::class, 'store'])->name('store');
    });
});

require __DIR__ . '/auth.php';
