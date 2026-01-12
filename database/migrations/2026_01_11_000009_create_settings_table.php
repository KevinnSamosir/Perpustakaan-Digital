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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->string('group', 50)->default('general');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'loan_duration_days', 'value' => '14', 'type' => 'integer', 'group' => 'loan', 'description' => 'Default loan duration in days', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_loans_per_member', 'value' => '5', 'type' => 'integer', 'group' => 'loan', 'description' => 'Maximum number of books a member can borrow', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'late_fee_per_day', 'value' => '1000', 'type' => 'integer', 'group' => 'loan', 'description' => 'Late fee per day in IDR', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_name', 'value' => 'Perpustakaan Digital', 'type' => 'string', 'group' => 'general', 'description' => 'Website name', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Sistem Manajemen Perpustakaan Digital', 'type' => 'string', 'group' => 'general', 'description' => 'Website description', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
