<?php

use App\Livewire\Dashboard;
use App\Http\Controllers\SFinlog\ArPerbulanController;
use App\Http\Controllers\SFinlog\DebiturPiutangController;
use App\Livewire\SFinlog\KertasKerjaInvestorSFinlog;
use App\Http\Controllers\SFinlog\PeminjamanController;
use App\Http\Controllers\SFinlog\PengajuanInvestasiController;
use App\Http\Controllers\SFinlog\PengajuanRestrukturisasiController;
use App\Http\Controllers\SFinlog\PengembalianPinjamanController;
use App\Http\Controllers\SFinlog\PenyaluranDanaInvestasiController;
use App\Http\Controllers\SFinlog\PenyaluranDepositoController;
use App\Http\Controllers\SFinlog\ProgramRestrukturisasiController;
use App\Http\Controllers\SFinlog\EvaluasiRestrukturisasiController;
use App\Livewire\DebiturPiutangIndex;
use App\Livewire\PenyaluranDeposito\PenyaluranDepositoIndex;
use App\Livewire\PengembalianInvestasi;
use App\Livewire\SFinlog\DashboardPembiayaanSfinlog;
use App\Livewire\SFinlog\DashboardInvestasiDepositoSfinlog;
use Illuminate\Support\Facades\Route;

// Dashboard Pembiayaan SFinlog - Protected by permission
Route::get('dashboard/pembiayaan', DashboardPembiayaanSfinlog::class)->name('dashboard.pembiayaan')->middleware('can:sfinlog.menu.dashboard_pembiayaan');

// Dashboard Investasi Deposito SFinlog - Protected by permission
Route::get('dashboard/investasi-deposito', DashboardInvestasiDepositoSfinlog::class)->name('dashboard.investasi-deposito')->middleware('can:sfinlog.menu.dashboard_investasi_deposito');

// Peminjaman
Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    // Static & Specific Routes FIRST
    Route::post('update-npa-status', [PeminjamanController::class, 'updateNpaStatus'])->name('update-npa-status');
    Route::get('data', [PeminjamanController::class, 'getData'])->name('data');
    Route::get('{id}/show-kontrak', [PeminjamanController::class, 'showKontrak'])->name('show-kontrak');
    Route::get('download-kontrak/{id}', [PeminjamanController::class, 'downloadKontrakPdf'])->name('download-kontrak');

    // Dynamic / Standard Resource Routes LAST
    Route::post('/', [PeminjamanController::class, 'store'])->name('store');
    Route::put('{id}', [PeminjamanController::class, 'update'])->name('update');
    Route::delete('{id}', [PeminjamanController::class, 'destroy'])->name('destroy');
});

// AR Perbulan - Handled by Livewire (see livewire_route.php)
// Index route: sfinlog.ar-perbulan.index
Route::post('ar-perbulan/update', [ArPerbulanController::class, 'updateAR'])->name('ar-perbulan.update');


// AR Performance - Moved to Livewire (see livewire_route.php)
// Index route: sfinlog.ar-performance.index
// AJAX endpoints (needed for modal)
Route::get('ar-performance/transactions', [\App\Http\Controllers\SFinlog\ArPerformanceFinlogController::class, 'getTransactions'])->name('ar-performance.transactions');
Route::get('ar-performance/export-pdf', [\App\Http\Controllers\SFinlog\ArPerformanceFinlogController::class, 'exportPDF'])->name('ar-performance.export-pdf');

// Program Restrukturisasi Routes - Full Livewire
Route::prefix('program-restrukturisasi')->name('program-restrukturisasi.')->group(function () {
    Route::get('/', \App\Livewire\ProgramRestrukturisasi\Index::class)->name('index');
    Route::get('create', \App\Livewire\ProgramRestrukturisasi\Create::class)->name('create');
    Route::get('{id}', \App\Livewire\ProgramRestrukturisasi\Show::class)->name('show');
    Route::get('{id}/edit', \App\Livewire\ProgramRestrukturisasi\Edit::class)->name('edit');
    Route::post('/', [ProgramRestrukturisasiController::class, 'store'])->name('store');
    Route::get('approved', [ProgramRestrukturisasiController::class, 'getApprovedRestrukturisasi'])->name('approved');
    Route::get('detail/{id}', [ProgramRestrukturisasiController::class, 'getRestrukturisasiDetail'])->name('detail');
});

