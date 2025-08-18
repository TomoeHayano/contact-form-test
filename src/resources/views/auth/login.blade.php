@extends('layouts.app')

@section('title', 'Login')
@section('page-title', 'Login')
@section('body-class', 'login-page')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('header-action')
  <a href="/register" class="header__link header__link--login">register</a>
@endsection


  {{-- @section('body-class', 'login-page') --}}

@section('content')
  <div class="login__card">
    <div class="login__inner">
      <form class="form" action="{{ route('login.attempt') }}" method="post" novalidate>
        @csrf

        <div class="form__group">
          <label class="form__label">メールアドレス</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form__input" placeholder="例: test@example.com" autocomplete="email">
          @error('email')
            <div class="form__error">{{ $message }}</div>
          @enderror
        </div>

        <div class="form__group">
          <label class="form__label">パスワード</label>
          <input type="password" name="password" class="form__input" placeholder="例: coachtech1106" autocomplete="current-password">
          @error('password')
            <div class="form__error">{{ $message }}</div>
          @enderror
        </div>

        <button class="button button--primary" type="submit">ログイン</button>
      </form>
    </div>
  </div>
@endsection
