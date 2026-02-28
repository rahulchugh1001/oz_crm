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
        Schema::create('items', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('code')->unique();

            $table->string('size')->nullable();

            // Precision important for mechanical items
            $table->decimal('weight', 10, 2)->default(0.00);

            // 1 = Active, 0 = Inactive
            $table->boolean('status')->default(1);

            $table->timestamps();

            // Instead of is_deleted
            $table->boolean('is_deleted')->default(0)->index();

            // Indexing for performance
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
