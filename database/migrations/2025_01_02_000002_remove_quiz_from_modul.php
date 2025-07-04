<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing quiz modules to tugas
        DB::table('modul')->where('jenis', 'quiz')->update(['jenis' => 'tugas']);

        // Update progress entries that reference quiz
        DB::table('progress')->where('jenis_aktivitas', 'selesai_quiz')->update(['jenis_aktivitas' => 'selesai_tugas']);

        // Alter the enum to remove quiz option
        DB::statement("ALTER TABLE modul MODIFY COLUMN jenis ENUM('materi', 'tugas')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add quiz back to enum
        DB::statement("ALTER TABLE modul MODIFY COLUMN jenis ENUM('materi', 'tugas', 'quiz')");
    }
};
