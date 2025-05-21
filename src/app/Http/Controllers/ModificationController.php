<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModificationRequest;
use App\Models\AdditionalBreak;
use App\Models\Attendance;
use App\Models\BreakModification;
use App\Models\BreakTime;
use App\Models\Modification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModificationController extends Controller
{
    public function showModificationList(Request $request)
    {
        $showApproved = $request->input('status') === 'approved' ? true : false;

        if (Auth::guard('admin')->check()) {
            $modifications = Auth::guard('admin')->user()->getAllModifications($showApproved);
            return view('admin.admin_modification_list', compact('showApproved', 'modifications'));
        } else {
            $modifications = Auth::user()->modifications()->where('is_approved', $showApproved)->with('attendance')->get();
            return view('modification_list', compact('showApproved', 'modifications'));
        }
    }

    public function requestModification(Attendance $attendance, ModificationRequest $request)
    {
        $modification = Modification::create([
            'attendance_id' => $attendance->id,
            'modified_punch_in' => $request->input('modified_punch_in'),
            'modified_punch_out' => $request->input('modified_punch_out'),
            'comment' => $request->input('comment'),
            'application_date' => Carbon::today()->format('Y-m-d'),
        ]);

        foreach ($attendance->breaks as $index => $break) {
            BreakModification::create([
                'modification_id' => $modification->id,
                'break_id' => $break->id,
                'modified_start_at' => $request->input("modified_break_in.$index"),
                'modified_end_at' => $request->input("modified_break_out.$index"),
            ]);
        }

        if ($request->filled('additional_break_in') && $request->filled('additional_break_out')) {
            AdditionalBreak::create([
                'modification_id' => $modification->id,
                'added_start_at' => $request->input('additional_break_in'),
                'added_end_at' => $request->input('additional_break_out'),
            ]);
        }

        return redirect("/attendance/$attendance->id?from=modification");
    }

    public function modifyAttendance(Attendance $attendance, ModificationRequest $request)
    {
        $attendance->update([
            'punch_in' => $request->input('modified_punch_in'),
            'punch_out' => $request->input('modified_punch_out'),            
        ]);

        foreach ($attendance->breaks as $index => $break) {
            $break->update([
                'start_at' => $request->input("modified_break_in.$index"),
                'end_at' => $request->input("modified_break_out.$index"),
            ]);
        }

        if ($request->filled('additional_break_in') && $request->filled('additional_break_out')) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_at' => $request->input('additional_break_in'),
                'end_at' => $request->input('additional_break_out'),
                'is_ended' => true,
            ]);
        }

        return redirect("/attendance/$attendance->id");
    }
}
