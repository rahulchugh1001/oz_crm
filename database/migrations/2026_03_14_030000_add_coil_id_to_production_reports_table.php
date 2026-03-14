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
            $table->unsignedBigInteger('coil_id')
                ->nullable()
                ->after('machine_id')
                ->comment('Attached coil at report creation time')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_reports', function (Blueprint $table) {
            $table->dropColumn('coil_id');
        });
    }
};
