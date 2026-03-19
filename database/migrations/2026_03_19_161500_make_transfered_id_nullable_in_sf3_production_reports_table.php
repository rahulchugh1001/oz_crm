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
        DB::statement("ALTER TABLE sf3_production_reports MODIFY transfered_id BIGINT UNSIGNED NULL COMMENT 'Legacy reference to sf002_stock_transfers.id'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE sf3_production_reports SET transfered_id = 0 WHERE transfered_id IS NULL");
        DB::statement("ALTER TABLE sf3_production_reports MODIFY transfered_id BIGINT UNSIGNED NOT NULL COMMENT 'Reference to sf002_stock_transfers.id'");
    }
};