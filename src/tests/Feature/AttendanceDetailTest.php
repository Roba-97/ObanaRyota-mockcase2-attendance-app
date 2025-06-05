<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:10
class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase, DammyUtils;

    private $user;
    private $attendance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->attendance = $this->createAttendance($this->user, Carbon::create(2025, 6, 1));
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
