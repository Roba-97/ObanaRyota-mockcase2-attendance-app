<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:13
class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attendance = Attendance::create([
            'user_id' => User::factory()->create()->id,
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

        $this->admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminPassword'),
            'email_verified_at' => Carbon::now(),
        ]);
    }

    public function test_show_attendance_detail_currectly()
    {
        $admin = $this->admin;
        $attendance = $this->attendance;

        $date = Carbon::parse($attendance->date);
        $expectedData = [
            $attendance->user->name,
            $date->year . '年',
            $date->month . '月' . $date->day .'日',
            'value="' . Carbon::parse($attendance->punch_in)->format('H:i') . '"',
            'value="' . Carbon::parse($attendance->punch_out)->format('H:i') . '"',
        ];

        $breaks = [];
        foreach($attendance->breaks as $break) {
            $array = [
                'value="' . Carbon::parse($break->start_at)->format('H:i') . '"',
                'value="' . Carbon::parse($break->end_at)->format('H:i') . '"',
            ];
            $breaks = array_merge($breaks, $array);
        }

        $expectedData = array_merge($expectedData , $breaks);

        $response = $this->actingAs($admin, 'admin')->get("/attendance/$attendance->id");

        $response->assertViewHas(['attendance' => $attendance]);
        $response->assertSeeInOrder($expectedData, false);
    }

    public function test_admin_modification_validate_stamp_consistency()
    {
        $admin = $this->admin;
        $attendance = $this->attendance;

        $response = $this->actingAs($admin, 'admin')->post("/stamp_correction/$attendance->id", [
            'modified_punch_in' => '09:00',
            'modified_punch_out' => '8:00',
            'modified_break_in' => ['12:00'],
            'modified_break_out' => ['13:00'],
            'additional_break_in' => null,
            'additional_break_out' => null,
            'comment' => 'test'
        ]);

        $response->assertSessionHasErrors([
            'modified_punch_in',
            'modified_punch_out',
        ]);

        $response->assertInvalid([
            'modified_punch_in' => '出勤時間もしくは退勤時間が不適切な値です',
            'modified_punch_out' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_modification_request_validate_break_in_is_before_punch_out()
    {
        $admin = $this->admin;
        $attendance = $this->attendance;

        $response = $this->actingAs($admin, 'admin')->post("/stamp_correction/$attendance->id", [
            'modified_punch_in' => '09:00',
            'modified_punch_out' => '17:00',
            'modified_break_in' => ['18:00'],
            'modified_break_out' => ['19:00'],
            'additional_break_in' => null,
            'additional_break_out' => null,
            'comment' => 'test'
        ]);

        $response->assertSessionHasErrors(['modified_break_in.0']);
        $response->assertInvalid(['modified_break_in.0' => '休憩時間が勤務時間外です']);
    }

    public function test_modification_request_validate_break_out_is_before_punch_out()
    {
        $admin = $this->admin;
        $attendance = $this->attendance;

        $response = $this->actingAs($admin, 'admin')->post("/stamp_correction/$attendance->id", [
            'modified_punch_in' => '09:00',
            'modified_punch_out' => '17:00',
            'modified_break_in' => ['16:00'],
            'modified_break_out' => ['18:00'],
            'additional_break_in' => null,
            'additional_break_out' => null,
            'comment' => 'test'
        ]);

        $response->assertSessionHasErrors(['modified_break_out.0']);
        $response->assertInvalid(['modified_break_out.0' => '休憩時間が勤務時間外です']);
    }

    public function test_modification_request_validate_comment_required()
    {
        $admin = $this->admin;
        $attendance = $this->attendance;

        $response = $this->actingAs($admin, 'admin')->post("/stamp_correction/$attendance->id", [
            'modified_punch_in' => '09:00',
            'modified_punch_out' => '17:00',
            'modified_break_in' => ['12:00'],
            'modified_break_out' => ['13:00'],
            'additional_break_in' => null,
            'additional_break_out' => null,
            'comment' => ''
        ]);

        $response->assertSessionHasErrors(['comment']);
        $response->assertInvalid(['comment' => '備考を記入してください']);
    }
}
