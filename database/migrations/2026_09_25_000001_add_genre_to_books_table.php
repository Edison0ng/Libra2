<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel `books` yang sebenarnya dipakai aplikasi ini (dibuat oleh
     * migrasi 2026_07_03_103541_create_books_table.php, berbasis ISBN)
     * TIDAK punya kolom `genre` sejak awal. Data CSV sumbernya ('genre')
     * jadi tidak pernah tersimpan. Migrasi ini menambahkannya.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'genre')) {
                $table->string('genre')->nullable()->after('Publisher');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'genre')) {
                $table->dropColumn('genre');
            }
        });
    }
};