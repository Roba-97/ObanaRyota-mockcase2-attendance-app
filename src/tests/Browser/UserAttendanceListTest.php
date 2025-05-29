<?php

namespace Tests\Browser;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:12
class UserAttendanceListTest extends DuskTestCase
{
    use DatabaseMigrations, DammyUtils;

    private const DAMMIES_NUM = 4;

    private $admin;
    private $users;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdmin();

        $this->users = User::factory()->count(self::DAMMIES_NUM)->create();
        foreach($this->users as $user) {
            $this->createAttendance($user, Carbon::today());
            $this->createAttendance($user, Carbon::yesterday());
            $this->createAttendance($user, Carbon::tomorrow());
        }
    }

    public function test_admin_get_user_daily_attendance_list()
    {
        $admin = $this->admin;
        $users = $this->users;

        $this->browse(function (Browser $browser) use ($admin, $users) {
            $browser = $browser->loginAs($admin, 'admin')
                ->visit('/admin/attendance/list')
                ->assertSee(Carbon::today()->format('Y/m/d')); // 遷移後にその日の日付が表示されることの確認

            // その日になされた全ユーザの勤怠情報の確認
            foreach($users as $user) {
                $browser->assertSee($user->name)
                    ->assertSee(Carbon::parse($user->findTodayAttendance()->punch_in)->format('H:s'))
                    ->assertSee(Carbon::parse($user->findTodayAttendance()->punch_out)->format('H:s'))
                    ->assertSee($user->findTodayAttendance()->getBreakDurationAttribute())
                    ->assertSee($user->findTodayAttendance()->getWorkDurationAttribute());
            }
        });

        // 「翌日」押下時の確認
        $this->browse(function (Browser $browser) use ($admin, $users) {
            $browser = $browser->loginAs($admin, 'admin')
                ->visit('/admin/attendance/list')
                ->clickLink('翌日')
                ->assertSee(Carbon::tomorrow()->format('Y/m/d'));
            
            foreach ($users as $user) {
                $attendance = $user->attendances()->where('date', Carbon::tomorrow()->format('Y-m-d'))->first();
                $browser->assertSee($user->name)
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:s'))
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:s'))
                    ->assertSee($attendance->getBreakDurationAttribute())
                    ->assertSee($attendance->getWorkDurationAttribute());
            }
        });

        // 「前日」押下時の確認
        $this->browse(function (Browser $browser) use ($admin, $users) {
            $browser = $browser->loginAs($admin, 'admin')
                ->visit('/admin/attendance/list')
                ->clickLink('前日')
                ->assertSee(Carbon::yesterday()->format('Y/m/d'));

            foreach ($users as $user) {
                $attendance = $user->attendances()->where('date', Carbon::yesterday()->format('Y-m-d'))->first();
                $browser->assertSee($user->name)
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:s'))
                    ->assertSee(Carbon::parse($attendance->punch_in)->format('H:s'))
                    ->assertSee($attendance->getBreakDurationAttribute())
                    ->assertSee($attendance->getWorkDurationAttribute());
            }
        });
    }
}
