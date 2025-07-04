<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::factory()->create([
            'nama' => 'Administrator',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nis' => null,
        ]);

        // Create Teachers
        $guru1 = User::factory()->create([
            'nama' => 'Pak Budi',
            'email' => 'budi@lms.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'nis' => null,
        ]);

        $guru2 = User::factory()->create([
            'nama' => 'Bu Sari',
            'email' => 'sari@lms.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'nis' => null,
        ]);

        // Create Students
        $siswa1 = User::factory()->create([
            'nama' => 'Ahmad Rizki',
            'email' => 'ahmad@lms.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12345',
        ]);

        $siswa2 = User::factory()->create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti@lms.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12346',
        ]);

        $siswa3 = User::factory()->create([
            'nama' => 'Dedi Setiawan',
            'email' => 'dedi@lms.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12347',
        ]);

        // Create Modules
        $moduls = [
            [
                'guru_id' => $guru1->id,
                'judul' => 'Pengenalan PHP Dasar',
                'isi' => 'Materi pengenalan PHP untuk pemula. Meliputi sintaks dasar, variabel, dan struktur kontrol.',
                'jenis' => 'materi',
                'url_file' => null,
                'deadline' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru1->id,
                'judul' => 'Tugas PHP - Membuat Calculator',
                'isi' => 'Buatlah aplikasi calculator sederhana menggunakan PHP. Upload file .php hasil pekerjaan Anda.',
                'jenis' => 'tugas',
                'url_file' => null,
                'deadline' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru2->id,
                'judul' => 'Konsep Database MySQL',
                'isi' => 'Pelajari konsep dasar database, ERD, dan normalisasi database.',
                'jenis' => 'materi',
                'url_file' => null,
                'deadline' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru2->id,
                'judul' => 'Quiz Database Fundamentals',
                'isi' => 'Quiz tentang konsep dasar database, relasi tabel, dan SQL dasar.',
                'jenis' => 'quiz',
                'url_file' => null,
                'deadline' => now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modul')->insert($moduls);

        // Create sample progress (student completed some modules)
        $progressData = [
            [
                'user_id' => $siswa1->id,
                'modul_id' => 1, // PHP Dasar
                'jumlah_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $siswa2->id,
                'modul_id' => 1, // PHP Dasar
                'jumlah_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $siswa2->id,
                'modul_id' => 3, // Database MySQL
                'jumlah_poin' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('progress')->insert($progressData);

        // Create sample answers
        $jawabanData = [
            [
                'modul_id' => 2, // Tugas Calculator
                'siswa_id' => $siswa1->id,
                'url_file' => null,
                'nilai' => null,
                'status' => 'dikirim',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modul_id' => 4, // Quiz Database
                'siswa_id' => $siswa2->id,
                'url_file' => null,
                'nilai' => 85,
                'status' => 'dinilai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('jawaban')->insert($jawabanData);
    }
}