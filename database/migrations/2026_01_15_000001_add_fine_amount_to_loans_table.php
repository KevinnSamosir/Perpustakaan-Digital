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
        Schema::table('loans', function (Blueprint $table) {
            // Add fine_amount column if not exists
            if (!Schema::hasColumn('loans', 'fine_amount')) {
                $table->decimal('fine_amount', 10, 2)->default(0)->after('status');
            }
            
            // Add notes column if not exists
            if (!Schema::hasColumn('loans', 'notes')) {
                $table->text('notes')->nullable()->after('fine_amount');
            }
        });
        
        // Update status enum to include 'overdue'
        // First update any 'late' status to 'overdue'
        DB::table('loans')->where('status', 'late')->update(['status' => 'overdue']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'fine_amount')) {
                $table->dropColumn('fine_amount');
            }
            if (Schema::hasColumn('loans', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
