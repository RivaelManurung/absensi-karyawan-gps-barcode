<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\AttendanceController;

// Route untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::get('/', fn() => redirect()->route('login'));

// Grup utama untuk semua user yang sudah login
Route::middleware('auth')->group(function () {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Route /home sudah dihapus karena logikanya pindah ke LoginController

    // --- AREA KHUSUS KARYAWAN (USER) ---
    Route::middleware('user')->group(function () {
        Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
        // Route untuk memproses form
        Route::post('/absensi/clock-in', [AttendanceController::class, 'storeClockIn'])->name('attendances.clockin');
        Route::post('/absensi/clock-out', [AttendanceController::class, 'storeClockOut'])->name('attendances.clockout');
        Route::post('/absensi/request', [AttendanceController::class, 'storeRequest'])->name('attendances.request.store');
    });

    // --- AREA KHUSUS ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('divisions', DivisionController::class);
        Route::resource('job-titles', JobTitleController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('barcodes', BarcodeController::class);
    });
});
