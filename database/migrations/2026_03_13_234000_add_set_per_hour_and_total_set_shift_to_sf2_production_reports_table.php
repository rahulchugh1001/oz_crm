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
            if (!Schema::hasColumn('sf2_production_reports', 'set_per_hour')) {
                $table->decimal('set_per_hour', 12, 2)
                    ->default(0)
                    ->after('item_id')
                    ->comment('Set / Hour');
            }

            if (!Schema::hasColumn('sf2_production_reports', 'total_set_shift')) {
                $table->decimal('total_set_shift', 12, 2)
                    ->default(0)
                    ->after('set_per_hour')
                    ->comment('Total Set / Shift');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf2_production_reports', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('sf2_production_reports', 'total_set_shift')) {
                $columnsToDrop[] = 'total_set_shift';
            }

            if (Schema::hasColumn('sf2_production_reports', 'set_per_hour')) {
                $columnsToDrop[] = 'set_per_hour';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
