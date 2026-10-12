<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_lengkap');
                $table->string('nim')->unique();
                $table->string('fakultas');
                $table->string('no_telepon');
                $table->text('alamat_kirim');
                $table->text('avatar_url')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('users');
}
};
