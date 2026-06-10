<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('content_reviews');
        Schema::dropIfExists('generated_contents');
    }

    public function down(): void
    {
        // Tabellen worden opnieuw aangemaakt wanneer Issue #13 (AI-generatie)
        // wordt gebouwd. Rollback van deze migratie is niet de bedoeling.
    }
};
