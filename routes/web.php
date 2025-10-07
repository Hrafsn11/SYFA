<?php

use App\Livewire\Dashboard;
use App\Livewire\Peminjaman;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\ConfigMatrixScore;
use Illuminate\Support\Facades\Route;
use App\Livewire\ConfigMatrixPinjaman;
use App\Livewire\PermissionManagement;

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
    Route::get('peminjaman', Peminjaman::class)->name('peminjaman');
    Route::get('config-matrix-pinjaman', ConfigMatrixPinjaman::class)->name('MatrixPinjaman');
    Route::get('config-matrix-score', ConfigMatrixScore::class)->name('MatrixScore');
    Route::get('ajukan-peminjaman', App\Livewire\Peminjaman\AjukanPeminjaman::class)->name('AjukanPeminjaman');
});

require __DIR__ . '/auth.php';
