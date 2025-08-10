<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\JobTitleController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\BarcodeController;

use App\Http\Controllers\User\AttendanceController;

// Route untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::get('/', fn() => redirect()->route('login'));

// Grup utama untuk semua user yang sudah login
Route::middleware('auth')->group(function () {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // --- AREA KHUSUS KARYAWAN (USER) ---
    Route::middleware('user')->group(function () {
        Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
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
        Route::get('/barcodes/{barcode}/show-qr', [BarcodeController::class, 'showQr'])->name('barcodes.show-qr');
        Route::get('/barcodes/{barcode}/download-qr', [BarcodeController::class, 'downloadQr'])->name('barcodes.download-qr');
    });
});
