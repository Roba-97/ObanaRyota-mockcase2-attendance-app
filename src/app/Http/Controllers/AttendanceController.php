<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Modification;
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

        if (session()->get($sessionKey)->isFuture()) {
            session()->put($sessionKey, Carbon::today());
        }

        $displayedMonth = session()->get($sessionKey)->format('Y/m');

        $year = session()->get($sessionKey)->year;
        $month = session()->get($sessionKey)->month;
        $attendances = Auth::user()->attendancesByMonth($year, $month);

        return view('attendance_list', compact('displayedMonth', 'attendances'));
    }

    public function showDetail(Request $request, $id)
    {
        if (Auth::guard('admin')->check()) {
            $attendance = Attendance::find($id)->load('breaks');
            return view('admin.attendance_detail', compact('attendance'));
        }

        $isFromModification = $request->query('from') === 'modification' ? true : false;
        if ($isFromModification) {
            $modification = Modification::find($id)->load('breakModifications', 'additionalBreak');
            $attendance = $modification->attendance;
            return view('modification_request', compact('attendance', 'modification'));
        }

        $attendance = Attendance::find($id);

        $latestModification = $attendance->modifications()->latest()->first();
        $isWaiting = $latestModification && !$latestModification->is_approved ? true : false;

        return view('attendance_detail', compact('isWaiting', 'attendance'));
    }
}
