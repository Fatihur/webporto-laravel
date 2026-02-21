<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('knowledge_entries')) {
            return;
        }

        Schema::table('knowledge_entries', function (Blueprint $table) {
            // Add vector embedding column (stored as JSON for MySQL compatibility)
            $table->json('embedding')->nullable()->after('content');
            // Add metadata for better search filtering
            $table->json('metadata')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('knowledge_entries')) {
            return;
        }

        Schema::table('knowledge_entries', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'metadata']);
        });
    }
};
