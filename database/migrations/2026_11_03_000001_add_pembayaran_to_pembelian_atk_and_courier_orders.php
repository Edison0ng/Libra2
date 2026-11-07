<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status pembayaran pesanan ATK (simulasi).
     *  - status_bayar : 'Belum Dibayar' | 'Lunas'
     *  - metode_bayar : 'Bayar di Tempat' | 'QRIS' | 'Transfer Bank' | 'E-Wallet' (NULL = belum dipilih/dibayar)
     *  - dibayar_at   : waktu lunas
     * Kolom yang sama (kecuali dibayar_at) juga dicatat di courier_orders supaya
     * kurir tahu apakah harus menagih tunai saat barang tiba.
     */
    public function up(): void
    {
        if (Schema::hasTable('pembelian_atk')) {
            Schema::table('pembelian_atk', function (Blueprint $table) {
                if (!Schema::hasColumn('pembelian_atk', 'status_bayar')) {
                    $table->string('status_bayar')->default('Belum Dibayar');
                }
                if (!Schema::hasColumn('pembelian_atk', 'metode_bayar')) {
                    $table->string('metode_bayar')->nullable();
                }
                if (!Schema::hasColumn('pembelian_atk', 'dibayar_at')) {
                    $table->timestamp('dibayar_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('courier_orders')) {
            Schema::table('courier_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('courier_orders', 'status_bayar')) {
                    $table->string('status_bayar')->nullable();
                }
                if (!Schema::hasColumn('courier_orders', 'metode_bayar')) {
                    $table->string('metode_bayar')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pembelian_atk')) {
            Schema::table('pembelian_atk', function (Blueprint $table) {
                foreach (['status_bayar', 'metode_bayar', 'dibayar_at'] as $col) {
                    if (Schema::hasColumn('pembelian_atk', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
        if (Schema::hasTable('courier_orders')) {
            Schema::table('courier_orders', function (Blueprint $table) {
                foreach (['status_bayar', 'metode_bayar'] as $col) {
                    if (Schema::hasColumn('courier_orders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};