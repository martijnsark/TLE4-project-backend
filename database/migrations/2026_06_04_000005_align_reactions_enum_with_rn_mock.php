<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite cannot alter enum columns in place — drop + re-add
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropColumn('reaction');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->enum('reaction', ['smile', 'meh', 'frown'])->after('article_id');
        });
    }

    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropColumn('reaction');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->enum('reaction', ['happy', 'shocked', 'sad'])->after('article_id');
        });
    }
};
