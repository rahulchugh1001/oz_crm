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
        Schema::create('coil_stock', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('manufacture_id')
                ->comment('coil_manufacture.id SUPPLIER NAME')
                ->index();

            $table->string('coil_no', 120)
                ->comment('Format: OZ-BBDS-CRC Coil - 53.10 X 1 mm');

            $table->string('coil_size', 60)
                ->comment('Example: 53.10 X 1 mm');

            $table->decimal('thickness', 8, 3)
                ->default(0)
                ->comment('Example: 0.950');

            $table->decimal('net_weight_kg', 12, 3)
                ->default(0)
                ->comment('NET WEIGHT (KG), e.g. 161');

            $table->enum('process', ['available', 'in_use', 'completed'])
                ->default('available')
                ->comment('Available, In Use, Completed');

            $table->boolean('status')
                ->default(1)
                ->comment('0,1');

            $table->boolean('is_deleted')
                ->default(0)
                ->comment('0,1');

            $table->timestamps();

            $table->foreign('manufacture_id')
                ->references('id')
                ->on('coil_manufacture')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['process', 'status', 'is_deleted'], 'coil_stock_process_status_deleted_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_stock', function (Blueprint $table) {
            $table->dropForeign(['manufacture_id']);
        });

        Schema::dropIfExists('coil_stock');
    }
};
