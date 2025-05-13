@extends('layouts.app')

@section('title')
<title>管理者画面-勤怠一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin_attendance_list.css')}}">
@endsection

@section('header')
@include('layouts.header')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content__about">
        <h2 class="content__about-text">日次勤怠一覧</h2>
    </div>
</div>
@endsection