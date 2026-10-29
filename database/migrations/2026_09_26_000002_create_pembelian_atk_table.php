<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan Toko ATK. Statusnya SENGAJA memakai kosakata yang SAMA dengan
     * tabel `pinjam` (lihat PinjamController/COURIER_FLOW_STEPS di frontend):
     * - 'Booking'                      -> ambil sendiri, menunggu diambil (ada batas waktu 2 jam, sama seperti buku)
     * - 'Booking Dikonfirmasi Admin'
     * - 'Sedang Dikemas'
     * - 'Menunggu Kurir'
     * - 'Sedang Diantar Kurir'         -> keempat ini = alur kurir, identik dgn peminjaman buku
     * - 'Selesai'                      -> sudah diambil/diterima (tanggal_selesai ikut terisi)
     * - 'Dibatalkan'
     *
     * Dengan begini, kartu status di tab Sirkulasi mahasiswa bisa dirender
     * pakai komponen/timeline yang SAMA dengan peminjaman buku, tidak perlu
     * logika terpisah.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pembelian_atk')) {
            Schema::create('pembelian_atk', function (Blueprint $table) {
                $table->string('id')->primary(); // UUID
                $table->string('user_id');
                $table->json('items'); // [{produk_id, kode, nama, harga, qty}, ...]
                $table->unsignedInteger('total')->default(0);
                $table->string('metode_pengiriman')->default('ambil_sendiri'); // 'ambil_sendiri' | 'antar'
                $table->text('alamat_kirim')->nullable(); // diisi kalau metode_pengiriman = 'antar'
                $table->string('status')->default('Booking');
                $table->timestamp('tanggal_selesai')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_atk');
    }
};