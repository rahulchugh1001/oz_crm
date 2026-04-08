<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('coil_number_id')
                ->nullable()
                ->after('coil_id')
                ->comment('Reference to coil_load_numbers.id')
                ->index();
        });

        // Add comment to existing coil_id column
        DB::statement("ALTER TABLE `production_reports` MODIFY `coil_id` BIGINT UNSIGNED NULL COMMENT 'Reference to coil_stock.id'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_reports', function (Blueprint $table) {
            $table->dropColumn('coil_number_id');
        });
    }
};
