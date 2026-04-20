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
        Schema::table('coil_load_allocations', function (Blueprint $table) {
            $table->string('coil_no', 120)->nullable()->after('machine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_load_allocations', function (Blueprint $table) {
            $table->dropColumn('coil_no');
        });
    }
};
