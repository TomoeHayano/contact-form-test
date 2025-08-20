@extends('layouts.app')

@section('title', 'Admin')
@section('page-title', 'Admin')
@section('body-class', 'admin-page')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('header-action')
  {{-- Fortify無し期間はダミー：将来は /logout に差し替え --}}
  <a href="/login" class="header__link header__link--login">logout</a>
@endsection

@section('content')
<main class="admin">

  {{-- 検索フォーム（部分一致/完全一致のセレクトは非表示方針） --}}
  <form method="GET" action="/admin" class="admin__filter">
    <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="名前やメールアドレスを入力してください">
    <select name="gender">
      <option value="">性別</option>
      <option value="1" {{ ($filters['gender']??'')==='1'?'selected':'' }}>男性</option>
      <option value="2" {{ ($filters['gender']??'')==='2'?'selected':'' }}>女性</option>
      <option value="3" {{ ($filters['gender']??'')==='3'?'selected':'' }}>その他</option>
    </select>
    <select name="category_id">
      <option value="">お問い合わせの種類</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ ($filters['category_id']??'')==$cat->id?'selected':'' }}>
          {{ $cat->content }}
        </option>
      @endforeach
    </select>
    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}">

    <button type="submit" class="btn btn--primary">検索</button>
    <a href="/admin" class="btn">リセット</a>
  </form>

{{-- ツールバー：左=エクスポート／右=数字ページャ --}}
<div class="admin__toolbar">
  <div class="admin__toolbar-left">
    <a href="/admin/export?{{ http_build_query(request()->query()) }}" class="btn">エクスポート</a>
  </div>

  <div class="admin__toolbar-right">
    <div class="pager">
      @if ($contacts->currentPage() > 1)
      
          <a class="pager__prev" href="{{ $contacts->url($contacts->currentPage()-1) }}">&lt;</a>
        @else
          <span class="pager__prev">&lt;</span>
        @endif

    @php($current = $contacts->currentPage())
    @php($last    = $contacts->lastPage())
    @for ($i = 1; $i <= $last; $i++)
      @if ($i === $current)
        <span class="pager__page is-current">{{ $i }}</span>
      @else
        <a class="pager__page" href="{{ $contacts->url($i) }}">{{ $i }}</a>
      @endif
    @endfor

    @if ($contacts->currentPage() < $last)
        <a class="pager__next" href="{{ $contacts->url($contacts->currentPage()+1) }}">&gt;</a>
      @else
        <span class="pager__next">&gt;</span>
      @endif
    </div>
  </div>
</div>

  {{-- 一覧 --}}
  <table class="table">
    <thead>
      <tr>
        <th>お名前</th><th>性別</th><th>メールアドレス</th><th>お問い合わせの種類</th><th></th>
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
</main>

{{-- モーダル --}}
<div id="modal" class="modal" hidden>
  <div class="modal__content">
    <button class="modal__close" aria-label="閉じる">×</button>
    <div class="modal__body"></div>
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
    const res = await fetch(`/admin/${id}`);
    const data = await res.json();

    const body = document.querySelector('.modal__body');
    body.innerHTML = `
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
    document.getElementById('deleteForm').action = `/admin/${id}`;
    document.getElementById('modal').hidden = false;
  });
});
document.querySelector('.modal__close')?.addEventListener('click', ()=>{
  document.getElementById('modal').hidden = true;
});
</script>
@endsection
