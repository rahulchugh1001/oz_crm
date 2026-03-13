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
                ->after('assign_role')
                ->comment('SF3 process assignment: line_1, line_2, line_3')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('sf002_stock_transfers', function (Blueprint $table) {
            $table->dropIndex(['sf3_process']);
            $table->dropColumn('sf3_process');
        });
    }
};
