<?php

namespace Tests\Browser;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:14
class AdminStaffListTest extends DuskTestCase
{
    use DatabaseMigrations, DammyUtils;

    private const DAMMIES_NUM = 5;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdmin();
    }

    public function test_show_all_user_infomation()
    {
        $admin = $this->admin;
        $users = User::factory()->count(self::DAMMIES_NUM)->create();

        $this->browse(function (Browser $browser) use ($admin, $users) {
            $browser = $browser->loginAs($admin, 'admin')->visit('/admin/staff/list');
            foreach ($users as $user) {
                $browser->assertSee($user->name)->assertSee($user->email);
            }
        });
    }

    public function test_admin_get_user_monthly_attendance_list()
    {
        $user = User::factory()->create();

        // ユーザの今月の勤怠データ作成
        $startOfMonth = Carbon::today()->startOfMonth();
        for ($day = 1; $day <= 31; $day += 5) {
            $date = $startOfMonth->copy()->addDays($day);
            $this->createAttendance($user, $date);
        }

        $attendances = $user->attendancesByMonth(Carbon::today()->year, Carbon::today()->month);
        $admin = $this->admin;

        $this->browse(function (Browser $browser) use ($admin, $user, $attendances) {
            $browser = $browser->loginAs($admin, 'admin')
                ->visit('/admin/staff/list')
                ->clickLink('詳細')
                ->assertPathIs("/admin/attendance/staff/$user->id");
            
            foreach($attendances as $attendance) {
                $browser->assertSee(Carbon::parse($attendance->date)->format('m/d'))
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:i'))
                    ->assertSee(Carbon::parse($attendance->punch_out)->format('H:i'));
            }
        });

        // 「前月」、「翌月」押下時の確認
        $this->browse(function (Browser $browser) use ($admin, $user) {
            $carbon = Carbon::today();
            $browser->loginAs($admin, 'admin')
                ->visit("/admin/attendance/staff/$user->id")
                ->clickLink('前月')
                ->assertSee($carbon->subMonth()->format('Y/m'))
                ->clickLink('翌月')
                ->assertSee($carbon->addMonth()->format('Y/m'));
        });
    }

    public function test_admin_access_attendance_detail_page()
    {
        $admin = $this->admin;
        $user = User::factory()->create();

        // 表示確認する勤怠データとして1件作成
        $attendance = $this->createAttendance($user, Carbon::today());

        $this->browse(function (Browser $browser) use ($admin, $user, $attendance) {
            $browser->loginAs($admin, 'admin')
                ->visit("/admin/attendance/staff/$user->id")
                ->clickLink('詳細')
                ->assertPathIs("/attendance/$attendance->id")
                ->assertSee($user->name)
                ->assertSee(Carbon::parse($attendance->date)->year . '年')
                ->assertSee(Carbon::parse($attendance->date)->month . '月' . Carbon::parse($attendance->date)->day . '日' );
        });
    }
}
