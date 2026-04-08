<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            $table->string('sf3_process', 20)
                ->nullable()
                ->comment('SF3 process assignment: line_1, line_2, line_3, line_4, line_5, line_6')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            $table->string('sf3_process', 20)
                ->nullable()
                ->comment('SF3 process assignment: line_1, line_2, line_3')
                ->change();
        });
    }
};
