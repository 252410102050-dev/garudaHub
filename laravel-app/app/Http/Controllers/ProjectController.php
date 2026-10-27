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
}
