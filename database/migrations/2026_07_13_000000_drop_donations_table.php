<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Keep the existing donations table and its data intact.
     */
    public function up(): void
    {
        // Intentionally left empty to avoid deleting existing data.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty; this migration does not change the schema.
    }
};
