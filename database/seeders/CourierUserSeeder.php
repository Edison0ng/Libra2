<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Membuat (atau memperbarui) akun Kurir.
 *
 * Kata sandi TIDAK ditulis di kode: isi COURIER_PASSWORD di file .env
 * (atau di Environment Variables Vercel), lalu jalankan:
 *
 *     php artisan db:seed --class=CourierUserSeeder
 *
 * Aman dijalankan berulang kali: akun dicari lewat NIM 'kurir', jadi tidak
 * akan dobel. Menjalankannya lagi juga berguna untuk mengganti kata sandi.
 */
class CourierUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('COURIER_PASSWORD');

        if (empty($password) || strlen($password) < 8) {
            $this->command?->error('COURIER_PASSWORD belum diisi di .env atau kurang dari 8 karakter. Akun kurir tidak dibuat.');
            return;
        }

        $user = User::where('nim', 'kurir')->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->command?->info('Akun kurir sudah ada: kata sandi diperbarui.');
            return;
        }

        User::create([
            'id'           => (string) Str::uuid(),
            'nama_lengkap' => env('COURIER_NAME', 'Kurir Libra'),
            'username'     => 'kurir',
            'nim'          => 'kurir',
            'email'        => env('COURIER_EMAIL', 'kurir@libra.local'),
            'fakultas'     => 'Logistik Perpustakaan',
            // Kolom ini NOT NULL di database, jadi tidak boleh null.
            'no_telepon'   => env('COURIER_PHONE', '-'),
            'alamat_kirim' => '-',
            'password'     => Hash::make($password),
        ]);

        $this->command?->info('Akun kurir dibuat. Login dengan NIM/Username: kurir');
    }
}