<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                // Tambahkan kolom username (unique) setelah nama_lengkap
                $table->string('username')->unique()->nullable()->after('nama_lengkap');
            }
            
            // Tambahkan kolom lain hanya jika belum ada di database
            if (!Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'fakultas')) {
                $table->string('fakultas')->nullable()->after('nim');
            }
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('fakultas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};