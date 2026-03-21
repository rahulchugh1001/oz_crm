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
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'quantity')) {
                $table->unsignedInteger('quantity')->nullable()->after('weight');
            }
        });

        if (Schema::hasColumn('items', 'quantity')) {
            DB::statement('ALTER TABLE items MODIFY quantity INT UNSIGNED NULL DEFAULT NULL');
        }

        if (Schema::hasColumn('items', 'weight')) {
            DB::statement('ALTER TABLE items MODIFY weight DECIMAL(10,2) NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'quantity')) {
            DB::statement('ALTER TABLE items MODIFY quantity DECIMAL(10,2) NULL DEFAULT NULL');
        }

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });

        if (Schema::hasColumn('items', 'weight')) {
            DB::statement('ALTER TABLE items MODIFY weight DECIMAL(10,2) NOT NULL DEFAULT 0.00');
        }
    }
};
