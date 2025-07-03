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
                'poin_reward' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru1->id,
                'judul' => 'Tugas PHP - Membuat Calculator',
                'isi' => 'Buatlah aplikasi calculator sederhana menggunakan PHP. Upload file .php hasil pekerjaan Anda.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(7),
                'poin_reward' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru2->id,
                'judul' => 'Konsep Database MySQL',
                'isi' => 'Pelajari konsep dasar database, ERD, dan normalisasi database.',
                'jenis' => 'materi',
                'poin_reward' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guru_id' => $guru2->id,
                'judul' => 'Quiz Database Fundamentals',
                'isi' => 'Quiz tentang konsep dasar database, relasi tabel, dan SQL dasar.',
                'jenis' => 'quiz',
                'deadline' => now()->addDays(3),
                'poin_reward' => 25,
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
                'jenis_aktivitas' => 'selesai_materi',
                'keterangan' => 'Menyelesaikan materi PHP Dasar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $siswa2->id,
                'modul_id' => 1, // PHP Dasar
                'jumlah_poin' => 10,
                'jenis_aktivitas' => 'selesai_materi',
                'keterangan' => 'Menyelesaikan materi PHP Dasar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $siswa2->id,
                'modul_id' => 3, // Database MySQL
                'jumlah_poin' => 15,
                'jenis_aktivitas' => 'selesai_materi',
                'keterangan' => 'Menyelesaikan materi Database MySQL',
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
                'isi_jawaban' => 'Saya telah membuat calculator dengan fungsi tambah, kurang, kali, bagi.',
                'status' => 'dikirim',
                'submitted_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modul_id' => 4, // Quiz Database
                'siswa_id' => $siswa2->id,
                'isi_jawaban' => 'Jawaban quiz database: 1.B, 2.A, 3.C, 4.D, 5.B',
                'nilai' => 85,
                'status' => 'dinilai',
                'komentar_guru' => 'Bagus! Pemahaman konsep database sudah baik.',
                'submitted_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('jawaban')->insert($jawabanData);
    }
}
