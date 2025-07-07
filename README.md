# 🎓 LMS Gamifikasi

Sistem Learning Management System (LMS) berbasis gamifikasi, dirancang untuk memotivasi siswa menyelesaikan pembelajaran dengan sistem poin.

## 🚀 Fitur Utama

### 👥 Role Based Access

-   **Admin**: Kelola user, lihat statistik poin, ranking siswa
-   **Guru**: Buat modul (materi, tugas), nilai jawaban, ranking siswa dari modul sendiri
-   **Siswa**: Akses modul, kerjakan tugas, kumpulkan poin, lihat ranking

### 🏫 Sistem Kelas

-   **Kelas**: Setiap siswa ditempatkan di kelas tertentu
-   **Modul**: Setiap modul diasign ke kelas tertentu
-   **Filtering**: Siswa hanya bisa melihat modul untuk kelasnya
-   **Ranking**: Siswa melihat ranking dalam kelasnya sendiri

### 📚 Modul

-   Jenis: `materi`, `tugas`
-   Tugas dapat diunggah dan dinilai
-   Materi bisa ditandai sebagai "Selesai" untuk dapat poin
-   Terintegrasi dengan mata pelajaran dan kelas

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

## 🚀 Setup Aplikasi

### 1. **Clone Repository**

```bash
git clone <repository-url>
cd lms-gamifikasi
```

### 2. **Install Dependencies**

```bash
composer install
npm install
```

### 3. **Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

### 4. **Database Setup (XAMPP)**

Buka file `.env` dan sesuaikan dengan XAMPP:

```env
APP_NAME="LMS Gamifikasi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_gamifikasi
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

### 5. **Buat Database**

-   Buka phpMyAdmin (http://localhost/phpmyadmin)
-   Buat database baru: `lms_gamifikasi`

### 6. **Migrate & Seed Database**

```bash
php artisan migrate:fresh --seed
```

### 7. **Setup Storage**

```bash
php artisan storage:link
```

### 8. **Compile Assets**

````bash
npm run dev
```w

### 9. **Jalankan Server**

```bash
php artisan serve
````

---

## 👥 Login Credentials

Berdasarkan `DatabaseSeeder.php`, berikut adalah email dan password untuk setiap role:

### 🛡️ **Admin**

-   **Email:** `admin@lms.id`
-   **Password:** `password`
-   **URL:** http://localhost:8000/admin

### 👨‍🏫 **Guru (Teachers)**

-   **Guru Matematika:**

    -   **Email:** `guru.matematika@lms.id` 
    -   **Password:** `password`
    -   **Nama:** Dr. Budi Santoso

-   **Guru Bahasa Indonesia:**

    -   **Email:** `guru.bahasa@lms.id`
    -   **Password:** `password`
    -   **Nama:** Sari Wulandari, S.Pd

-   **Guru IPA:**

    -   **Email:** `guru.ipa@lms.id`
    -   **Password:** `password`
    -   **Nama:** Dr. Ahmad Hidayat

-   **Guru TIK:**

    -   **Email:** `guru.tik@lms.id`
    -   **Password:** `password`
    -   **Nama:** Rina Kusuma, M.Kom

-   **Guru Bahasa Inggris:**

    -   **Email:** `guru.english@lms.id`
    -   **Password:** `password`
    -   **Nama:** John Smith, M.Ed

-   **URL Guru:** http://localhost:8000/guru

### 👨‍🎓 **Siswa (Students)**

-   **Siswa Utama:**

    -   **Email:** `siswa@lms.id`
    -   **Password:** `password`
    -   **Nama:** Siti Nurhaliza
    -   **NIS:** 12345
    -   **Kelas:** X IPA 1

-   **Siswa Lainnya:**

    -   **Email:** `siswa1@lms.id` sampai `siswa15@lms.id`
    -   **Password:** `password`
    -   **Nama:** Siswa 1 sampai Siswa 15
    -   **Kelas:** Tersebar di berbagai kelas

-   **URL Siswa:** http://localhost:8000/siswa

---

## 📝 Akses Panel

### **Login Terpusat**

-   **URL:** http://localhost:8000/login
-   Setelah login, akan diarahkan ke panel sesuai role

### **Panel Terpisah**

-   **Admin Panel:** http://localhost:8000/admin
-   **Guru Panel:** http://localhost:8000/guru
-   **Siswa Panel:** http://localhost:8000/siswa

---

## 🎯 Testing Flow

1. **Login sebagai Admin** → Lihat statistik, kelola user, ranking lengkap
2. **Login sebagai Guru** → Buat modul, nilai tugas, lihat ranking siswa
3. **Login sebagai Siswa** → Akses modul, kerjakan tugas, lihat ranking

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
| kelas_id   | FK        | FK ke `kelas.id` (siswa) |
| email      | string    | Unique                   |
| password   | string    | Hashed                   |
| role       | enum      | `admin`, `guru`, `murid` |
| timestamps | timestamp |                          |

### `kelas`

| Kolom      | Tipe      | Keterangan               |
| ---------- | --------- | ------------------------ |
| id         | bigint    | Primary Key              |
| nama_kelas | string    | Nama kelas (ex: X IPA 1) |
| kode_kelas | string    | Kode unik kelas (ex: X1) |
| deskripsi  | text      | Nullable                 |
| is_active  | boolean   | Default true             |
| timestamps | timestamp |                          |

### `modul`

| Kolom             | Tipe     | Keterangan                |
| ----------------- | -------- | ------------------------- |
| id                | bigint   | PK                        |
| guru_id           | FK       | FK ke `users.id`          |
| mata_pelajaran_id | FK       | FK ke `mata_pelajaran.id` |
| kelas_id          | FK       | FK ke `kelas.id`          |
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
| Sistem Kelas      | ✅     | Pengelompokkan siswa dan filtering modul    |
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
