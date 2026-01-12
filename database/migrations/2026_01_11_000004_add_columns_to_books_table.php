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
        Schema::table('books', function (Blueprint $table) {
            // Add new columns
            $table->string('slug')->unique()->after('title')->nullable();
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->after('author')->constrained('authors')->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->after('author_id')->constrained('publishers')->nullOnDelete();
            $table->string('cover_image')->nullable()->after('description');
            $table->string('file_path')->nullable()->after('cover_image');
            $table->enum('file_type', ['pdf', 'epub', 'both'])->default('pdf')->after('file_path');
            $table->integer('pages')->nullable()->after('file_type');
            $table->string('language', 50)->default('Indonesia')->after('pages');
            $table->boolean('is_featured')->default(false)->after('available_stock');
            $table->boolean('is_active')->default(true)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['author_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropColumn([
                'slug',
                'category_id',
                'author_id',
                'publisher_id',
                'cover_image',
                'file_path',
                'file_type',
                'pages',
                'language',
                'is_featured',
                'is_active'
            ]);
        });
    }
};
