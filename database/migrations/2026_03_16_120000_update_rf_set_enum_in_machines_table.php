<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('machines') || ! Schema::hasColumn('machines', 'rf_set')) {
            return;
        }

        // Normalize existing data to match the new enum values.
        // 1) Clean up common variants (case / spacing).
        DB::statement("UPDATE machines SET rf_set = 'Inner' WHERE rf_set IS NOT NULL AND LOWER(TRIM(rf_set)) = 'inner'");
        DB::statement("UPDATE machines SET rf_set = 'Outer' WHERE rf_set IS NOT NULL AND LOWER(TRIM(rf_set)) = 'outer'");
        DB::statement("UPDATE machines SET rf_set = 'Middle' WHERE rf_set IS NOT NULL AND LOWER(TRIM(rf_set)) = 'middle'");
        DB::statement("UPDATE machines SET rf_set = 'Ball Cage' WHERE rf_set IS NOT NULL AND LOWER(REPLACE(REPLACE(TRIM(rf_set), '-', ''), ' ', '')) IN ('ballcage')");

        // 2) If old values exist (e.g. RF-01, RF-02, etc), infer from machine name/code where possible.
        DB::statement("UPDATE machines SET rf_set = 'Ball Cage' WHERE (rf_set IS NULL OR rf_set NOT IN ('Inner','Outer','Middle','Ball Cage')) AND (machine_code LIKE 'BC%' OR name LIKE '%Ball%')");
        DB::statement("UPDATE machines SET rf_set = 'Inner' WHERE (rf_set IS NULL OR rf_set NOT IN ('Inner','Outer','Middle','Ball Cage')) AND name LIKE '%Inner%'");
        DB::statement("UPDATE machines SET rf_set = 'Outer' WHERE (rf_set IS NULL OR rf_set NOT IN ('Inner','Outer','Middle','Ball Cage')) AND name LIKE '%Outer%'");
        DB::statement("UPDATE machines SET rf_set = 'Middle' WHERE (rf_set IS NULL OR rf_set NOT IN ('Inner','Outer','Middle','Ball Cage')) AND name LIKE '%Middle%'");

        // 3) Anything still not matching becomes NULL (keeps migration safe for enum conversion).
        DB::statement("UPDATE machines SET rf_set = NULL WHERE rf_set IS NOT NULL AND rf_set NOT IN ('Inner','Outer','Middle','Ball Cage')");

        // Update column type to enum (MySQL).
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE machines MODIFY rf_set ENUM('Inner','Outer','Middle','Ball Cage') NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('machines') || ! Schema::hasColumn('machines', 'rf_set')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE machines MODIFY rf_set VARCHAR(255) NULL');
        }
    }
};
