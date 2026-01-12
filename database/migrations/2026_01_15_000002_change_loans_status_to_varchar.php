<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status column from enum to varchar to allow more flexible values
        DB::statement("ALTER TABLE loans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'borrowed'");
        
        // Update 'late' to 'overdue' for consistency
        DB::table('loans')->where('status', 'late')->update(['status' => 'overdue']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to enum (may cause data loss if other values exist)
        DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('borrowed', 'returned', 'late') DEFAULT 'borrowed'");
    }
};
