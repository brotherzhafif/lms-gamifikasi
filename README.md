# 🎓 LMS Gamifikasi

Sistem Learning Management System (LMS) berbasis gamifikasi, dirancang untuk memotivasi siswa menyelesaikan pembelajaran dengan sistem poin.

## 🚀 Fitur Utama

### 👥 Role Based Access

-   **Admin**: Kelola user, lihat statistik poin
-   **Guru**: Buat modul (materi, tugas), nilai jawaban
-   **Siswa**: Akses modul, kerjakan tugas, kumpulkan poin

### 📚 Modul

-   Jenis: `materi`, `tugas`
-   Tugas dapat diunggah dan dinilai
-   Materi bisa ditandai sebagai "Selesai" untuk dapat poin

### 📝 Jawaban

-   Tugas dikerjakan siswa dengan sistem status:
    -   `belum`, `draft`, `dikirim`, `terlambat`, `dinilai`

### 🎯 Progress & Poin

-   Siswa mendapat poin saat menyelesaikan:
    -   Membaca materi
    -   Mengirim tugas
-   Data disimpan di tabel `progress`

---

## 🧱 Struktur Tabel

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

| Kolom      | Tipe     | Keterangan        |
| ---------- | -------- | ----------------- |
| id         | bigint   | PK                |
| guru_id    | FK       | FK ke `users.id`  |
| judul      | string   |                   |
| isi        | text     |                   |
| jenis      | enum     | `materi`, `tugas` |
| file_path  | json     | Untuk upload file |
| deadline   | datetime | Nullable          |
| timestamps |          |                   |

### `jawaban`

| Kolom      | Tipe    | Keterangan                                          |
| ---------- | ------- | --------------------------------------------------- |
| id         | bigint  | PK                                                  |
| modul_id   | FK      | FK ke `modul.id`                                    |
| siswa_id   | FK      | FK ke `users.id`                                    |
| file_path  | json    | Upload tugas                                        |
| nilai      | integer | Nullable                                            |
| status     | enum    | `belum`, `draft`, `dikirim`, `terlambat`, `dinilai` |
| timestamps |         |                                                     |

### `progress`

| Kolom       | Tipe    | Keterangan          |
| ----------- | ------- | ------------------- |
| id          | bigint  | PK                  |
| user_id     | FK      | FK ke `users.id`    |
| modul_id    | FK      | FK ke `modul.id`    |
| jumlah_poin | integer | Poin yang diberikan |
| timestamps  |         |                     |

---

## 📈 Alur Penggunaan Siswa

1. Login → dashboard
2. Akses daftar modul
3. **Materi**:
    - Baca → klik **"Tandai selesai"**
    - Otomatis tambah ke `progress`
4. **Tugas**:
    - Upload file → status `draft`
    - Klik **"Kumpulkan"** → status `dikirim` / `terlambat`
    - Guru memberi nilai → status jadi `dinilai`
5. Total poin tampil di dashboard

---

## 🔮 Fitur Opsional Mendatang

| Fitur         | Deskripsi                          |
| ------------- | ---------------------------------- |
| Forum Diskusi | Komentar per materi                |
| Badge / Level | Level siswa dari akumulasi poin    |
| Reminder      | Notifikasi deadline tugas          |
| Ranking       | Leaderboard berdasarkan total poin |

---

## 🛠 Teknologi

-   **Laravel 11**
-   **Livewire**
-   **Filament Admin**
-   **Aiven DB** (PostgreSQL/MySQL)
-   **S3 Storage** (opsional, upload file)
-   **Deploy**: Railway, Vercel, atau Render

---

## 🧑‍💻 Pengembangan Selanjutnya

-   [ ] Buat migration dari struktur tabel
-   [ ] Setup relasi Eloquent di model
-   [ ] Buat komponen Livewire untuk siswa
-   [ ] Tambahkan Filament Resource untuk admin & guru
-   [ ] Tambahkan leaderboard & progress chart

---

## 📄 Lisensi

Proyek ini dapat dikembangkan ulang untuk keperluan pendidikan.
