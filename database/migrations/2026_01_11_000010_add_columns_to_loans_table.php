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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('status');
            $table->text('notes')->nullable()->after('fine_amount');
            $table->foreignId('approved_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['fine_amount', 'notes', 'approved_by']);
        });
    }
};
