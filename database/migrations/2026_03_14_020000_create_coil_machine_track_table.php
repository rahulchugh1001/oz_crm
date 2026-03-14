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
        Schema::create('coil_machine_track', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id')->index();
            $table->unsignedBigInteger('coil_id')->index();
            $table->decimal('load_weight', 12, 3)->default(0)->comment('Loaded weight in KG');
            $table->decimal('unload_weight', 12, 3)->nullable()->comment('Pending weight in KG at unload');
            $table->enum('type', ['load', 'unload'])->index();
            $table->unsignedBigInteger('reference_track_id')->nullable()->index()->comment('Linked load track for unload');
            $table->timestamp('event_at')->nullable()->index();
            $table->string('remark', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->boolean('status')->default(1)->comment('0,1');
            $table->boolean('is_deleted')->default(0)->comment('0,1');
            $table->timestamps();

            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('coil_id')
                ->references('id')
                ->on('coil_stock')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('reference_track_id')
                ->references('id')
                ->on('coil_machine_track')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index(['machine_id', 'coil_id', 'type', 'status', 'is_deleted'], 'coil_machine_track_machine_coil_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_machine_track', function (Blueprint $table) {
            $table->dropForeign(['machine_id']);
            $table->dropForeign(['coil_id']);
            $table->dropForeign(['reference_track_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('coil_machine_track');
    }
};
