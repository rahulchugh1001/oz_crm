<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coil_machine', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coil_stock_id');
            $table->unsignedBigInteger('machine_id');
            $table->timestamps();
            $table->unique(['coil_stock_id', 'machine_id']);
            $table->index(['machine_id', 'coil_stock_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coil_machine');
    }
};
