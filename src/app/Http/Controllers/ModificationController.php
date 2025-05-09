<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\ModificationRequest;
use App\Models\Attendance;
use App\Models\Modification;
use App\Models\BreakModification;

class ModificationController extends Controller
{
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
            BreakModification::create([
                'modification_id' => $modification->id,
                'break_id' => null,
                'modified_start_at' => $request->input('additional_break_in'),
                'modified_end_at' => $request->input('additional_break_out'),
            ]);
        }

        return redirect('/attendance/' . $attendance->id);
    }
}
