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
        Schema::create('sf3_production_reports', function (Blueprint $table) {
            $table->id();

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

            $table->enum('sf3_process', ['line_1', 'line_2', 'line_3'])
                ->comment('Assemble SF3 process line')
                ->index();

            $table->unsignedBigInteger('transfered_id')
                ->comment('Reference to sf002_stock_transfers.id')
                ->index();

            $table->unsignedBigInteger('item_id')
                ->comment('Reference to items.id')
                ->index();

            $table->decimal('set_per_hour', 12, 2)
                ->default(0)
                ->comment('Set / Hour');

            $table->decimal('total_set_shift', 12, 2)
                ->default(0)
                ->comment('Total Set / Shift');

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

            $table->integer('staff_count')
                ->default(0)
                ->comment('Total staff count');

            $table->boolean('status')
                ->default(1)
                ->comment('1 = Active, 0 = Inactive');

            $table->boolean('is_deleted')
                ->default(0)
                ->comment('0 = No, 1 = Yes');

            $table->timestamps();

            $table->index(['report_date', 'shift'], 'sf3_report_date_shift_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sf3_production_reports');
    }
};
