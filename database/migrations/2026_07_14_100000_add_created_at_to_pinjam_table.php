<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom created_at ke tabel pinjam. Kolom ini WAJIB ada
     * supaya "Batas Pengambilan Resv." (deadline 2 jam) dihitung dari waktu
     * booking ASLI dibuat, bukan dari waktu setiap kali halaman di-refresh.
     * Sebelumnya tabel pinjam tidak punya kolom timestamp sama sekali,
     * sehingga countdown selalu reset ke "sekarang" setiap kali data
     * diambil ulang dari API.
     */
    public function up(): void
    {
        if (Schema::hasTable('pinjam') && !Schema::hasColumn('pinjam', 'created_at')) {
            Schema::table('pinjam', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });

            // Isi created_at untuk data lama (yang sudah ada sebelum migrasi ini)
            // dengan tanggal_pinjam supaya tidak NULL, meski jamnya tidak presisi.
            DB::table('pinjam')->whereNull('created_at')->update([
                'created_at' => DB::raw('tanggal_pinjam::timestamp'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pinjam') && Schema::hasColumn('pinjam', 'created_at')) {
            Schema::table('pinjam', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }
    }
};
