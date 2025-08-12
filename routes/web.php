<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\JobTitleController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
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
        
        // Profile Management Routes
        Route::prefix('profile')->name('user.profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [App\Http\Controllers\User\ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [App\Http\Controllers\User\ProfileController::class, 'changePassword'])->name('change-password');
            Route::put('/update-password', [App\Http\Controllers\User\ProfileController::class, 'updatePassword'])->name('update-password');
            Route::delete('/delete-photo', [App\Http\Controllers\User\ProfileController::class, 'deleteProfilePhoto'])->name('delete-photo');
        });
    });

    // --- AREA KHUSUS ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ✅ PERBAIKAN UTAMA ADA DI SINI
        // Route khusus harus didefinisikan sebelum resource route
        Route::get('/users/per-division', [UserController::class, 'perDivision'])->name('users.per-division');
        
        // API endpoint untuk memuat user berdasarkan divisi
        Route::get('/divisions/{division}/users', [DivisionController::class, 'getUsers'])->name('divisions.users');
        
        // Kita batasi resource controller agar hanya membuat route yang kita butuhkan
        Route::resource('users', UserController::class)->only([
            'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
        ]);

        Route::resource('divisions', DivisionController::class);
        Route::resource('job-titles', JobTitleController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('statuses', StatusController::class); // Tambah route status
        Route::resource('barcodes', BarcodeController::class);
        Route::get('/barcodes/{barcode}/show-qr', [BarcodeController::class, 'showQr'])->name('barcodes.show-qr');
        Route::get('/barcodes/{barcode}/download-qr', [BarcodeController::class, 'downloadQr'])->name('barcodes.download-qr');
        
        // Admin Profile Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'index'])->name('index');
            Route::put('/update', [AdminProfileController::class, 'update'])->name('update');
            Route::put('/update-password', [AdminProfileController::class, 'updatePassword'])->name('update-password');
            Route::post('/update-photo', [AdminProfileController::class, 'updatePhoto'])->name('update-photo');
            Route::delete('/remove-photo', [AdminProfileController::class, 'removePhoto'])->name('remove-photo');
        });
        
        // Reports
        
        // Routes untuk Leave Requests / Pengajuan Izin
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LeaveRequestController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Admin\LeaveRequestController::class, 'show'])->name('show');
            Route::patch('/{id}/approve', [App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('reject');
            Route::get('/{id}/download-attachment', [App\Http\Controllers\Admin\LeaveRequestController::class, 'downloadAttachment'])->name('download-attachment');
            Route::get('/{id}/view-attachment', [App\Http\Controllers\Admin\LeaveRequestController::class, 'viewAttachment'])->name('view-attachment');
        });

        // Reports routes - Unified
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/divisions/{division}/users', [ReportController::class, 'getUsersByDivision'])->name('divisions.users');
    });
});