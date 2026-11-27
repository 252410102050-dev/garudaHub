@php
    $pemenang = $project->bids->firstWhere('status', 'diterima');
    $rating = $project->rating;
    $pemilik = auth()->check() && auth()->id() === $project->client_id;
@endphp

@if ($project->status === 'ditutup' && $pemenang)
    <div class="mt-6 rounded-xl bg-white p-6 shadow-md">
        <h2 class="mb-3 text-xl font-bold">Rating untuk {{ $pemenang->developer->name }}</h2>

        @if ($rating)
            <p class="text-2xl text-amber-500">{{ str_repeat('★', $rating->skor) }}{{ str_repeat('☆', 5 - $rating->skor) }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $rating->komentar ?: 'Tanpa komentar.' }}</p>
        @elseif ($pemilik)
            <form method="POST" action="{{ route('ratings.store', $project) }}" class="space-y-3">
                @csrf
                <div>
                    <label for="skor" class="font-semibold">Bintang</label>
                    <select id="skor" name="skor"
                        class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('skor') == $i)>{{ $i }} bintang</option>
                        @endfor
                    </select>
                    @error('skor') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="komentar" class="font-semibold">Komentar (opsional)</label>
                    <textarea id="komentar" name="komentar" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-300 p-2 focus:outline-none focus:ring-4 focus:ring-blue-300">{{ old('komentar') }}</textarea>
                    @error('komentar') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <button type="submit"
                    class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-white hover:bg-amber-600 transition-colors">
                    Kirim Rating
                </button>
            </form>
        @else
            <p class="text-sm text-slate-500">Belum ada rating untuk proyek ini.</p>
        @endif
    </div>
@endif
