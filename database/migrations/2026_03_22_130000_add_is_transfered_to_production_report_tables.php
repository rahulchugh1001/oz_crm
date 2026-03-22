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
            if (!Schema::hasColumn('production_reports', 'is_transfered')) {
                $table->unsignedTinyInteger('is_transfered')
                    ->default(0)
                    ->comment('0: Not Yet, 1: Transfered');
            }
        });

        Schema::table('sf2_production_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('sf2_production_reports', 'is_transfered')) {
                $table->unsignedTinyInteger('is_transfered')
                    ->default(0)
                    ->comment('0: Not Yet, 1: Transfered');
            }
        });

        Schema::table('sf3_production_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('sf3_production_reports', 'is_transfered')) {
                $table->unsignedTinyInteger('is_transfered')
                    ->default(0)
                    ->comment('0: Not Yet, 1: Transfered');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_reports', function (Blueprint $table) {
            if (Schema::hasColumn('production_reports', 'is_transfered')) {
                $table->dropColumn('is_transfered');
            }
        });

        Schema::table('sf2_production_reports', function (Blueprint $table) {
            if (Schema::hasColumn('sf2_production_reports', 'is_transfered')) {
                $table->dropColumn('is_transfered');
            }
        });

        Schema::table('sf3_production_reports', function (Blueprint $table) {
            if (Schema::hasColumn('sf3_production_reports', 'is_transfered')) {
                $table->dropColumn('is_transfered');
            }
        });
    }
};
