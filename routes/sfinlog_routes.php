<?php

use App\Livewire\Dashboard;
use App\Http\Controllers\SFinlog\ArPerformanceController;
use App\Http\Controllers\SFinlog\ArPerbulanController;
use App\Http\Controllers\SFinlog\DebiturPiutangController;
use App\Http\Controllers\SFinlog\KertasKerjaInvestorSFinlogController;
use App\Http\Controllers\SFinlog\PeminjamanController;
use App\Http\Controllers\SFinlog\PengajuanInvestasiController;
use App\Http\Controllers\SFinlog\PengajuanRestrukturisasiController;
use App\Http\Controllers\SFinlog\PengembalianInvestasiController;
use App\Http\Controllers\SFinlog\PengembalianPinjamanController;
use App\Http\Controllers\SFinlog\PenyaluranDanaInvestasiController;
use App\Http\Controllers\SFinlog\PenyaluranDepositoController;
use App\Http\Controllers\SFinlog\ProgramRestrukturisasiController;
use App\Http\Controllers\SFinlog\EvaluasiRestrukturisasiController;
use App\Livewire\DebiturPiutangIndex;
use App\Livewire\PenyaluranDeposito\PenyaluranDepositoIndex;
use App\Livewire\PengembalianInvestasi;
use App\Livewire\ReportPengembalian;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('dashboard', Dashboard::class)->name('dashboard.index');

// Peminjaman
Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    Route::post('/', [PeminjamanController::class, 'store'])->name('store');
    Route::put('{id}', [PeminjamanController::class, 'update'])->name('update');
    Route::delete('{id}', [PeminjamanController::class, 'destroy'])->name('destroy');
    Route::post('update-npa-status', [PeminjamanController::class, 'updateNpaStatus'])->name('update-npa-status');
    Route::get('data', [PeminjamanController::class, 'getData'])->name('data');
    Route::get('{id}/show-kontrak', [PeminjamanController::class, 'showKontrak'])->name('show-kontrak');
});

// AR Perbulan - Handled by Livewire (see livewire_route.php)
// Index route: sfinlog.ar-perbulan.index
Route::post('ar-perbulan/update', [ArPerbulanController::class, 'updateAR'])->name('ar-perbulan.update');


// AR Performance
Route::get('ar-performance', [ArPerformanceController::class, 'index'])->name('ar-performance.index');
Route::get('ar-performance/transactions', [ArPerformanceController::class, 'getTransactions'])->name('ar-performance.transactions');
Route::get('ar-performance/export-pdf', [ArPerformanceController::class, 'exportPDF'])->name('ar-performance.export-pdf');

// Restrukturisasi Routes
Route::prefix('pengajuan-restrukturisasi')->name('pengajuan-restrukturisasi.')->group(function () {
    Route::get('/', [PengajuanRestrukturisasiController::class, 'index'])->name('index');
    Route::post('/', [PengajuanRestrukturisasiController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanRestrukturisasiController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanRestrukturisasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanRestrukturisasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanRestrukturisasiController::class, 'destroy'])->name('destroy');
    Route::get('peminjaman/{idDebitur}', [PengajuanRestrukturisasiController::class, 'getPeminjamanListApi'])->name('peminjaman.list');
    Route::get('detail-pengajuan/{id}', [PengajuanRestrukturisasiController::class, 'getPengajuanDetail'])->name('detail-pengajuan');
    // Evaluasi endpoints
    Route::post('{id}/evaluasi', [EvaluasiRestrukturisasiController::class, 'save'])->name('evaluasi.save');
    Route::post('{id}/decision', [EvaluasiRestrukturisasiController::class, 'decision'])->name('evaluasi.decision');
});

// Program Restrukturisasi Routes
Route::prefix('program-restrukturisasi')->name('program-restrukturisasi.')->group(function () {
    Route::get('/', function () {return view('livewire.sfinlog.program-restrukturisasi.index');})->name('index');
    Route::get('create', \App\Livewire\ProgramRestrukturisasiCreate::class)->name('create');
    Route::get('{id}', \App\Livewire\ProgramRestrukturisasiShow::class)->name('show');
    Route::get('{id}/edit', \App\Livewire\ProgramRestrukturisasiEdit::class)->name('edit');
    Route::post('/', [ProgramRestrukturisasiController::class, 'store'])->name('store');
    Route::get('approved', [ProgramRestrukturisasiController::class, 'getApprovedRestrukturisasi'])->name('approved');
    Route::get('detail/{id}', [ProgramRestrukturisasiController::class, 'getRestrukturisasiDetail'])->name('detail');
});

// Pengembalian Pinjaman - Handled by Livewire (see livewire_route.php)
// Route pengembalian untuk SFinlog sudah menggunakan Livewire component
// Index route: sfinlog.pengembalian-pinjaman.index

// Debitur Piutang
Route::get('debitur-piutang', DebiturPiutangIndex::class)->name('debitur-piutang.index');
Route::get('debitur-piutang/histori', [DebiturPiutangController::class, 'getHistoriPembayaran'])->name('debitur-piutang.histori');
Route::get('debitur-piutang/summary', [DebiturPiutangController::class, 'getSummaryData'])->name('debitur-piutang.summary');

// Report Pengembalian
Route::get('report-pengembalian', ReportPengembalian::class)->name('report-pengembalian.index');

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
    Route::post('{id}/generate-kontrak', [PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
    
    Route::get('history/{historyId}', [PengajuanInvestasiController::class, 'getHistoryDetail'])->name('history-detail');
});

// Report Penyaluran Dana Investasi
Route::get('report-penyaluran-dana-investasi', [PenyaluranDanaInvestasiController::class, 'index'])->name('report-penyaluran-dana-investasi.index');

// Penyaluran Deposito
Route::get('penyaluran-deposito', PenyaluranDepositoIndex::class)->name('penyaluran-deposito.index');
Route::prefix('penyaluran-deposito')->name('penyaluran-deposito.')->group(function () {
    Route::post('/', [PenyaluranDepositoController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PenyaluranDepositoController::class, 'edit'])->name('edit');
    Route::put('{id}', [PenyaluranDepositoController::class, 'update'])->name('update');
    Route::delete('{id}', [PenyaluranDepositoController::class, 'destroy'])->name('destroy');
    Route::post('{id}/upload-bukti', [PenyaluranDepositoController::class, 'uploadBukti'])->name('upload-bukti');
});

// Kertas Kerja Investor SFinlog
Route::get('kertas-kerja-investor-sfinlog', [KertasKerjaInvestorSFinlogController::class, 'index'])->name('kertas-kerja-investor-sfinlog.index');

// Pengembalian Investasi
Route::get('pengembalian-investasi', PengembalianInvestasi::class)->name('pengembalian-investasi.index');
Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
    Route::post('/', [PengembalianInvestasiController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PengembalianInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengembalianInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengembalianInvestasiController::class, 'destroy'])->name('destroy');
});

