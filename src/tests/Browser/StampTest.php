<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

// テストケースID:6,7,8
class StampTest extends DuskTestCase
{
    use DatabaseMigrations;

    private const STATUS_SELECTOR = '.attendance__status';
    private const BUTTON_SELECTOR = '.attendance__button';
    private const BREAK_BUTTON_SELECTOR = '.attendance__button--break';
    
    // 出勤、退勤、休憩ボタンが正しく機能することのテスト
    public function test_stamp_buttons_function_properly()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->assertSeeIn(self::BUTTON_SELECTOR, '出勤') // 出勤ボタン表示の確認
                ->press('出勤')
                ->assertSeeIn(self::STATUS_SELECTOR, '出勤中') // 出勤ボタン押下後のステータス表示の確認
                ->assertSeeIn(self::BREAK_BUTTON_SELECTOR, '休憩入') // 休憩入ボタン表示の確認
                ->press('休憩入')
                ->assertSeeIn(self::STATUS_SELECTOR, '休憩中') // 休憩入ボタン押下後のステータス表示の確認
                ->assertSeeIn(self::BREAK_BUTTON_SELECTOR, '休憩戻') // 休憩戻ボタン表示の確認
                ->press('休憩戻')
                ->assertSeeIn(self::STATUS_SELECTOR, '出勤中') // 休憩戻ボタン押下後のステータス表示の確認
                ->assertSeeIn(self::BREAK_BUTTON_SELECTOR, '休憩入') // 再度休憩入ボタンが表示されることの確認
                ->press('休憩入')
                ->assertSeeIn(self::BREAK_BUTTON_SELECTOR, '休憩戻') // 再度休憩戻ボタンが表示されることの確認
                ->press('休憩戻')
                ->assertSeeIn(self::BUTTON_SELECTOR, '退勤') // 退勤ボタン表示の確認
                ->press('退勤')
                ->assertSeeIn(self::STATUS_SELECTOR, '退勤済'); // 退勤ボタン押下後のステータスの確認
        });

        // 出勤は一日一回のみ
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->assertDontSee('出勤') // 出勤ボタンが表示されないことの確認
                ->assertSeeIn(self::STATUS_SELECTOR, '退勤済')
                ->assertSee('お疲れ様でした。');
        });
    }
}
