<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/punch_in', [StampController::class, 'createStamp']);
    Route::patch('/attendance/punch_out', [StampController::class, 'updateStamp']);
    Route::post('/attendance/break_in', [StampController::class, 'createBreak']);
    Route::patch('/attendance/break_out', [StampController::class, 'updateBreak']);
    Route::get('/attendance/list', [AttendanceController::class, 'showList']);
});
