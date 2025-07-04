<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = [
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK', 'deskripsi' => 'Mata pelajaran Matematika'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIND', 'deskripsi' => 'Mata pelajaran Bahasa Indonesia'],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BING', 'deskripsi' => 'Mata pelajaran Bahasa Inggris'],
            ['nama_mapel' => 'IPA (Ilmu Pengetahuan Alam)', 'kode_mapel' => 'IPA', 'deskripsi' => 'Mata pelajaran Ilmu Pengetahuan Alam'],
            ['nama_mapel' => 'IPS (Ilmu Pengetahuan Sosial)', 'kode_mapel' => 'IPS', 'deskripsi' => 'Mata pelajaran Ilmu Pengetahuan Sosial'],
            ['nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan', 'kode_mapel' => 'PPKn', 'deskripsi' => 'Mata pelajaran PPKn'],
            ['nama_mapel' => 'Pendidikan Agama Islam', 'kode_mapel' => 'PAI', 'deskripsi' => 'Mata pelajaran Pendidikan Agama Islam'],
            ['nama_mapel' => 'Pendidikan Jasmani dan Kesehatan', 'kode_mapel' => 'PJOK', 'deskripsi' => 'Mata pelajaran Pendidikan Jasmani'],
            ['nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SBUD', 'deskripsi' => 'Mata pelajaran Seni Budaya'],
            ['nama_mapel' => 'Prakarya', 'kode_mapel' => 'PKR', 'deskripsi' => 'Mata pelajaran Prakarya'],
            ['nama_mapel' => 'Teknologi Informasi dan Komunikasi', 'kode_mapel' => 'TIK', 'deskripsi' => 'Mata pelajaran TIK'],
            ['nama_mapel' => 'Mulok (Muatan Lokal)', 'kode_mapel' => 'MULOK', 'deskripsi' => 'Mata pelajaran Muatan Lokal'],
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::create($mapel);
        }
    }
}
