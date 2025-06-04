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
                <i class="fa-solid fa-arrow-left-long month-nav__icon-arrow"></i>
                <span>前月</span>
            </a>
        </div>
        <div class="attendance-list__show-month">
            <img class="month-nav__icon-calender" src="{{ asset('img/calendar.png')}}" alt="">
            <span>{{ $displayedMonth }}</span>
        </div>
        <div @class(['attendance-list__next-month', 'attendance-list__next-month--hidden'=> \Carbon\Carbon::createFromFormat('Y/m', $displayedMonth)->isCurrentMonth()])>
            <a href="/attendance/list?month=next">
                <span>翌月</span>
                <i class="fa-solid fa-arrow-right-long month-nav__icon-arrow"></i>
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

        @php
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        $firstDayOfMonth = \Carbon\Carbon::createFromFormat('Y/m', $displayedMonth)->startOfMonth();
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        @endphp

        @for ($date = $firstDayOfMonth->copy(); $date->lte($lastDayOfMonth); $date->addDay())
        @php
        if($date->isFuture()) {
        break;
        }
        $attendance = $attendances->where('date', $date->copy()->format('Y-m-d'))->first();
        @endphp
        <tr class="attendance-list__table-row">
            <td class="attendance-list__table-text">{{ $date->format('m/d') }}({{ $weekDays[$date->dayOfWeek] }})</td>
            @if ($attendance)
            <td class="attendance-list__table-text">{{ \Carbon\Carbon::parse($attendance->punch_in)->format('H:i'); }}</td>
            <td class="attendance-list__table-text">{{ $attendance->status === 3 ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '' }}</td>
            <td class="attendance-list__table-text">{{ $attendance->break_duration }}</td>
            <td class="attendance-list__table-text">{{ $attendance->status === 3 ? $attendance->work_duration : '' }}</td>
            <td class="attendance-list__table-text attendance-list__table-text--bold"><a href="/attendance/{{ $attendance->id }}">詳細</a></td>
            @else
            <td class="attendance-list__table-text">
                @if($date->isToday())
                勤務外
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
        @endfor
    </table>
</div>

@endsection