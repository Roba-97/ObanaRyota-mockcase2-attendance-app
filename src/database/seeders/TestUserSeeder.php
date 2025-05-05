<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Seeder;
use PhpParser\Node\Stmt\Break_;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => Carbon::now(),
        ]);

        // 初期値：今月の月初日
        $previous = Carbon::now()->copy()->startOfMonth()->subMonths(3); // 3ヶ月前の月初
        $next = Carbon::now()->copy()->startOfMonth()->addMonths(1);     // 来月の月初

        for ($i = 0; $i < 3; $i++) {

            $startDateOfPreviousMonth = $previous->copy()->startOfMonth();
            while (!$startDateOfPreviousMonth->isLastOfMonth()) {
                if (!$startDateOfPreviousMonth->isWeekend()) {
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $startDateOfPreviousMonth->format('Y-m-d'),
                        'punch_in' => '09:00:00',
                        'punch_out' => '18:00:00',
                        'status' => 3,
                    ]);
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_at' => '12:00:00',
                        'end_at' => '13:00:00',
                        'is_ended' => true,
                    ]);
                }
                $startDateOfPreviousMonth->addDay();
            }
            $previous->addMonth();

            $startDateOfNextMonth = $next->copy()->startOfMonth();
            while (!$startDateOfNextMonth->isLastOfMonth()) {
                if (!$startDateOfNextMonth->isWeekend()) {
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $startDateOfNextMonth->format('Y-m-d'),
                        'punch_in' => '09:00:00',
                        'punch_out' => '18:00:00',
                        'status' => 3,
                    ]);
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_at' => '12:00:00',
                        'end_at' => '13:00:00',
                        'is_ended' => true,
                    ]);
                }
                $startDateOfNextMonth->addDay();
            }
            $next->addMonth();
        }
    }
}
