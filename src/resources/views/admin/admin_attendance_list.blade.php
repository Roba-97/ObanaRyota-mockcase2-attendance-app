@extends('layouts.app')

@section('title')
<title>管理者画面-勤怠一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin_attendance_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">日次勤怠一覧</h2>
    </div>
    <div class="attendance-list__date-nav">
        <div class="attendance-list__yesterday">
            <a href="/admin/attendance/list?date=yesterday">
                <img class="date-nav__icon-arrow" src="{{ asset('img/arrow.png')}}" alt="">
                <span>前日</span>
            </a>
        </div>
        <div class="attendance-list__show-date">
            <img class="date-nav__icon-calender" src="{{ asset('img/calender.png')}}" alt="">
            <span></span>
        </div>
        <div class="attendance-list__tomorrow">
            <a href="/admin/attendance/list?date=tomorrow">
                <span>翌日</span>
                <img class="date-nav__icon-arrow date-nav__icon-arrow--rotate" src="{{ asset('img/arrow.png')}}" alt="">
            </a>
        </div>
    </div>
    <table class="attedance-list__table">
        <tr class="attendance-list__table-row">
            <th class="attendance-list__table-header">名前</th>
            <th class="attendance-list__table-header">出勤</th>
            <th class="attendance-list__table-header">退勤</th>
            <th class="attendance-list__table-header">休憩</th>
            <th class="attendance-list__table-header">合計</th>
            <th class="attendance-list__table-header">詳細</th>
        </tr>
        @for($i = 0; $i < 10; $i++)
        <tr class="attendance-list__table-row">
            <td class="attendance-list__table-text">テスト太郎</td>
            <td class="attendance-list__table-text">08:00</td>
            <td class="attendance-list__table-text">19:00</td>
            <td class="attendance-list__table-text">1:00</td>
            <td class="attendance-list__table-text">10:00</td>
            <td class="attendance-list__table-text attendance-list__table-text--bold"><a href="">詳細</a></td>
        </tr>
        @endfor
    </table>
</div>
@endsection