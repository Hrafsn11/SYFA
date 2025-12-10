<?php

use App\Livewire\Dashboard;
use App\Http\Controllers\ArPerbulanController;
use App\Http\Controllers\ArPerformanceController;
use App\Http\Controllers\DebiturPiutangController;
use App\Http\Controllers\KertasKerjaInvestorSFinanceController;
use App\Http\Controllers\Peminjaman\PeminjamanController;
use App\Http\Controllers\PengembalianPinjamanController;
use App\Http\Controllers\PenyaluranDanaInvestasiController;
use App\Http\Controllers\PenyaluranDepositoController;
use App\Http\Controllers\PengajuanInvestasiController;
use App\Http\Controllers\PengajuanRestrukturisasiController;
use App\Http\Controllers\PengembalianInvestasiController;
use App\Livewire\ArPerbulan;
use App\Livewire\ArPerformanceIndex;
use App\Livewire\DebiturPiutangIndex;
use App\Livewire\PenyaluranDeposito\PenyaluranDepositoIndex;
use App\Livewire\PengembalianInvestasi;
use App\Livewire\ReportPengembalian;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('dashboard', Dashboard::class)->name('dashboard.index');
Route::get('dashboard/pembiayaan', Dashboard::class)->name('dashboard.pembiayaan');
Route::get('dashboard/investasi-deposito', Dashboard::class)->name('dashboard.investasi-deposito');

// Peminjaman Routes
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

// AR Perbulan
Route::get('ar-perbulan', ArPerbulan::class)->name('ar-perbulan.index');
Route::post('ar-perbulan/update', [ArPerbulanController::class, 'updateAR'])->name('ar-perbulan.update');

// AR Performance
Route::get('ar-performance', ArPerformanceIndex::class)->name('ar-performance.index');
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
    Route::post('{id}/evaluasi', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'save'])->name('evaluasi.save');
    Route::post('{id}/decision', [\App\Http\Controllers\EvaluasiRestrukturisasiController::class, 'decision'])->name('evaluasi.decision');
});

// Program Restrukturisasi Routes
Route::prefix('program-restrukturisasi')->name('program-restrukturisasi.')->group(function () {
    Route::get('/', function () {return view('program-restrukturisasi.index');})->name('index');
    Route::get('create', \App\Livewire\ProgramRestrukturisasiCreate::class)->name('create');
    Route::get('{id}', \App\Livewire\ProgramRestrukturisasiShow::class)->name('show');
    Route::get('{id}/edit', \App\Livewire\ProgramRestrukturisasiEdit::class)->name('edit');
    Route::post('/', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'store'])->name('store');
    Route::get('approved', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getApprovedRestrukturisasi'])->name('approved');
    Route::get('detail/{id}', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getRestrukturisasiDetail'])->name('detail');
});

// Pengembalian Routes
Route::get('pengembalian', [PengembalianPinjamanController::class, 'index'])->name('pengembalian.index');
Route::get('pengembalian/create', [PengembalianPinjamanController::class, 'create'])->name('pengembalian.create');
Route::post('pengembalian', [PengembalianPinjamanController::class, 'store'])->name('pengembalian.store');

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
    Route::get('/', [PengajuanInvestasiController::class, 'index'])->name('index');
    Route::get('create', [PengajuanInvestasiController::class, 'create'])->name('create');
    Route::post('/', [PengajuanInvestasiController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanInvestasiController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanInvestasiController::class, 'destroy'])->name('destroy');
    Route::post('{id}/approval', [PengajuanInvestasiController::class, 'approval'])->name('approval');
    Route::get('history/{historyId}', [PengajuanInvestasiController::class, 'getHistoryDetail'])->name('history.detail');
    Route::post('{id}/update-status', [PengajuanInvestasiController::class, 'updateStatus'])->name('update-status');
    Route::post('{id}/upload-bukti', [PengajuanInvestasiController::class, 'uploadBuktiTransfer'])->name('upload-bukti');
    Route::get('{id}/preview-kontrak', [PengajuanInvestasiController::class, 'previewKontrak'])->name('preview-kontrak');
    Route::post('{id}/generate-kontrak', [PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
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

// Kertas Kerja Investor
Route::get('kertas-kerja-investor-sfinance', [KertasKerjaInvestorSFinanceController::class, 'index'])->name('kertas-kerja-investor-sfinance.index');

// Pengembalian Investasi
Route::get('pengembalian-investasi', PengembalianInvestasi::class)->name('pengembalian-investasi.index');
Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
    Route::post('/', [PengembalianInvestasiController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PengembalianInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengembalianInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengembalianInvestasiController::class, 'destroy'])->name('destroy');
});
