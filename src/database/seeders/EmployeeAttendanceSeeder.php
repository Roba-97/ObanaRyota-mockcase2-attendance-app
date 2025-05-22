<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakModification;
use App\Models\BreakTime;
use App\Models\Modification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(5)->create();

        $startDate = Carbon::today()->firstOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        foreach ($users as $user) {
            $date = $startDate->copy();

            while ($date <= $endDate) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => '09:00:00',
                    'punch_out' => '17:00:00',
                    'status' => 3,
                ]);
                $break = BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_at' => '12:00:00',
                    'end_at' => '13:00:00',
                ]);

                if ($date->isMonday() && $date->copy()->weekOfMonth === 3) {
                    $modification = Modification::create([
                        'attendance_id' => $attendance->id,
                        'modified_punch_in' => '10:00:00',
                        'modified_punch_out' => '19:00:00',
                        'comment' => '電車遅延のため',
                        'application_date' => Carbon::today()->format('Y-m-d'),
                    ]);
                    BreakModification::create([
                        'modification_id' => $modification->id,
                        'break_id' => $break->id,
                        'modified_start_at' => '15:00:00',
                        'modified_end_at' => '16:00:00',
                    ]);
                }

                $date->addDay();
            }
        }
    }
}
