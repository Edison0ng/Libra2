<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Verifikasi email mulai diwajibkan untuk pendaftar BARU. Akun yang sudah
     * ada (mahasiswa lama, admin, kurir) ditandai terverifikasi supaya tidak
     * terkunci dari login.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Sengaja kosong: status verifikasi lama tidak bisa dipulihkan.
    }
};