<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StampController extends Controller
{
    public function createStamp()
    {
        $punchIn = Carbon::now();

        Attendance::create([
            'user_id' => Auth::user()->id,
            'date' => $punchIn->format('Y-m-d'),
            'punch_in' => $punchIn->format('H:i'),
            'punch_out' => $punchIn->format('H:i'),
            'status' => 1
        ]);

        return redirect('/attendance');
    }

    public function updateStamp()
    {
        $punchOut = Carbon::now();
        $todayAttendance = Auth::user()->findTodayAttendance();

        $todayAttendance->update([
            'punch_out' => $punchOut->format('H:i'),
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
            'start_at' => $breakIn->format('H:i'),
            'end_at' => $breakIn->format('H:i'),
        ]);

        return redirect('/attendance');
    }

    public function updateBreak()
    {
        $breakOut = Carbon::now();
        $todayAttendance = Auth::user()->findTodayAttendance();

        $todayAttendance->update(['status' => 1]);
        $todayAttendance->breaks()->where('is_ended', false)->first()->update(['end_at' => $breakOut->format('H:i'), 'is_ended' => true]);

        return redirect('/attendance');
    }
}
