@extends('layouts.app')

@section('title')
<title>管理者画面-勤怠一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@php
$date = \Carbon\Carbon::parse($displayedDate);
@endphp
@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">{{ $date->year }}年{{ $date->month }}月{{ $date->day }}日の勤怠</h2>
    </div>
    <div class="attendance-list__date-nav">
        <div class="attendance-list__yesterday">
            <a href="/admin/attendance/list?date=yesterday">
                <i class="fa-solid fa-arrow-left-long date-nav__icon-arrow"></i>
                <span>前日</span>
            </a>
        </div>
        <div class="attendance-list__show-date">
            <img class="date-nav__icon-calender" src="{{ asset('img/calendar.png')}}" alt="">
            <span>{{ $displayedDate }}</span>
        </div>
        <div class="attendance-list__tomorrow">
            <a href="/admin/attendance/list?date=tomorrow">
                <span>翌日</span>
                <i class="fa-solid fa-arrow-right-long date-nav__icon-arrow"></i>
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

        @foreach($usersWithAttendances as $user)
        <tr class="attendance-list__table-row">
            <td class="attendance-list__table-text">{{ $user->name }}</td>
            @php
            $attendance = $user->attendances->first();
            @endphp
            @if($attendance)
            <td class="attendance-list__table-text">{{ \Carbon\Carbon::parse($attendance->punch_in)->format('H:i'); }}</td>
            <td class="attendance-list__table-text">{{ $attendance->status === 3 ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : ''; }}</td>
            <td class="attendance-list__table-text">{{ $attendance->break_duration }}</td>
            <td class="attendance-list__table-text">{{ $attendance->status === 3 ? $attendance->work_duration : ''}}</td>
            <td class="attendance-list__table-text attendance-list__table-text--bold"><a href="/attendance/{{ $attendance->id }}">詳細</a></td>
            @else
            <td class="attendance-list__table-text">
                @if(\Carbon\Carbon::parse($displayedDate)->isToday())
                出勤前
                @else
                休
                @endif
            </td>
            <td class="attendance-list__table-text">ー</td>
            <td class="attendance-list__table-text">ー</td>
            <td class="attendance-list__table-text">ー</td>
            <td class="attendance-list__table-text">ー</td>
            @endif
        </tr>
        @endforeach
    </table>
</div>
@endsection