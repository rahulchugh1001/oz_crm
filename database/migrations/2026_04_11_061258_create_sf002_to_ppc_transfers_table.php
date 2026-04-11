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
        Schema::create('sf002_to_ppc_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->index();
            $table->string('type', 10)->comment('ced or zinc')->index();
            $table->unsignedBigInteger('transfer_by')->nullable()->index();
            $table->string('assign_role', 20)->nullable()->default('PPC')->index();
            $table->unsignedBigInteger('assign_to')->nullable()->index();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->date('date');
            $table->time('time');
            $table->tinyInteger('is_accept')->default(0)->comment('0=pending,1=accepted,2=rejected')->index();
            $table->text('remark')->nullable();
            $table->text('ppc_remark')->nullable();
            $table->decimal('reject_quantity', 12, 2)->nullable()->default(0);
            $table->unsignedBigInteger('reject_reason_id')->nullable()->index();
            $table->boolean('is_deleted')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf002_to_ppc_transfers');
    }
};
