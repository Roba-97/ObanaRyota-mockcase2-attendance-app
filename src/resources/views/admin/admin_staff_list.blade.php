@extends('layouts.app')

@section('title')
<title>管理者画面-スタッフ一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin_staff_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">スタッフ一覧</h2>
    </div>
    <table class="attedance-list__table">
        <tr class="staff-list__table-row">
            <th class="staff-list__table-header">名前</th>
            <th class="staff-list__table-header">メールアドレス</th>
            <th class="staff-list__table-header">月次勤怠</th>
        </tr>
        @foreach($staff as $member)
        <tr class="staff-list__table-row">
            <td class="staff-list__table-text">{{ $member->name }}</td>
            <td class="staff-list__table-text">{{ $member->email }}</td>
            <td class="staff-list__table-text"><a href="/admin/attendance/staff/{{ $member->id }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection