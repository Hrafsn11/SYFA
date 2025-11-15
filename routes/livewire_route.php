<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// dashboard
Route::get('dashboard', Dashboard::class)->name('dashboard.index');

Route::get('/master-data/kol', \App\Livewire\MasterData\MasterKol::class)->name('master-data.kol.index');
Route::get('/master-data/sumber-pendanaan-eksternal', \App\Livewire\MasterData\SumberPendanaanEksternal::class)->name('master-data.sumber-pendanaan-eksternal.index');
Route::get('/master-data/debitur-dan-investor', \App\Livewire\MasterData\DebiturDanInvestor::class)->name('master-data.debitur-dan-investor.index');
Route::get('/master-data/master-karyawan-ski', \App\Livewire\MasterData\MasterKaryawanSki::class)->name('master-data.master-karyawan-ski.index');

Route::get('config-matrix-pinjaman', \App\Livewire\ConfigMatrixPinjaman\ConfigMatrixPinjamanIndex::class)->name('config-matrix-pinjaman.index');