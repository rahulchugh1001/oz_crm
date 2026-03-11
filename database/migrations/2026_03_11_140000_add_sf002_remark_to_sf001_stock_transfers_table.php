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
            $table->string('sf002_remark', 500)
                ->nullable()
                ->comment('Optional remark added by SF002 user')
                ->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->dropColumn('sf002_remark');
        });
    }
};
