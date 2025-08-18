@extends('layouts.app')

@section('title', 'Thanks')
@section('page-title', 'Thanks')
@section('body-class', 'contact-page')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<main class="register">
  <div class="register__card">
    <div class="register__inner" style="text-align:center;">
      <p>お問い合わせありがとうございました。</p>
      <a href="/" class="button button--primary" style="margin-top: 18px;">HOME</a>
    </div>
  </div>
</main>
@endsection
