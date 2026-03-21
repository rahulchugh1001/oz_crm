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
        Schema::create('sf3_stock_usages', function (Blueprint $table) {
            $table->id()->comment('Primary key of SF3 stock usage tracking table');
            $table->unsignedBigInteger('report_id')->index()->comment('SF3 production report id from sf3_production_reports.id');
            $table->unsignedBigInteger('item_id')->index()->comment('SF3 item id used in production report');
            $table->unsignedBigInteger('stock_id')->index()->comment('Stock item id (items.id where category is Store/Stock)');
            $table->decimal('in_stock', 12, 2)->default(0)->comment('Current in-stock quantity in items.quantity after save/update');
            $table->decimal('used_stock', 12, 2)->default(0)->comment('Consumed quantity for this stock item in the report');
            $table->boolean('status')->default(1)->comment('Row status: 1 = active, 0 = inactive');
            $table->boolean('is_deleted')->default(0)->comment('Soft delete flag: 0 = no, 1 = yes');
            $table->timestamp('created_at')->nullable()->comment('Record creation timestamp');
            $table->timestamp('updated_at')->nullable()->comment('Record last update timestamp');

            $table->index(['report_id', 'stock_id'], 'sf3_stock_usages_report_stock_idx');
            $table->index(['item_id', 'stock_id'], 'sf3_stock_usages_item_stock_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf3_stock_usages');
    }
};
