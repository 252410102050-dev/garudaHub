@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow-md">
    <h1 class="mb-4 text-2xl font-bold">Masuk</h1>

    <form method="POST" action="/login" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="password" class="font-semibold">Password</label>
            <input id="password" type="password" name="password" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('password') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>

        <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors">
            Masuk
        </button>
    </form>
</div>
@endsection
