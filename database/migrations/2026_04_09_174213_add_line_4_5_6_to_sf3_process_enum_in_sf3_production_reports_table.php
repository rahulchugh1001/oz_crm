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
        DB::statement("ALTER TABLE sf3_production_reports MODIFY COLUMN sf3_process ENUM('line_1','line_2','line_3','line_4','line_5','line_6') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE sf3_production_reports MODIFY COLUMN sf3_process ENUM('line_1','line_2','line_3') NOT NULL");
    }
};
