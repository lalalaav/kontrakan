<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiayaUpgradeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KontrakSewaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\UnitKontrakanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/password', [AuthController::class, 'editPassword'])->name('password.edit');
    Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');

    // Dipakai kedua peran: admin lihat semua, penyewa hanya laporannya sendiri.
    Route::get('/kerusakan', [LaporanKerusakanController::class, 'index'])->name('kerusakan.index');

    // URI khusus penyewa, tidak boleh sama dengan /kontrak/{kontrak}.
    Route::get('/kontrak-saya/{kontrak}', [KontrakSewaController::class, 'show'])->name('penyewa.kontrak.show');

    Route::middleware('role:admin,petugas')->group(function () {
        Route::resource('lokasi', LokasiController::class)->except('show');
        Route::resource('unit', UnitKontrakanController::class)->parameters(['unit' => 'unit'])->except('show');

        Route::get('/kontrak', [KontrakSewaController::class, 'index'])->name('kontrak.index');
        Route::get('/kontrak/buat', [KontrakSewaController::class, 'create'])->name('kontrak.create');
        Route::post('/kontrak', [KontrakSewaController::class, 'store'])->name('kontrak.store');
        Route::get('/kontrak/{kontrak}', [KontrakSewaController::class, 'show'])->name('kontrak.show');
        Route::patch('/kontrak/{kontrak}/status', [KontrakSewaController::class, 'updateStatus'])->name('kontrak.status');
        Route::post('/kontrak/{kontrak}/tagihan', [KontrakSewaController::class, 'buatTagihan'])->name('kontrak.tagihan');

        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::patch('/pembayaran/{pembayaran}/verifikasi', [PembayaranController::class, 'verify'])->name('pembayaran.verify');

        Route::patch('/kerusakan/{kerusakan}', [LaporanKerusakanController::class, 'update'])->name('kerusakan.update');
        Route::resource('upgrade', BiayaUpgradeController::class)->except('show');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
        Route::get('/laporan/csv', [LaporanController::class, 'csv'])->name('laporan.csv');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('user', UserController::class)->except('show');
    });

    Route::middleware('role:penyewa')->group(function () {
        Route::get('/kontrak-baru', [KontrakSewaController::class, 'create'])->name('penyewa.kontrak.create');
        Route::post('/kontrak-baru', [KontrakSewaController::class, 'store'])->name('penyewa.kontrak.store');

        Route::get('/pembayaran/{kontrak}/buat', [PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('/pembayaran/{kontrak}', [PembayaranController::class, 'store'])->name('pembayaran.store');

        Route::get('/kerusakan/buat', [LaporanKerusakanController::class, 'create'])->name('kerusakan.create');
        Route::post('/kerusakan', [LaporanKerusakanController::class, 'store'])->name('kerusakan.store');
    });
});
