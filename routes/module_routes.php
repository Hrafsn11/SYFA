<?php

use App\Livewire\Dashboard;
use App\Livewire\DashboardInvestasiDeposito;
use App\Livewire\DashboardPembiayaanSfinance;
use App\Http\Controllers\ArPerbulanController;
use App\Http\Controllers\ArPerformanceController;
use App\Http\Controllers\DebiturPiutangController;
use App\Livewire\LaporanInvestasiSFinance;
use App\Http\Controllers\PengembalianPinjamanController;
use App\Http\Controllers\PenyaluranDanaInvestasiController;
use App\Http\Controllers\PengajuanInvestasiController;
use App\Http\Controllers\PengajuanCicilanController;
use App\Http\Controllers\PengembalianInvestasiController;
use App\Livewire\ArPerbulan;
use App\Livewire\ArPerformanceIndex;
use App\Livewire\DebiturPiutangIndex;
use App\Livewire\PenyaluranDanaInvestasi\PenyaluranDanaInvestasiIndex;
use App\Livewire\PengembalianInvestasi;
use App\Livewire\ReportPengembalian;
use Illuminate\Support\Facades\Route;

// Dashboard - Protected by permission
Route::get('dashboard', Dashboard::class)->name('dashboard.index')->middleware('can:sfinance.menu.dashboard_pembiayaan');
Route::get('dashboard/pembiayaan', DashboardPembiayaanSfinance::class)->name('dashboard.pembiayaan')->middleware('can:sfinance.menu.dashboard_pembiayaan');
Route::get('dashboard/investasi', DashboardInvestasiDeposito::class)->name('dashboard.investasi')->middleware('can:sfinance.menu.dashboard_pembiayaan_investasi');

// AR Perbulan
Route::get('laporan-tagihan-bulanan', ArPerbulan::class)->name('laporan-tagihan-bulanan.index');
Route::post('laporan-tagihan-bulanan/update', [ArPerbulanController::class, 'updateAR'])->name('laporan-tagihan-bulanan.update');

// AR Performance
Route::get('monitoring-pembayaran', ArPerformanceIndex::class)->name('monitoring-pembayaran.index');
Route::get('monitoring-pembayaran/transactions', [ArPerformanceController::class, 'getTransactions'])->name('monitoring-pembayaran.transactions');
Route::get('monitoring-pembayaran/export-pdf', [ArPerformanceController::class, 'exportPDF'])->name('monitoring-pembayaran.export-pdf');

// Restrukturisasi Routes
Route::prefix('pengajuan-cicilan')->name('pengajuan-cicilan.')->group(function () {
    Route::get('/', [PengajuanCicilanController::class, 'index'])->name('index');
    Route::post('/', [PengajuanCicilanController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanCicilanController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanCicilanController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanCicilanController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanCicilanController::class, 'destroy'])->name('destroy');
    Route::post('{id}/update-dokumen', [PengajuanCicilanController::class, 'updateDokumen'])->name('update-dokumen');
    Route::get('peminjaman/{idDebitur}', [PengajuanCicilanController::class, 'getPeminjamanListApi'])->name('peminjaman.list');
    Route::get('detail-pengajuan/{id}', [PengajuanCicilanController::class, 'getPengajuanDetail'])->name('detail-pengajuan');
    // Evaluasi endpoints
    Route::post('{id}/evaluasi', [\App\Http\Controllers\EvaluasiCicilanController::class, 'save'])->name('evaluasi.save');
    Route::post('{id}/decision', [\App\Http\Controllers\EvaluasiCicilanController::class, 'decision'])->name('evaluasi.decision');
});

// Program Restrukturisasi Routes - Full Livewire
Route::prefix('penyesuaian-cicilan')->name('penyesuaian-cicilan.')->group(function () {
    Route::get('/', \App\Livewire\PenyesuaianCicilan\Index::class)->name('index');
    Route::get('create', \App\Livewire\PenyesuaianCicilan\Create::class)->name('create');
    Route::get('{id}', \App\Livewire\PenyesuaianCicilan\Show::class)->name('show');
    Route::get('{id}/edit', \App\Livewire\PenyesuaianCicilan\Edit::class)->name('edit');
    Route::post('/', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'store'])->name('store');
    Route::get('approved', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'getApprovedCicilan'])->name('approved');
    Route::get('detail/{id}', [\App\Http\Controllers\PenyesuaianCicilanController::class, 'getCicilanDetail'])->name('detail');
});


Route::post('pengembalian/store', [PengembalianPinjamanController::class, 'store'])->name('pengembalian.store');

// Debitur Piutang
Route::get('riwayat-tagihan', DebiturPiutangIndex::class)->name('riwayat-tagihan.index');
Route::get('riwayat-tagihan/histori', [DebiturPiutangController::class, 'getHistoriPembayaran'])->name('riwayat-tagihan.histori');
Route::get('riwayat-tagihan/summary', [DebiturPiutangController::class, 'getSummaryData'])->name('riwayat-tagihan.summary');

// Report Pengembalian
Route::get('laporan-pengembalian', ReportPengembalian::class)->name('laporan-pengembalian.index');

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

// Penyaluran Dana Investasi
Route::get('penyaluran-dana-investasi', PenyaluranDanaInvestasiIndex::class)->name('penyaluran-dana-investasi.index');
Route::prefix('penyaluran-dana-investasi')->name('penyaluran-dana-investasi.')->group(function () {
    Route::post('/', [PenyaluranDanaInvestasiController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PenyaluranDanaInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PenyaluranDanaInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PenyaluranDanaInvestasiController::class, 'destroy'])->name('destroy');
    Route::post('{id}/upload-bukti', [PenyaluranDanaInvestasiController::class, 'uploadBukti'])->name('upload-bukti');
});

// Laporan Investasi SFinance
Route::get('laporan-investasi-sfinance', LaporanInvestasiSFinance::class)->name('laporan-investasi-sfinance.index');

// Pengembalian Investasi sfinance
Route::get('pengembalian-investasi', PengembalianInvestasi::class)->name('pengembalian-investasi.index');
Route::prefix('pengembalian-investasi')->name('pengembalian-investasi.')->group(function () {
    Route::post('/', [PengembalianInvestasiController::class, 'store'])->name('store');
    Route::get('{id}/edit', [PengembalianInvestasiController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengembalianInvestasiController::class, 'update'])->name('update');
    Route::delete('{id}', [PengembalianInvestasiController::class, 'destroy'])->name('destroy');
});
