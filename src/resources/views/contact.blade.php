@extends('layouts.app')

@section('title', 'Contact')
@section('page-title', 'Contact')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}"><!-- 既存のフォーム系ルックを流用 -->
@endsection

@section('content')
<main class="register">  {{-- 既存フォームレイアウトを再利用 --}}
  <div class="register__card">
    <div class="register__inner">
      <form class="contact-form" action="/contacts/confirm" method="post">
        @csrf

        {{-- お名前（姓・名） --}}
        <div class="form__group">
          <label class="form__label">お名前 <span style="color:#C0352B;">※</span></label>
          <div style="display:flex; gap:12px;">
            <input type="text" name="last_name" class="form__input" placeholder="例: 山田" value="{{ old('last_name') }}">
            <input type="text" name="first_name" class="form__input" placeholder="例: 太郎" value="{{ old('first_name') }}">
          </div>
          @error('last_name')<div class="form__error">{{ $message }}</div>@enderror
          @error('first_name')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- 性別（デフォルト男性） --}}
        <div class="form__group">
          <label class="form__label">性別 <span style="color:#C0352B;">※</span></label>
          <label><input type="radio" name="gender" value="1" {{ old('gender','1')=='1'?'checked':'' }}> 男性</label>
          <label style="margin-left:16px;"><input type="radio" name="gender" value="2" {{ old('gender')=='2'?'checked':'' }}> 女性</label>
          <label style="margin-left:16px;"><input type="radio" name="gender" value="3" {{ old('gender')=='3'?'checked':'' }}> その他</label>
          @error('gender')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form__group">
          <label class="form__label">メールアドレス <span style="color:#C0352B;">※</span></label>
          <input type="email" name="email" class="form__input" placeholder="例: test@example.com" value="{{ old('email') }}">
          @error('email')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- 電話番号（ハイフン無し） --}}
        <div class="form__group">
          <label class="form__label">電話番号 <span style="color:#C0352B;">※</span></label>
          <input type="text" name="tel" class="form__input" placeholder="例: 08012" value="{{ old('tel') }}">
          @error('tel')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- 住所 --}}
        <div class="form__group">
          <label class="form__label">住所 <span style="color:#C0352B;">※</span></label>
          <input type="text" name="address" class="form__input" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3" value="{{ old('address') }}">
          @error('address')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- 建物名（任意） --}}
        <div class="form__group">
          <label class="form__label">建物名</label>
          <input type="text" name="building" class="form__input" placeholder="例: 千駄ヶ谷マンション101" value="{{ old('building') }}">
          @error('building')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- お問い合わせの種類 --}}
        <div class="form__group">
          <label class="form__label">お問い合わせの種類 <span style="color:#C0352B;">※</span></label>
          <select name="category_id" class="form__input">
            <option value="">選択してください</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ $category->content }}</option>
            @endforeach
          </select>
          @error('category_id')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        {{-- お問い合わせ内容（120文字以内） --}}
        <div class="form__group">
          <label class="form__label">お問い合わせ内容 <span style="color:#C0352B;">※</span></label>
          <textarea name="detail" class="form__input" rows="5" placeholder="お問い合わせ内容をご記載ください">{{ old('detail') }}</textarea>
          @error('detail')<div class="form__error">{{ $message }}</div>@enderror
        </div>

        <button class="button button--primary" type="submit">確認画面</button>
      </form>
    </div>
  </div>
</main>
@endsection