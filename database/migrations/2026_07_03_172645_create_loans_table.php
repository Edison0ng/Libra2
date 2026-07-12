<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->id(); 
                $table->uuid('user_id'); // Menggunakan uuid agar cocok dengan id di tabel users
                $table->string('book_id');
                $table->foreign('book_id')->references('ISBN')->on('books')->onDelete('cascade');
                $table->date('tanggal_pinjam');
                $table->date('tanggal_kembali')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
