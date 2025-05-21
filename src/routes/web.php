<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\ModificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LoginController;

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('attendance')->group(function() {
        Route::get('/', [StampController::class, 'index']);
        Route::post('/punch_in', [StampController::class, 'createStamp']);
        Route::patch('/punch_out', [StampController::class, 'updateStamp']);
        Route::post('/break_in', [StampController::class, 'createBreak']);
        Route::patch('/break_out', [StampController::class, 'updateBreak']);
        Route::get('/list', [AttendanceController::class, 'showList']);
    });
    Route::post('/stamp_correction_request/{attendance}', [ModificationController::class, 'requestModification']);
});

Route::group(['middleware' => ['auth:admin']], function () {
    Route::prefix('admin')->group(function () {
        Route::get('/attendance/list', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/staff/list', [AdminController::class, 'showStaffList']);
        Route::get('/attendance/staff/{user}', [AdminController::class, 'showStaffMonthlyAttendance']);
        Route::get('/attendance/staff/{user}/export', [AdminController::class, 'csvExport']);
    });
    Route::get('/stamp_correction_request/approve/{modification}', [AdminController::class, 'showModificationRequest']);
    Route::post('/stamp_correction_request/approve/{modification}', [ModificationController::class, 'approveModificationRequest']);
    Route::post('/stamp_correction/{attendance}', [ModificationController::class, 'modifyAttendance']);
});

Route::group(['middleware' => ['auth.any']], function () {
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'showDetail']);
    Route::get('/stamp_correction_request/list', [ModificationController::class, 'showModificationList']);
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'adminLogin']);
    Route::get('/logout', [LoginController::class, 'adminLogout']);
});



