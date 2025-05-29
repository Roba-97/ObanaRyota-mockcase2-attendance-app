<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:6,7,8(テスト内容の各最終項目)
class StampConfirmTest extends TestCase
{
    use RefreshDatabase, DammyUtils;

    private $user;
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdmin();
        
        $this->user = User::factory()->create();
        $this->createAttendance($this->user, Carbon::today());
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
