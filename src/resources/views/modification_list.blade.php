@extends('layouts.app')

@section('title')
<title>申請一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/modification_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">申請一覧</h2>
    </div>
    <div class="tab">
        @if($showApproved)
        <a class="tab__link" href="/stamp_correction_request/list">承認待ち</a>
        <a class="tab__link tab__link--active" href="/stamp_correction_request/list?status=approved">承認済み</a>
        @else
        <a class="tab__link tab__link--active" href="/stamp_correction_request/list">承認待ち</a>
        <a class="tab__link" href="/stamp_correction_request/list?status=approved">承認済み</a>
        @endif
    </div>
    <table class="modification-list__table">
        <tr class="modification-list__table-row">
            <th class="modification-list__table-header">状態</th>
            <th class="modification-list__table-header">名前</th>
            <th class="modification-list__table-header">対象日時</th>
            <th class="modification-list__table-header">申請理由</th>
            <th class="modification-list__table-header">申請日時</th>
            <th class="modification-list__table-header">詳細</th>
        </tr>
        @foreach($modifications as $modification)
        <tr class="modification-list__table-row">
            <td class="modification-list__table-cel">{{ $modification->is_approved ? '承認済み' : '承認待ち' }}</td>
            <td class="modification-list__table-cel">{{ $modification->attendance->user->name }}</td>
            <td class="modification-list__table-cel">{{\Carbon\Carbon::parse($modification->attendance->date)->format('Y/m/d') }}</td>
            <td class="modification-list__table-cel">{{ $modification->comment }}</td>
            <td class="modification-list__table-cel">{{\Carbon\Carbon::parse($modification->application_date)->format('Y/m/d') }}</td>
            <td class="modification-list__table-cel"><a href="/attendance/{{ $modification->attendance_id }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>

@endsection