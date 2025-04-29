@extends('layouts.app')

@section('title')
<title>勤怠登録</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_register.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="attendance">
    <div class="attendance__container">
        <p class="attendance__status">
            @switch($status)
            @case(0)
            勤務外
            @break
            @case(1)
            出勤中
            @break
            @case(2)
            休憩中
            @break
            @case(3)
            退勤済
            @break
            @default
            @endswitch
        </p>
        <p id="current_date" class="attendance__date"></p>
        <p id="current_time" class="attendance__time"></p>
        @switch($status)
        @case(0)
        <form class="attendance__form" action="/attendance/punch_in" method="post">
            @csrf
            <button class="attendance__button">出勤</button>
        </form>
        @break
        @case(1)
        <div class="attendance__form-flex">
            <form class="attendance__form" action="/attendance/punch_out" method="post">
                @csrf
                @method('patch')
                <button class="attendance__button">退勤</button>
            </form>
            <form class="attendance__form" action="/attendance/break_in" method="post">
                @csrf
                <button class="attendance__button attendance__button--break">休憩入</button>
            </form>
        </div>
        @break
        @case(2)
        <form class="attendance__form" action="/attendance/break_out" method="post">
            @csrf
            @method('patch')
            <button class="attendance__button attendance__button--break">休憩戻</button>
        </form>
        @break
        @case(3)
        <p class="attendance__message">お疲れ様でした。</p>
        @break
        @default
        @endswitch
    </div>
</div>
<script src="{{ asset('js/show_current_date_time.js') }}"></script>
@endsection