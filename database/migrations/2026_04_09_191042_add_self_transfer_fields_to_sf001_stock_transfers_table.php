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
            $table->boolean('is_self_transferred')->default(false)->after('is_deleted')
                ->comment('true if this entry was created via SF2 self-transfer (CED↔ZINC)');
            $table->unsignedBigInteger('self_transferred_parent_id')->nullable()->after('is_self_transferred')
                ->comment('Stores sf001_stock_transfers.id of the parent record from which quantity was deducted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->dropColumn(['is_self_transferred', 'self_transferred_parent_id']);
        });
    }
};
