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
        Schema::create('sf2_production_reports', function (Blueprint $table) {
            $table->id();

            // Required base fields
            $table->enum('type', ['ced', 'zinc'])
                ->comment('SF2 process type')
                ->index();

            $table->unsignedBigInteger('created_by')
                ->nullable()
                ->comment('User ID who created this report')
                ->index();

            $table->date('report_date')
                ->comment('Production report date')
                ->index();

            $table->enum('shift', ['morning', 'night'])
                ->nullable()
                ->comment('Production shift')
                ->index();

            $table->unsignedBigInteger('transfered_id')
                ->comment('Reference to sf001_stock_transfers.id')
                ->index();

            $table->unsignedBigInteger('item_id')
                ->comment('Reference to items.id')
                ->index();

            // Time-wise hourly fields
            $table->decimal('hour_8_9', 12, 2)->default(0);
            $table->decimal('hour_9_10', 12, 2)->default(0);
            $table->decimal('hour_10_11', 12, 2)->default(0);
            $table->decimal('hour_11_12', 12, 2)->default(0);
            $table->decimal('hour_12_1', 12, 2)->default(0);
            $table->decimal('hour_1_2', 12, 2)->default(0);
            $table->decimal('hour_2_3', 12, 2)->default(0);
            $table->decimal('hour_3_4', 12, 2)->default(0);
            $table->decimal('hour_4_5', 12, 2)->default(0);
            $table->decimal('hour_5_6', 12, 2)->default(0);
            $table->decimal('hour_6_7', 12, 2)->default(0);
            $table->decimal('hour_7_8', 12, 2)->default(0);

            $table->decimal('actual_set_shift', 12, 2)
                ->default(0)
                ->comment('Actual / Set / Shift');

            $table->decimal('manpower_workman', 12, 2)
                ->default(0)
                ->comment('Manpower / Workman');

            $table->boolean('status')
                ->default(1)
                ->comment('1 = Active, 0 = Inactive');

            $table->boolean('is_deleted')
                ->default(0)
                ->comment('0 = No, 1 = Yes');

            $table->timestamps();

            $table->unique(['type', 'transfered_id'], 'sf2_type_transfer_unique');
            $table->index(['report_date', 'shift'], 'sf2_report_date_shift_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf2_production_reports');
    }
};
