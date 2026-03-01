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
        Schema::create('production_reports', function (Blueprint $table) {
            $table->id();

            // Machine (Not Foreign)
            $table->unsignedBigInteger('machine_id')
                  ->comment('Machine reference ID')
                  ->index();

            // Slide Size Master (items.id)
            $table->unsignedBigInteger('slide_size_id')
                  ->comment('Reference to items table (size master)')
                  ->index();

            $table->date('report_date')->index();

            $table->enum('shift', ['Morning', 'Night'])
                  ->comment('Production shift')
                  ->index();

            $table->integer('total_set_shift')
                  ->default(0)
                  ->comment('Planned total sets for shift');

            $table->integer('set_per_hour')
                  ->default(0)
                  ->comment('Planned sets per hour');

            // Hourly Production
            $table->integer('hour_8_9')->default(0);
            $table->integer('hour_9_10')->default(0);
            $table->integer('hour_10_11')->default(0);
            $table->integer('hour_11_12')->default(0);
            $table->integer('hour_12_1')->default(0);
            $table->integer('hour_1_2')->default(0);
            $table->integer('hour_2_3')->default(0);
            $table->integer('hour_3_4')->default(0);
            $table->integer('hour_4_5')->default(0);
            $table->integer('hour_5_6')->default(0);
            $table->integer('hour_6_7')->default(0);
            $table->integer('hour_7_8')->default(0);

            $table->integer('actual_set_shift')
                  ->default(0)
                  ->comment('Total actual produced sets');

            $table->integer('workman_count')
                  ->default(0)
                  ->comment('Number of workers involved');

            $table->integer('staff_count')
                  ->default(0)
                  ->comment('Number of staff supervising');

            $table->boolean('status')
                  ->default(1)
                  ->comment('1 = Active, 0 = Inactive');

            $table->boolean('is_deleted')
                  ->default(0)
                  ->comment('0 = No, 1 = Yes');

            $table->timestamps();

            // Prevent duplicate shift entry
            $table->unique(
                  ['machine_id', 'slide_size_id', 'report_date', 'shift'],
                  'prd_machine_size_date_shift_unique'
                  );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_reports');
    }
};
