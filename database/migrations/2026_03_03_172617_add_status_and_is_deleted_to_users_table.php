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
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('is_deleted')
                    ->default(0)
                    ->comment('0 = Active Record, 1 = Deleted Record')
                    ->after('remember_token');

                $table->tinyInteger('status')
                    ->default(1)
                    ->comment('1 = Active User, 0 = Inactive User')
                    ->after('is_deleted');
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
