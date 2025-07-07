<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Modul;
use App\Models\Progress;
use App\Models\Jawaban;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed mata pelajaran first
        $this->call(MataPelajaranSeeder::class);

        // Seed kelas
        $this->call(KelasSeeder::class);

        // Create Admin
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@lms.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create multiple teachers for different subjects
        $guruMatematika = User::create([
            'nama' => 'Dr. Budi Santoso',
            'email' => 'guru.matematika@lms.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $guruBahasaIndonesia = User::create([
            'nama' => 'Sari Wulandari, S.Pd',
            'email' => 'guru.bahasa@lms.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $guruIPA = User::create([
            'nama' => 'Dr. Ahmad Hidayat',
            'email' => 'guru.ipa@lms.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $guruTIK = User::create([
            'nama' => 'Rina Kusuma, M.Kom',
            'email' => 'guru.tik@lms.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $guruBahasaInggris = User::create([
            'nama' => 'John Smith, M.Ed',
            'email' => 'guru.english@lms.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Get classes
        $kelas1 = Kelas::where('kode_kelas', 'X1')->first(); // Kelas X IPA 1
        $kelas2 = Kelas::where('kode_kelas', 'XI1')->first(); // Kelas XI IPA 1
        $kelas3 = Kelas::where('kode_kelas', 'XII1')->first(); // Kelas XII IPA 1

        // Create main student
        $siswa = User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siswa@lms.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12345',
            'kelas_id' => $kelas1->id, // Assign to X IPA 1
        ]);

        // Create more students for leaderboard distributed across classes
        $kelasList = Kelas::all();
        for ($i = 1; $i <= 15; $i++) {
            $randomKelas = $kelasList->random();
            User::create([
                'nama' => "Siswa $i",
                'email' => "siswa$i@lms.id",
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nis' => str_pad(12345 + $i, 5, '0', STR_PAD_LEFT),
                'kelas_id' => $randomKelas->id,
            ]);
        }

        // Get mata pelajaran
        $matematika = MataPelajaran::where('kode_mapel', 'MTK')->first();
        $bahasaIndonesia = MataPelajaran::where('kode_mapel', 'BIND')->first();
        $bahasaInggris = MataPelajaran::where('kode_mapel', 'BING')->first();
        $ipa = MataPelajaran::where('kode_mapel', 'IPA')->first();
        $tik = MataPelajaran::where('kode_mapel', 'TIK')->first();

        // Create modules with class assignments
        $modulList = [
            // MATEMATIKA MODULES - X IPA 1
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Bilangan Bulat dan Operasinya',
                'isi' => 'Bilangan bulat adalah bilangan yang terdiri dari bilangan negatif, nol, dan bilangan positif. Dalam pembelajaran ini kita akan mempelajari operasi dasar bilangan bulat seperti penjumlahan, pengurangan, perkalian, dan pembagian.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Tugas: Operasi Hitung Bilangan Bulat',
                'isi' => 'Soal Latihan: 1. Hitunglah: 25 + (-17), 2. Hitunglah: (-8) × 6, 3. Hitunglah: 72 ÷ (-9). Kerjakan dengan langkah-langkah yang jelas dan benar.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(5),
                'poin_reward' => 20,
                'is_active' => true,
            ],
            // MATEMATIKA MODULES - XI IPA 1
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Pecahan dan Desimal',
                'isi' => 'Pecahan adalah bilangan yang menyatakan bagian dari keseluruhan. Pecahan dapat diubah ke bentuk desimal dan sebaliknya.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Tugas: Konversi Pecahan ke Desimal',
                'isi' => 'Tugas Matematika: 1. Ubahlah 3/4 ke bentuk desimal, 2. Ubahlah 0.75 ke bentuk pecahan. Tuliskan cara pengerjaan dengan lengkap.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(3),
                'poin_reward' => 15,
                'is_active' => true,
            ],
            // MATEMATIKA MODULES - XII IPA 1
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas3->id,
                'judul' => 'Geometri: Bangun Datar',
                'isi' => 'Bangun datar adalah bangun yang memiliki dua dimensi, yaitu panjang dan lebar. Contoh bangun datar antara lain persegi, persegi panjang, segitiga, dan lingkaran.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelas3->id,
                'judul' => 'Tugas: Menghitung Luas dan Keliling',
                'isi' => 'Soal Geometri: Hitunglah luas dan keliling dari bangun datar berikut: 1. Persegi dengan sisi 8 cm, 2. Lingkaran dengan jari-jari 7 cm. Gunakan rumus yang tepat.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(7),
                'poin_reward' => 25,
                'is_active' => true,
            ],

            // BAHASA INDONESIA MODULES
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Teks Deskripsi',
                'isi' => 'Teks deskripsi adalah teks yang menggambarkan atau melukiskan sesuatu secara detail dan jelas sehingga pembaca dapat membayangkan objek yang dideskripsikan.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Tugas: Menulis Teks Deskripsi',
                'isi' => 'Tugas Menulis: Buatlah teks deskripsi tentang tempat wisata favorit kalian dengan minimal 200 kata! Perhatikan struktur dan ciri-ciri teks deskripsi.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(6),
                'poin_reward' => 20,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Puisi dan Unsur-unsurnya',
                'isi' => 'Puisi adalah karya sastra yang menggunakan kata-kata indah dan bermakna. Puisi memiliki unsur-unsur seperti tema, amanat, dan majas.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],

            // IPA MODULES
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Sistem Tata Surya',
                'isi' => 'Tata surya adalah sistem gravitasi yang terdiri dari Matahari dan semua objek astronomi yang terikat oleh gravitasinya, termasuk planet, satelit, asteroid, dan komet.',
                'jenis' => 'materi',
                'poin_reward' => 15,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Fotosintesis pada Tumbuhan',
                'isi' => 'Fotosintesis adalah proses pembuatan makanan oleh tumbuhan hijau dengan bantuan sinar matahari, air, dan karbon dioksida.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],

            // TIK MODULES
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Pengenalan Microsoft Office',
                'isi' => 'Microsoft Office adalah paket aplikasi perkantoran yang dikembangkan oleh Microsoft. Terdiri dari Word, Excel, PowerPoint, dan aplikasi lainnya.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Dasar-dasar Programming',
                'isi' => 'Programming atau pemrograman adalah proses menulis, menguji, dan memelihara kode yang membangun suatu program komputer.',
                'jenis' => 'materi',
                'poin_reward' => 15,
                'is_active' => true,
            ],

            // BAHASA INGGRIS MODULES
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'kelas_id' => $kelas1->id,
                'judul' => 'Basic Grammar: Present Tense',
                'isi' => 'Present tense is used to describe habits, unchanging situations, general truths, and fixed arrangements.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'kelas_id' => $kelas2->id,
                'judul' => 'Reading Comprehension',
                'isi' => 'Reading comprehension is the ability to process text, understand its meaning, and to integrate with what the reader already knows.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
        ];

        // Create all modules
        foreach ($modulList as $modulData) {
            Modul::create($modulData);
        }

        // Create sample answers and progress for the main student (only for their class modules)
        $userKelasModuls = Modul::where('kelas_id', $siswa->kelas_id)->get();

        foreach ($userKelasModuls->take(3) as $modul) {
            if ($modul->jenis === 'tugas') {
                // Create jawaban for tugas
                Jawaban::create([
                    'modul_id' => $modul->id,
                    'siswa_id' => $siswa->id,
                    'isi_jawaban' => 'Ini adalah jawaban saya untuk tugas ' . $modul->judul,
                    'status' => 'dikirim',
                    'nilai' => rand(80, 100),
                    'submitted_at' => now()->subDays(rand(1, 5)),
                ]);

                // Add progress for completed tugas
                Progress::create([
                    'user_id' => $siswa->id,
                    'modul_id' => $modul->id,
                    'jumlah_poin' => $modul->poin_reward,
                    'jenis_aktivitas' => 'tugas_selesai',
                    'keterangan' => 'Menyelesaikan tugas: ' . $modul->judul,
                ]);
            } else {
                // Add progress for reading materi
                Progress::create([
                    'user_id' => $siswa->id,
                    'modul_id' => $modul->id,
                    'jumlah_poin' => $modul->poin_reward,
                    'jenis_aktivitas' => 'baca_materi',
                    'keterangan' => 'Membaca materi: ' . $modul->judul,
                ]);
            }
        }

        // Create sample progress for other students (for their respective class modules)
        $otherStudents = User::where('role', 'murid')->where('id', '!=', $siswa->id)->take(10)->get();

        foreach ($otherStudents as $student) {
            $studentKelasModuls = Modul::where('kelas_id', $student->kelas_id)->take(rand(1, 3))->get();

            foreach ($studentKelasModuls as $modul) {
                Progress::create([
                    'user_id' => $student->id,
                    'modul_id' => $modul->id,
                    'jumlah_poin' => $modul->poin_reward,
                    'jenis_aktivitas' => $modul->jenis === 'materi' ? 'baca_materi' : 'tugas_selesai',
                    'keterangan' => $modul->jenis === 'materi'
                        ? 'Membaca materi: ' . $modul->judul
                        : 'Menyelesaikan tugas: ' . $modul->judul,
                ]);
            }
        }
    }
}
