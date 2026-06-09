<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('comment_template_id')->constrained()->cascadeOnDelete();

            $table->morphs('commentable');

            $table->timestamps();

            $table->unique([
                'user_id',
                'comment_template_id',
                'commentable_id',
                'commentable_type',
            ], 'unique_user_template_commentable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
