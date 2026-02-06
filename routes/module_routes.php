<?php

use App\Livewire\Dashboard;
use App\Livewire\DashboardInvestasiDeposito;
use App\Livewire\DashboardPembiayaanSfinance;
use App\Http\Controllers\ArPerbulanController;
use App\Http\Controllers\ArPerformanceController;
use App\Http\Controllers\DebiturPiutangController;
use App\Livewire\KertasKerjaInvestorSFinance;
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

// Dashboard - Protected by permission
Route::get('dashboard', Dashboard::class)->name('dashboard.index')->middleware('can:sfinance.menu.dashboard_pembiayaan');
Route::get('dashboard/pembiayaan', DashboardPembiayaanSfinance::class)->name('dashboard.pembiayaan')->middleware('can:sfinance.menu.dashboard_pembiayaan');
Route::get('dashboard/investasi-deposito', DashboardInvestasiDeposito::class)->name('dashboard.investasi-deposito')->middleware('can:sfinance.menu.dashboard_pembiayaan_investasi');

// Peminjaman Routes - Index, Create, Edit sudah menggunakan Livewire
Route::get('peminjaman', \App\Livewire\PengajuanPinjaman\Index::class)->name('peminjaman');
Route::get('peminjaman/create', \App\Livewire\PengajuanPinjaman\Create::class)->name('peminjaman.create');
Route::get('peminjaman/{id}/edit', \App\Livewire\PengajuanPinjaman\Create::class)->name('peminjaman.edit');

// Peminjaman Routes - Controller (fitur yang belum ada di Livewire)
Route::get('peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.detail');
Route::put('peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
Route::post('peminjaman/{id}/preview-kontrak', [PeminjamanController::class, 'previewKontrak'])->name('peminjaman.preview-kontrak');
Route::post('peminjaman/{id}/download-kontrak', [PeminjamanController::class, 'downloadKontrak'])->name('peminjaman.download-kontrak');
// Route::get('ajukan-peminjaman', [PeminjamanController::class, 'create'])->name('ajukanpeminjaman'); // Diganti oleh peminjaman.create Livewire
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
    Route::post('{id}/update-dokumen', [PengajuanRestrukturisasiController::class, 'updateDokumen'])->name('update-dokumen');
    Route::get('peminjaman/{idDebitur}', [PengajuanRestrukturisasiController::class, 'getPeminjamanListApi'])->name('peminjaman.list');
    Route::get('detail-pengajuan/{id}', [PengajuanRestrukturisasiController::class, 'getPengajuanDetail'])->name('detail-pengajuan');
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
    Route::post('/', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'store'])->name('store');
    Route::get('approved', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getApprovedRestrukturisasi'])->name('approved');
    Route::get('detail/{id}', [\App\Http\Controllers\ProgramRestrukturisasiController::class, 'getRestrukturisasiDetail'])->name('detail');
});


Route::post('pengembalian/store', [PengembalianPinjamanController::class, 'store'])->name('pengembalian.store');

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
    Route::get('{id}/download-kontrak', [PengajuanInvestasiController::class, 'downloadKontrakPdf'])->name('download-kontrak');
    Route::post('{id}/generate-kontrak', [PengajuanInvestasiController::class, 'generateKontrak'])->name('generate-kontrak');
    Route::get('{id}/download-sertifikat', [PengajuanInvestasiController::class, 'downloadSertifikat'])->name('download-sertifikat');
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
Route::get('kertas-kerja-investor-sfinance', KertasKerjaInvestorSFinance::class)->name('kertas-kerja-investor-sfinance.index');

// Pengembalian Investasi sfinance
Route::get('pengembalian-investasi', PengembalianInvestasi::class)->name('pengembalian-investasi.index');
Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
    Route::post('/', [PengembalianInvestasiController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PengembalianInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengembalianInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengembalianInvestasiController::class, 'destroy'])->name('destroy');
});
