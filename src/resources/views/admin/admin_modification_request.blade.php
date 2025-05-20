@extends('layouts.app')

@section('title')
<title>管理者画面-修正申請承認</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin_modification_request.css') }}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠詳細</h2>
    </div>
    <form class="modification-request__form" action="/admin/stamp_correction/{{ $modification->id }}" method="post">
        @csrf
        <table class="form__table">
            <tr class="form__table-row">
                <th class="form__table-header">名前</th>
                <td class="form__table-cel">
                    <span class="form__table-text">{{ $modification->attendance->user->name }}</span>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">日付</th>
                <td class="form__table-cel">
                    <div class="form__date-group">
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->attendance->date)->year }}年</span>
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->attendance->date)->month }}月{{ \Carbon\Carbon::parse($modification->attendance->date)->day }}日</span>
                    </div>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">出勤・退勤</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->modified_punch_in)->format('H:i') }}</span>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->modified_punch_out)->format('H:i') }}</span>
                    </div>
                </td>
            </tr>
            @foreach ($modification->breakModifications as $index => $break)
            <tr class="form__table-row">
                <th class="form__table-header">休憩{{ $loop->first ? '' : $loop->iteration }}</th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($break->modified_start_at)->format('H:i') }}</span>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($break->modified_end_at)->format('H:i') }}</span>
                    </div>
                </td>
            </tr>
            @endforeach
            <tr class="form__table-row">
                <th class="form__table-header">
                    休憩{{ $modification->breakModifications->isNotEmpty() ? $modification->breakModifications->count() + 1 : '' }}
                </th>
                <td class="form__table-cel">
                    <div class="form__time-group">
                        @if($modification->additionalBreak)
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->additionalBreak->added_start_at)->format('H:i') }}</span>
                        <span class="form__table-text form__table-text--time-separator">~</span>
                        <span class="form__table-text">{{ \Carbon\Carbon::parse($modification->additionalBreak->added_end_at)->format('H:i') }}</span>
                        @endif
                    </div>
                </td>
            </tr>
            <tr class="form__table-row">
                <th class="form__table-header">備考</th>
                <td class="form__table-cel">
                    <span class="form__table-sentence">{{ $modification->comment}} </span>
                </td>
            </tr>
        </table>
        @if ($modification->is_approved)
        <button class="form__button form__button--approved" disabled>承認済み</button>
        @else
        <button class="form__button" type="submit">承認</button>
        @endif
    </form>
</div>
@endsection