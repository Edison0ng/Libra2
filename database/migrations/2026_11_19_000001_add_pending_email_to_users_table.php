<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan email baru yang sedang menunggu verifikasi saat pengguna
 * mengganti email dari halaman Profil. Email lama tetap dipakai (dan akun
 * tetap terverifikasi) sampai tautan di email baru diklik.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'pending_email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pending_email')->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'pending_email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pending_email');
            });
        }
    }
};