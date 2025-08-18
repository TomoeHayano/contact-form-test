@extends('layouts.app') 

@section('title', 'Register')
@section('page-title', 'Register')
@section('body-class', 'register-page')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

{{-- @section('body-class', 'register-page') --}}

@section('content')
  <div class="register__card">
    <div class="register__inner">
      <form action="/register" method="post" novalidate>
        @csrf

        {{-- お名前 --}}
        <div class="form__group">
          <label class="form__label" for="name">お名前</label>
          <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            class="form__input"
            placeholder="例: 山田　太郎"
            autocomplete="name"
          >
          @error('name')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form__group">
          <label class="form__label" for="email">メールアドレス</label>
          <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form__input"
            placeholder="例: test@example.com"
            autocomplete="email"
          >
          @error('email')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>

        {{-- パスワード --}}
        <div class="form__group">
          <label class="form__label" for="password">パスワード</label>
          <input
            id="password"
            type="password"
            name="password"
            class="form__input"
            placeholder="例: coachtech1106"
            autocomplete="new-password"
          >
          @error('password')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="button button--primary">登録</button>
      </form>
    </div>
  </div>
@endsection
