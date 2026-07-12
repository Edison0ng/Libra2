<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menghapus check constraints dari database live Supabase Postgres
        // sehingga status bebas diisi 'resolved', 'paid', dll dari panel admin.
        DB::statement("ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_check;");
        DB::statement("ALTER TABLE invoices DROP CONSTRAINT IF EXISTS invoices_status_check;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
