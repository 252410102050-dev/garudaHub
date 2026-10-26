<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->string('kategori', 50);
            $table->unsignedBigInteger('budget_min')->default(0);
            $table->unsignedBigInteger('budget_max')->default(0);
            $table->date('batas_lelang');
            $table->enum('status', ['terbuka', 'ditutup'])->default('terbuka');
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
