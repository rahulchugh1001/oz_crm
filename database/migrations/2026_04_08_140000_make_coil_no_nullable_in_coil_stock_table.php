<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coil_stock', function (Blueprint $table) {
            $table->string('coil_no', 120)->nullable()->change();
        });

        // Drop the unique index on coil_no
        Schema::table('coil_stock', function (Blueprint $table) {
            $table->dropUnique(['coil_no']);
        });
    }

    public function down(): void
    {
        Schema::table('coil_stock', function (Blueprint $table) {
            $table->unique('coil_no');
        });

        Schema::table('coil_stock', function (Blueprint $table) {
            $table->string('coil_no', 120)->nullable(false)->change();
        });
    }
};
