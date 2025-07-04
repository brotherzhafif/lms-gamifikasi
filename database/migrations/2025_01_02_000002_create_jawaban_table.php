<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('users')->onDelete('cascade');
            $table->text('isi_jawaban')->nullable();
            $table->json('url_file')->nullable();
            $table->integer('nilai')->nullable();
            $table->enum('status', ['belum', 'draft', 'dikirim', 'terlambat', 'dinilai'])->default('belum');
            $table->text('komentar_guru')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Unique constraint: one answer per student per module
            $table->unique(['modul_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};