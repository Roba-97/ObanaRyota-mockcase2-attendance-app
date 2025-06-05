@extends('layouts.app')

@section('title')
<title>管理者ログイン</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css')}}">
@endsection

@section('content')
<div class="login-form">
    <h2 class="login-form__heading">管理者ログイン</h2>
    <div class="login-form__inner">
        <form class="login-form__form" action="/admin/login" method="post">
            @csrf
            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="mail" name="email" id="email" value="{{ old('email') }}">
                <p class="login-form__error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password" value="{{ old('password') }}">
                <p class="login-form__error-message">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <button class="login-form__btn" type="submit">管理者ログインする</button>
        </form>
    </div>
</div>
@endsection