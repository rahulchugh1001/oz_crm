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
        Schema::create('coil_machine_track_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coil_machine_track_id')->nullable()->index();
            $table->unsignedBigInteger('machine_id')->nullable()->index();
            $table->unsignedBigInteger('coil_id')->nullable()->index();
            $table->enum('action_type', ['load', 'unload', 'update', 'delete'])->index();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('message', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->boolean('status')->default(1)->comment('0,1');
            $table->boolean('is_deleted')->default(0)->comment('0,1');
            $table->timestamps();

            $table->foreign('coil_machine_track_id')
                ->references('id')
                ->on('coil_machine_track')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('coil_id')
                ->references('id')
                ->on('coil_stock')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index(['action_type', 'status', 'is_deleted'], 'coil_machine_track_logs_action_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_machine_track_logs', function (Blueprint $table) {
            $table->dropForeign(['coil_machine_track_id']);
            $table->dropForeign(['machine_id']);
            $table->dropForeign(['coil_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('coil_machine_track_logs');
    }
};
