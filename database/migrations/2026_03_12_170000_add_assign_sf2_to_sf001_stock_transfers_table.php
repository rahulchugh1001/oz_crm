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
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->enum('assign_sf2', ['CED', 'ZINC'])
                ->nullable()
                ->after('assign_role')
                ->comment('SF2 process assignment');

            $table->index(['assign_sf2', 'is_accept']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->dropIndex('sf001_stock_transfers_assign_sf2_is_accept_index');
            $table->dropColumn('assign_sf2');
        });
    }
};
