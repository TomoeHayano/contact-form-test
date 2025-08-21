@extends('layouts.app')

@section('title', 'Confirm')
@section('page-title', 'Confirm')
@section('body-class', 'contact-page')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
  <style>
    /* ログインボタンを非表示にする */
    .header__link.header__link--login {
      display: none !important;
    }
  </style>
@endsection

@section('body-class', 'contact-page')

@section('header-action')

@section('content')
<main class="register">
  <div class="register__card">
    <div class="register__inner">

      {{-- 表示 --}}
      <dl class="modal__definition_list">
        <dt>お名前</dt>
        <dd>{{ $input['last_name'] }} {{ $input['first_name'] }}</dd>

        <dt>性別</dt>
        <dd>{{ ['','男性','女性','その他'][$input['gender']] ?? '' }}</dd>

        <dt>メール</dt>
        <dd>{{ $input['email'] }}</dd>

        <dt>電話番号</dt>
        <dd>{{ $input['tel'] }}</dd>

        <dt>住所</dt>
        <dd>{{ $input['address'] }}</dd>

        <dt>建物名</dt>
        <dd>{{ $input['building'] ?? '' }}</dd>

        <dt>お問い合わせの種類</dt>
        <dd>{{ $categoryName ?? '' }}</dd>

        <dt>お問い合わせ内容</dt>
        <dd>{{ $input['detail'] }}</dd>
      </dl>
    </div>
  </div>  

      {{-- 送信 --}}
      <form action="/thanks" method="post">
        @csrf
        @foreach($input as $input_key => $input_value)
          <input type="hidden" name="{{ $input_key }}" value="{{ $input_value }}">
        @endforeach
        <div class="button-group">
          <button class="button button--primary" type="submit">送信</button>
          <a href="javascript:void(0)" onclick="history.back()" class="button">修正</a>
        </div>
      </form>
</main>
@endsection