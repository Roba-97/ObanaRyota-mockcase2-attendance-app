<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:6,7,8
class AttendanceStampTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // 出勤機能
    public function test_punch_in()
    {
        $user = $this->user;

        $respose = $this->actingAs($user)->get('/attendance');
        $respose->assertSee('<button class="attendance__button">出勤</button>', false);

        $respose = $this->actingAs($user)->post('/attendance/punch_in');

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->startOfMinute()->format('H:i:s'),
            'status' => 1,
        ]);
    }

    public function test_punch_in_only_once_a_day()
    {
        $user = $this->user;
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->format('H:i'),
            'punch_out' => Carbon::now()->format('H:i'),
            'status' => 3,
        ]);

        $respose = $this->actingAs($user)->get('/attendance');

        $respose->assertDontSee('<button class="attendance__button">出勤</button>', false);
        $respose->assertSeeText('お疲れ様でした。');
    }

    // 休憩機能


    // 退勤機能
    public function test_punch_out()
    {
        $user = $this->user;
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->format('H:i'),
            'punch_out' => Carbon::now()->format('H:i'),
            'status' => 1,
        ]);

        $respose = $this->actingAs($user)->get('/attendance');
        $respose->assertSee('<button class="attendance__button">退勤</button>', false);

        $respose = $this->actingAs($user)->patch('/attendance/punch_out');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'punch_out' => Carbon::now()->startOfMinute()->format('H:i:s'),
            'status' => 3,
        ]);
    }
}
