<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PENTING: tabel `users` SUDAH ADA (dibuat oleh tim/bagian admin),
     * berisi kolom: id (uuid), nama_lengkap, nim, fakultas, no_telepon,
     * alamat_kirim, avatar_url, created_at.
     *
     * Migration ini HANYA menambah kolom `password` yang belum ada,
     * TIDAK membuat tabel baru dan TIDAK menghapus data yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->after('avatar_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
