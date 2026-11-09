<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('produk_atk', 'gambar')) {
            Schema::table('produk_atk', function (Blueprint $table) {
                // URL/path gambar produk (diupload oleh admin). Null = pakai ikon default.
                $table->text('gambar')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('produk_atk', 'gambar')) {
            Schema::table('produk_atk', function (Blueprint $table) {
                $table->dropColumn('gambar');
            });
        }
    }
};
