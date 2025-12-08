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

Route::get('config-matrix-pinjaman', \App\Livewire\ConfigMatrixPinjaman\Index::class)->name('config-matrix-pinjaman.index');

Route::prefix('pengembalian')->name('pengembalian.')->group(function () {
    Route::get('/', \App\Livewire\PengembalianPinjaman\Index::class)->name('index');
    Route::get('create', \App\Livewire\PengembalianPinjaman\Create::class)->name('create');
});

Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    Route::get('/', \App\Livewire\PengajuanPinjaman\Index::class)->name('index');
    Route::get('create', \App\Livewire\PengajuanPinjaman\Create::class)->name('create');
    Route::get('{id}/edit', \App\Livewire\PengajuanPinjaman\Create::class)->name('edit');
});

