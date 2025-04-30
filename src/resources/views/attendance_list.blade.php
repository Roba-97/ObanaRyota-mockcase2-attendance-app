@extends('layouts.app')

@section('title')
<title>勤怠一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠一覧</h2>
    </div>
    <div class="attendance-list__month-nav">
        <div class="attendance-list__last-month">
            <a href="/attendance/list?month=previous">
                <img class="month-nav__icon-arrow" src="{{ asset('img/arrow.png')}}" alt="">
                <span>先月</span>
            </a>
        </div>
        <div class="attendance-list__show-month">
            <img class="month-nav__icon-calender" src="{{ asset('img/calender.png')}}" alt="">
            <span>2023/06</span>
        </div>
        <div class="attendance-list__next-month">
            <a href="/attendance/list?month=next">
                <span>翌月</span>
                <img class="month-nav__icon-arrow month-nav__icon-arrow--rotate" src="{{ asset('img/arrow.png')}}" alt="">
            </a>
        </div>
    </div>
    <table class="attedance-list__table">
        <tr class="attendance-list__table-row">
            <th class="attendance-list__table-header">日付</th>
            <th class="attendance-list__table-header">出勤</th>
            <th class="attendance-list__table-header">退勤</th>
            <th class="attendance-list__table-header">休憩</th>
            <th class="attendance-list__table-header">合計</th>
            <th class="attendance-list__table-header">詳細</th>
        </tr>
        @for ($i = 0; $i < 25; $i++)
            <tr class="attendance-list__table-row">
            <td class="attendance-list__table-text">06/01(木)</td>
            <td class="attendance-list__table-text">09:00</td>
            <td class="attendance-list__table-text">18:00</td>
            <td class="attendance-list__table-text">1:00</td>
            <td class="attendance-list__table-text">8:00</td>
            <td class="attendance-list__table-text attendance-list__table-text--bold">詳細</td>
            </tr>
            @endfor
    </table>
</div>

@endsection