<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('title')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <h1 class="header__heading">coachtech勤怠管理</h1>
        <div class="header__logo"></div>
        @yield('header')
    </header>
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
    <main class="content--after-login">
        @yield('content')
    </main>
    @else
    <main class="content">
        @yield('content')
    </main>
    @endif
</body>

</html>