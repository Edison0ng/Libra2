<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom `email` ke tabel `users` yang sudah ada.
     * Dibutuhkan agar fitur "Lupa Password" bisa mengirim link reset
     * ke email pengguna (standar Laravel Password Broker).
     *
     * Nullable & unique: opsional saat register lama, tapi wajib unik
     * kalau diisi supaya tidak ada 2 akun berbagi email yang sama.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique()->after('username');
            }
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            // Dibutuhkan saat proses reset password (token "ingat saya" dirotasi
            // otomatis oleh Illuminate\Auth setelah password berhasil diganti).
            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};