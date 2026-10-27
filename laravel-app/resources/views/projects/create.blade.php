@extends('layouts.app')
@section('title', 'Pasang Proyek')
@section('content')
<a href="{{ route('projects.index') }}" class="text-sm text-blue-700 hover:underline">← Kembali ke daftar</a>

<div class="mt-3 rounded-xl bg-white p-6 shadow-md">
    <h1 class="mb-4 text-2xl font-bold">Pasang Proyek</h1>

    <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="judul" class="font-semibold">Judul</label>
            <input id="judul" type="text" name="judul" value="{{ old('judul') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('judul') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="deskripsi" class="font-semibold">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">{{ old('deskripsi') }}</textarea>
            @error('deskripsi') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="kategori" class="font-semibold">Kategori</label>
            <input id="kategori" type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Web, Mobile, Desain, Data, AI..."
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('kategori') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="budget_min" class="font-semibold">Budget minimal (Rp)</label>
                <input id="budget_min" type="number" name="budget_min" value="{{ old('budget_min') }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                @error('budget_min') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="budget_max" class="font-semibold">Budget maksimal (Rp)</label>
                <input id="budget_max" type="number" name="budget_max" value="{{ old('budget_max') }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                @error('budget_max') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <label for="batas_lelang" class="font-semibold">Batas lelang</label>
            <input id="batas_lelang" type="date" name="batas_lelang" value="{{ old('batas_lelang') }}"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('batas_lelang') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors">
            Tayangkan Proyek
        </button>
    </form>
</div>
@endsection
