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
            $table->string('assign_role', 10)
                ->nullable()
                ->after('transfer_by')
                ->comment('Target role for transfer assignment: SF002 or SF003');

            $table->unsignedBigInteger('assign_to')
                ->nullable()
                ->default(null)
                ->change();

            $table->index(['assign_role', 'is_accept']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sf001_stock_transfers', function (Blueprint $table) {
            $table->dropIndex('sf001_stock_transfers_assign_role_is_accept_index');
            $table->dropColumn('assign_role');

            $table->unsignedBigInteger('assign_to')
                ->nullable(false)
                ->change();
        });
    }
};
