<?php

namespace Tests\Browser;

use App\Models\Modification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestHelpers\DammyUtils;

// テストケースID:11.5~
class ModificationRequestTest extends DuskTestCase
{
    use DatabaseMigrations, DammyUtils;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_modification_request_function_properly()
    {
        $user = $this->user;

        // テスト用に1件のみ出勤データを作成
        $attendance = $this->createAttendance($user, Carbon::today());

        $this->browse(function (Browser $browser) use ($user, $attendance) {
            $browser = $browser->loginAs($user)
                ->visit("/attendance/$attendance->id")
                ->type('comment', '電車遅延のため')
                ->press('修正')
                ->clickLink('申請');

            // 「承認待ち」に申請したデータが表示されることの確認
            $modification = Modification::where('is_approved', false)->first();
            $browser->loginAs($user)
                ->visit('/stamp_correction_request/list')
                ->assertSee(Carbon::parse($modification->attendance->date)->format('Y/m/d'))
                ->assertSee($modification->comment)
                ->assertSee(Carbon::parse($modification->application_date)->format('Y/m/d'));
        });

        $admin = $this->createAdmin();
        $modification = Modification::where('is_approved', false)->first();

        // 管理者の申請一覧、申請承認画面に表示されることの確認
        $this->browse(function (Browser $browser) use ($admin, $modification) {
            $browser->loginAs($admin, 'admin')
                ->visit('/stamp_correction_request/list') // 管理者の申請一覧画面に表示されることの確認
                ->assertSee(Carbon::parse($modification->attendance->date)->format('Y/m/d'))
                ->assertSee($modification->comment)
                ->assertSee(Carbon::parse($modification->application_date)->format('Y/m/d'))
                ->clickLink('詳細') // 管理者の承認画面に表示されることの確認
                ->assertSee(Carbon::parse($modification->attendance->date)->year . '年')
                ->assertSee(Carbon::parse($modification->attendance->date)->month . '月' . Carbon::parse($modification->attendance->date)->day . '日')
                ->assertSee(Carbon::parse($modification->modified_punch_in)->format('H:i'))
                ->assertSee(Carbon::parse($modification->modified_punch_out)->format('H:i'))
                ->assertSee($modification->comment)
                ->press('承認'); // 承認済みデータを作成
        });

        // 「承認済み」に管理者が承認したデータが表示されることの確認
        $this->browse(function (Browser $browser) use ($user, $modification) {
            $browser->loginAs($user)
                ->visit('/stamp_correction_request/list')
                ->clickLink('承認済み')
                ->assertSee(Carbon::parse($modification->attendance->date)->format('Y/m/d'))
                ->assertSee($modification->comment)
                ->assertSee(Carbon::parse($modification->application_date)->format('Y/m/d'));
        });
    }

    public function test_user_access_modification_detail_page()
    {
        $user = $this->user;
        $attendance = $this->createAttendance($user, Carbon::today());
        $modification = $this->createModification($attendance, false);

        $this->browse(function (Browser $browser) use ($user, $modification) {
            $browser->loginAs($user)
                ->visit('/stamp_correction_request/list')
                ->clickLink('詳細')
                ->assertPathIs("/attendance/$modification->id")
                ->assertSee(Carbon::parse($modification->modified_punch_in)->format('H:i'))
                ->assertSee(Carbon::parse($modification->modified_punch_out)->format('H:i'))
                ->assertSee($modification->comment)
                ->assertSee('上記の修正内容を申請しています。');
        });
    }
}
