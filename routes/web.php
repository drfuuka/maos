<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Laporan\LaporanGudepController;
use App\Http\Controllers\Laporan\LaporanPengurusController;
use App\Http\Controllers\Lpj\LpjGudepController;
use App\Http\Controllers\Lpj\LpjPengurusController;
use App\Http\Controllers\Proposal\ProposalGudepController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Proposal\ProposalPengurusController;
use Illuminate\Support\Facades\Route;

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


Route::group(['controller' => AuthController::class], function() {
    Route::get('login', 'showLoginForm')->name('login.index');
    Route::get('register', 'showRegisterForm')->name('register.index');
    
    Route::post('login', 'login')->name('login.authenticate');
    Route::post('register', 'register')->name('register.create');
    Route::get('logout', 'logout')->name('logout');

    Route::get('forgot-password', 'showForgotPassword')->name('forgot-password.index');
    Route::post('forgot-password', 'sendForgotPassword')->name('forgot-password.send');
    Route::get('forgot-password/{token}', 'showResetPassword')->name('password.reset');
    Route::post('reset-password/', 'resetPassword')->name('password.update');

});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'showUserProfile'])->name('profile.index');
    Route::put('/profile', [AuthController::class, 'update'])->name('profile.update');

    // start: route gudep
    Route::middleware(['role:Gudep'])->group(function () {
        Route::resource('proposal-gudep', ProposalGudepController::class);
        Route::resource('lpj-gudep', LpjGudepController::class);
    });

    Route::middleware(['role:Gudep,Ketua'])->group(function () {
        Route::resource('proposal-gudep', ProposalGudepController::class)->only(['update']);
        Route::resource('lpj-gudep', LpjGudepController::class)->only(['update']);
    });

    Route::middleware(['role:Gudep,Admin,Ketua'])->group(function () {
        Route::post('laporan-gudep-export', [LaporanGudepController::class, 'export'])->name('laporan-gudep.export');
        Route::post('proposal-gudep-export', [ProposalGudepController::class, 'export'])->name('proposal-gudep.export');
        Route::post('lpj-gudep-export', [LpjGudepController::class, 'export'])->name('lpj-gudep.export');

        Route::get('proposal-gudep-export/{id}', [ProposalGudepController::class, 'exportItem'])->name('proposal-gudep.export-item');
        Route::get('lpj-gudep-export/{id}', [LpjGudepController::class, 'exportItem'])->name('lpj-gudep.export-item');

        Route::resource('laporan-gudep', LaporanGudepController::class);
        Route::resource('proposal-gudep', ProposalGudepController::class);
        Route::resource('lpj-gudep', LpjGudepController::class);
    });
    // end: route gudep

    // start: route pengurus
    Route::middleware(['role:Pengurus'])->group(function () {
        Route::resource('proposal-pengurus', ProposalPengurusController::class);
        Route::resource('lpj-pengurus', LpjPengurusController::class);
    });

    Route::middleware(['role:Pengurus,Ketua'])->group(function () {
        Route::resource('proposal-pengurus', ProposalPengurusController::class)->only(['update']);
        Route::resource('lpj-pengurus', LpjPengurusController::class)->only(['update']);
    });

    Route::middleware(['role:Pengurus,Admin,Ketua'])->group(function () {
        Route::post('laporan-pengurus-export', [LaporanPengurusController::class, 'export'])->name('laporan-pengurus.export');
        Route::post('proposal-pengurus-export', [ProposalPengurusController::class, 'export'])->name('proposal-pengurus.export');
        Route::post('lpj-pengurus-export', [LpjPengurusController::class, 'export'])->name('lpj-pengurus.export');

        Route::get('proposal-pengurus-export/{id}', [ProposalPengurusController::class, 'exportItem'])->name('proposal-pengurus.export-item');
        Route::get('lpj-pengurus-export/{id}', [LpjPengurusController::class, 'exportItem'])->name('lpj-pengurus.export-item');

        Route::resource('laporan-pengurus', LaporanPengurusController::class);
        Route::resource('proposal-pengurus', ProposalPengurusController::class);
        Route::resource('lpj-pengurus', LpjPengurusController::class);
    });
    // end: route pengurus

    Route::middleware('role:Admin')->group(function () {
        Route::get('user/{userId}/activate', [DashboardController::class, 'activateUser'])->name('activate-user');

        Route::resource('pengguna', PenggunaController::class);
    });
});
