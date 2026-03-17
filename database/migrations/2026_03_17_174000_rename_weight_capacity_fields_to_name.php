<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weight_capacities', function (Blueprint $table) {
            if (! Schema::hasColumn('weight_capacities', 'name')) {
                $table->string('name', 50)->after('id');
            }
        });

        // Backfill name from existing label/capacity when present.
        if (Schema::hasColumn('weight_capacities', 'label')) {
            DB::table('weight_capacities')
                ->where(function ($q) {
                    $q->whereNull('name')->orWhere('name', '');
                })
                ->update([
                    'name' => DB::raw('label'),
                    'updated_at' => now(),
                ]);
        } elseif (Schema::hasColumn('weight_capacities', 'capacity')) {
            DB::table('weight_capacities')
                ->where(function ($q) {
                    $q->whereNull('name')->orWhere('name', '');
                })
                ->update([
                    'name' => DB::raw("CONCAT(capacity, 'kg')"),
                    'updated_at' => now(),
                ]);
        }

        // Make name unique.
        Schema::table('weight_capacities', function (Blueprint $table) {
            $table->unique('name');
        });

        // Drop old uniques + columns
        Schema::table('weight_capacities', function (Blueprint $table) {
            if (Schema::hasColumn('weight_capacities', 'label')) {
                try { $table->dropUnique(['label']); } catch (\Throwable $e) {}
                $table->dropColumn('label');
            }
            if (Schema::hasColumn('weight_capacities', 'capacity')) {
                try { $table->dropUnique(['capacity']); } catch (\Throwable $e) {}
                $table->dropColumn('capacity');
            }
        });

        // Update machines.weight_capacity to string to store name values.
        Schema::table('machines', function (Blueprint $table) {
            // change() requires doctrine/dbal; do safe add+swap instead
            if (! Schema::hasColumn('machines', 'weight_capacity_name')) {
                $table->string('weight_capacity_name', 50)->nullable()->after('coil_id');
            }
        });

        // Try mapping old numeric capacity to seeded label if any rows exist.
        // If machines.weight_capacity still exists, move its value into weight_capacity_name as "<n>kg-STD" best-effort.
        if (Schema::hasColumn('machines', 'weight_capacity')) {
            DB::table('machines')
                ->whereNotNull('weight_capacity')
                ->update([
                    'weight_capacity_name' => DB::raw("CONCAT(weight_capacity, 'kg')"),
                    'updated_at' => now(),
                ]);
        }

        Schema::table('machines', function (Blueprint $table) {
            if (Schema::hasColumn('machines', 'weight_capacity')) {
                $table->dropColumn('weight_capacity');
            }
        });

        Schema::table('machines', function (Blueprint $table) {
            if (Schema::hasColumn('machines', 'weight_capacity_name')) {
                $table->renameColumn('weight_capacity_name', 'weight_capacity');
            }
        });
    }

    public function down(): void
    {
        // Restore old columns (capacity + label) and machine smallint weight_capacity.
        Schema::table('weight_capacities', function (Blueprint $table) {
            if (Schema::hasColumn('weight_capacities', 'name')) {
                $table->dropUnique(['name']);
                $table->dropColumn('name');
            }
            if (! Schema::hasColumn('weight_capacities', 'capacity')) {
                $table->unsignedSmallInteger('capacity')->nullable();
            }
            if (! Schema::hasColumn('weight_capacities', 'label')) {
                $table->string('label', 50)->nullable();
            }
        });

        Schema::table('machines', function (Blueprint $table) {
            if (Schema::hasColumn('machines', 'weight_capacity')) {
                $table->dropColumn('weight_capacity');
            }
            $table->unsignedSmallInteger('weight_capacity')->nullable()->after('coil_id');
        });
    }
};

