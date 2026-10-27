@extends('layouts.app')
@section('title', 'Edit Proyek')
@section('content')
<a href="{{ route('projects.show', $project) }}" class="text-sm text-blue-700 hover:underline">← Kembali ke proyek</a>

<div class="mt-3 rounded-xl bg-white p-6 shadow-md">
    <h1 class="mb-4 text-2xl font-bold">Edit Proyek</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="judul" class="font-semibold">Judul</label>
            <input id="judul" type="text" name="judul" value="{{ old('judul', $project->judul) }}"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('judul') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="deskripsi" class="font-semibold">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">{{ old('deskripsi', $project->deskripsi) }}</textarea>
            @error('deskripsi') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="kategori" class="font-semibold">Kategori</label>
            <input id="kategori" type="text" name="kategori" value="{{ old('kategori', $project->kategori) }}"
                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
            @error('kategori') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="budget_min" class="font-semibold">Budget minimal (Rp)</label>
                <input id="budget_min" type="number" name="budget_min" value="{{ old('budget_min', $project->budget_min) }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                @error('budget_min') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="budget_max" class="font-semibold">Budget maksimal (Rp)</label>
                <input id="budget_max" type="number" name="budget_max" value="{{ old('budget_max', $project->budget_max) }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                @error('budget_max') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="batas_lelang" class="font-semibold">Batas lelang</label>
                <input id="batas_lelang" type="date" name="batas_lelang"
                    value="{{ old('batas_lelang', $project->batas_lelang->format('Y-m-d')) }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                @error('batas_lelang') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="status" class="font-semibold">Status</label>
                <select id="status" name="status"
                    class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <option value="terbuka" @selected(old('status', $project->status) === 'terbuka')>Terbuka</option>
                    <option value="ditutup" @selected(old('status', $project->status) === 'ditutup')>Ditutup</option>
                </select>
                @error('status') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-300 transition-colors">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
