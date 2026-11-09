<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menandai kapan giliran "hold" eksklusif seseorang di antrian dimulai.
     * NULL = belum/tidak sedang mendapat giliran.
     * Diisi saat orang itu jadi paling depan antrian & bukunya tersedia;
     * kalau lewat 24 jam tanpa booking, baris wishlist-nya dihapus (dicoret)
     * dan giliran dilempar ke orang berikutnya.
     */
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (! Schema::hasColumn('wishlists', 'hold_started_at')) {
                $table->timestamp('hold_started_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (Schema::hasColumn('wishlists', 'hold_started_at')) {
                $table->dropColumn('hold_started_at');
            }
        });
    }
};