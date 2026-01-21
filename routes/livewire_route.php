<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// File preview route (dengan auth + signed URL untuk keamanan ganda)
Route::get('/file-preview/{filename}', [\App\Http\Controllers\FilePreviewController::class, 'preview'])
    ->name('file.preview')
    ->middleware('signed');

// dashboard
Route::get('dashboard', Dashboard::class)->name('dashboard.index');

Route::get('/master-data/kol', \App\Livewire\MasterData\MasterKol::class)->name('master-data.kol.index');
Route::get('/master-data/sumber-pendanaan-eksternal', \App\Livewire\MasterData\SumberPendanaanEksternal::class)->name('master-data.sumber-pendanaan-eksternal.index');
Route::get('/master-data/debitur-investor', \App\Livewire\MasterData\DebiturDanInvestor::class)->name('master-data.debitur-investor.index');
Route::get('/master-data/master-karyawan-ski', \App\Livewire\MasterData\MasterKaryawanSki::class)->name('master-data.master-karyawan-ski.index');

// Penyaluran Deposito
Route::get('penyaluran-deposito', \App\Livewire\PenyaluranDeposito\PenyaluranDepositoIndex::class)->name('penyaluran-deposito.index');

// Pengembalian Investasi
Route::get('pengembalian-investasi', \App\Livewire\PengembalianInvestasi::class)->name('pengembalian-investasi.index');

Route::get('config-matrix-pinjaman', \App\Livewire\ConfigMatrixPinjaman\Index::class)->name('config-matrix-pinjaman.index');

Route::prefix('pengembalian')->name('pengembalian.')->group(function () {
    Route::get('/', \App\Livewire\PengembalianPinjaman\Index::class)->name('index');
    Route::get('create', \App\Livewire\PengembalianPinjaman\Create::class)->name('create');
    Route::get('detail/{id}', \App\Livewire\PengembalianPinjaman\Detail::class)->name('detail');
});

Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    Route::get('/', \App\Livewire\PengajuanPinjaman\Index::class)->name('index');
    Route::get('create', \App\Livewire\PengajuanPinjaman\Create::class)->name('create');
    Route::get('{id}/edit', \App\Livewire\PengajuanPinjaman\Create::class)->name('edit');
});

// AR Performance
Route::get('ar-performance', \App\Livewire\ArPerformanceIndex::class)->name('ar-performance.index');

// Debitur Piutang
Route::get('debitur-piutang', \App\Livewire\DebiturPiutangIndex::class)->name('debitur-piutang.index');

// Master Cells Project
Route::get('/master-data/cells-project', \App\Livewire\MasterData\MasterCellsProject::class)->name('master-data.cells-project.index');

// SFinlog - Pengajuan Investasi
Route::get('sfinlog/pengajuan-investasi', \App\Livewire\SFinlog\PengajuanInvestasi::class)->name('sfinlog.pengajuan-investasi.index');
Route::get('sfinlog/pengajuan-investasi/detail/{id}', \App\Livewire\SFinlog\PengajuanInvestasiDetail::class)->name('sfinlog.pengajuan-investasi.detail');

// SFinlog - Peminjaman Dana
Route::get('sfinlog/peminjaman', \App\Livewire\SFinlog\Peminjaman::class)->name('sfinlog.peminjaman.index');
Route::get('sfinlog/peminjaman/create', \App\Livewire\SFinlog\PeminjamanCreate::class)->name('sfinlog.peminjaman.create');
Route::get('sfinlog/peminjaman/edit/{id}', \App\Livewire\SFinlog\PeminjamanEdit::class)->name('sfinlog.peminjaman.edit');
Route::get('sfinlog/peminjaman/detail/{id}', \App\Livewire\SFinlog\PeminjamanDetail::class)->name('sfinlog.peminjaman.detail');

// SFinlog - Pengembalian Pinjaman
Route::get('sfinlog/pengembalian-pinjaman', \App\Livewire\SFinlog\PengembalianPinjamanFinlog::class)->name('sfinlog.pengembalian-pinjaman.index');
Route::get('sfinlog/pengembalian-pinjaman/create', \App\Livewire\SFinlog\PengembalianPinjamanFinlogCreate::class)->name('sfinlog.pengembalian-pinjaman.create');
Route::get('sfinlog/pengembalian-pinjaman/detail/{id}', \App\Livewire\SFinlog\DetailPengembalian::class)->name('sfinlog.pengembalian-pinjaman.detail');

// SFinlog - AR Perbulan
Route::get('sfinlog/ar-perbulan', \App\Livewire\SFinlog\ArPerbulan::class)->name('sfinlog.ar-perbulan.index');

// SFinlog - AR Performance
Route::get('sfinlog/ar-performance', \App\Livewire\SFinlog\ArPerformanceIndex::class)->name('sfinlog.ar-performance.index');
// SFinlog - Report Pengembalian
Route::get('sfinlog/report-pengembalian', \App\Livewire\SFinlog\ReportPengembalian::class)->name('sfinlog.report-pengembalian.index');

// SFinlog - Debitur Piutang
Route::get('sfinlog/debitur-piutang', \App\Livewire\SFinlog\DebiturPiutangFinlog::class)->name('sfinlog.debitur-piutang.index');

Route::get('sfinlog/cell-project-profile', \App\Livewire\SFinlog\CellProjectProfile::class)->name('sfinlog.cell-project-profile.index');
// Portofolio
// Route::get('portofolio', \App\Livewire\Portofolio\Index::class)->name('portofolio.index');
