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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('book_isbn');
                $table->string('peminjam_id');
                $table->date('tanggal_pinjam');
                $table->date('tanggal_kembali')->nullable();
                $table->enum('status', [
                    'dipinjam',
                    'dikembalikan'
                ])->default('dipinjam');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};