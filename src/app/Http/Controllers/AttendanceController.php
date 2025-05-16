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
        $from = $request->query('from');
        $isFromModification = false;
        $isWaiting = false;
        $modification = null;
        
        $attendance->load('modifications');

        if ($attendance->modifications()->exists()) {
            foreach ($attendance->modifications as $mod) {
                if (!$mod->is_approved) {
                    $isWaiting = true;
                    if ($from === 'modification') {
                        $modification = $mod->load('breakModifications', 'additionalBreak');
                        $isFromModification = true;
                    }
                    break;
                }
            }
        }

        return view('attendance_detail', compact('isWaiting', 'isFromModification', 'attendance', 'modification'));
    }
}
