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
        Schema::table('coil_stock', function (Blueprint $table) {
            $table->enum('process_type', ['load', 'unload'])
                ->nullable()
                ->after('process')
                ->comment('Latest stock movement type');
        });

        DB::statement("ALTER TABLE coil_stock MODIFY process ENUM('available', 'in_use', 'completed', 'out_of_stock') NOT NULL DEFAULT 'available'");
        DB::statement("UPDATE coil_stock SET process = 'out_of_stock' WHERE net_weight_kg = 0 AND process <> 'completed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE coil_stock SET process = 'completed' WHERE process = 'out_of_stock'");
        DB::statement("ALTER TABLE coil_stock MODIFY process ENUM('available', 'in_use', 'completed') NOT NULL DEFAULT 'available'");

        Schema::table('coil_stock', function (Blueprint $table) {
            $table->dropColumn('process_type');
        });
    }
};
