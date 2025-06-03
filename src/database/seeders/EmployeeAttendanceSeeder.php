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
    private const NUM_USERS = 5;
    private const NUM_PAST_MONTH = 3;
    private const CREATE_ATTENDANCE_RATE = 75;
    private const CREATE_MODIFICATION_RATE = 7;

    private $punchInHourOptions = [8, 9, 10, 11];
    private $punchOutHourOptions = [17, 18, 19, 20];
    private $breakInHourOptions = [12, 13, 14];
    private $commentOptions = [
        '打刻忘れ',
        '体調不良による遅刻',
        '電車遅延による、勤務時刻の修正',
        'システム障害により打刻システムが利用できず、復旧後に正しい時刻で打刻修正を依頼しました。'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(self::NUM_USERS)->create();

        $endDate = Carbon::yesterday();
        $startDate = Carbon::yesterday()->copy()->subMonths(self::NUM_PAST_MONTH)->firstOfMonth();

        foreach ($users as $user) {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                if (rand(0, 100) > self::CREATE_ATTENDANCE_RATE) {
                    $date->addDay();
                    continue;
                }

                $punchInHour = $this->punchInHourOptions[array_rand($this->punchInHourOptions)];
                $punchInMinute = rand(0, 59);
                $punchIn = $date->copy()->setTime($punchInHour, $punchInMinute, 0);

                $punchOutHour = $this->punchOutHourOptions[array_rand($this->punchOutHourOptions)];
                $punchOutMinute = rand(0, 59);
                $punchOut = $date->copy()->setTime($punchOutHour, $punchOutMinute);

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                    'punch_in' => $punchIn->startOfMinute()->format('H:i:s'),
                    'punch_out' => $punchOut->startOfMinute()->format('H:i:s'),
                    'status' => 3,
                ]);

                $breakInHour = $this->breakInHourOptions[array_rand($this->breakInHourOptions)];
                $breakInMinute = rand(0, 59);
                $breakIn = $date->copy()->setTime($breakInHour, $breakInMinute);
                $breakOut = $breakIn->copy()->addMinutes(rand(30, 75));

                $break = BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_at' => $breakIn->startOfMinute()->format('H:i:s'),
                    'end_at' => $breakOut->startOfMinute()->format('H:i:s'),
                    'is_ended' => true,
                ]);

                if (rand(0, 100) < self::CREATE_MODIFICATION_RATE) {
                    $modifiedPunchIn = $punchIn->copy()->addMinutes(rand(-60, 60));
                    $modifiedPunchOut = $punchOut->copy()->addMinutes(rand(-60, 60));
                    $comment = $this->commentOptions[array_rand($this->commentOptions)];

                    $modification = Modification::create([
                        'attendance_id' => $attendance->id,
                        'modified_punch_in' => $modifiedPunchIn->startOfMinute()->format('H:i:s'),
                        'modified_punch_out' => $modifiedPunchOut->startOfMinute()->format('H:i:s'),
                        'comment' => $comment,
                        'application_date' => $date->copy()->addDay()->format('Y-m-d'),
                    ]);

                    $shiftMinutes = rand(-30, 30);
                    $modifiedBreakIn = $breakIn->copy()->addMinutes($shiftMinutes);
                    $modifiedBreakOut = $breakOut->copy()->addMinutes($shiftMinutes);

                    BreakModification::create([
                        'modification_id' => $modification->id,
                        'break_id' => $break->id,
                        'modified_start_at' => $modifiedBreakIn->startOfMinute()->format('H:i:s'),
                        'modified_end_at' => $modifiedBreakOut->startOfMinute()->format('H:i:s'),
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
