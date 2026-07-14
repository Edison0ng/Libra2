<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan buku yang di-wishlist oleh user karena sedang dipinjam
     * orang lain. Dipakai untuk mengirim notifikasi "buku tersedia lagi"
     * ke setiap user yang mewishlist saat buku tsb dikembalikan.
     */
    public function up(): void
    {
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('user_id');
                $table->string('book_id');
                $table->timestamp('created_at')->nullable();

                // Satu user tidak boleh mewishlist buku yang sama dua kali
                $table->unique(['user_id', 'book_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
