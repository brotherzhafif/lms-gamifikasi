<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasList = [
            // Kelas X
            [
                'nama_kelas' => 'X-1',
                'kode_kelas' => 'X1',
                'deskripsi' => 'Kelas X IPA 1',
                'tingkat' => 'X',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'X-2',
                'kode_kelas' => 'X2',
                'deskripsi' => 'Kelas X IPA 2',
                'tingkat' => 'X',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'X-3',
                'kode_kelas' => 'X3',
                'deskripsi' => 'Kelas X IPS 1',
                'tingkat' => 'X',
                'jurusan' => 'IPS',
                'kapasitas' => 30,
                'is_active' => true,
            ],

            // Kelas XI
            [
                'nama_kelas' => 'XI-1',
                'kode_kelas' => 'XI1',
                'deskripsi' => 'Kelas XI IPA 1',
                'tingkat' => 'XI',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XI-2',
                'kode_kelas' => 'XI2',
                'deskripsi' => 'Kelas XI IPA 2',
                'tingkat' => 'XI',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XI-3',
                'kode_kelas' => 'XI3',
                'deskripsi' => 'Kelas XI IPS 1',
                'tingkat' => 'XI',
                'jurusan' => 'IPS',
                'kapasitas' => 30,
                'is_active' => true,
            ],

            // Kelas XII
            [
                'nama_kelas' => 'XII-1',
                'kode_kelas' => 'XII1',
                'deskripsi' => 'Kelas XII IPA 1',
                'tingkat' => 'XII',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XII-2',
                'kode_kelas' => 'XII2',
                'deskripsi' => 'Kelas XII IPA 2',
                'tingkat' => 'XII',
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'XII-3',
                'kode_kelas' => 'XII3',
                'deskripsi' => 'Kelas XII IPS 1',
                'tingkat' => 'XII',
                'jurusan' => 'IPS',
                'kapasitas' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($kelasList as $kelas) {
            Kelas::create($kelas);
        }
    }
}
