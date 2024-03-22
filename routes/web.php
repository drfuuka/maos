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
use App\Mail\AktivasiPengguna;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

});

Route::get('anu', function () {
    Mail::to("michael@mail.com")->send(new AktivasiPengguna(Auth::user()));
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:Gudep,Ketua,Admin')->group(function () {
        Route::get('laporan-gudep-export', [LaporanGudepController::class, 'export'])->name('laporan-gudep.export');
        Route::resource('laporan-gudep', LaporanGudepController::class);

        Route::get('proposal-gudep-export', [ProposalGudepController::class, 'export'])->name('proposal-gudep.export');
        Route::resource('proposal-gudep', ProposalGudepController::class);

        Route::get('lpj-gudep-export', [LpjGudepController::class, 'export'])->name('lpj-gudep.export');
        Route::resource('lpj-gudep', LpjGudepController::class);
    });
    
    Route::middleware('role:Pengurus,Ketua,Admin')->group(function () {
        Route::get('laporan-pengurus-export', [LaporanPengurusController::class, 'export'])->name('laporan-pengurus.export');
        Route::resource('laporan-pengurus', LaporanPengurusController::class);

        Route::get('proposal-pengurus-export', [ProposalPengurusController::class, 'export'])->name('proposal-pengurus.export');
        Route::resource('proposal-pengurus', ProposalPengurusController::class);

        Route::get('lpj-pengurus-export', [LpjPengurusController::class, 'export'])->name('lpj-pengurus.export');
        Route::resource('lpj-pengurus', LpjPengurusController::class);
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('user/{userId}/activate', [DashboardController::class, 'activateUser'])->name('activate-user');

        Route::resource('pengguna', PenggunaController::class);
    });
});
