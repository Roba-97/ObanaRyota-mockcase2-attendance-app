<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    // 未ログインユーザに対するリダイレクト処理
    // 管理者ページからはじかれたら管理者ログイン
    // 一般ユーザページからはじかれたらユーザログイン

    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectToOriginal($request, $guards)
        );
    }

    protected function redirectToOriginal($request, array $guards)
    {
        foreach ($guards as $guard) {
            if ($guard === 'admin') {
                return route('admin.login');
            }
        }
    }
}
