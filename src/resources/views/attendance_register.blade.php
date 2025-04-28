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
        <p class="attendance__status">勤務外</p>
        <p id="current_date" class="attendance__date"></p>
        <p id="current_time" class="attendance__time"></p>
        <form action="" class="attendance__form">
            <button class="attendance__button">出勤</button>
        </form>
    </div>
</div>
<script src="{{ asset('js/show_current_date_time.js') }}"></script>
@endsection