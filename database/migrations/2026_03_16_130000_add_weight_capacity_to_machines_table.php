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
        Schema::table('machines', function (Blueprint $table) {
            if (Schema::hasColumn('machines', 'weight_capacity')) {
                return;
            }

            $table->unsignedSmallInteger('weight_capacity')
                ->nullable()
                ->after('coil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            if (! Schema::hasColumn('machines', 'weight_capacity')) {
                return;
            }

            $table->dropColumn('weight_capacity');
        });
    }
};
