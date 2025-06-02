<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

// テストケースID:16.2~
class EmailVerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    private string $mailHogUrl = 'http://mailhog:8025';
    private string $verifyLinkText = 'Verify Email Address';

    public function test_email_verification_function_properly()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('/register')
                ->type('name', 'test')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('登録する')
                ->assertPathIs('/email/verify') // メール認証誘導ページに遷移したことの確認
                ->assertSee('メール認証を完了してください。')
                ->clickLink('認証はこちらから')
                ->assertSee('MailHog') // リンクをクリックするとメール認証サイトに遷移することの確認
                ->waitForText('a few seconds ago', 30)
                ->clickAtPoint(540, 125) // 最新のメールをクリック(windowサイズ1920:1080から座標で指定)
                ->withinFrame('#preview-html', function (Browser $frame) {
                    // iframe内の認証リンクをクリック
                    $frame->clickLink($this->verifyLinkText);
                });

            // ウィンドウハンドルを取得し、新しいウィンドウに切り替える(新規タブが展開されるため)
            $windowHandles = $browser->driver->getWindowHandles();
            if (count($windowHandles) > 1) {
                $browser->driver->switchTo()->window(last($windowHandles)); // 最後（最新）のウィンドウに切り替え
            } else {
                $this->fail('認証リンクが正常にクリックされていません');
            }

            $browser->assertPathIs('/attendance')
                ->assertSee('出勤')
                ->screenshot('verified');
        });
    }
}
