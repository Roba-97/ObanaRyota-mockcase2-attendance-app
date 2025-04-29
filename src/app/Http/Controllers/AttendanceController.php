<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        if (!Auth::user()->findTodayAttendance()) {
            return view('attendance_register', ['status' => 0]);
        } else {
            $status = Auth::user()->findTodayAttendance()->status;
            return view('attendance_register', ['status' => $status]);
        }
    }
}
