<?php

use App\Data\CategoryData;
use App\Models\Blog;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cache:warm', function () {
    $this->info('Warming up application caches...');

    // Warm up featured projects
    Cache::remember('projects.featured', 3600, function () {
        return Project::featured()->recent()->limit(3)->get();
    });
    $this->info('✓ Featured projects cached.');

    // Warm up experiences
    Cache::remember('experiences.ordered', 21600, function () {
        return Experience::ordered()->limit(5)->get();
    });
    $this->info('✓ Experiences cached.');

    // Warm up blog categories
    Cache::remember('blog.categories', 3600, function () {
        return Blog::published()->distinct()->pluck('category');
    });
    $this->info('✓ Blog categories cached.');

    // Warm up first page of blogs
    Cache::remember('blog.posts.page.1', 900, function () {
        return Blog::published()->orderBy('published_at', 'desc')->paginate(9);
    });
    $this->info('✓ Blog posts page 1 cached.');

    // Warm up categories data
    Cache::remember('categories.all', 86400, function () {
        return CategoryData::all();
    });
    $this->info('✓ Categories data cached.');

    // Warm up project categories
    $categories = ['graphic-design', 'software-dev', 'data-analysis', 'networking'];
    foreach ($categories as $category) {
        Cache::remember('projects.category.' . $category, 1800, function () use ($category) {
            return Project::byCategory($category)->recent()->get();
        });
    }
    Cache::remember('projects.category.all', 1800, function () {
        return Project::recent()->get();
    });
    $this->info('✓ Project categories cached.');

    $this->info('');
    $this->info('Cache warming completed successfully!');
})->purpose('Warm up application caches for better performance');
