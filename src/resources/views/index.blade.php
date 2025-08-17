{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin')
@section('page-title', 'Admin')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('header-action')
  {{-- 開発中のダミー。Fortify戻したらPOST logoutに変更推奨 --}}
  <a href="{{ url('/login') }}" class="header__link header__link--login">logout</a>
@endsection

@section('content')
<div class="filters">
<main class="admin">

  {{-- フィルタ --}}
  <form method="GET" action="{{ route('admin') }}" class="admin__filter">
    <input type="text" name="name"  value="{{ $filters['name']  ?? '' }}" placeholder="名前やメールアドレスを入力してください">
    <input type="text" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="メールアドレス">

    <select name="gender">
      <option value="">性別</option>
      <option value="1" @selected(($filters['gender'] ?? '')==='1')>男性</option>
      <option value="2" @selected(($filters['gender'] ?? '')==='2')>女性</option>
      <option value="3" @selected(($filters['gender'] ?? '')==='3')>その他</option>
    </select>

    <select name="category_id">
      <option value="">お問い合わせの種類</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '')==$category->id)>
          {{ $category->content }}
        </option>
      @endforeach
    </select>

    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}">

    
    <button type="submit" class="btn btn--primary">検索</button>
    <a href="{{ route('admin') }}" class="btn btn--reset">リセット</a>
  </form>

  
    <div class="filters__export">
        <a href="{{ route('admin.export', request()->query()) }}" class="btn btn--export">エクスポート</a>
    </div>
    </div>

  {{-- 一覧 --}}
  <table class="table">
    <thead>
      <tr>
        <th>お名前</th>
        <th>性別</th>
        <th>メールアドレス</th>
        <th>お問い合わせの種類</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($contacts as $contact)
        <tr>
          <td>{{ $contact->last_name }} {{ $contact->first_name }}</td>
          <td>{{ ['','男性','女性','その他'][$contact->gender] ?? '' }}</td>
          <td>{{ $contact->email }}</td>
          <td>{{ optional($contact->category)->content }}</td>
          <td>
            <button class="btn btn--link js-detail" data-id="{{ $contact->id }}">詳細</button>
          </td>
        </tr>
      @empty
        <tr><td colspan="5">データがありません</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- 7件/ページ --}}
  {{ $contacts->links() }}
</main>

{{-- モーダル --}}
<div id="modal" class="modal" hidden>
  <div class="modal__content">
    <button class="modal__close" aria-label="閉じる">×</button>
    <div class="modal__body"><!-- JSで埋め込み --></div>

    <form id="deleteForm" method="POST" class="modal__footer">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn--danger">削除</button>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.js-detail').forEach((button) => {
  button.addEventListener('click', async () => {
    const id = button.dataset.id;
    const res = await fetch(`{{ url('/admin') }}/${id}`);
    const data = await res.json();

    const body = document.querySelector('.modal__body');
    body.innerHTML = `
      <h3>FashionablyLate</h3>
      <dl class="modal__dl">
        <dt>お名前</dt><dd>${data.last_name} ${data.first_name}</dd>
        <dt>性別</dt><dd>${['','男性','女性','その他'][data.gender] ?? ''}</dd>
        <dt>メールアドレス</dt><dd>${data.email ?? ''}</dd>
        <dt>電話番号</dt><dd>${data.tel ?? ''}</dd>
        <dt>住所</dt><dd>${data.address ?? ''}</dd>
        <dt>建物名</dt><dd>${data.building ?? ''}</dd>
        <dt>お問い合わせの種類</dt><dd>${data.category?.content ?? ''}</dd>
        <dt>お問い合わせ内容</dt><dd>${data.detail ?? ''}</dd>
      </dl>
    `;
    const form = document.getElementById('deleteForm');
    form.action = `{{ url('/admin') }}/${id}`;

    document.getElementById('modal').hidden = false;
  });
});

document.querySelector('.modal__close')?.addEventListener('click', () => {
  document.getElementById('modal').hidden = true;
});
</script>
@endsection
