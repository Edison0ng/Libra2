# Folder Backup Migrasi

File-file di folder ini **BUKAN** migrasi aktif — Laravel secara default hanya
membaca migrasi dari `database/migrations/`, jadi apa pun yang ada di sini
**tidak akan ikut berjalan** saat `php artisan migrate`.

## 2026_07_03_172638_create_books_table.php

Ini migrasi tabel `books` versi SKEMA LAMA (primary key `id` auto-increment,
kolom `title`, `author`, `genre`, `year`, `status`), dari rancangan awal
proyek sebelum beralih ke skema berbasis ISBN (dataset buku asli/CSV import).

Migrasi tabel `books` yang **sebenarnya dipakai sekarang** ada di:
`database/migrations/2026_07_03_103541_create_books_table.php`
(primary key `ISBN`, kolom `Book-Title`, `Book-Author`, dst — sesuai CSV).

File ini dulu masih nyangkut di folder `migrations/` aktif padahal sudah
tidak relevan, dan BERBAHAYA kalau sampai ikut jalan: dia mencoba
`Schema::create('books', ...)` lagi TANPA pengecekan `hasTable()`, jadi kalau
database masih kosong dan migrasi dijalankan urut dari awal, migrasi ini akan
GAGAL dengan error "table already exists" (karena migrasi ISBN sudah lebih
dulu membuat tabel `books`).

File ini disimpan di sini murni sebagai arsip/cadangan sejarah proyek, sesuai
permintaan -- bukan untuk dijalankan lagi. Kalau suatu saat memang perlu
dipakai lagi, pindahkan manual kembali ke `database/migrations/` dan
sesuaikan dulu (tambahkan `if (!Schema::hasTable(...))`, dan pastikan tidak
bentrok dengan skema ISBN yang sekarang jadi sumber kebenaran aplikasi).