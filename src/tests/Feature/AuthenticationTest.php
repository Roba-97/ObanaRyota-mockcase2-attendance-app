<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// テストケースID:2,3
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Admin::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminPassword'),
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => Carbon::now(),
        ]);
    }

    public function test_user_login_validate_email_required()
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertInvalid(['email' =>  'メールアドレスを入力してください',]);
    }

    public function test_user_login_validate_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertInvalid(['password' =>  'パスワードを入力してください',]);
    }

    public function test_user_login_validate_unregistered()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password'
        ]);

        $response->assertInvalid(['email' =>  'ログイン情報が登録されていません',]);
    }

    public function test_admin_login_validate_email_required()
    {
        $response = $this->post('/admin/login', [
            'password' => 'adminPassword',
        ]);

        $response->assertInvalid(['email' =>  'メールアドレスを入力してください',]);
    }

    public function test_admin_login_validate_password_required()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
        ]);

        $response->assertInvalid(['password' =>  'パスワードを入力してください',]);
    }

    public function test_admin_login_validate_unregistered()
    {
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password'
        ]);

        $response->assertInvalid(['email' =>  'ログイン情報が登録されていません',]);
    }
}
