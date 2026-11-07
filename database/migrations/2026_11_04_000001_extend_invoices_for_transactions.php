<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) return;

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'nomor'))         $table->string('nomor')->nullable();
            if (!Schema::hasColumn('invoices', 'jenis'))         $table->string('jenis', 30)->nullable();   // Peminjaman | Denda | ATK
            if (!Schema::hasColumn('invoices', 'referensi_id'))  $table->string('referensi_id')->nullable(); // id pinjam / pembelian_atk
            if (!Schema::hasColumn('invoices', 'nama_pengguna')) $table->string('nama_pengguna')->nullable();
            if (!Schema::hasColumn('invoices', 'deskripsi'))     $table->text('deskripsi')->nullable();
            if (!Schema::hasColumn('invoices', 'jumlah'))        $table->bigInteger('jumlah')->default(0);
            if (!Schema::hasColumn('invoices', 'metode_bayar'))  $table->string('metode_bayar', 50)->nullable();
        });

        // Invoice denda / ATK tidak punya ISBN & tanggal pinjam.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE invoices ALTER COLUMN book_isbn DROP NOT NULL');
            DB::statement('ALTER TABLE invoices ALTER COLUMN tanggal_pinjam DROP NOT NULL');
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('book_isbn')->nullable()->change();
                $table->date('tanggal_pinjam')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // dibiarkan kosong (aman)
    }
};