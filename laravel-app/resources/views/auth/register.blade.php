@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="card">
    <h1>Daftar</h1>
    <form method="POST" action="/register">
        @csrf
        <label for="name">Nama</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Password (min. 8 karakter)</label>
        <input id="password" type="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">Ulangi password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <label for="role">Daftar sebagai</label>
        <select id="role" name="role">
            <option value="klien" @selected(old('role') === 'klien')>Klien (pasang proyek)</option>
            <option value="developer" @selected(old('role') === 'developer')>Developer (ajukan penawaran)</option>
        </select>
        @error('role') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Daftar</button>
    </form>
</div>
@endsection
