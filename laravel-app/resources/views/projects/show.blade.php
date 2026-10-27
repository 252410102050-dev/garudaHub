@extends('layouts.app')
@section('title', $project->judul)
@section('content')
@php
    $bisaKelola = auth()->check()
        && (auth()->id() === $project->client_id || auth()->user()->role === 'admin');
    $pemilik = auth()->check() && auth()->id() === $project->client_id;
    $sudahTawar = auth()->check() && $project->bids->contains('developer_id', auth()->id());
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

{{-- Form ajukan penawaran (khusus developer) --}}
@auth
    @if (auth()->user()->role === 'developer')
        @if ($sudahTawar)
            <div class="mt-6 rounded-xl bg-blue-50 p-4 text-sm text-blue-800">
                Kon wes nawar proyek iki. Penawaranmu ada di daftar bawah.
            </div>
        @elseif ($project->bisaDitawar())
            <div class="mt-6 rounded-xl bg-white p-6 shadow-md">
                <h2 class="mb-3 text-xl font-bold">Ajukan Penawaran</h2>
                <form method="POST" action="{{ route('bids.store', $project) }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="harga" class="font-semibold">Harga (Rp)</label>
                            <input id="harga" type="number" name="harga" value="{{ old('harga') }}"
                                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                            @error('harga') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="durasi" class="font-semibold">Durasi (hari)</label>
                            <input id="durasi" type="number" name="durasi" value="{{ old('durasi') }}"
                                class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                            @error('durasi') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="proposal" class="font-semibold">Proposal</label>
                        <textarea id="proposal" name="proposal" rows="3"
                            class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">{{ old('proposal') }}</textarea>
                        @error('proposal') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors">
                        Kirim Penawaran
                    </button>
                </form>
            </div>
        @else
            <div class="mt-6 rounded-xl bg-slate-200 p-4 text-sm text-slate-600">
                Lelang proyek iki wes ditutup utawa wes kliwat batas wektune.
            </div>
        @endif
    @endif
@else
    <div class="mt-6 rounded-xl bg-white p-4 text-sm shadow-md">
        <a href="{{ route('login') }}" class="text-blue-700 underline">Masuk</a> sebagai developer kanggo ngajokake penawaran.
    </div>
@endauth

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

            @if ($project->status === 'terbuka' && $bid->status === 'pending')
                <div class="mt-3 flex gap-2">
                    @if ($pemilik)
                        <form method="POST" action="{{ route('bids.pick', $bid) }}"
                            onsubmit="return confirm('Pilih penawaran ini sebagai pemenang? Penawaran lain otomatis ditolak.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="rounded-lg bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                                Pilih Pemenang
                            </button>
                        </form>
                    @endif

                    @if (auth()->check() && auth()->id() === $bid->developer_id)
                        <form method="POST" action="{{ route('bids.destroy', $bid) }}"
                            onsubmit="return confirm('Tarik penawaranmu dari proyek ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-lg bg-slate-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-slate-700 transition-colors">
                                Tarik Penawaran
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-xl bg-white p-5 text-center text-slate-500 shadow-md">
            Belum ada penawaran untuk proyek ini.
        </div>
    @endforelse
</div>
@endsection
