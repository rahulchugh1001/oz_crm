<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('item_machine')) {
            return;
        }

        $constraints = DB::select(
            "SELECT DISTINCT CONSTRAINT_NAME AS name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'item_machine'
               AND REFERENCED_TABLE_NAME IS NOT NULL"
        );

        foreach ($constraints as $constraint) {
            $name = $constraint->name ?? null;
            if (!$name) {
                continue;
            }

            DB::statement("ALTER TABLE `item_machine` DROP FOREIGN KEY `{$name}`");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('item_machine')) {
            return;
        }

        Schema::table('item_machine', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();
        });
    }
};
