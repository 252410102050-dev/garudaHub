<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Garuda-Hub')</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

<nav class="flex items-center gap-4 bg-slate-900 px-6 py-3 text-white">
    <a href="/" class="mr-auto text-xl font-bold">🦅 Garuda-Hub</a>
    @auth
        <span class="text-sm text-slate-300">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="text-sm underline hover:text-slate-300">Keluar</button>
        </form>
    @else
        <a href="/login" class="hover:text-slate-300">Masuk</a>
        <a href="/register" class="rounded-md bg-blue-600 px-4 py-1.5 font-semibold hover:bg-blue-700">Daftar</a>
    @endauth
</nav>

<main class="mx-auto max-w-3xl p-4 md:p-6">
    @session('success')
        <div class="mb-4 rounded-lg bg-emerald-100 px-4 py-3 text-emerald-800">{{ $value }}</div>
    @endsession

    @yield('content')
</main>

</body>
</html>
