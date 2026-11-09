<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Katalog produk Toko ATK yang SEBENARNYA (database), menggantikan data
     * dummy yang sebelumnya cuma hidup di localStorage browser (baik sisi
     * mahasiswa maupun admin punya salinan sendiri-sendiri yang tidak
     * saling terhubung).
     */
    public function up(): void
    {
        if (! Schema::hasTable('produk_atk')) {
            Schema::create('produk_atk', function (Blueprint $table) {
                $table->string('id')->primary(); // UUID
                $table->string('kode')->unique(); // mis. ATK-001
                $table->string('nama');
                $table->string('kategori')->nullable();
                $table->unsignedInteger('harga')->default(0);
                $table->unsignedInteger('stok')->default(0);
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_atk');
    }
};