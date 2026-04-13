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
        DB::statement("ALTER TABLE reject_reasons MODIFY category ENUM('SF1', 'SF2', 'SF3', 'Both') NOT NULL DEFAULT 'SF1'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE reject_reasons MODIFY category ENUM('SF1', 'SF2', 'Both') NOT NULL DEFAULT 'SF1'");
    }
};
