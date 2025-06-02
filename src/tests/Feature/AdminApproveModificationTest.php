<?php

namespace Tests\Feature;

use App\Models\Modification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:15
class AdminApproveModificationTest extends TestCase
{
    use RefreshDatabase, DammyUtils;

    private const DAMMIES_NUM = 8;
    private const APPROVED_NUM = 4;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdmin();

        $users = User::factory()->count(self::DAMMIES_NUM)->create();
        foreach($users as $index => $user) {
            $attendance = $this->createAttendance($user, Carbon::create(2025, 6, 1));
            $isApproved = $index < self::APPROVED_NUM ? true : false;

            $this->createModification($attendance, $isApproved);
        }
    }

    public function test_show_all_waiting_approval_modifications()
    {
        $admin = $this->admin;
        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list');

        $response->assertViewHas('modifications', function ($mods) {
            return $mods->every(function ($mod) {
                return (bool) $mod->is_approved === false;
            });
        });
    }

    public function test_show_all_approved_modifications()
    {
        $admin = $this->admin;
        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list?status=approved');

        $response->assertViewHas('modifications', function ($mods) {
            return $mods->every(function ($mod) {
                return (bool) $mod->is_approved === true;
            });
        });
    }

    public function test_show_modification_request_currectly()
    {
        $admin = $this->admin;
        $modification = Modification::where('is_approved', false)->first();

        $date = Carbon::parse($modification->attendance->date);
        $expectedData = [
            $modification->attendance->user->name,
            $date->year . '年',
            $date->month . '月' . $date->day . '日',
            Carbon::parse($modification->modified_punch_in)->format('H:i'),
            Carbon::parse($modification->modified_punch_out)->format('H:i'),
        ];

        $breaks = [];
        foreach ($modification->breakModifications as $break) {
            $array = [
                Carbon::parse($break->modified_start_at)->format('H:i'),
                Carbon::parse($break->modified_end_at)->format('H:i'),
            ];
            $breaks = array_merge($breaks, $array);
        }
        $breaks = array_merge($breaks, [$modification->comment]);
        $expectedData = array_merge($expectedData, $breaks);

        $response = $this->actingAs($admin, 'admin')->get("/stamp_correction_request/approve/$modification->id");
        $response->assertViewHas(['modification' => $modification]);
        $response->assertSeeInOrder($expectedData, false);
    }

    public function test_approve_modification_request_currectly()
    {
        $admin = $this->admin;
        $modification = Modification::where('is_approved', false)->first();

        $response = $this->actingAs($admin, 'admin')->post("/stamp_correction_request/approve/$modification->id");
        $response->assertStatus(302);

        $this->assertTrue($modification->refresh()->is_approved);

        $this->assertDatabaseHas('attendances',[
            'id' => $modification->attendance_id,
            'punch_in' => $modification->modified_punch_in,
            'punch_out' => $modification->modified_punch_out,
        ]);

        foreach ($modification->breakModifications as $breakMod) {
            $this->assertDatabaseHas('breaks', [
                'id' => $breakMod->break_id,
                'attendance_id' => $modification->attendance_id,
                'start_at' => $breakMod->modified_start_at,
                'end_at' => $breakMod->modified_end_at
            ]);
        }
    }
}
