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
        if (!Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_id'); // UUID mahasiswa
                $table->string('title');
                $table->string('author');
                $table->string('category');
                $table->string('condition');
                $table->text('note')->nullable();
                $table->string('status')->default('Menunggu Verifikasi');
                $table->date('tanggal_donasi');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
