<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Modification;
use Carbon\Carbon;
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

    public function showList(Request $request)
    {
        $monthInput = $request->input('month');
        $sessionKey = 'displayedMonth';

        if ($monthInput === null) {
            session()->forget('displayMonth');
            session()->put($sessionKey, Carbon::today());
        } else {
            if ($monthInput === 'next') {
                session()->put($sessionKey, session()->get($sessionKey)->addMonth(1));
            }
            if ($monthInput === 'previous') {
                session()->put($sessionKey, session()->get($sessionKey)->subMonth(1));
            }
        }

        $displayedMonth = session()->get($sessionKey)->format('Y/m');

        $year = session()->get($sessionKey)->year;
        $month = session()->get($sessionKey)->month;
        $attendances = Auth::user()->attendancesByMonth($year, $month);

        return view('attendance_list', compact('displayedMonth', 'attendances'));
    }

    public function showDetail(Attendance $attendance)
    {
        return view('attendance_detail', compact('attendance'));
    }

    public function showModificationList(Request $request)
    {
        $status = $request->input('status');

        if ($status === 'approved') {
            $showApproved = true;
            $modifications = Auth::user()->modifications()->where('is_approved', true)->with('attendance')->get();
        } else {
            $showApproved = false;
            $modifications = Auth::user()->modifications()->where('is_approved', false)->with('attendance')->get();
        }

        return view('modification_list', compact('showApproved', 'modifications'));
    }
}
