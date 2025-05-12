@extends('layouts.app')

@section('title')
<title>勤怠詳細</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@php
$punchIn = $isFromModification ? $modification->modified_punch_in : $attendance->punch_in;
$punchOut = $isFromModification ? $modification->modified_punch_out : $attendance->punch_out;
$breaks = $isFromModification ? $modification->breakModifications : $attendance->breaks;
$start_at = $isFromModification ? 'modified_start_at' : 'start_at';
$end_at = $isFromModification ? 'modified_end_at' : 'end_at';
$additionalBreakIn = $isFromModification && $modification->additionalBreak !== null ? $modification->additionalBreak->added_start_at : '';
$additionalBreakOut = $isFromModification && $modification->additionalBreak !== null ? $modification->additionalBreak->added_end_at : '';
$comment = $isFromModification ? $modification->comment : '';
@endphp

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠詳細</h2>
    </div>
    <form class="attendance-detail__form" action="/stamp_correction_request/{{ $attendance->id }}" method="post">
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
                            value="{{ old('modified_punch_in', \Carbon\Carbon::parse($punchIn)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_punch_out" type="time"
                            value="{{ old('modified_punch_out', \Carbon\Carbon::parse($punchOut)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                    </div>
                    @if ($errors->has('modified_punch_in') || $errors->has('modified_punch_out'))
                    <p class="form__error-message">
                        {{ $errors->has('modified_punch_in') ? $errors->first('modified_punch_in') : $errors->first('modified_punch_out') }}
                    </p>
                    @endif
                </td>
            </tr>
            @foreach ($breaks as $index => $break)
            <tr class="form__table-row">
                <th class="form__table-header">休憩{{ $loop->first ? '' : $loop->iteration }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="modified_break_in[]" type="time"
                            value="{{ old("modified_break_in.$index", \Carbon\Carbon::parse($break->$start_at)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="modified_break_out[]" type="time"
                            value="{{ old("modified_break_out.$index", \Carbon\Carbon::parse($break->$end_at)->format('H:i')) }}" {{ $isWaiting? 'readonly' : '' }}>
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
                    休憩{{ $attendance->breaks->isNotEmpty() ? $breaks->count() + 1 : '' }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <input class="form__input" name="additional_break_in" type="time"
                            value="{{ old('additional_break_in', $additionalBreakIn) }}" {{ $isWaiting? 'readonly' : '' }}>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <input class="form__input" name="additional_break_out" type="time"
                            value="{{ old('additional_break_out', $additionalBreakOut) }}" {{ $isWaiting? 'readonly' : '' }}>
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
                    <textarea class="form__textarea" name="comment" rows="3" {{ $isWaiting? 'readonly' : '' }}>{{ old('comment', $comment) }}</textarea>
                    @error('comment')
                    <p class="form__error-message">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
        @if ($isWaiting && $isFromModification)
        <p class="form__waiting-message">*承認待ちのため修正はできません。<br>*上記の修正内容を申請しています。</p>
        @elseif ($isWaiting)
        <p class="form__waiting-message">*承認待ちのため修正はできません。<br>*上記の内容は承認によって変更されます。</p>
        @else
        <button class="form__button" type="submit">修正</button>
        @endif
    </form>
</div>
@endsection