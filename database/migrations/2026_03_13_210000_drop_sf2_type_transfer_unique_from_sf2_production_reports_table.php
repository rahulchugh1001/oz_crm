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
        Schema::table('sf2_production_reports', function (Blueprint $table) {
            $table->dropUnique('sf2_type_transfer_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf2_production_reports', function (Blueprint $table) {
            $table->unique(['type', 'transfered_id'], 'sf2_type_transfer_unique');
        });
    }
};
