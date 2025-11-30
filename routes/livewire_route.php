<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// dashboard
Route::get('dashboard', Dashboard::class)->name('dashboard.index');

Route::get('/master-data/kol', \App\Livewire\MasterData\MasterKol::class)->name('master-data.kol.index');
Route::get('/master-data/sumber-pendanaan-eksternal', \App\Livewire\MasterData\SumberPendanaanEksternal::class)->name('master-data.sumber-pendanaan-eksternal.index');
Route::get('/master-data/debitur-investor', \App\Livewire\MasterData\DebiturDanInvestor::class)->name('master-data.debitur-investor.index');
Route::get('/master-data/master-karyawan-ski', \App\Livewire\MasterData\MasterKaryawanSki::class)->name('master-data.master-karyawan-ski.index');

// Penyaluran Deposito
Route::get('penyaluran-deposito', \App\Livewire\PenyaluranDeposito\PenyaluranDepositoIndex::class)->name('penyaluran-deposito.index');

Route::get('config-matrix-pinjaman', \App\Livewire\ConfigMatrixPinjaman\Index::class)->name('config-matrix-pinjaman.index');

// AR Performance
Route::get('ar-performance', \App\Livewire\ArPerformanceIndex::class)->name('ar-performance.index');

// Debitur Piutang
Route::get('debitur-piutang', \App\Livewire\DebiturPiutangIndex::class)->name('debitur-piutang.index');


