<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('call_to_actions', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('context_text')->nullable()->change();
            $table->text('goal_text')->nullable()->change();
            $table->string('target_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('call_to_actions', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->text('context_text')->nullable(false)->change();
            $table->text('goal_text')->nullable(false)->change();
            $table->string('target_url')->nullable(false)->change();
        });
    }
};
