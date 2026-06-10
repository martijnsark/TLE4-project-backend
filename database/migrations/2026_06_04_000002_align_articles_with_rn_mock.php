<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Separate calls required: SQLite cannot drop and add in the same Blueprint pass
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('tone');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_good_news')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->json('body_paragraphs')->nullable();
            $table->enum('tone', ['Live', 'Achtergrond', 'Reportage', 'Opinie'])->default('Achtergrond');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'is_good_news', 'is_trending', 'body_paragraphs', 'tone']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->enum('tone', ['serious', 'light', 'humorous'])->default('serious');
        });
    }
};
