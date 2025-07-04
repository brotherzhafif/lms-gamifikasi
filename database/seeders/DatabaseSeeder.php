<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Modul;
use App\Models\Progress;
use App\Models\Jawaban;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Guru
        $guru = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Create Siswa
        $siswa = User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siswa@example.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12345',
        ]);

        // Create more students for leaderboard
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'nama' => "Siswa $i",
                'email' => "siswa$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nis' => str_pad(12345 + $i, 5, '0', STR_PAD_LEFT),
            ]);
        }

        // Create sample modules
        $modul1 = Modul::create([
            'guru_id' => $guru->id,
            'judul' => 'PHP Dasar',
            'isi' => '<h2>Pengenalan PHP</h2><p>PHP adalah bahasa pemrograman yang sangat populer untuk pengembangan web...</p>',
            'jenis' => 'materi',
            'poin_reward' => 10,
            'is_active' => true,
        ]);

        $modul2 = Modul::create([
            'guru_id' => $guru->id,
            'judul' => 'Tugas: Membuat Kalkulator',
            'isi' => '<h2>Tugas Kalkulator</h2><p>Buatlah program kalkulator sederhana menggunakan PHP...</p>',
            'jenis' => 'tugas',
            'deadline' => now()->addDays(7),
            'poin_reward' => 20,
            'is_active' => true,
        ]);

        $modul3 = Modul::create([
            'guru_id' => $guru->id,
            'judul' => 'Latihan: Variable dan Tipe Data',
            'isi' => '<h2>Latihan Variable</h2><p>1. Apa itu variable dalam PHP?<br>2. Sebutkan tipe data dalam PHP...</p>',
            'jenis' => 'tugas',
            'deadline' => now()->addDays(3),
            'poin_reward' => 15,
            'is_active' => true,
        ]);

        // Create sample progress
        Progress::create([
            'user_id' => $siswa->id,
            'modul_id' => $modul1->id,
            'jumlah_poin' => 10,
            'jenis_aktivitas' => 'selesai_materi',
            'keterangan' => 'Menyelesaikan materi PHP Dasar',
        ]);

        // Create sample answers
        Jawaban::create([
            'modul_id' => $modul2->id,
            'siswa_id' => $siswa->id,
            'isi_jawaban' => 'Berikut adalah kode kalkulator PHP yang saya buat...',
            'status' => 'dikirim',
            'submitted_at' => now(),
        ]);

        // Add random progress for other students
        $allStudents = User::where('role', 'murid')->get();
        $allModules = Modul::all();

        foreach ($allStudents as $student) {
            // Random progress for each student
            $randomModules = $allModules->random(rand(1, 2));
            foreach ($randomModules as $module) {
                if (!Progress::where('user_id', $student->id)->where('modul_id', $module->id)->exists()) {
                    Progress::create([
                        'user_id' => $student->id,
                        'modul_id' => $module->id,
                        'jumlah_poin' => rand(5, 25),
                        'jenis_aktivitas' => 'selesai_' . $module->jenis,
                        'keterangan' => "Menyelesaikan {$module->jenis}: {$module->judul}",
                    ]);
                }
            }
        }
    }
}