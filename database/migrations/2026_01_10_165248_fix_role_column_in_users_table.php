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
        // This migration is no longer needed as role column already exists as string
        // Leaving empty to avoid errors
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do
    }
};
