<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:6,7,8(テスト内容の各最終項目)
class StampConfirmTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'punch_in' => '09:00:00',
            'punch_out' => '17:00:00',
            'status' => 3,
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
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

    public function test_confirm_stamp_time_in_admin_view()
    {
        $exceptedData = [
            Carbon::today()->format('Y/m/d'),
            Carbon::parse('09:00:00')->format('H:i'),
            Carbon::parse('17:00:00')->format('H:i'),
            '1:00'
        ];

        $admin = $this->admin;
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');
        $response->assertSeeTextInOrder($exceptedData);
    }
}
