<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_capacities', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('capacity')->unique();
            $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->boolean('is_deleted')->default(0)->comment('0=Not Deleted, 1=Deleted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_capacities');
    }
};

