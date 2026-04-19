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
        Schema::create('coil_load_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coil_id')->constrained('coil_stock')->onDelete('cascade');
            $table->foreignId('machine_id')->constrained('machines')->onDelete('cascade');
            $table->decimal('allocated_weight', 10, 3);
            $table->decimal('consumed_weight', 10, 3)->default(0);
            $table->decimal('remaining_weight', 10, 3);
            $table->string('status')->default('active'); // active, completed, returned
            $table->unsignedBigInteger('load_track_id')->nullable();
            $table->unsignedBigInteger('unload_track_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coil_load_allocations');
    }
};
