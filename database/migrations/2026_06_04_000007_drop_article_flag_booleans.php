<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_good_news', 'is_trending']);
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_good_news')->default(false)->after('status');
            $table->boolean('is_trending')->default(false)->after('is_good_news');
        });
    }
};
