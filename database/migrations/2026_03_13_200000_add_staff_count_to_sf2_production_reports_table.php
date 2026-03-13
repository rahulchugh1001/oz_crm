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
            $table->integer('staff_count')
                ->default(0)
                ->after('manpower_workman')
                ->comment('Total staff count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf2_production_reports', function (Blueprint $table) {
            $table->dropColumn('staff_count');
        });
    }
};
