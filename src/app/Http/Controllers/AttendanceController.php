<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function showList(Request $request)
    {
        $monthInput = $request->input('month');
        $sessionKey = 'displayedMonth';

        if ($monthInput === null) {
            session()->forget($sessionKey);
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

    public function showDetail(Attendance $attendance, Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $attendance->load('breaks');
            return view('admin.admin_attendance_detail', compact('attendance'));
        }

        $isFromModification = $request->query('from') === 'modification' ? true : false;
        $isWaiting = false;
        $modification = null;
        
        if ($attendance->modifications()->exists()) {
            $attendance->load('modifications');
            foreach ($attendance->modifications as $mod) {
                if (!$mod->is_approved) {
                    $isWaiting = true;
                    if ($isFromModification) {
                        $modification = $mod->load('breakModifications', 'additionalBreak');
                    }
                    break;
                }
            }
        }

        return view('attendance_detail', compact('isWaiting', 'isFromModification', 'attendance', 'modification'));
    }
}
