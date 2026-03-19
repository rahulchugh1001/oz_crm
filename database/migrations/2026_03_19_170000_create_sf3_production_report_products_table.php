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
        Schema::create('sf3_production_report_products', function (Blueprint $table) {
            $table->id()->comment('Primary key of the SF3 production report products table');
            $table->unsignedBigInteger('mst_item_id')->comment('Master SF3 production report id from sf3_production_reports table')->index();
            $table->unsignedBigInteger('product_id')->comment('Product item id copied from item_sf3_products.product');
            $table->decimal('quantity_required', 12, 2)->default(0)->comment('Required quantity for the product based on total set per shift');
            $table->decimal('quantity_used', 12, 2)->default(0)->comment('Used quantity for the product based on actual set per shift');
            $table->boolean('status')->default(1)->comment('Row status: 1 = active, 0 = inactive');
            $table->boolean('is_deleted')->default(0)->comment('Soft delete flag: 0 = no, 1 = yes');
            $table->timestamp('created_at')->nullable()->comment('Record creation timestamp');
            $table->timestamp('updated_at')->nullable()->comment('Record last update timestamp');

            $table->index('product_id');
            $table->index(['mst_item_id', 'product_id'], 'sf3_prod_report_products_master_product_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf3_production_report_products');
    }
};