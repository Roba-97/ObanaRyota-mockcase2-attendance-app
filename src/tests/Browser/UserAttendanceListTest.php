<?php

namespace Tests\Browser;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:9
class UserAttendanceListTest extends DuskTestCase
{
    use DatabaseMigrations, DammyUtils;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_get_monthly_attendance_list()
    {
        $user = $this->user;

        // ユーザの今月の勤怠データ作成
        $startOfMonth = Carbon::today()->startOfMonth();
        $date = $startOfMonth->copy();
        while(!$date->isTomorrow()) {
            $this->createAttendance($user, $date);
            $date->addDay();
        }

        $attendances = $user->attendancesByMonth(Carbon::today()->year, Carbon::today()->month);
        
        $this->browse(function (Browser $browser) use ($user, $attendances) {
            $browser = $browser->loginAs($user)
                ->visit('attendance/list')
                ->assertSee(Carbon::today()->format('Y/m')); // 遷移後現在の月が表示されることの確認

            foreach ($attendances as $attendance) {
                $browser->assertSee(Carbon::parse($attendance->date)->format('m/d'))
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:i'))
                    ->assertSee(Carbon::parse($attendance->punch_out)->format('H:i'));
            }
        });

        // 「前月」、「翌月」押下時の確認
        $this->browse(function (Browser $browser) use ($user) {
            $carbon = Carbon::today();
            $browser->loginAs($user)
                ->visit('attendance/list')
                ->clickLink('前月')
                ->assertSee($carbon->subMonth()->format('Y/m'))
                ->clickLink('翌月')
                ->assertSee($carbon->addMonth()->format('Y/m'));
        });
    }

    public function test_user_access_attendance_detail_page()
    {
        $user = User::factory()->create();

        // 表示確認する勤怠データとして1件作成
        $attendance = $this->createAttendance($user, Carbon::today());

        $this->browse(function (Browser $browser) use ($user, $attendance) {
            $browser->loginAs($user)
                ->visit('attendance/list')
                ->clickLink('詳細')
                ->assertPathIs("/attendance/$attendance->id")
                ->assertSee($user->name)
                ->assertSee(Carbon::parse($attendance->date)->year . '年')
                ->assertSee(Carbon::parse($attendance->date)->month . '月' . Carbon::parse($attendance->date)->day . '日');
        });
    }
}
