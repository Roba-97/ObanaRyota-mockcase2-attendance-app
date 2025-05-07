<nav class="header__nav">
    <ul class="header__nav-list">
        <li class="header__nav-item"><a href="/attendance">勤怠</a></li>
        <li class="header__nav-item"><a href="/attendance/list">勤怠一覧</a></li>
        <li class="header__nav-item"><a href="/stamp_correction_request/list">申請</a></li>
        <li class="header__nav-item">
            <form action="/logout" method="post">
                @csrf
                <button class="header__nav-logout" type="submit">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>