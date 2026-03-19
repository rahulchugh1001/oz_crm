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
        Schema::table('sf3_production_report_products', function (Blueprint $table) {
            $table->unsignedBigInteger('transfered_id')
                ->nullable()
                ->after('mst_item_id')
                ->comment('Reference to sf002_stock_transfers.id used to track consumed stock source')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf3_production_report_products', function (Blueprint $table) {
            $table->dropColumn('transfered_id');
        });
    }
};
