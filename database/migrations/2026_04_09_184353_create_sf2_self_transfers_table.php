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
        Schema::create('sf2_self_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->index();
            $table->string('from_type', 10)->comment('ced or zinc');
            $table->string('to_type', 10)->comment('ced or zinc');
            $table->decimal('quantity', 12, 2);
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('transfer_by')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf2_self_transfers');
    }
};
