@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow-md">
    <h1 class="mb-4 text-2xl font-bold">Daftar</h1>

    <form method="POST" action="/register" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="font-semibold">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="email" class="font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="password" class="font-semibold">Password (min. 8 karakter)</label>
            <input id="password" type="password" name="password" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('password') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="font-semibold">Ulangi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
        </div>

        <div>
            <label for="role" class="font-semibold">Daftar sebagai</label>
            <select id="role" name="role"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                <option value="klien" @selected(old('role') === 'klien')>Klien (pasang proyek)</option>
                <option value="developer" @selected(old('role') === 'developer')>Developer (ajukan penawaran)</option>
            </select>
            @error('role') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors">
            Daftar
        </button>
    </form>
</div>
@endsection
