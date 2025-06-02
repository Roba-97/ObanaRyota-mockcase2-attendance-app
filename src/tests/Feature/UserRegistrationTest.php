<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

// テストケースID:1, 16.1
class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_validate_name_required()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertInvalid(['name' => 'お名前を入力してください',]);
    }

    public function test_user_registration_validate_email_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertInvalid(['email' => 'メールアドレスを入力してください',]);
    }

    public function test_user_registration_validate_password_less_than_7()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'shorter',
            'password_confirmation' => 'shorter'
        ]);

        $response->assertInvalid(['password' => 'パスワードは8文字以上で入力してください',]);
    }

    public function test_user_registration_validate_password_same_as_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'password_confirmation' => 'password'
        ]);

        $response->assertInvalid(['password_confirmation' => 'パスワードと一致しません',]);
    }

    public function test_user_registration_validate_password_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password_confirmation' => 'password'
        ]);

        $response->assertInvalid(['password' => 'パスワードを入力してください',]);
    }

    public function test_user_registration_success()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);
    }

    public function test_verification_email_sent_properly()
    {
        Notification::fake();

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post(route('register'), $userData);
        $user = User::where('email', 'test@example.com')->first();

        // ユーザーがまだメール認証されていないことを確認
        $this->assertFalse($user->hasVerifiedEmail());

        // メールがユーザーに送信されたこと確認
        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }
}
