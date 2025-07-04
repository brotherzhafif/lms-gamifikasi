<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Modul;
use App\Models\Progress;
use App\Models\Jawaban;
use App\Models\MataPelajaran;
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

        // Create main student
        $siswa = User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siswa@lms.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'nis' => '12345',
        ]);

        // Create more students for leaderboard
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'nama' => "Siswa $i",
                'email' => "siswa$i@lms.id",
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nis' => str_pad(12345 + $i, 5, '0', STR_PAD_LEFT),
            ]);
        }

        // Get mata pelajaran
        $matematika = MataPelajaran::where('kode_mapel', 'MTK')->first();
        $bahasaIndonesia = MataPelajaran::where('kode_mapel', 'BIND')->first();
        $bahasaInggris = MataPelajaran::where('kode_mapel', 'BING')->first();
        $ipa = MataPelajaran::where('kode_mapel', 'IPA')->first();
        $tik = MataPelajaran::where('kode_mapel', 'TIK')->first();

        // MATEMATIKA MODULES
        $matematikaModules = [
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Bilangan Bulat dan Operasinya',
                'isi' => 'Bilangan bulat adalah bilangan yang terdiri dari bilangan negatif, nol, dan bilangan positif. Dalam pembelajaran ini kita akan mempelajari operasi dasar bilangan bulat seperti penjumlahan, pengurangan, perkalian, dan pembagian.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Tugas: Operasi Hitung Bilangan Bulat',
                'isi' => 'Soal Latihan: 1. Hitunglah: 25 + (-17), 2. Hitunglah: (-8) × 6, 3. Hitunglah: 72 ÷ (-9). Kerjakan dengan langkah-langkah yang jelas dan benar.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(5),
                'poin_reward' => 20,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Pecahan dan Desimal',
                'isi' => 'Pecahan adalah bilangan yang menyatakan bagian dari keseluruhan. Pecahan dapat diubah ke bentuk desimal dan sebaliknya.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Tugas: Konversi Pecahan ke Desimal',
                'isi' => 'Tugas Matematika: 1. Ubahlah 3/4 ke bentuk desimal, 2. Ubahlah 0.75 ke bentuk pecahan. Tuliskan cara pengerjaan dengan lengkap.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(3),
                'poin_reward' => 15,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Geometri: Bangun Datar',
                'isi' => 'Bangun datar adalah bangun yang memiliki dua dimensi, yaitu panjang dan lebar. Contoh bangun datar antara lain persegi, persegi panjang, segitiga, dan lingkaran.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruMatematika->id,
                'mata_pelajaran_id' => $matematika->id,
                'judul' => 'Tugas: Menghitung Luas dan Keliling',
                'isi' => 'Soal Geometri: Hitunglah luas dan keliling dari bangun datar berikut: 1. Persegi dengan sisi 8 cm, 2. Lingkaran dengan jari-jari 7 cm. Gunakan rumus yang tepat.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(7),
                'poin_reward' => 25,
                'is_active' => true,
            ],
        ];

        // BAHASA INDONESIA MODULES
        $bahasaIndonesiaModules = [
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'judul' => 'Teks Deskripsi',
                'isi' => 'Teks deskripsi adalah teks yang menggambarkan atau melukiskan sesuatu secara detail dan jelas sehingga pembaca dapat membayangkan objek yang dideskripsikan.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
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
                'judul' => 'Puisi dan Unsur-unsurnya',
                'isi' => 'Puisi adalah karya sastra yang menggunakan kata-kata indah dan bermakna. Puisi memiliki unsur-unsur seperti tema, amanat, dan majas.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaIndonesia->id,
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'judul' => 'Tugas: Analisis Puisi',
                'isi' => 'Tugas Sastra: Analisislah puisi "Aku" karya Chairil Anwar dari segi tema, amanat, dan majas yang digunakan! Berikan penjelasan yang detail.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(4),
                'poin_reward' => 15,
                'is_active' => true,
            ],
        ];

        // IPA MODULES
        $ipaModules = [
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'judul' => 'Sistem Pernapasan Manusia',
                'isi' => 'Sistem pernapasan adalah sistem organ yang berfungsi untuk mengambil oksigen dan mengeluarkan karbon dioksida. Organ utama sistem pernapasan adalah paru-paru.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'judul' => 'Tugas: Organ Pernapasan',
                'isi' => 'Tugas IPA: Buatlah diagram organ pernapasan manusia lengkap dengan keterangannya! Jelaskan fungsi masing-masing organ.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(8),
                'poin_reward' => 25,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'judul' => 'Fotosintesis pada Tumbuhan',
                'isi' => 'Fotosintesis adalah proses pembuatan makanan pada tumbuhan dengan bantuan cahaya matahari. Proses ini terjadi di daun dengan bantuan klorofil.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruIPA->id,
                'mata_pelajaran_id' => $ipa->id,
                'judul' => 'Tugas: Proses Fotosintesis',
                'isi' => 'Tugas Biologi: 1. Sebutkan bahan-bahan fotosintesis! 2. Jelaskan proses fotosintesis secara singkat! Berikan contoh konkret.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(3),
                'poin_reward' => 18,
                'is_active' => true,
            ],
        ];

        // TIK MODULES
        $tikModules = [
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'judul' => 'Pengenalan HTML',
                'isi' => 'HTML (HyperText Markup Language) adalah bahasa markup untuk membuat halaman web. HTML menggunakan tag-tag untuk menentukan struktur konten.',
                'jenis' => 'materi',
                'poin_reward' => 15,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'judul' => 'Tugas: Membuat Halaman Web Sederhana',
                'isi' => 'Tugas HTML: Buatlah halaman web sederhana tentang profil diri menggunakan HTML dengan minimal 5 tag berbeda! Upload file HTML yang sudah dibuat.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(10),
                'poin_reward' => 30,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'judul' => 'CSS untuk Styling',
                'isi' => 'CSS (Cascading Style Sheets) digunakan untuk mengatur tampilan halaman web. CSS dapat mengubah warna, font, layout, dan elemen visual lainnya.',
                'jenis' => 'materi',
                'poin_reward' => 15,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruTIK->id,
                'mata_pelajaran_id' => $tik->id,
                'judul' => 'Tugas: HTML dan CSS',
                'isi' => 'Tugas Web Development: 1. Apa perbedaan HTML dan CSS? 2. Sebutkan 3 cara menambahkan CSS ke HTML! Berikan contoh kode untuk masing-masing cara.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(5),
                'poin_reward' => 20,
                'is_active' => true,
            ],
        ];

        // BAHASA INGGRIS MODULES
        $bahasaInggrisModules = [
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'judul' => 'Simple Present Tense',
                'isi' => 'Simple Present Tense is used to express habits, general truths, and daily activities. The formula is Subject + Verb (s/es for third person singular) + Object.',
                'jenis' => 'materi',
                'poin_reward' => 12,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'judul' => 'Task: Daily Activities Essay',
                'isi' => 'Writing Task: Write an essay about your daily activities using Simple Present Tense (minimum 150 words)! Include at least 10 different verbs.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(7),
                'poin_reward' => 25,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'judul' => 'Vocabulary: Family Members',
                'isi' => 'Learn vocabulary related to family members: father, mother, brother, sister, grandfather, grandmother, uncle, aunt, cousin, and nephew.',
                'jenis' => 'materi',
                'poin_reward' => 10,
                'is_active' => true,
            ],
            [
                'guru_id' => $guruBahasaInggris->id,
                'mata_pelajaran_id' => $bahasaInggris->id,
                'judul' => 'Task: Family Tree',
                'isi' => 'Vocabulary Task: Create your family tree and describe each member using English! Write at least 2 sentences for each family member.',
                'jenis' => 'tugas',
                'deadline' => now()->addDays(4),
                'poin_reward' => 18,
                'is_active' => true,
            ],
        ];

        // Create all modules
        $allModules = array_merge(
            $matematikaModules,
            $bahasaIndonesiaModules,
            $ipaModules,
            $tikModules,
            $bahasaInggrisModules
        );

        $createdModules = [];
        foreach ($allModules as $moduleData) {
            $createdModules[] = Modul::create($moduleData);
        }

        // Create sample progress and answers for the main student
        $mainStudentModules = collect($createdModules)->random(8);
        foreach ($mainStudentModules as $module) {
            // Create progress for some modules
            if (rand(1, 100) <= 70) { // 70% chance
                Progress::create([
                    'user_id' => $siswa->id,
                    'modul_id' => $module->id,
                    'jumlah_poin' => $module->poin_reward,
                    'jenis_aktivitas' => 'selesai_' . $module->jenis,
                    'keterangan' => "Menyelesaikan {$module->jenis}: {$module->judul}",
                ]);
            }

            // Create answers for tugas and quiz
            if (in_array($module->jenis, ['tugas']) && rand(1, 100) <= 60) { // 60% chance
                Jawaban::create([
                    'modul_id' => $module->id,
                    'siswa_id' => $siswa->id,
                    'isi_jawaban' => $this->generateSampleAnswer($module),
                    'status' => ['draft', 'dikirim', 'dinilai'][rand(0, 2)],
                    'nilai' => rand(0, 100) > 30 ? rand(60, 100) : null, // 70% get grades
                    'submitted_at' => rand(0, 100) > 30 ? now()->subDays(rand(1, 10)) : null,
                ]);
            }
        }

        // Add random progress for other students
        $allStudents = User::where('role', 'murid')->where('id', '!=', $siswa->id)->get();

        foreach ($allStudents as $student) {
            $randomModules = collect($createdModules)->random(rand(3, 12));
            foreach ($randomModules as $module) {
                if (rand(1, 100) <= 50) { // 50% chance for other students
                    Progress::create([
                        'user_id' => $student->id,
                        'modul_id' => $module->id,
                        'jumlah_poin' => rand(5, $module->poin_reward),
                        'jenis_aktivitas' => 'selesai_' . $module->jenis,
                        'keterangan' => "Menyelesaikan {$module->jenis}: {$module->judul}",
                    ]);
                }

                // Create some answers
                if (in_array($module->jenis, ['tugas']) && rand(1, 100) <= 40) { // 40% chance
                    Jawaban::create([
                        'modul_id' => $module->id,
                        'siswa_id' => $student->id,
                        'isi_jawaban' => $this->generateSampleAnswer($module),
                        'status' => ['draft', 'dikirim', 'dinilai'][rand(0, 2)],
                        'nilai' => rand(0, 100) > 40 ? rand(50, 100) : null,
                        'submitted_at' => rand(0, 100) > 40 ? now()->subDays(rand(1, 15)) : null,
                    ]);
                }
            }
        }
    }

    private function generateSampleAnswer($module)
    {
        $answers = [
            'tugas' => [
                'Saya telah menyelesaikan tugas ini dengan baik. Berikut adalah jawaban saya...',
                'Berdasarkan materi yang telah dipelajari, saya akan menjawab pertanyaan-pertanyaan berikut...',
                'Tugas ini sangat menarik dan menambah wawasan saya. Jawaban saya adalah...',
                'Setelah mempelajari materi dengan seksama, berikut adalah hasil pekerjaan saya...',
            ],
        ];

        return $answers['tugas'][array_rand($answers['tugas'])];
    }
}