<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modification;
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
        $usersWithAttendances = Auth::guard('admin')->user()->getAttendancesByDate($displayedDate);

        return view('admin.attendance_list', compact('displayedDate', 'usersWithAttendances'));
    }

    public function showStaffList()
    {
        $staff = User::all();
        return view('admin.staff_list', compact('staff'));
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

        return view('admin.staff_attendance', compact('displayedMonth', 'attendances', 'user'));
    }

    public function showModificationRequest(Modification $modification)
    {
        $modification->load('attendance', 'breakModifications', 'additionalBreak');
        return view('admin.modification_request', compact('modification'));
    }

    public function csvExport(User $user)
    {
        $sessionKey = 'displayedMonth';
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];

        $csvHeader = ['日付', '出勤', '退勤' , '休憩', '合計'];
        $exportData = [];
        array_push($exportData, $csvHeader);

        $year = session()->get($sessionKey)->year;
        $month = session()->get($sessionKey)->month;
        $attendances = $user->attendancesByMonth($year, $month);

        $firstDayOfMonth = Carbon::createFromFormat('Y/m', $year . '/' . $month)->startOfMonth();
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        for ($date = $firstDayOfMonth->copy(); $date->lte($lastDayOfMonth); $date->addDay()) {
            $attendance = $attendances->where('date', $date->copy()->format('Y-m-d'))->first();
            if ($attendance) {
                $row = [
                    $date->format('m/d') . '(' . $weekDays[$date->dayOfWeek] . ')',
                    Carbon::parse($attendance->punch_in)->format('H:i'),
                    Carbon::parse($attendance->punch_out)->format('H:i'),
                    $attendance->break_duration,
                    $attendance->work_duration
                ];
            } else {
                $row = [
                    $date->format('m/d') . '(' . $weekDays[$date->dayOfWeek] . ')',
                    '休',
                ];
            }
            array_push($exportData, $row);
        }
 
        $stream = fopen('php://temp', 'r+b');
        foreach ($exportData as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = mb_convert_encoding(stream_get_contents($stream), 'SJIS-win', 'UTF-8');
        fclose($stream);

        $filename = $user->name . '_' . $year . '年' . $month . '月_勤怠一覧.csv';

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
