<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless(auth()->id() === $project->client_id, 403, 'Iki dudu proyekmu, sing nilai yo klien-e dewe.');

        $pemenang = $project->bids()->where('status', 'diterima')->first();
        abort_unless($project->status === 'ditutup' && $pemenang, 403, 'Proyek iki durung duwe pemenang.');

        if ($project->rating()->exists()) {
            return back()->withErrors(['skor' => 'Proyek iki wes diwenehi rating, ojo rakus.']);
        }

        $validated = $request->validate([
            'skor' => 'required|integer|between:1,5',
            'komentar' => 'nullable|string|max:500',
        ], [
            'required' => ':attribute ojo dikosongno talah, diisi sek.',
            'between' => ':attribute kudu antarane :min nganti :max.',
        ]);

        $project->rating()->create([
            'client_id' => auth()->id(),
            'developer_id' => $pemenang->developer_id,
            'skor' => $validated['skor'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        return to_route('projects.show', $project)
            ->with('success', 'Rating wes dikirim, matur nuwun.');
    }
}
