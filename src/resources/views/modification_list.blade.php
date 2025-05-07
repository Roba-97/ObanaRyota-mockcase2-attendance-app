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

        <a class="tab__link tab__link--active" href="">承認待ち</a>
        <a class="tab__link" href="">承認済み</a>


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
        @for($i = 0; $i < 10; $i++)
            <tr class="modification-list__table-row">
            <td class="modification-list__table-cel">承認待ち</td>
            <td class="modification-list__table-cel">西玲奈</td>
            <td class="modification-list__table-cel">2023/06/01</td>
            <td class="modification-list__table-cel">遅延のため</td>
            <td class="modification-list__table-cel">2023/06/02</td>
            <td class="modification-list__table-cel">詳細</td>
            </tr>
            @endfor
    </table>
</div>

@endsection