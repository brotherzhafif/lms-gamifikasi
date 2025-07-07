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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('kode_kelas', 10)->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('tingkat', ['X', 'XI', 'XII']); // Untuk SMA
            $table->enum('jurusan', ['IPA', 'IPS', 'Umum'])->default('Umum');
            $table->integer('kapasitas')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
