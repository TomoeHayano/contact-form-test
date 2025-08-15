<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FashionablyLate')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @yield('css') 
</head>
<body class="@yield('body-class')">

    {{-- ヘッダ --}}
    <header class="header">
        <div class="header__inner">
            <div class="site-title">FashionablyLate</div>
            <nav class="header__nav">
                <a href="{{ route('login') }}" class="header__link header__link--login">login</a>
            </nav>
        </div>
    </header>

    {{-- ページごとの内容 --}}
    @yield('content')

</body>
</html>