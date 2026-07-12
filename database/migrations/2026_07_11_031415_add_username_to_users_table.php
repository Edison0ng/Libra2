<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom username (unique)
            $table->string('username')->unique()->nullable()->after('name');
            
            // Tambahkan kolom lain yang mungkin diperlukan
            $table->string('nim')->nullable()->after('username');
            $table->string('fakultas')->nullable()->after('nim');
            $table->string('avatar_url')->nullable()->after('fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nim', 'fakultas', 'avatar_url']);
        });
    }
};