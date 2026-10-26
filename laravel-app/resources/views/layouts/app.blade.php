<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Garuda-Hub')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f5f7; color: #222; }
        header { background: #1b3a5c; color: #fff; padding: 12px 24px; display: flex; gap: 16px; align-items: center; }
        header a { color: #fff; text-decoration: none; }
        header .brand { font-weight: bold; font-size: 1.2rem; margin-right: auto; }
        header form { display: inline; margin: 0; }
        main { max-width: 760px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #bbb; border-radius: 4px; }
        button { margin-top: 16px; padding: 8px 16px; background: #1b3a5c; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        button.link { margin: 0; padding: 0; background: none; text-decoration: underline; }
        .flash { background: #dff0d8; padding: 10px 14px; border-radius: 4px; margin-bottom: 16px; }
        .error { color: #b00020; font-size: .9rem; margin-top: 4px; }
    </style>
</head>
<body>
<header>
    <a class="brand" href="/">Garuda-Hub</a>
    @auth
        <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <form method="POST" action="/logout">
            @csrf
            <button class="link" type="submit">Keluar</button>
        </form>
    @else
        <a href="/login">Masuk</a>
        <a href="/register">Daftar</a>
    @endauth
</header>
<main>
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
