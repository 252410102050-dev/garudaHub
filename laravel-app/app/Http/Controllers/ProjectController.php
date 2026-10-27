<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Daftar proyek + pencarian judul/kategori
    public function index(Request $request)
    {
        $q = $request->query('q');

        $projects = Project::with('client')
            ->withCount('bids')
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                      ->orWhere('kategori', 'like', "%{$q}%");
            })
            ->latest()
            ->get();

        return view('projects.index', ['projects' => $projects, 'q' => $q]);
    }

    // Detail proyek + semua penawarannya
    public function show(Project $project)
    {
        $project->load('client', 'bids.developer');

        return view('projects.show', ['project' => $project]);
    }

    // Form pasang proyek (khusus klien)
    public function create()
    {
        abort_unless(auth()->user()->role === 'klien', 403, 'Hanya klien yang boleh memasang proyek.');

        return view('projects.create');
    }

    // Simpan proyek baru
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'klien', 403, 'Hanya klien yang boleh memasang proyek.');

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',
            'batas_lelang' => 'required|date|after:today',
        ], [
            'required' => ':attribute ojo dikosongno talah, diisi sek.',
            'integer' => ':attribute kudu angka lur.',
            'budget_max.gte' => 'Budget maksimal ojo luwih cilik teko budget minimal, iso-isone.',
            'batas_lelang.after' => 'Batas lelang kudu tanggal sing durung kelakon, ojo mundur.',
        ]);

        // Pemilik proyek = klien yang sedang login (bukan dari input form)
        $validated['client_id'] = auth()->id();

        $project = Project::create($validated);

        return to_route('projects.show', $project)
            ->with('success', 'Proyekmu wes tayang, tinggal nunggu developer podo rebutan.');
    }

    // Form edit proyek (pemilik atau admin)
    public function edit(Project $project)
    {
        $this->otorisasi($project);

        return view('projects.edit', ['project' => $project]);
    }

    // Simpan perubahan proyek
    public function update(Request $request, Project $project)
    {
        $this->otorisasi($project);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',
            'batas_lelang' => 'required|date',
            'status' => 'required|in:terbuka,ditutup',
        ], [
            'required' => ':attribute ojo dikosongno talah, diisi sek.',
            'integer' => ':attribute kudu angka lur.',
            'budget_max.gte' => 'Budget maksimal ojo luwih cilik teko budget minimal, iso-isone.',
        ]);

        $project->update($validated);

        return to_route('projects.show', $project)
            ->with('success', 'Proyek wes dibenerno, mantep.');
    }

    // Hapus proyek (penawaran ikut terhapus lewat cascade)
    public function destroy(Project $project)
    {
        $this->otorisasi($project);

        $project->delete();

        return to_route('projects.index')
            ->with('success', 'Proyek wes dibuang, ilang tanpa jejak.');
    }

    // Hanya pemilik proyek atau admin
    private function otorisasi(Project $project): void
    {
        $user = auth()->user();

        abort_unless(
            $user->id === $project->client_id || $user->role === 'admin',
            403,
            'Iki dudu proyekmu, ojo ndemek-ndemek.'
        );
    }
}
