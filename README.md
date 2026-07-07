# LIBRA — Integrasi Laravel + Supabase (Login & Register)

Paket ini berisi kode backend Laravel yang menghubungkan form Login &
Register LIBRA ke database Supabase secara real-time (tanpa data akun yang
di-hardcode), plus modifikasi frontend agar form benar-benar memanggil API.

## Isi Paket
```
app/Services/SupabaseService.php      -> wrapper HTTP ke Supabase REST API
app/Http/Controllers/AuthController.php -> logic register() & login()
routes/api.php                        -> route POST /api/register, /api/login
config/services.php                   -> tambahan konfigurasi kredensial Supabase
.env.example                          -> variabel env yang perlu ditambahkan
database/sql/create_users_table.sql   -> skrip SQL tabel users di Supabase
frontend/login.html                   -> versi login.html yang sudah terhubung API
frontend/register.html                -> versi register.html yang sudah terhubung API
```

## Langkah 1 — Buat Tabel di Supabase
1. Buka project Anda di https://supabase.com/dashboard
2. Masuk ke **SQL Editor -> New Query**
3. Jalankan isi file `database/sql/create_users_table.sql`

Struktur tabel `users` yang dihasilkan:

| Kolom      | Tipe        | Keterangan                          |
|------------|-------------|--------------------------------------|
| id         | uuid (PK)   | auto-generate                        |
| fullname   | text        | nama lengkap                         |
| username   | text unique | dipakai untuk login                  |
| email      | text unique | email kampus, juga bisa untuk login  |
| password   | text        | **hash bcrypt**, bukan plaintext     |
| created_at | timestamptz | otomatis terisi saat insert          |

RLS (Row Level Security) diaktifkan tanpa policy untuk `anon`/`authenticated`,
sehingga tabel ini **hanya bisa diakses lewat backend Laravel** yang memakai
Service Role Key. Browser/frontend tidak pernah menyentuh Supabase secara
langsung — semua lewat API Laravel.

## Langkah 2 — Ambil Kredensial Supabase
1. Di dashboard Supabase: **Project Settings -> API**
2. Salin **Project URL** -> ini nilai `SUPABASE_URL`
3. Salin **service_role key** (bukan `anon public`) -> ini nilai
   `SUPABASE_SERVICE_KEY`

⚠️ **service_role key setara admin penuh ke database.** Jangan pernah
menaruhnya di kode frontend/JavaScript, hanya di `.env` backend.

## Langkah 3 — Konfigurasi Project Laravel
1. Salin isi `.env.example` ke file `.env` project Laravel Anda (tambahkan
   dua baris `SUPABASE_URL` dan `SUPABASE_SERVICE_KEY`, isi dengan nilai
   dari Langkah 2).
2. Salin `config/services.php` -> gabungkan blok `'supabase' => [...]`
   ke dalam array yang di-return oleh `config/services.php` milik Anda
   (jangan menimpa entri lain seperti mailgun/aws yang mungkin sudah ada).
3. Salin `app/Services/SupabaseService.php` ke folder `app/Services/`
   project Anda (buat foldernya jika belum ada).
4. Salin `app/Http/Controllers/AuthController.php` ke
   `app/Http/Controllers/`.
5. Buka `routes/api.php` project Anda, tambahkan dua baris route dari file
   `routes/api.php` pada paket ini (import `AuthController` di bagian atas
   file juga).
6. Pastikan `config/cors.php` mengizinkan origin tempat file HTML Anda
   dibuka (misalnya `http://127.0.0.1:5500` jika pakai Live Server), supaya
   fetch() dari browser tidak diblokir CORS. Contoh cepat di
   `config/cors.php`:
   ```php
   'paths' => ['api/*'],
   'allowed_methods' => ['*'],
   'allowed_origins' => ['*'], // batasi ke domain spesifik saat production
   'allowed_headers' => ['*'],
   ```
7. Jalankan server Laravel:
   ```bash
   php artisan serve
   ```
   Secara default akan berjalan di `http://localhost:8000`.

## Langkah 4 — Sesuaikan Frontend
File `frontend/login.html` dan `frontend/register.html` pada paket ini
sudah memuat konstanta:
```js
const API_BASE_URL = 'http://localhost:8000/api';
```
Ubah nilai ini jika Laravel Anda berjalan di alamat/port lain, atau jika
sudah di-deploy ke domain production (mis. `https://api.libra-kampus.id/api`).

Gunakan kedua file ini untuk menggantikan `login.html` dan `register.html`
yang lama, karena keduanya sudah:
- Memanggil `POST /api/register` dan `POST /api/login` menggunakan `fetch()`
- Menampilkan pesan error/success **dinamis dari server** (bukan teks statis)
  di elemen `#errBanner` / `#successBanner` yang sudah ada
- Menampilkan `alert()` berisi pesan sukses yang menyertakan nama pengguna
  (`data.message`, contoh: "Selamat datang kembali, Rahmad!") saat login
  berhasil
- Menyimpan data user (fullname, username, email — tanpa password) ke
  `localStorage`/`sessionStorage` setelah login berhasil, untuk dipakai di
  halaman lain (mis. menyapa nama user di dashboard)

## Alur Kerja Ringkas

**Register:**
`register.html` -> `POST /api/register` -> Laravel validasi input & cek
duplikat username/email ke Supabase secara real-time -> hash password
dengan bcrypt -> insert row baru ke Supabase -> Laravel balas JSON sukses
-> frontend redirect ke `login.html`.

**Login:**
`login.html` -> `POST /api/login` -> Laravel query Supabase berdasarkan
username **atau** email -> `Hash::check()` password terhadap hash yang
tersimpan -> jika cocok, balas JSON `success: true` beserta `message` dan
`user` -> frontend `alert()` pesan tersebut lalu redirect ke `index.html`.

Tidak ada pembedaan role admin/user di alur ini (sesuai permintaan) —
semua akun diperlakukan sama sebagai "user" generik.

## Catatan Keamanan (Penting)
- Password **tidak pernah** disimpan atau dibandingkan sebagai plaintext;
  selalu lewat `Hash::make()` / `Hash::check()` bawaan Laravel (bcrypt).
- Tidak ada satupun data akun yang ditulis manual di kode — semua
  pengecekan login/register query langsung ke Supabase saat request masuk.
- Pertimbangkan menambahkan rate limiting (`throttle` middleware Laravel)
  pada route `/api/login` untuk mencegah brute-force di tahap produksi.
