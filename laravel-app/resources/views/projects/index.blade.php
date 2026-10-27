@extends('layouts.app')
@section('title', 'Daftar Proyek')
@section('content')
<h1 class="mb-4 text-2xl font-bold">Daftar Proyek</h1>

<form method="GET" action="{{ route('projects.index') }}" class="mb-6 flex gap-2">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul atau kategori..."
        class="w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
    <button type="submit"
        class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 transition-colors">
        Cari
    </button>
</form>

<div class="space-y-4">
    @forelse ($projects as $project)
        <div class="rounded-xl bg-white p-5 shadow-md">
            <div class="flex items-start justify-between gap-2">
                <h2 class="text-lg font-bold">
                    <a href="{{ route('projects.show', $project) }}" class="text-blue-700 hover:underline">
                        {{ $project->judul }}
                    </a>
                </h2>
                <span class="rounded-full px-3 py-0.5 text-xs font-semibold
                    {{ $project->status === 'terbuka' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            <p class="mt-1 text-sm text-slate-500">
                oleh {{ $project->client->name }} · {{ $project->kategori }} ·
                batas {{ $project->batas_lelang->format('d M Y') }}
            </p>
            <p class="mt-2 text-sm">{{ \Illuminate\Support\Str::limit($project->deskripsi, 110) }}</p>
            <p class="mt-3 text-sm font-semibold">
                Rp {{ number_format($project->budget_min, 0, ',', '.') }} –
                Rp {{ number_format($project->budget_max, 0, ',', '.') }}
                <span class="font-normal text-slate-500">· {{ $project->bids_count }} penawaran</span>
            </p>
        </div>
    @empty
        <div class="rounded-xl bg-white p-5 text-center text-slate-500 shadow-md">
            Belum ada proyek yang cocok. Yuk pasang yang pertama!
        </div>
    @endforelse
</div>
@endsection
