<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pinjam')) {
            Schema::create('pinjam', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('user_id');
                $table->string('book_id');
                $table->string('status');
                $table->date('tanggal_pinjam');
                $table->date('tenggat_waktu');
                $table->date('tanggal_kembali')->nullable();
                $table->integer('denda')->default(0);
            });
        }
    }
    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('pinjam');
}
};