// Pengembalian Pinjaman - Handled by Livewire (see livewire_route.php)
// Route pengembalian untuk SFinlog sudah menggunakan Livewire component
// Index route: sfinlog.pengembalian-pinjaman.index
// Optional: provide a POST endpoint for non-Livewire submissions or UniversalFormAction
Route::post('pengembalian-pinjaman/store', [PengembalianPinjamanController::class, 'store'])
    ->name('pengembalian-pinjaman.store');

// Debitur Piutang

Route::get('debitur-piutang/histori', [DebiturPiutangController::class, 'getHistoriPembayaran'])->name('debitur-piutang.histori');
Route::get('debitur-piutang/summary', [DebiturPiutangController::class, 'getSummaryData'])->name('debitur-piutang.summary');

// Report Pengembalian Finlog
Route::get('report-pengembalian', \App\Livewire\SFinlog\ReportPengembalian::class)->name('report-pengembalian.index');

// Investasi Routes
Route::prefix('form-kerja-investor')->name('form-kerja-investor.')->group(function () {
    Route::get('/', [PengajuanInvestasiController::class, 'index'])->name('index');
    Route::post('/', [PengajuanInvestasiController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanInvestasiController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanInvestasiController::class, 'destroy'])->name('destroy');
    Route::post('{id}/update-status', [PengajuanInvestasiController::class, 'updateStatus'])->name('update-status');
    Route::post('{id}/upload-bukti', [PengajuanInvestasiController::class, 'uploadBuktiTransfer'])->name('upload-bukti');
    Route::post('{id}/generate-kontrak', [PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
});

Route::prefix('pengajuan-investasi')->name('pengajuan-investasi.')->group(function () {
    Route::get('create', [PengajuanInvestasiController::class, 'create'])->name('create');
    Route::post('/', [PengajuanInvestasiController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanInvestasiController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanInvestasiController::class, 'destroy'])->name('destroy');
    Route::post('{id}/approval', [PengajuanInvestasiController::class, 'approval'])->name('approval');
    Route::post('{id}/upload-bukti', [PengajuanInvestasiController::class, 'uploadBuktiTransfer'])->name('upload-bukti');
    Route::get('{id}/preview-kontrak', [PengajuanInvestasiController::class, 'previewKontrak'])->name('preview-kontrak');
    Route::get('{id}/download-kontrak', [PengajuanInvestasiController::class, 'downloadKontrakPdf'])->name('download-kontrak');
    Route::post('{id}/generate-kontrak', [PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
    Route::get('{id}/download-sertifikat', [PengajuanInvestasiController::class, 'downloadSertifikat'])->name('download-sertifikat');

    Route::get('history/{historyId}', [PengajuanInvestasiController::class, 'getHistoryDetail'])->name('history-detail');
});

// Report Penyaluran Dana Investasi
Route::get('report-penyaluran-dana-investasi', [PenyaluranDanaInvestasiController::class, 'index'])->name('report-penyaluran-dana-investasi.index');

// Pengembalian Investasi SFinlog
Route::get('pengembalian-investasi', \App\Livewire\SFinlog\PengembalianInvestasiFinlog::class)->name('pengembalian-investasi.index');
Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
    Route::post('/', [\App\Http\Controllers\SFinlog\PengembalianInvestasiController::class, 'store'])->name('store');
});

// Penyaluran Deposito SFinlog
Route::get('penyaluran-deposito-sfinlog', \App\Livewire\SFinlog\PenyaluranDepositoSfinlogIndex::class)->name('penyaluran-deposito-sfinlog.index');
Route::prefix('penyaluran-deposito-sfinlog')->name('penyaluran-deposito-sfinlog.')->group(function () {
    Route::post('/', [PenyaluranDepositoController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PenyaluranDepositoController::class, 'edit'])->name('edit');
    Route::put('{id}', [PenyaluranDepositoController::class, 'update'])->name('update');
    Route::delete('{id}', [PenyaluranDepositoController::class, 'destroy'])->name('destroy');
    Route::post('{id}/upload-bukti', [PenyaluranDepositoController::class, 'uploadBukti'])->name('upload-bukti');
});

// Kertas Kerja Investor SFinlog
Route::get('kertas-kerja-investor-sfinlog', KertasKerjaInvestorSFinlog::class)->name('kertas-kerja-investor-sfinlog.index');
