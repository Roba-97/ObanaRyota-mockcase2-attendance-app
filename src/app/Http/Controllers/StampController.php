<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StampController extends Controller
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
    
    public function createStamp()
    {
        $punchIn = Carbon::now();

        Attendance::create([
            'user_id' => Auth::user()->id,
            'date' => $punchIn->format('Y-m-d'),
            'punch_in' => $punchIn->startOfMinute()->format('H:i:s'),
            'punch_out' => $punchIn->startOfMinute()->format('H:i:s'),
            'status' => 1
        ]);

        return redirect('/attendance');
    }

    public function updateStamp()
    {
        $punchOut = Carbon::now();
        $todayAttendance = Auth::user()->findTodayAttendance();

        $todayAttendance->update([
            'punch_out' => $punchOut->startOfMinute()->format('H:i:s'),
            'status' => 3
        ]);

        return redirect('/attendance');
    }

    public function createBreak()
    {
        $breakIn = Carbon::now();
        $todayAttendance = Auth::user()->findTodayAttendance();

        $todayAttendance->update(['status' => 2]);
        BreakTime::create([
            'attendance_id' => $todayAttendance->id,
            'start_at' => $breakIn->startOfMinute()->format('H:i:s'),
            'end_at' => $breakIn->startOfMinute()->format('H:i:s'),
        ]);

        return redirect('/attendance');
    }

    public function updateBreak()
    {
        $breakOut = Carbon::now();
        $todayAttendance = Auth::user()->findTodayAttendance();

        $todayAttendance->update(['status' => 1]);
        $todayAttendance->breaks()
            ->where('is_ended', false)->first()
            ->update([
                'end_at' => $breakOut->startOfMinute()->format('H:i:s'),
                'is_ended' => true
            ]);

        return redirect('/attendance');
    }
}
