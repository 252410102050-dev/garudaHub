@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="card">
    <h1>Masuk</h1>
    <form method="POST" action="/login">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label><input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya</label>

        <button type="submit">Masuk</button>
    </form>
</div>
@endsection
