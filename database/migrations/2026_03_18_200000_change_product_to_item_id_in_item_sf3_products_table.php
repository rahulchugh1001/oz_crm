<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_sf3_products', function (Blueprint $table) {
            $table->dropColumn('product');
        });

        Schema::table('item_sf3_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product')
                ->comment('product link to the item.id and category: SF1-SF2, Store')
                ->after('item_id');
        });
    }

    public function down(): void
    {
        Schema::table('item_sf3_products', function (Blueprint $table) {
            $table->dropColumn('product');
        });

        Schema::table('item_sf3_products', function (Blueprint $table) {
            $table->string('product')->after('item_id');
        });
    }
};
