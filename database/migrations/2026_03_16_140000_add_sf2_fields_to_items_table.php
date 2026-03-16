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
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'name_sf2')) {
                $table->string('name_sf2')->nullable()->after('name');
            }

            if (! Schema::hasColumn('items', 'code_sf2')) {
                $table->string('code_sf2')->nullable()->after('code');
                $table->unique('code_sf2');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'code_sf2')) {
                $table->dropUnique(['code_sf2']);
                $table->dropColumn('code_sf2');
            }

            if (Schema::hasColumn('items', 'name_sf2')) {
                $table->dropColumn('name_sf2');
            }
        });
    }
};
