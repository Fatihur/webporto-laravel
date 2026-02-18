<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_blog_automations', function (Blueprint $table) {
            $table->json('content_angles')->nullable()->after('content_prompt');
            $table->string('last_used_angle')->nullable()->after('content_angles');
            $table->unsignedInteger('generation_count')->default(0)->after('last_used_angle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_blog_automations', function (Blueprint $table) {
            $table->dropColumn(['content_angles', 'last_used_angle', 'generation_count']);
        });
    }
};
