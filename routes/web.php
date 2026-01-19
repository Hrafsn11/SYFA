<?php

use App\Http\Controllers\RencanaPenagihanDepositoController;
use App\Http\Controllers\ArPerbulanController;
use App\Http\Controllers\ArPerformanceController;
use App\Http\Controllers\KertasKerjaInvestorSFinanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Peminjaman\PeminjamanController;
use App\Http\Controllers\PengembalianPinjamanController;
use App\Http\Controllers\PenyaluranDanaInvestasiController;
use App\Http\Controllers\PenyaluranDepositoController;
use App\Livewire\ArPerbulan;
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

    // Peminjaman Routes
    Route::prefix('peminjaman')->name('peminjaman.')->controller(PeminjamanController::class)->group(function () {
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

    // Route::get('pengembalian', [PengembalianPinjamanController::class, 'index'])->name('pengembalian.index');
    // Route::get('pengembalian/create', [PengembalianPinjamanController::class, 'create'])->name('pengembalian.create');
    Route::post('pengembalian/store', [PengembalianPinjamanController::class, 'store'])->name('pengembalian.store');
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
    Route::get('peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.detail');
    Route::get('peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::post('peminjaman/{id}/preview-kontrak', [PeminjamanController::class, 'previewKontrak'])->name('peminjaman.preview-kontrak');
    Route::post('peminjaman/{id}/download-kontrak', [PeminjamanController::class, 'downloadKontrak'])->name('peminjaman.download-kontrak');
    Route::get('ajukan-peminjaman', [PeminjamanController::class, 'create'])->name('ajukanpeminjaman');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('peminjaman/{id}/approval', [PeminjamanController::class, 'approval'])->name('peminjaman.approval');
    Route::get('peminjaman/history/{historyId}', [PeminjamanController::class, 'getHistoryDetail'])->name('peminjaman.history.detail');
    Route::patch('peminjaman/{id}/toggle-active', [PeminjamanController::class, 'toggleActive'])->name('peminjaman.toggle-active');

    // Restrukturisasi Routes - Controller actions only (index route in livewire_route.php)
    Route::prefix('pengajuan-restrukturisasi')->name('pengajuan-restrukturisasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'destroy'])->name('destroy');
        Route::get('peminjaman/{idDebitur}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'getPeminjamanListApi'])->name('peminjaman.list');
        Route::get('detail-pengajuan/{id}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'getPengajuanDetail'])->name('detail-pengajuan');
        // Evaluasi endpoints
        Route::post('{id}/evaluasi', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'save'])->name('evaluasi.save');
        Route::post('{id}/decision', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'decision'])->name('evaluasi.decision');
    });

    // Program Restrukturisasi Routes - Full Livewire
    Route::prefix('program-restrukturisasi')->name('program-restrukturisasi.')->group(function () {
        Route::get('/', \App\Livewire\ProgramRestrukturisasi\Index::class)->name('index');
        Route::get('create', \App\Livewire\ProgramRestrukturisasi\Create::class)->name('create');
        Route::get('{id}', \App\Livewire\ProgramRestrukturisasi\Show::class)->name('show');
        Route::get('{id}/edit', \App\Livewire\ProgramRestrukturisasi\Edit::class)->name('edit');
        // Controller endpoints (if still needed)
        Route::post('/', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'store'])->name('store');
        Route::get('approved', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getApprovedRestrukturisasi'])->name('approved');
        Route::get('detail/{id}', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getRestrukturisasiDetail'])->name('detail');
    });

    // Detail restrukturisasi menggunakan Controller dengan Pure AJAX (bukan Livewire component)
    Route::get('detail-restrukturisasi/{id}', [\App\Http\Controllers\PengajuanRestrukturisasiController::class, 'show'])->name('detail-restrukturisasi');

    // Pengembalian Pinjaman - Migrated to Livewire (see routes/livewire_route.php)
    // Index and Create routes are handled by Livewire components
    Route::post('pengembalian/store', [PengembalianPinjamanController::class, 'store'])->name('pengembalian.store');
    Route::get('pengembalian/export-pdf', [PengembalianPinjamanController::class, 'exportPdf'])->name('pengembalian.export-pdf');

    // Debitur Piutang - Migrated to Livewire (see routes/livewire_route.php)
    // Route::get('debitur-piutang', function () {
    //     return view('livewire.debitur-piutang.index');
    // })->name('debitur-piutang.index');

    // AJAX endpoints for Debitur Piutang modals (Table 2 & 3)
    Route::get('debitur-piutang/histori', [App\Http\Controllers\DebiturPiutangController::class, 'getHistoriPembayaran'])->name('debitur-piutang.histori');
    Route::get('debitur-piutang/summary', [App\Http\Controllers\DebiturPiutangController::class, 'getSummaryData'])->name('debitur-piutang.summary');

    // Ar Routes
    Route::get('ar-perbulan', ArPerbulan::class)->name('ar-perbulan.index');
    Route::post('ar-perbulan/update', [ArPerbulanController::class, 'updateAR'])->name('ar-perbulan.update');

    // AR Performance - Migrated to Livewire (see routes/livewire_route.php)
    // Main route moved to Livewire
    // Route::get('ar-performance', [ArPerformanceController::class, 'index'])->name('ar-performance.index');

    // AJAX endpoints (still needed for modal)
    Route::get('ar-performance/transactions', [ArPerformanceController::class, 'getTransactions'])->name('ar-performance.transactions');
    Route::get('ar-performance/export-pdf', [ArPerformanceController::class, 'exportPDF'])->name('ar-performance.export-pdf');

    Route::get('report-pengembalian', \App\Livewire\ReportPengembalian::class)->name('report-pengembalian.index');
    Route::get('report-pengembalian/export-pdf', [\App\Http\Controllers\ReportPengembalianController::class, 'exportPdf'])->name('report-pengembalian.export-pdf');

    Route::get('report-penyaluran-dana-investasi', [PenyaluranDanaInvestasiController::class, 'index'])->name('report-penyaluran-dana-investasi.index');
    Route::get('kertas-kerja-investor-sfinance', [KertasKerjaInvestorSFinanceController::class, 'index'])->name('kertas-kerja-investor-sfinance.index');

    Route::prefix('penyaluran-deposito')->name('penyaluran-deposito.')->group(function () {
        Route::post('/', [PenyaluranDepositoController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PenyaluranDepositoController::class, 'edit'])->name('edit');
        Route::put('{id}', [PenyaluranDepositoController::class, 'update'])->name('update');
        Route::delete('{id}', [PenyaluranDepositoController::class, 'destroy'])->name('destroy');
        Route::post('{id}/upload-bukti', [PenyaluranDepositoController::class, 'uploadBukti'])->name('upload-bukti');
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
        Route::post('{id}/download-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'downloadKontrakPdf'])->name('download-kontrak');
        Route::post('{id}/generate-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
        Route::get('{id}/download-sertifikat', [\App\Http\Controllers\PengajuanInvestasiController::class, 'downloadSertifikat'])->name('download-sertifikat');
    });

    // Kertas Kerja Investor SFinance 
    Route::prefix('kertas-kerja-investor-sfinance')->name('kertas-kerja-investor-sfinance.')->group(function () {
        Route::get('/', [KertasKerjaInvestorSFinanceController::class, 'index'])->name('index');
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

    // Route::prefix('portofolio')->name('portofolio.')->group(function () {
    //     Route::get('laporan-investasi/{id}', [\App\Http\Controllers\PortofolioController::class, 'getData'])->name('get-data');
    //     Route::post('store/{id}', [\App\Http\Controllers\PortofolioController::class, 'store'])->name('store');
    // });
});

require __DIR__ . '/auth.php';
