{{-- resources/views/admin/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin')
@section('page-title', 'Admin')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('header-action')
  {{-- 一旦Fortify外してるならダミーでもOK。戻したら logout へ変更 --}}
  <a href="{{ url('/login') }}" class="header__link header__link--login">logout</a>
@endsection

@section('content')
<main class="admin">
  {{-- フィルタ --}}

  <form method="GET" action="{{ url('/admin') }}">
    <input type="text" name="name" value="{{ $f['name'] ?? '' }}" placeholder="名前やメールアドレスを入力してください">
    <input type="text" name="email" value="{{ $f['email'] ?? '' }}" placeholder="メールアドレス">
    
    <select name="gender">
      <option value="">性別</option>
      <option value="all" @selected(($f['gender'] ?? '')==='all')>全て</option>
      <option value="1"   @selected(($f['gender'] ?? '')==='1')>男性</option>
      <option value="2"   @selected(($f['gender'] ?? '')==='2')>女性</option>
      <option value="3"   @selected(($f['gender'] ?? '')==='3')>その他</option>
    </select>

    <select name="category_id">
      <option value="">お問い合わせの種類</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(($f['category_id'] ?? '')==$cat->id)>{{ $cat->content }}</option>
      @endforeach
    </select>

    <input type="date" name="date" value="{{ $f['date'] ?? '' }}">

    <button type="submit" class="btn btn--primary">検索</button>
    <a href="{{ route('admin') }}" class="btn">リセット</a>
    <a href="{{ route('admin.export', request()->query()) }}" class="btn">エクスポート</a>
  </form>

  {{-- 一覧 --}}
  <table class="table">
    <thead>
      <tr>
        <th>お名前</th><th>性別</th><th>メールアドレス</th><th>お問い合わせの種類</th><th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($contacts as $c)
        <tr>
          <td>{{ $c->last_name }} {{ $c->first_name }}</td>
          <td>{{ ['','男性','女性','その他'][$c->gender] ?? '' }}</td>
          <td>{{ $c->email }}</td>
          <td>{{ optional($c->category)->content }}</td>
          <td>
            <button class="btn btn--link js-detail" data-id="{{ $c->id }}">詳細</button>
          </td>
        </tr>
      @empty
        <tr><td colspan="5">データがありません</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $contacts->links() }} {{-- 7件/ページ --}}
</main>

{{-- モーダル（シンプル実装） --}}
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
document.querySelectorAll('.js-detail').forEach(btn=>{
  btn.addEventListener('click', async ()=>{
    const id = btn.dataset.id;
    const res = await fetch(`{{ url('/admin') }}/${id}`);
    const data = await res.json();

    const body = document.querySelector('.modal__body');
    body.innerHTML = `
      <h3>FashionablyLate</h3>
      <dl class="modal__dl">
        <dt>お名前</dt><dd>${data.last_name} ${data.first_name}</dd>
        <dt>性別</dt><dd>${['','男性','女性','その他'][data.gender]??''}</dd>
        <dt>メールアドレス</dt><dd>${data.email}</dd>
        <dt>電話番号</dt><dd>${data.tel??''}</dd>
        <dt>住所</dt><dd>${data.address??''}</dd>
        <dt>建物名</dt><dd>${data.building??''}</dd>
        <dt>お問い合わせの種類</dt><dd>${data.category?.content??''}</dd>
        <dt>お問い合わせ内容</dt><dd>${data.detail??''}</dd>
      </dl>
    `;
    const form = document.getElementById('deleteForm');
    form.action = `{{ url('/admin') }}/${id}`;

    const modal = document.getElementById('modal');
    modal.hidden = false;
  });
});

document.querySelector('.modal__close')?.addEventListener('click', ()=> {
  document.getElementById('modal').hidden = true;
});
</script>
@endsection