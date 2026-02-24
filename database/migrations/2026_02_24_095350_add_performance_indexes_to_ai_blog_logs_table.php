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
        Schema::table('ai_blog_logs', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_ai_blog_logs_status_created_at');
            $table->index(['ai_blog_automation_id', 'status'], 'idx_ai_blog_logs_automation_status');
            $table->index(['started_at', 'completed_at'], 'idx_ai_blog_logs_duration_window');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_blog_logs', function (Blueprint $table) {
            $table->dropIndex('idx_ai_blog_logs_status_created_at');
            $table->dropIndex('idx_ai_blog_logs_automation_status');
            $table->dropIndex('idx_ai_blog_logs_duration_window');
        });
    }
};
