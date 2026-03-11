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
        Schema::create('sf001_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->comment('Reference item id');
            $table->unsignedBigInteger('transfer_by')->comment('User id who transfers quantity');
            $table->unsignedBigInteger('assign_to')->comment('Target user id receiving transfer (SF002 user)');
            $table->decimal('quantity', 12, 2)->comment('Transfer quantity');
            $table->date('date')->comment('Transfer date');
            $table->time('time')->comment('Transfer time');
            $table->unsignedTinyInteger('is_accept')->default(0)->comment('0:pending,1:accept,2:reject');
            $table->string('remark', 500)->nullable()->comment('Optional transfer remark');       
            $table->boolean('is_deleted')->default(false)->comment('Soft delete flag: 0=active,1=deleted');
            $table->timestamps();

            $table->index(['item_id', 'is_deleted', 'is_accept']);
            $table->index(['assign_to', 'is_accept']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf001_stock_transfers');
    }
};
