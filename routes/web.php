<?php

use App\Livewire\Dashboard;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\ConfigMatrixScore;
use Illuminate\Support\Facades\Route;
use App\Livewire\PermissionManagement;
use App\Http\Controllers\ArPerbulanController;
use App\Http\Controllers\ArPerformanceController;
use App\Http\Controllers\FormKerjaInvestorController;
use App\Http\Controllers\ReportPengembalianController;
use App\Http\Controllers\PengembalianPinjamanController;
use App\Http\Controllers\Peminjaman\PeminjamanController;
use App\Http\Controllers\PenyaluranDanaInvestasiController;
use App\Http\Controllers\RencanaPenagihanDepositoController;
use App\Http\Controllers\Peminjaman\PeminjamanInvoiceController;
use App\Http\Controllers\Peminjaman\PeminjamanInstallmentFinancingController;
use App\Http\Controllers\KertasKerjaInvestorSFinanceController;

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

// Authenticated routes with Jetstream
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('users', UserManagement::class)->name('users.index');
    Route::get('roles', RoleManagement::class)->name('roles.index');
    Route::get('permissions', PermissionManagement::class)->name('permissions.index');
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
    Route::get('peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.detail');
    Route::get('peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::get('peminjaman/{id}/preview-kontrak', [PeminjamanController::class, 'previewKontrak'])->name('peminjaman.preview-kontrak');
    Route::get('ajukan-peminjaman', [PeminjamanController::class, 'create'])->name('ajukanpeminjaman');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    // Route::post('peminjaman/invoice', [PeminjamanInvoiceController::class, 'store'])->name('peminjaman.invoice.store');
    Route::post('peminjaman/installment', [PeminjamanInstallmentFinancingController::class, 'store'])->name('peminjaman.installment.store');
    Route::post('peminjaman/po', [\App\Http\Controllers\Peminjaman\PeminjamanPoFinancingController::class, 'store'])->name('peminjaman.po.store');
    Route::post('peminjaman/factoring', [\App\Http\Controllers\Peminjaman\PeminjamanFactoringController::class, 'store'])->name('peminjaman.factoring.store');
    Route::put('peminjaman/factoring/{id}', [\App\Http\Controllers\Peminjaman\PeminjamanFactoringController::class, 'update'])->name('peminjaman.factoring.update');
    Route::delete('peminjaman/factoring/{id}', [\App\Http\Controllers\Peminjaman\PeminjamanFactoringController::class, 'destroy'])->name('peminjaman.factoring.destroy');
    Route::post('peminjaman/{id}/approval', [PeminjamanController::class, 'approval'])->name('peminjaman.approval');
    Route::get('peminjaman/history/{historyId}', [PeminjamanController::class, 'getHistoryDetail'])->name('peminjaman.history.detail');
    Route::patch('peminjaman/{id}/toggle-active', [PeminjamanController::class, 'toggleActive'])->name('peminjaman.toggle-active');

    Route::get('pengembalian', [PengembalianPinjamanController::class, 'index'])->name('pengembalian.index');
    Route::get('pengembalian/create', [PengembalianPinjamanController::class, 'create'])->name('pengembalian.create');

    Route::get('debitur-piutang', function () {
        return view('livewire.debitur-piutang.index');
    })->name('debitur-piutang.index');

    Route::get('ar-perbulan', [ArPerbulanController::class, 'index'])->name('ar-perbulan.index');
    Route::get('ar-performance', [ArPerformanceController::class, 'index'])->name('ar-performance.index');
    Route::get('ar-performance/transactions', [ArPerformanceController::class, 'getTransactions'])->name('ar-performance.transactions');

    Route::get('report-pengembalian', [ReportPengembalianController::class, 'index'])->name('report-pengembalian.index');

    Route::get('report-penyaluran-dana-investasi', [PenyaluranDanaInvestasiController::class, 'index'])->name('report-penyaluran-dana-investasi.index');
    Route::get('kertas-kerja-investor-sfinance', [KertasKerjaInvestorSFinanceController::class, 'index'])->name('kertas-kerja-investor-sfinance.index');    
    // Rencana Penagihan Deposito
    Route::prefix('rencana-penagihan-deposito')->name('rencana-penagihan-deposito.')->group(function () {
        Route::get('ski', [RencanaPenagihanDepositoController::class, 'ski'])->name('ski');
        Route::get('penerima-dana', [RencanaPenagihanDepositoController::class, 'penerimaDana'])->name('penerima-dana');
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
        Route::post('{id}/generate-kontrak', [\App\Http\Controllers\PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
    });

    Route::get('config-matrix-pinjaman', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'index'])->name('matrixpinjaman');
    Route::post('config-matrix-pinjaman', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'store']);
    Route::get('config-matrix-pinjaman/{id}/edit', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'edit']);
    Route::put('config-matrix-pinjaman/{id}', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'update']);
    Route::delete('config-matrix-pinjaman/{id}', [\App\Http\Controllers\ConfigMatrixPinjamanController::class, 'destroy']);
    Route::get('config-matrix-score', ConfigMatrixScore::class)->name('matrixscore');
    Route::get('master-data/master-data-kol', [\App\Http\Controllers\Master\MasterKolController::class, 'index'])->name('masterdatakol.index');
    Route::get('master-data/sumber-pendanaan-eksternal', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'index'])->name('sumberpendanaaneksternal.index');

    // Master Debitur dan Investor
    Route::prefix('master-data/debitur-investor')->name('master-data.debitur-investor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'destroy'])->name('destroy');
        Route::patch('{id}/toggle-status', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('{id}/delete-signature', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'deleteSignature'])->name('delete-signature');
        Route::get('{id}/history-kol', [\App\Http\Controllers\Master\DebiturDanInvestorController::class, 'historyKol'])->name('history-kol');
    });

    // Master KOL
    Route::prefix('master-data/kol')->name('master-data.kol.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\MasterKolController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\MasterKolController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Master\MasterKolController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\MasterKolController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'destroy'])->name('destroy');
    });

    // Master Sumber Pendanaan Eksternal
    Route::prefix('master-data/sumber-pendanaan-eksternal')->name('master-data.sumber-pendanaan-eksternal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'create'])->name('create');
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
});

require __DIR__ . '/auth.php';
