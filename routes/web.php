<?php

use App\Livewire\Dashboard;
use App\Livewire\Peminjaman\PeminjamanIndex;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\ConfigMatrixScore;
use Illuminate\Support\Facades\Route;
use App\Livewire\ConfigMatrixPinjaman;
use App\Livewire\PermissionManagement;
use App\Livewire\Peminjaman\PeminjamanCreate;
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

Route::view('/', 'welcome');

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
    Route::get('peminjaman', PeminjamanIndex::class)->name('peminjaman');
    Route::get('ajukan-peminjaman', PeminjamanCreate::class)->name('ajukanpeminjaman');
    Route::get('config-matrix-pinjaman', ConfigMatrixPinjaman::class)->name('matrixpinjaman');
    Route::get('config-matrix-score', ConfigMatrixScore::class)->name('matrixscore');
});

require __DIR__.'/auth.php';
