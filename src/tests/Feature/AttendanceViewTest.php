<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:4,5
class AttendanceViewTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_show_current_date_time_correctly()
    {
        $user = $this->user;
        $now = Carbon::now();

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        $date = $now->year . '年'. $now->month . '月'. $now->day . '日('. $weekDays[$now->dayOfWeek] . ')';
        $time = $now->copy()->format('H:i');

        $response->assertSeeText($date);
        $response->assertSeeText($time);
    }

    public function test_show_status_out_of_work()
    {
        $user = $this->user; 

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertViewHas(['status' => 0]);
        $response->assertSeeText('勤務外');
    }

    public function test_show_status_while_at_work()
    {
        $user = $this->user;
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->format('H:i'),
            'punch_out' => Carbon::now()->format('H:i'),
            'status' => 1
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertViewHas(['status' => 1]);
        $response->assertSeeText('出勤中');
        
    }

    public function test_show_status_on_break()
    {
        $user = $this->user;
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->format('H:i'),
            'punch_out' => Carbon::now()->format('H:i'),
            'status' => 2
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertViewHas(['status' => 2]);
        $response->assertSeeText('休憩中');
    }

    public function test_show_status_leaving_work()
    {
        $user = $this->user;
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'punch_in' => Carbon::now()->format('H:i'),
            'punch_out' => Carbon::now()->format('H:i'),
            'status' => 3
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertViewHas(['status' => 3]);
        $response->assertSeeText('退勤済');
    }
}
