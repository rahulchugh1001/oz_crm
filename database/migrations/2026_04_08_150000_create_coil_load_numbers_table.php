<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coil_load_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coil_id');
            $table->unsignedBigInteger('coil_machine_track_id');
            $table->string('coil_no', 120);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('coil_id')->references('id')->on('coil_stock')->onDelete('cascade');
            $table->foreign('coil_machine_track_id')->references('id')->on('coil_machine_track')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('coil_id');
            $table->index('coil_machine_track_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coil_load_numbers');
    }
};
