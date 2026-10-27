<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    // Developer mengajukan penawaran
    public function store(Request $request, Project $project)
    {
        abort_unless(auth()->user()->role === 'developer', 403, 'Hanya developer sing oleh nawar.');
        abort_unless($project->bisaDitawar(), 403, 'Lelang proyek iki wes ditutup.');

        if ($project->bids()->where('developer_id', auth()->id())->exists()) {
            return back()->withErrors(['harga' => 'Kon wes nawar proyek iki, ojo rakus.']);
        }

        $validated = $request->validate([
            'harga' => 'required|integer|min:1',
            'durasi' => 'required|integer|min:1',
            'proposal' => 'required|string',
        ], [
            'required' => ':attribute ojo dikosongno talah, diisi sek.',
            'integer' => ':attribute kudu angka lur.',
            'min' => ':attribute minimal :min, ojo nol.',
        ]);

        $validated['developer_id'] = auth()->id();

        $project->bids()->create($validated);

        return to_route('projects.show', $project)
            ->with('success', 'Penawaranmu wes mlebu, tinggal ndongo.');
    }

    // Klien pemilik proyek memilih pemenang
    public function pick(Bid $bid)
    {
        $project = $bid->project;

        abort_unless(auth()->id() === $project->client_id, 403, 'Iki dudu proyekmu, sing milih pemenang yo klien-e dewe.');

        DB::transaction(function () use ($bid, $project) {
            // Kunci baris proyek supaya tidak bisa dipilih dua kali bersamaan
            $terkunci = Project::lockForUpdate()->findOrFail($project->id);
            abort_unless($terkunci->status === 'terbuka', 403, 'Pemenang proyek iki wes dipilih.');

            $terkunci->bids()->where('id', '!=', $bid->id)->update(['status' => 'ditolak']);
            $bid->update(['status' => 'diterima']);
            $terkunci->update(['status' => 'ditutup']);
        });

        return to_route('projects.show', $project)
            ->with('success', 'Pemenang wes dipilih, sing liyane mbuh menangis ning pojokan.');
    }

    // Developer menarik penawarannya sendiri (selama masih pending)
    public function destroy(Bid $bid)
    {
        abort_unless(auth()->id() === $bid->developer_id, 403, 'Iki dudu penawaranmu.');
        abort_unless(
            $bid->status === 'pending' && $bid->project->status === 'terbuka',
            403,
            'Penawaran sing wes diputus ora iso ditarik.'
        );

        $project = $bid->project;
        $bid->delete();

        return to_route('projects.show', $project)
            ->with('success', 'Penawaranmu wes ditarik, mundur teratur.');
    }
}
