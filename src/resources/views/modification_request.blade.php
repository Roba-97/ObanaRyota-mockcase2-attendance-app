@extends('layouts.app')

@section('title')
<title>勤怠詳細</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/modification_request.css') }}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">勤怠詳細</h2>
    </div>
    <div class="modification-request__inner">
        <table class="inner__table">
            <tr class="inner__table-row">
                <th class="inner__table-header">名前</th>
                <td class="inner__table-cel">
                    <span class="inner__table-text">{{ $attendance->user->name }}</span>
                </td>
            </tr>
            <tr class="inner__table-row">
                <th class="inner__table-header">日付</th>
                <td class="inner__table-cel">
                    <div class="inner__date-group">
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($attendance->date)->year }}年</span>
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($attendance->date)->month }}月{{ \Carbon\Carbon::parse($attendance->date)->day }}日</span>
                    </div>
                </td>
            </tr>
            <tr class="inner__table-row">
                <th class="inner__table-header">出勤・退勤</th>
                <td class="inner__table-cel">
                    <div class="inner__time-group">
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($modification->modified_punch_in)->format('H:i') }}</span>
                        <span class="inner__table-text inner__table-text--time-separator">~</span>
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($modification->modified_punch_out)->format('H:i') }}</span>
                    </div>
                </td>
            </tr>
            @foreach ($modification->breakModifications as $index => $break)
            <tr class="inner__table-row">
                <th class="inner__table-header">休憩{{ $loop->first ? '' : $loop->iteration }}</th>
                <td class="inner__table-cel">
                    <div class="inner__time-group">
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($break->modified_start_at)->format('H:i') }}</span>
                        <span class="inner__table-text inner__table-text--time-separator">~</span>
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($break->modified_end_at)->format('H:i') }}</span>
                    </div>
                </td>
            </tr>
            @endforeach
            <tr class="inner__table-row">
                <th class="inner__table-header">
                    休憩{{ $modification->breakModifications->isNotEmpty() ? $modification->breakModifications->count() + 1 : '' }}
                </th>
                <td class="inner__table-cel">
                    @if($modification->additionalBreak)
                    <div class="inner__time-group">
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($modification->additionalBreak->added_start_at)->format('H:i') }}</span>
                        <span class="inner__table-text inner__table-text--time-separator">~</span>
                        <span class="inner__table-text">{{ \Carbon\Carbon::parse($modification->additionalBreak->added_end_at)->format('H:i') }}</span>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="inner__table-row">
                <th class="inner__table-header">備考</th>
                <td class="inner__table-cel">
                    <span class="inner__table-cel--comment">{{ $modification->comment }}</span>
                </td>
            </tr>
        </table>
        @if ($isWaiting)
        <p class="inner__waiting-message">*上記の修正内容を申請しています。</p>
        @else
        <p class="inner__waiting-message">*上記の内容で修正が承認されました。</p>
        @endif
    </div>
</div>
@endsection