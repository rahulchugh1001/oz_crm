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
        Schema::table('coil_machine_track_logs', function (Blueprint $table) {
            $table->decimal('load_weight', 12, 3)
                ->default(0)
                ->after('action_type')
                ->comment('How much weight was loaded');

            $table->decimal('unload_weight', 12, 3)
                ->default(0)
                ->after('load_weight')
                ->comment('How much weight was unloaded');

            $table->decimal('total_weight', 12, 3)
                ->default(0)
                ->after('unload_weight')
                ->comment('Total loaded weight reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_machine_track_logs', function (Blueprint $table) {
            $table->dropColumn(['load_weight', 'unload_weight', 'total_weight']);
        });
    }
};
