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
        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->string('ISBN')->primary();
                $table->string('Book-Title');
                $table->string('Book-Author');
                $table->integer('Year-Of-Publication');
                $table->string('Publisher');
                $table->text('Image-URL-S')->nullable();
                $table->text('Image-URL-M')->nullable();
                $table->text('Image-URL-L')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
