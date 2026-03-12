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
            $table->decimal('reject_quantity', 12, 2)
                ->default(0)
                ->after('quantity')
                ->comment('Rejected quantity from assigned transfer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->dropColumn('reject_quantity');
        });
    }
};
