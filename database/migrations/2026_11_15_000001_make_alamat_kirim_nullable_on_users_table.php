<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kolom users.alamat_kirim dibuat NOT NULL di migrasi awal, padahal alamat
 * bersifat opsional (form register boleh kosong, validasi di AuthController
 * & UserController 'nullable'). Akibatnya pendaftaran tanpa alamat gagal
 * dengan SQLSTATE[23502] not-null violation.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN alamat_kirim DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET alamat_kirim = '' WHERE alamat_kirim IS NULL");
        DB::statement('ALTER TABLE users ALTER COLUMN alamat_kirim SET NOT NULL');
    }
};