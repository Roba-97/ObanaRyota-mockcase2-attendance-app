<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('adminLogout');
    }

    public function index() {
       return view('admin.admin_login');
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        //認証の試行を処理

        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('admin')->attempt($credentials)) { // ログイン試行

            $request->session()->regenerate(); // セッション更新
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME); // 日次勤怠一覧へ
        }

        return back()->withErrors([ // ログインに失敗した場合
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
