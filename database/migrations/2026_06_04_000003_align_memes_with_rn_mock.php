<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memes', function (Blueprint $table) {
            $table->string('author')->nullable();
            $table->string('author_name')->nullable();
            $table->text('top')->nullable();
            $table->text('bot')->nullable();
            $table->string('cat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('memes', function (Blueprint $table) {
            $table->dropColumn(['author', 'author_name', 'top', 'bot', 'cat']);
        });
    }
};
