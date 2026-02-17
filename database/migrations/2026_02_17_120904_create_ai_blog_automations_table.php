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
        Schema::create('ai_blog_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('topic_prompt');
            $table->text('content_prompt');
            $table->enum('category', ['design', 'technology', 'tutorial', 'insights'])->default('technology');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'custom'])->default('daily');
            $table->time('scheduled_at')->default('09:00:00');
            $table->boolean('is_active')->default(true);
            $table->integer('max_articles_per_day')->default(1);
            $table->boolean('auto_publish')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_blog_automations');
    }
};
