{{-- resources/views/contact.blade.php --}}
@extends('layouts.app')

@section('title', 'Contact')
@section('page-title', 'Contact')

@section('css')
  {{-- 後で作る contact.css を読み込みたい場合だけ --}}
  <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection

@section('body-class', 'contact-page')

@section('header-action')
@endsection

@section('content')
<main class="contact">
  <div class="contact__card">
    <div class="contact__inner">

      <form action="/confirm" method="post" novalidate class="form form--contact">
        @csrf

        {{-- お名前（姓・名）※ --}}
        <div class="form__group form__group--name">
          <label class="form__label"><span class="form__label-text">お名前</span> <span class="form__req">※</span></label>
          <div class="form__name-fields">
            <input
              type="text"
              name="last_name"
              value="{{ old('last_name') }}"
              class="form__input form__input--name"
              placeholder="例: 山田"
              autocomplete="family-name"
            >
            <input
              type="text"
              name="first_name"
              value="{{ old('first_name') }}"
              class="form__input form__input--name"
              placeholder="例: 太郎"
              autocomplete="given-name"
            >
          </div>
          @error('last_name')  <p class="form__error">{{ $message }}</p> @enderror
          @error('first_name') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- 性別 ※（ラジオ）デフォルト：男性 --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">性別</span> <span class="form__req">※</span></label>
          <div class="form__radios">
            <label class="form__radio">
              <input type="radio" name="gender" value="1" {{ old('gender', '1')=='1' ? 'checked' : '' }}>
              <span>男性</span>
            </label>
            <label class="form__radio">
              <input type="radio" name="gender" value="2" {{ old('gender')=='2' ? 'checked' : '' }}>
              <span>女性</span>
            </label>
            <label class="form__radio">
              <input type="radio" name="gender" value="3" {{ old('gender')=='3' ? 'checked' : '' }}>
              <span>その他</span>
            </label>
          </div>
          @error('gender') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- メールアドレス ※ --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">メールアドレス</span> <span class="form__req">※</span></label>
          <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form__input"
            placeholder="例: test@example.com"
            autocomplete="email"
          >
          @error('email') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- 電話番号 ※（教材要件：半角数字・ハイフンなし）--}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">電話番号</span><span class="form__req">※</span></label>
          <div class="form__tel-fields">
            <input
              type="text"
              name="tel1"
              value="{{ old('tel1') }}"
              class="form__input form__input--tel"
              inputmode="numeric"
              pattern="\d*"
              placeholder="080"
              maxlength="5"
            >
            <span class="form__tel-separator">-</span>
            
            <input
              type="text"
              name="tel2"
              value="{{ old('tel2') }}"
              class="form__input form__input--tel"
              inputmode="numeric"
              pattern="\d*"
              placeholder="1234"
              maxlength="5"
            >  
            <span class="form__tel-separator">-</span>
            <input
              type="text"
              name="tel3"
              value="{{ old('tel3') }}"
              class="form__input form__input--tel"
              inputmode="numeric"
              pattern="\d*"
              placeholder="5678"
              maxlength="5"
            >
          </div>
          @error('tel1') <p class="form__error">{{ $message }}</p> @enderror
          @error('tel2') <p class="form__error">{{ $message }}</p> @enderror
          @error('tel3') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- 住所 ※ --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">住所</span> <span class="form__req">※</span></label>
          <input
            type="text"
            name="address"
            value="{{ old('address') }}"
            class="form__input"
            placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3"
            autocomplete="street-address"
          >
          @error('address') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- 建物名（任意） --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">建物名</span></label>
          <input
            type="text"
            name="building"
            value="{{ old('building') }}"
            class="form__input"
            placeholder="例: 千駄ヶ谷マンション101"
            autocomplete="address-line2"
          >
          @error('building') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- お問い合わせの種類 ※（デフォルト文言「選択してください」） --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">お問い合わせの種類</span> <span class="form__req">※</span></label>
          <select name="category_id" class="form__select">
            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>選択してください</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : '' }}>
                {{ $category->content }}
              </option>
            @endforeach
          </select>
          @error('category_id') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        {{-- お問い合わせ内容 ※（120文字以内） --}}
        <div class="form__group">
          <label class="form__label"><span class="form__label-text">お問い合わせ内容</span> <span class="form__req">※</span></label>
          <textarea
            name="detail"
            rows="6"
            maxlength="120"
            class="form__textarea"
            placeholder="お問い合わせ内容をご記載ください"
          >{{ old('detail') }}</textarea>
          @error('detail') <p class="form__error">{{ $message }}</p> @enderror
        </div>

        <div class="form__actions">
          <button type="submit" class="button button--primary">確認画面</button>
        </div>
      </form>

    </div>
  </div>
</main>
@endsection
