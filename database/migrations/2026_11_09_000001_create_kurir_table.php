<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat table / view 'kurir' di Supabase yang terhubung
     * langsung dengan sistem pesanan pengantaran 'courier_orders'.
     */
    public function up(): void
    {
        // Jika belum ada tabel courier_orders, buat terlebih dahulu
        if (!Schema::hasTable('courier_orders')) {
            Schema::create('courier_orders', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('order_id')->index();
                $table->string('user_id')->nullable()->index();
                $table->string('user_name');
                $table->string('user_identity')->nullable();
                $table->string('user_phone')->nullable();
                $table->text('alamat');
                $table->text('items');
                $table->integer('total_items')->default(1);
                $table->bigInteger('total_pembayaran')->default(0);
                $table->string('status')->default('Menunggu');
                $table->string('waktu_pemesanan')->nullable();
                $table->string('waktu_mulai_pengantaran')->nullable();
                $table->string('waktu_selesai_pengantaran')->nullable();
                $table->integer('durasi_pengantaran_detik')->nullable();
                $table->string('total_durasi')->nullable();
                $table->string('status_bayar')->nullable();
                $table->string('metode_bayar')->nullable();
                $table->timestamps();
            });
        }

        // Buat VIEW updatable 'kurir' yang terhubung langsung ke 'courier_orders'
        DB::statement('CREATE OR REPLACE VIEW kurir AS SELECT * FROM courier_orders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS kurir');
    }
};

