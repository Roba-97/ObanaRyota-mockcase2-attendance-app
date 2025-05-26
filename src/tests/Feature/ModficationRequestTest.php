<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:11.1~4
class ModficationRequestTest extends TestCase
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

    public function test_modification_request_validate_stamp_consistency()
    {
        $user = $this->user;
        $attendance = $this->attendance;

        $response = $this->actingAs($user)->post("/stamp_correction_request/$attendance->id",[
            'modified_punch_in' => '09:00',
            'modified_punch_out' => '08:00',
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
        $user = $this->user;
        $attendance = $this->attendance;

        $response = $this->actingAs($user)->post("/stamp_correction_request/$attendance->id", [
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
        $user = $this->user;
        $attendance = $this->attendance;

        $response = $this->actingAs($user)->post("/stamp_correction_request/$attendance->id", [
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
        $user = $this->user;
        $attendance = $this->attendance;

        $response = $this->actingAs($user)->post("/stamp_correction_request/$attendance->id", [
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
