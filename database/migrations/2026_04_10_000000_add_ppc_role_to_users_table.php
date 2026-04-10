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
        // Add 'PPC' to the enum values for the 'role' column in the users table
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'SF001', 'SF002', 'SF003', 'Stock', 'PPC') NOT NULL DEFAULT 'SF001' COMMENT 'User Role: Admin, SF001, SF002, SF003, Stock, PPC'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'PPC' from the enum values for the 'role' column in the users table
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'SF001', 'SF002', 'SF003', 'Stock') NOT NULL DEFAULT 'SF001' COMMENT 'User Role: Admin, SF001, SF002, SF003, Stock'");
    }
};
