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
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            $table->decimal('used_quantity', 12, 2)
                ->default(0)
                ->after('quantity')
                ->comment('Used quantity consumed during SF3 assembly, sourced from sf3_production_report_products.quantity_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            $table->dropColumn('used_quantity');
        });
    }
};
