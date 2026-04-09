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
        Schema::table('sf3_production_reports', function (Blueprint $table) {
            $table->decimal('hour_8_9', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_9_10', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_10_11', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_11_12', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_12_1', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_1_2', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_2_3', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_3_4', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_4_5', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_5_6', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_6_7', 12, 2)->nullable()->default(null)->change();
            $table->decimal('hour_7_8', 12, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf3_production_reports', function (Blueprint $table) {
            $table->decimal('hour_8_9', 12, 2)->default(0)->change();
            $table->decimal('hour_9_10', 12, 2)->default(0)->change();
            $table->decimal('hour_10_11', 12, 2)->default(0)->change();
            $table->decimal('hour_11_12', 12, 2)->default(0)->change();
            $table->decimal('hour_12_1', 12, 2)->default(0)->change();
            $table->decimal('hour_1_2', 12, 2)->default(0)->change();
            $table->decimal('hour_2_3', 12, 2)->default(0)->change();
            $table->decimal('hour_3_4', 12, 2)->default(0)->change();
            $table->decimal('hour_4_5', 12, 2)->default(0)->change();
            $table->decimal('hour_5_6', 12, 2)->default(0)->change();
            $table->decimal('hour_6_7', 12, 2)->default(0)->change();
            $table->decimal('hour_7_8', 12, 2)->default(0)->change();
        });
    }
};
