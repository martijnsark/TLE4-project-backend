<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_to_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('context_text');
            $table->text('goal_text');
            $table->string('target_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_to_actions');
    }
};
