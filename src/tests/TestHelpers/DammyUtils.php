<?php

namespace Tests\TestHelpers;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\BreakModification;
use App\Models\Modification;
use App\Models\User;
use Carbon\Carbon;

trait DammyUtils
{
    public function createAdmin()
    {
        $admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminPassword'),
            'email_verified_at' => Carbon::now(),
        ]);
        return $admin;
    }

    public function createAttendance(User $user, Carbon $carbon)
    {
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $carbon->format('Y-m-d'),
            'punch_in' => '09:00:00',
            'punch_out' => '17:00:00',
            'status' => 3,
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_at' => '12:00:00',
            'end_at' => '13:00:00',
            'is_ended' => true,
        ]);
        return $attendance;
    }

    public function createModification(Attendance $attendance, $isApproved) {
        $modification = Modification::create([
            'attendance_id' => $attendance->id,
            'modified_punch_in' => '10:00:00',
            'modified_punch_out' => '19:00:00',
            'comment' => '電車の遅延のため',
            'application_date' => Carbon::today()->format('Y-m-d'),
            'is_approved' => $isApproved,
        ]);
        BreakModification::create([
            'modification_id' => $modification->id,
            'break_id' => $attendance->breaks()->first()->id,
            'modified_start_at' => '13:00:00',
            'modified_end_at' => '14:00:00',
        ]);
        return $modification;
    }
}
