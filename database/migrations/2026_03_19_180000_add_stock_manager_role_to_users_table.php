<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE users SET role = 'SF001' WHERE role = 'Stock Manager'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'SF001', 'SF002', 'SF003', 'Stock') NOT NULL DEFAULT 'SF001' COMMENT 'User Role: Admin, SF001, SF002, SF003, Stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'SF001' WHERE role = 'Stock'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'SF001', 'SF002', 'SF003') NOT NULL DEFAULT 'SF001' COMMENT 'User Role: Admin, SF001, SF002, SF003'");
    }
};