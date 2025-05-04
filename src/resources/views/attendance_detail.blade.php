@extends('layouts.app')

@section('title')
<title>勤怠詳細</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠詳細</h2>
    </div>
    <form class="attendance-detail__form" action="" method="post">
        @csrf
        <table class="form__table">
            <tr class="form__table-row">
                <th class="form__table-header">名前</th>
                <td class="form__table-cel">
                    <span class="form__table-text">{{ $attendance->user->name }}</span>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">日付</th>
                <td class="form__table-cel">
                    <div class="form__date-group">
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($attendance->date)->year }}年</span>
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($attendance->date)->month }}月{{ \Carbon\Carbon::parse($attendance->date)->day }}日</span>
                    </div>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">出勤・退勤</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="modified_punch_in" type="text" value="{{ \Carbon\Carbon::parse($attendance->punch_in)->format('H:i') }}">
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_punch_out" type="text" value="{{\Carbon\Carbon::parse($attendance->punch_out)->format('H:i') }}">
                    </div>
                </td>
            </tr>
            @foreach ($attendance->breaks as $break)
            <tr class="form__table-row">
                <th class="form__table-header">休憩{{ $loop->first ? '' : $loop->iteration }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="modified_break_in" type="text" value="{{ \Carbon\Carbon::parse($break->start_at)->format('H:i') }}">
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_break_out" type="text" value="{{ \Carbon\Carbon::parse($break->end_at)->format('H:i') }}">
                    </div>
                </td>
            </tr>
            @endforeach
            <tr class="form__table-row">
                <th class="form__table-header">休憩{{ $attendance->breaks->isNotEmpty() ? $attendance->breaks->count() + 1 : '' }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="modified_break_in" type="text" value="">
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_break_out" type="text" value="">
                    </div>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">備考</th>
                <td class="form__table-cel">
                    <textarea class="form__textarea" name="comment" rows="3">電車遅延のため</textarea>
                </td>
            </tr>
        </table>
        <button class="form__button" type="submit">修正</button>
    </form>
</div>
@endsection