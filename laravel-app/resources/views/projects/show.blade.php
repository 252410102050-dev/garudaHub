@extends('layouts.app')
@section('title', $project->judul)
@section('content')
@php
    $bisaKelola = auth()->check()
        && (auth()->id() === $project->client_id || auth()->user()->role === 'admin');
@endphp

<a href="{{ route('projects.index') }}" class="text-sm text-blue-700 hover:underline">← Kembali ke daftar</a>

<div class="mt-3 rounded-xl bg-white p-6 shadow-md">
    <div class="flex items-start justify-between gap-2">
        <h1 class="text-2xl font-bold">{{ $project->judul }}</h1>
        <span class="rounded-full px-3 py-0.5 text-xs font-semibold
            {{ $project->status === 'terbuka' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
            {{ ucfirst($project->status) }}
        </span>
    </div>

    <p class="mt-1 text-sm text-slate-500">
        oleh {{ $project->client->name }} · {{ $project->kategori }} ·
        batas {{ $project->batas_lelang->format('d M Y') }}
    </p>

    <p class="mt-4">{{ $project->deskripsi }}</p>

    <p class="mt-4 font-semibold">
        Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }} –
        Rp {{ number_format($project->budget_max, 0, ',', '.') }}
    </p>

    @if ($bisaKelola)
        <div class="mt-5 flex gap-2 border-t border-slate-200 pt-4">
            <a href="{{ route('projects.edit', $project) }}"
                class="rounded-lg bg-yellow-400 px-4 py-1.5 text-sm font-semibold hover:bg-yellow-500 transition-colors">
                Edit
            </a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                onsubmit="return confirm('Yakin mau hapus proyek ini beserta semua penawarannya?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </form>
        </div>
    @endif
</div>

<h2 class="mb-3 mt-6 text-xl font-bold">Penawaran ({{ $project->bids->count() }})</h2>

<div class="space-y-3">
    @forelse ($project->bids as $bid)
        <div class="rounded-xl bg-white p-5 shadow-md">
            <div class="flex items-start justify-between gap-2">
                <strong>{{ $bid->developer->name }}</strong>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold
                    @if ($bid->status === 'diterima') bg-emerald-100 text-emerald-800
                    @elseif ($bid->status === 'ditolak') bg-red-100 text-red-700
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ ucfirst($bid->status) }}
                </span>
            </div>
            <p class="mt-1 text-sm font-semibold">
                Rp {{ number_format($bid->harga, 0, ',', '.') }} · {{ $bid->durasi }} hari
            </p>
            <p class="mt-2 text-sm">{{ $bid->proposal }}</p>
        </div>
    @empty
        <div class="rounded-xl bg-white p-5 text-center text-slate-500 shadow-md">
            Belum ada penawaran untuk proyek ini.
        </div>
    @endforelse
</div>
@endsection
