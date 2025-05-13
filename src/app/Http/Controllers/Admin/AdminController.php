<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $dateInput = $request->input('date');
        $sessionKey = 'displayedDate';

        if ($dateInput === null) {
            session()->forget($sessionKey);
            session()->put($sessionKey, Carbon::today());
        } else {
            if ($dateInput === 'tomorrow') {
                session()->put($sessionKey, session()->get($sessionKey)->addDay());
            }
            if ($dateInput === 'yesterday') {
                session()->put($sessionKey, session()->get($sessionKey)->subDay());
            }
        }

        $displayedDate = session()->get($sessionKey)->format('Y/m/d');
        $attendances = Auth::guard('admin')->user()->getAttendancesByDate($displayedDate);

        return view('admin.admin_attendance_list', compact('displayedDate', 'attendances'));
    }

    public function showStaffList()
    {
        $staff = User::all();
        return view('admin.admin_staff_list', compact('staff'));
    }

    public function showStaffMonthlyAttendance(User $user, Request $request)
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
        $attendances = $user->attendancesByMonth($year, $month);

        return view('admin.admin_staff_attendance', compact('displayedMonth', 'attendances', 'user'));
    }
}
