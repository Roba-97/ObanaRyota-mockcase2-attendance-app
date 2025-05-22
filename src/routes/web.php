<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\ModificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LoginController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
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

// メール確認の通知
Route::get('/email/verify', function () {
    return view('auth.verify_email');
})->middleware('auth')->name('verification.notice');

// メール確認のハンドラ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール確認の再送信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');