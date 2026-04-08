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
        Schema::table('production_reports', function (Blueprint $table) {
            $table->integer('total_set_shift')->nullable()->default(null)->change();
            $table->integer('set_per_hour')->nullable()->default(null)->change();
            $table->integer('hour_8_9')->nullable()->default(null)->change();
            $table->integer('hour_9_10')->nullable()->default(null)->change();
            $table->integer('hour_10_11')->nullable()->default(null)->change();
            $table->integer('hour_11_12')->nullable()->default(null)->change();
            $table->integer('hour_12_1')->nullable()->default(null)->change();
            $table->integer('hour_1_2')->nullable()->default(null)->change();
            $table->integer('hour_2_3')->nullable()->default(null)->change();
            $table->integer('hour_3_4')->nullable()->default(null)->change();
            $table->integer('hour_4_5')->nullable()->default(null)->change();
            $table->integer('hour_5_6')->nullable()->default(null)->change();
            $table->integer('hour_6_7')->nullable()->default(null)->change();
            $table->integer('hour_7_8')->nullable()->default(null)->change();
            $table->integer('actual_set_shift')->nullable()->default(null)->change();
            $table->integer('workman_count')->nullable()->default(null)->change();
            $table->integer('staff_count')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_reports', function (Blueprint $table) {
            $table->integer('total_set_shift')->default(0)->change();
            $table->integer('set_per_hour')->default(0)->change();
            $table->integer('hour_8_9')->default(0)->change();
            $table->integer('hour_9_10')->default(0)->change();
            $table->integer('hour_10_11')->default(0)->change();
            $table->integer('hour_11_12')->default(0)->change();
            $table->integer('hour_12_1')->default(0)->change();
            $table->integer('hour_1_2')->default(0)->change();
            $table->integer('hour_2_3')->default(0)->change();
            $table->integer('hour_3_4')->default(0)->change();
            $table->integer('hour_4_5')->default(0)->change();
            $table->integer('hour_5_6')->default(0)->change();
            $table->integer('hour_6_7')->default(0)->change();
            $table->integer('hour_7_8')->default(0)->change();
            $table->integer('actual_set_shift')->default(0)->change();
            $table->integer('workman_count')->default(0)->change();
            $table->integer('staff_count')->default(0)->change();
        });
    }
};
