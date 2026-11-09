<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan pengantaran (buku & ATK) untuk portal Kurir.
     * Disimpan di database supaya pengguna dan kurir bisa memakai perangkat
     * yang berbeda (sebelumnya hanya localStorage di browser yang sama).
     *
     * Kolom waktu_* sengaja bertipe string (ISO 8601 dengan zona waktu) supaya
     * browser tidak salah membaca zona waktu -- sama seperti kasus created_at
     * di PinjamController.
     */
    public function up(): void
    {
        if (Schema::hasTable('courier_orders')) {
            return;
        }

        Schema::create('courier_orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_id')->index();
            $table->string('user_id')->nullable()->index();
            $table->string('user_name');
            $table->string('user_identity')->nullable();
            $table->string('user_phone')->nullable();
            $table->text('alamat');
            $table->text('items');                      // JSON array item pesanan
            $table->integer('total_items')->default(0);
            $table->bigInteger('total_pembayaran')->default(0);
            $table->string('status')->default('Menunggu')->index(); // Menunggu | Sedang Diantar | Selesai
            $table->string('waktu_pemesanan')->nullable();
            $table->string('waktu_mulai_pengantaran')->nullable();
            $table->string('waktu_selesai_pengantaran')->nullable();
            $table->integer('durasi_pengantaran_detik')->nullable();
            $table->string('total_durasi')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_orders');
    }
};