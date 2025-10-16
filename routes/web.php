<?php

use App\Livewire\Dashboard;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\ConfigMatrixScore;
use Illuminate\Support\Facades\Route;
use App\Livewire\ConfigMatrixPinjaman;
use App\Livewire\PermissionManagement;
use App\Livewire\Peminjaman\PeminjamanIndex;
use App\Livewire\Peminjaman\PeminjamanCreate;
use App\Http\Controllers\Peminjaman\PeminjamanController;

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

Route::get('/', function () {
    return redirect()->route('login');
});

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
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
    Route::get('peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.detail');
    Route::get('ajukan-peminjaman', [PeminjamanController::class, 'create'])->name('ajukanpeminjaman');
    // Detail route for a specific peminjaman record
    Route::get('config-matrix-pinjaman', ConfigMatrixPinjaman::class)->name('matrixpinjaman');
    Route::get('config-matrix-score', ConfigMatrixScore::class)->name('matrixscore');
<<<<<<< HEAD

    Route::get('master-data/master-data-kol', MasterDataKolIndex::class)->name('masterdatakol.index');
    Route::get('master-data/sumber-pendanaan-eksternal', Index::class)->name('sumberpendanaaneksternal.index');
    Route::get('master-data/debitur-investor', DebiturInvestorIndex::class)->name('debiturinvestor.index');
=======
    Route::get('master-data/master-data-kol', [\App\Http\Controllers\Master\MasterKolController::class, 'index'])->name('masterdatakol.index');
    Route::get('master-data/sumber-pendanaan-eksternal', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'index'])->name('sumberpendanaaneksternal.index');
    
    // Master Debitur 
    Route::prefix('master/debitur')->name('master.debitur.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Master\DebiturController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\DebiturController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Master\DebiturController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\Master\DebiturController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\DebiturController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\DebiturController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\DebiturController::class, 'destroy'])->name('destroy');
    });

    // Master KOL
    Route::prefix('master/kol')->name('master.kol.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Master\MasterKolController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\MasterKolController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Master\MasterKolController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\MasterKolController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterKolController::class, 'destroy'])->name('destroy');
    });

    // Master Sumber Pendanaan Eksternal
    Route::prefix('master/sumber')->name('master.sumber.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'store'])->name('store');
        Route::get('{id}/edit', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'update'])->name('update');
        Route::delete('{id}', [\App\Http\Controllers\Master\MasterSumberPendanaanEksternalController::class, 'destroy'])->name('destroy');
    });
>>>>>>> d54439ad30dd6bcd7cd69b3d1c1d14f91d8126c4
});

require __DIR__.'/auth.php';
