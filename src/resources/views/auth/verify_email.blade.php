@extends('layouts.app')

@section('title')
<title>メール認証確認</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify_email.css')}}">
@endsection

@section('content')
<div class="verify">
    <div class="verify__inner">
        <p class="verify__message">
            @if(session('message'))
            {{ session('message') }}<br>
            @else
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            @endif
            メール認証を完了してください。
        </p>
        <a class="verify__button" href="{{ config('mail.mailhog_url', 'http://localhost:8025') }}">認証はこちらから</a>
        <form class="verify__form" action="/email/verification-notification" method="post">
            @csrf
            <button class="verify__link" type="submit">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection