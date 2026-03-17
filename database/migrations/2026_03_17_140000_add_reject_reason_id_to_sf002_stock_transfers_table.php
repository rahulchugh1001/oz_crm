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
        if (!Schema::hasColumn('sf002_stock_transfers', 'reject_reason_id')) {
            Schema::table('sf002_stock_transfers', function (Blueprint $table) {
                $table->unsignedBigInteger('reject_reason_id')->nullable()->after('reject_quantity');
                $table->index('reject_reason_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('sf002_stock_transfers', 'reject_reason_id')) {
                $table->dropIndex(['reject_reason_id']);
                $table->dropColumn('reject_reason_id');
            }
        });
    }
};
