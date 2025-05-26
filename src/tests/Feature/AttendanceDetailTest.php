<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:10
class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $attendance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2025-06-01',
            'punch_in' => '09:00:00',
            'punch_out' => '17:00:00',
            'status' => 3, 
        ]);

        BreakTime::create([
            'attendance_id' => $this->attendance->id,
            'start_at' => '12:00:00',
            'end_at' => '13:00:00',
            'is_ended' => true,
        ]);
    }

    public function test_show_user_name_correctly()
    {
        $user = $this->user;
        $attendance = $this->attendance;

        $response = $this->actingAs($user)->get("/attendance/$attendance->id");
        $response->assertSeeText($user->name);
    }

    public function test_show_date_correctly()
    {
        $user = $this->user;
        $attendance = $this->attendance;
        $expectedData = ['2025年', '6月1日'];

        $response = $this->actingAs($user)->get("/attendance/$attendance->id");
        $response->assertSeeTextInOrder($expectedData);
    }

    public function test_show_stamp_time_correctly()
    {
        $user = $this->user;
        $attendance = $this->attendance;
        $expectedData = ['value="09:00"', 'value="17:00"'];

        $response = $this->actingAs($user)->get("/attendance/$attendance->id");
        $response->assertSeeInOrder($expectedData, false);
    }

    public function test_show_break_time_correctly()
    {
        $user = $this->user;
        $attendance = $this->attendance;
        $expectedData = ['value="12:00"', 'value="13:00"'];

        $response = $this->actingAs($user)->get("/attendance/$attendance->id");
        $response->assertSeeInOrder($expectedData, false);
    }
}
