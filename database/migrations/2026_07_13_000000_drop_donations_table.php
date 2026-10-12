<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fitur donasi buku dihapus dari LIBRA, sehingga tabel 'donations'
     * tidak lagi diperlukan.
     */
    public function up(): void
    {
        Schema::dropIfExists('donations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_id');
                $table->string('title');
                $table->string('author');
                $table->string('category');
                $table->string('condition');
                $table->text('note')->nullable();
                $table->string('status')->default('Menunggu Verifikasi');
                $table->date('tanggal_donasi');
                $table->timestamps();
            });
        }
    }
};
