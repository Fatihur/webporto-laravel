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
        // Projects table indexes
        Schema::table('projects', function (Blueprint $table) {
            // Index for featured projects query
            $table->index(['is_featured', 'project_date'], 'idx_projects_featured_date');

            // Index for category filtering
            $table->index('category', 'idx_projects_category');

            // Composite index for category + date queries
            $table->index(['category', 'project_date'], 'idx_projects_category_date');

            // Index for slug lookups
            $table->index('slug', 'idx_projects_slug');
        });

        // Blogs table indexes
        Schema::table('blogs', function (Blueprint $table) {
            // Index for published posts query
            $table->index(['is_published', 'published_at'], 'idx_blogs_published');

            // Index for category filtering
            $table->index('category', 'idx_blogs_category');

            // Composite index for published + category queries
            $table->index(['is_published', 'category', 'published_at'], 'idx_blogs_published_category_date');

            // Index for slug lookups
            $table->index('slug', 'idx_blogs_slug');
        });

        // Experiences table indexes
        Schema::table('experiences', function (Blueprint $table) {
            // Index for ordered query
            $table->index(['order', 'start_date'], 'idx_experiences_order_date');

            // Index for current positions
            $table->index('is_current', 'idx_experiences_current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_featured_date');
            $table->dropIndex('idx_projects_category');
            $table->dropIndex('idx_projects_category_date');
            $table->dropIndex('idx_projects_slug');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('idx_blogs_published');
            $table->dropIndex('idx_blogs_category');
            $table->dropIndex('idx_blogs_published_category_date');
            $table->dropIndex('idx_blogs_slug');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropIndex('idx_experiences_order_date');
            $table->dropIndex('idx_experiences_current');
        });
    }
};
