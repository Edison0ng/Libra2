<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perpanjangan peminjaman: maksimal 1 kali per peminjaman.
     *  - diperpanjang_at   : kapan diperpanjang (NULL = belum pernah). Ini
     *                        penanda "sekali saja".
     *  - perpanjangan_hari : berapa hari yang ditambahkan (1-3).
     */
    public function up(): void
    {
        if (! Schema::hasTable('pinjam')) return;

        Schema::table('pinjam', function (Blueprint $table) {
            if (! Schema::hasColumn('pinjam', 'diperpanjang_at')) {
                $table->timestamp('diperpanjang_at')->nullable();
            }
            if (! Schema::hasColumn('pinjam', 'perpanjangan_hari')) {
                $table->unsignedSmallInteger('perpanjangan_hari')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pinjam')) return;

        Schema::table('pinjam', function (Blueprint $table) {
            foreach (['diperpanjang_at', 'perpanjangan_hari'] as $col) {
                if (Schema::hasColumn('pinjam', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};