<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('source_url');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['article_id', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_sources');
    }
};
