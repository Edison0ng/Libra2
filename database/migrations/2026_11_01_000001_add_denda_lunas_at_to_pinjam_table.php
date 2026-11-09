<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Waktu denda dilunasi (simulasi pembayaran). NULL = belum dibayar.
     */
    public function up(): void
    {
        if (Schema::hasTable('pinjam') && !Schema::hasColumn('pinjam', 'denda_lunas_at')) {
            Schema::table('pinjam', function (Blueprint $table) {
                $table->timestamp('denda_lunas_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pinjam') && Schema::hasColumn('pinjam', 'denda_lunas_at')) {
            Schema::table('pinjam', function (Blueprint $table) {
                $table->dropColumn('denda_lunas_at');
            });
        }
    }
};