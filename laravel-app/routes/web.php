<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

// Halaman utama langsung ke daftar proyek
Route::redirect('/', '/projects');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

// Khusus yang sudah login (create harus sebelum {project})
Route::middleware('auth')->group(function () {
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
});

Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
