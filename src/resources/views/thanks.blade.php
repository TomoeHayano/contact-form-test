@extends('layouts.app')

@section('title', 'Thanks')

@section('content')
<main class="register" style="text-align:center; padding:80px 0;">
  <p style="color:#8B7969; font-size:20px;">お問い合わせありがとうございました</p>
  <a href="{{ route('contact.create') }}" class="button button--primary" style="margin-top:24px;">HOME</a>
</main>
@endsection
