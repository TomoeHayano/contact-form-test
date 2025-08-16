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
                    @hasSection('header-action')
                        @yield('header-action')
                    @else
                        <a href="{{ route('login') }}" class="header__link header__link--login">login</a>
                    @endif
                </nav>
        </div>
    </header>

    @hasSection('page-title')
        <div class="page-title">
            <h1 class="page-title__text">@yield('page-title')</h1>
        </div>
    @endif

    @yield('content')

</body>
</html>