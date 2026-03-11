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
        // Map legacy role values before shrinking/changing enum values.
        DB::statement("UPDATE users SET role = 'SF001' WHERE role = 'User' OR role IS NULL");

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'SF001', 'SF002', 'SF003') NOT NULL DEFAULT 'SF001' COMMENT 'User Role: Admin, SF001, SF002, SF003'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map SF roles back to legacy role value before restoring enum.
        DB::statement("UPDATE users SET role = 'User' WHERE role IN ('SF001', 'SF002', 'SF003') OR role IS NULL");

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'User') NOT NULL DEFAULT 'User' COMMENT 'User Role: Admin or User'");
    }
};
