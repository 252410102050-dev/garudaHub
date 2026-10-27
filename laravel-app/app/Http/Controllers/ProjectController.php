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
}
