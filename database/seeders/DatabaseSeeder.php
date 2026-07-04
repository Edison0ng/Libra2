<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

public function run()
{
    // 1. Impor Books
    $this->importBooks();

    // 2. Impor Users
    $this->importUsers();

    // 3. Impor Pinjam
    $this->importLoans();
}

private function importBooks() {
    $handle = fopen(base_path('database/data/Books_Clean_Final.csv'), 'r');
    fgetcsv($handle); // Skip header
    while (($data = fgetcsv($handle)) !== FALSE) {
        DB::table('books')->insert([
            'ISBN' => $data[0],
            'title' => $data[1],
            'author' => $data[2],
            'year' => $data[3],
            'publisher' => $data[4],
        ]);
    }
    fclose($handle);
}

private function importUsers() {
    $handle = fopen(base_path('database/data/users.csv'), 'r');
    fgetcsv($handle); 
    while (($data = fgetcsv($handle)) !== FALSE) {
        DB::table('users')->insert([
            'id' => $data[0],
            'nama_lengkap' => $data[1],
            'nim' => $data[2],
            'fakultas' => $data[3],
            'no_telepon' => $data[4],
            'alamat_kirim' => $data[5],
            'avatar_url' => $data[6],
            'created_at' => $data[7],
        ]);
    }
    fclose($handle);
}

private function importLoans() {
    $handle = fopen(base_path('database/data/pinjam.csv'), 'r');
    fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== FALSE) {
        DB::table('loans')->insert([
            'id' => $data[0],
            'user_id' => $data[1],
            'book_id' => $data[2],
            'status' => $data[3],
            'tanggal_pinjam' => $data[4],
            'tenggat_waktu' => $data[5],
            'tanggal_kembali' => $data[6] ?: null, // Menangani nilai kosong
            'denda' => $data[7],
        ]);
    }
    fclose($handle);
}
}
