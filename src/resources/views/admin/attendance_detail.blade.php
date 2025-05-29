@extends('layouts.app')

@section('title')
<title>管理者画面-勤怠詳細</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@php
$latestModification = $attendance->modifications()->latest()->first();
$isWaiting = $latestModification && !$latestModification->is_approved;
@endphp

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠詳細</h2>
    </div>
    <form class="attendance-detail__form" action="/stamp_correction/{{ $attendance->id }}" method="post">
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
                        <input class="form__input" name="modified_punch_in" type="time"
                            value="{{ old('modified_punch_in', \Carbon\Carbon::parse($attendance->punch_in)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_punch_out" type="time"
                            value="{{ old('modified_punch_out', \Carbon\Carbon::parse($attendance->punch_out)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                    </div>
                    @if ($errors->has('modified_punch_in') || $errors->has('modified_punch_out'))
                    <p class="form__error-message">
                        {{ $errors->has('modified_punch_in') ? $errors->first('modified_punch_in') : $errors->first('modified_punch_out') }}
                    </p>
                    @endif
                </td>
            </tr>
            @foreach ($attendance->breaks as $index => $break)
            <tr class="form__table-row">
                <th class="form__table-header">休憩{{ $loop->first ? '' : $loop->iteration }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="modified_break_in[]" type="time"
                            value="{{ old("modified_break_in.$index", \Carbon\Carbon::parse($break->start_at)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_break_out[]" type="time"
                            value="{{ old("modified_break_out.$index", \Carbon\Carbon::parse($break->end_at)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                    </div>
                    @if ($errors->has("modified_break_in.$index") || $errors->has("modified_break_out.$index"))
                    <p class="form__error-message">
                        {{ $errors->has("modified_break_in.$index") ? $errors->first("modified_break_in.$index") : $errors->first("modified_break_out.$index") }}
                    </p>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="form__table-row">
                <th class="form__table-header">
                    休憩{{ $attendance->breaks->isNotEmpty() ? $attendance->breaks->count() + 1 : '' }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="additional_break_in" type="time"
                            value="{{ old('additional_break_in') }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="additional_break_out" type="time"
                            value="{{ old('additional_break_out') }}" {{ $isWaiting? 'readonly' : '' }}>
                    </div>
                    @if ($errors->has('additional_break_in') || $errors->has('additional_break_out'))
                    <p class="form__error-message">
                        {{ $errors->has('additional_break_in') ? $errors->first('additional_break_in') : $errors->first('additional_break_out') }}
                    </p>
                    @endif
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">備考</th>
                <td class="form__table-cel">
                    <textarea class="form__textarea" name="comment" rows="3" {{ $isWaiting? 'readonly' : '' }}>{{ old('comment') }}</textarea>
                    @error('comment')
                    <p class="form__error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
        @if ($isWaiting)
        <p class="form__waiting-message">
            *この勤怠には修正の申請があります。<br>
            <a class="form__confirm-link" href="/stamp_correction_request/approve/{{ $latestModification->id }}">確認</a>
        </p>
        @else
        <button class="form__button" type="submit">修正</button>
        @endif
    </form>
</div>
@endsection