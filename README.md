# 🎓 LMS Gamifikasi

Sistem Learning Management System (LMS) berbasis gamifikasi, dirancang untuk memotivasi siswa menyelesaikan pembelajaran dengan sistem poin.

## 🚀 Fitur Utama

### 👥 Role Based Access

-   **Admin**: Kelola user, lihat statistik poin, ranking siswa
-   **Guru**: Buat modul (materi, tugas), nilai jawaban, ranking siswa dari modul sendiri
-   **Siswa**: Akses modul, kerjakan tugas, kumpulkan poin, lihat ranking

### 📚 Modul

-   Jenis: `materi`, `tugas`
-   Tugas dapat diunggah dan dinilai
-   Materi bisa ditandai sebagai "Selesai" untuk dapat poin
-   Terintegrasi dengan mata pelajaran

### 📝 Jawaban

-   Tugas dikerjakan siswa dengan sistem status:
    -   `belum`, `draft`, `dikirim`, `terlambat`, `dinilai`

### 🎯 Progress & Poin

-   Siswa mendapat poin saat menyelesaikan:
    -   Membaca materi
    -   Mengirim tugas
-   Data disimpan di tabel `progress`

### 🏆 Ranking System

-   **Siswa**: Lihat ranking pribadi dan posisi di leaderboard
-   **Guru**: Lihat ranking siswa berdasarkan modul yang dibuat
-   **Admin**: Lihat ranking lengkap semua siswa dengan statistik

---

## 🧱 Struktur Tabel

### `mata_pelajaran`

| Kolom      | Tipe      | Keterangan               |
| ---------- | --------- | ------------------------ |
| id         | bigint    | Primary Key              |
| nama_mapel | string    | Nama mata pelajaran      |
| kode_mapel | string    | Kode unik mata pelajaran |
| deskripsi  | text      | Nullable                 |
| is_active  | boolean   | Default true             |
| timestamps | timestamp |                          |

### `users`

| Kolom      | Tipe      | Keterangan               |
| ---------- | --------- | ------------------------ |
| id         | bigint    | Primary Key              |
| nama       | string    |                          |
| nis        | string    | Nullable (siswa saja)    |
| email      | string    | Unique                   |
| password   | string    | Hashed                   |
| role       | enum      | `admin`, `guru`, `murid` |
| timestamps | timestamp |                          |

### `modul`

| Kolom             | Tipe     | Keterangan                |
| ----------------- | -------- | ------------------------- |
| id                | bigint   | PK                        |
| guru_id           | FK       | FK ke `users.id`          |
| mata_pelajaran_id | FK       | FK ke `mata_pelajaran.id` |
| judul             | string   |                           |
| isi               | text     |                           |
| jenis             | enum     | `materi`, `tugas`         |
| file_path         | json     | Untuk upload file         |
| deadline          | datetime | Nullable                  |
| poin_reward       | integer  | Default 10                |
| is_active         | boolean  | Default true              |
| timestamps        |          |                           |

### `jawaban`

| Kolom         | Tipe     | Keterangan                                          |
| ------------- | -------- | --------------------------------------------------- |
| id            | bigint   | PK                                                  |
| modul_id      | FK       | FK ke `modul.id`                                    |
| siswa_id      | FK       | FK ke `users.id`                                    |
| isi_jawaban   | text     | Jawaban siswa                                       |
| url_file      | json     | Upload file jawaban                                 |
| nilai         | integer  | Nullable                                            |
| status        | enum     | `belum`, `draft`, `dikirim`, `terlambat`, `dinilai` |
| submitted_at  | datetime | Nullable                                            |
| komentar_guru | text     | Nullable                                            |
| timestamps    |          |                                                     |

### `progress`

| Kolom           | Tipe    | Keterangan          |
| --------------- | ------- | ------------------- |
| id              | bigint  | PK                  |
| user_id         | FK      | FK ke `users.id`    |
| modul_id        | FK      | FK ke `modul.id`    |
| jumlah_poin     | integer | Poin yang diberikan |
| jenis_aktivitas | string  | Jenis aktivitas     |
| keterangan      | text    | Keterangan progress |
| timestamps      |         |                     |

---

## 📈 Alur Penggunaan Siswa

1. Login → dashboard
2. Akses daftar modul berdasarkan mata pelajaran
3. **Materi**:
    - Baca → klik **"Tandai selesai"**
    - Otomatis tambah ke `progress`
4. **Tugas**:
    - Upload file → status `draft`
    - Klik **"Kumpulkan"** → status `dikirim` / `terlambat`
    - Guru memberi nilai → status jadi `dinilai`
5. Total poin tampil di dashboard
6. Lihat ranking di leaderboard

---

## 🔮 Fitur yang Sudah Implementasi

| Fitur             | Status | Deskripsi                                   |
| ----------------- | ------ | ------------------------------------------- |
| Mata Pelajaran    | ✅     | Sistem kategorisasi modul berdasarkan mapel |
| Ranking Siswa     | ✅     | Leaderboard untuk siswa, guru, admin        |
| Progress Tracking | ✅     | Tracking poin dan kemajuan siswa            |
| File Upload       | ✅     | Upload file untuk tugas dan materi          |
| Multi-role Access | ✅     | Admin, Guru, Siswa dengan akses berbeda     |

---

## 🛠 Teknologi

-   **Laravel 11**
-   **Livewire**
-   **Filament Admin**
-   **PostgreSQL/MySQL**
-   **File Storage** (local/S3)

---

## 📄 Lisensi

Proyek ini dapat dikembangkan ulang untuk keperluan pendidikan.
