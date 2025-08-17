@extends('layouts.app')

@section('title', 'Confirm')
@section('page-title', 'Confirm')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<main class="register">
  <div class="register__card">
    <div class="register__inner">
      <table class="table">
        <tr><th>お名前</th><td>{{ $input['last_name'] }}　{{ $input['first_name'] }}</td></tr>
        <tr><th>性別</th><td>{{ ['','男性','女性','その他'][$input['gender']] }}</td></tr>
        <tr><th>メールアドレス</th><td>{{ $input['email'] }}</td></tr>
        <tr><th>電話番号</th><td>{{ $input['tel'] }}</td></tr>
        <tr><th>住所</th><td>{{ $input['address'] }}</td></tr>
        <tr><th>建物名</th><td>{{ $input['building'] ?? '' }}</td></tr>
        <tr><th>お問い合わせの種類</th><td>{{ $categoryName }}</td></tr>
        <tr><th>お問い合わせ内容</th><td style="white-space:pre-wrap;">{{ $input['detail'] }}</td></tr>
      </table>

      {{-- 送信用 hidden --}}
      <form action="{{ route('contact.store') }}" method="post" style="margin-top:16px;">
        @csrf
        @foreach($input as $k=>$v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach

        <button class="button button--primary" type="submit">送信</button>
        <a href="{{ url()->previous() }}" onclick="history.back();return false;" class="button" style="margin-left:12px;">修正</a>
      </form>
    </div>
  </div>
</main>
@endsection
