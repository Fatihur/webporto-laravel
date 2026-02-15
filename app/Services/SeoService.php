<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Experience;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SeoService
{
    /**
     * Generate sitemap XML
     */
    public function generateSitemap(): string
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
                    ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

        // Static pages
        $staticPages = [
            ['url' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['url' => route('projects'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['url' => route('blog'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['url' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->generateUrlEntry($page['url'], $page['changefreq'], $page['priority']);
        }

        // Blog posts
        $blogs = Blog::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($blogs as $blog) {
            $entry = $this->generateUrlEntry(
                route('blog.show', $blog->slug),
                'weekly',
                '0.8',
                $blog->updated_at->toIso8601String()
            );

            // Add image if exists
            if ($blog->featured_image) {
                $entry = str_replace(
                    '</url>',
                    $this->generateImageTag($blog->featured_image, $blog->title) . '</url>',
                    $entry
                );
            }

            $sitemap .= $entry;
        }

        // Projects
        $projects = Project::orderBy('sort_order')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($projects as $project) {
            $entry = $this->generateUrlEntry(
                route('projects.show', $project->slug),
                'weekly',
                '0.8',
                $project->updated_at->toIso8601String()
            );

            if ($project->featured_image) {
                $entry = str_replace(
                    '</url>',
                    $this->generateImageTag($project->featured_image, $project->title) . '</url>',
                    $entry
                );
            }

            $sitemap .= $entry;
        }

        $sitemap .= '</urlset>';

        return $sitemap;
    }

    /**
     * Generate URL entry for sitemap
     */
    protected function generateUrlEntry(
        string $url,
        string $changefreq = 'weekly',
        string $priority = '0.5',
        ?string $lastmod = null
    ): string {
        $entry = '  <url>' . PHP_EOL;
        $entry .= '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;

        if ($lastmod) {
            $entry .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        }

        $entry .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $entry .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        $entry .= '  </url>' . PHP_EOL;

        return $entry;
    }

    /**
     * Generate image tag for sitemap
     */
    protected function generateImageTag(string $imageUrl, string $title): string
    {
        return '    <image:image>' . PHP_EOL .
               '      <image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>' . PHP_EOL .
               '      <image:title>' . htmlspecialchars($title) . '</image:title>' . PHP_EOL .
               '    </image:image>' . PHP_EOL;
    }

    /**
     * Save sitemap to storage
     */
    public function saveSitemap(): bool
    {
        $sitemap = $this->generateSitemap();
        return Storage::disk('public')->put('sitemap.xml', $sitemap);
    }

    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt(): string
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /logout\n";
        $content .= "Disallow: /register\n";
        $content .= "Disallow: /password/*\n";
        $content .= "Disallow: /email/*\n";
        $content .= "\n";
        $content .= "Sitemap: " . route('sitemap') . "\n";

        return $content;
    }

    /**
     * Generate JSON-LD structured data for a blog post
     */
    public function generateBlogStructuredData(Blog $blog): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blog->title,
            'description' => $blog->excerpt ?? $blog->meta_description,
            'image' => $blog->featured_image,
            'datePublished' => $blog->published_at?->toIso8601String(),
            'dateModified' => $blog->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => config('app.author_name', 'Developer'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $blog->slug),
            ],
        ];
    }

    /**
     * Generate JSON-LD structured data for a project
     */
    public function generateProjectStructuredData(Project $project): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareSourceCode',
            'name' => $project->title,
            'description' => $project->description,
            'codeRepository' => $project->link,
            'dateCreated' => $project->created_at->toIso8601String(),
            'dateModified' => $project->updated_at->toIso8601String(),
            'programmingLanguage' => $project->tech_stack ?? [],
            'author' => [
                '@type' => 'Person',
                'name' => config('app.author_name', 'Developer'),
            ],
        ];

        if ($project->featured_image) {
            $data['image'] = $project->featured_image;
        }

        return $data;
    }

    /**
     * Generate JSON-LD structured data for the website
     */
    public function generateWebsiteStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'description' => config('app.description', 'Portfolio Website'),
            'author' => [
                '@type' => 'Person',
                'name' => config('app.author_name', 'Developer'),
                'url' => config('app.url'),
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate JSON-LD structured data for breadcrumbs
     */
    public function generateBreadcrumbStructuredData(array $items): array
    {
        $itemListElement = [];
        $position = 1;

        foreach ($items as $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }

    /**
     * Generate JSON-LD for Person (portfolio owner)
     */
    public function generatePersonStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => config('app.author_name', 'Developer'),
            'url' => config('app.url'),
            'image' => asset('images/profile.jpg'),
            'sameAs' => [
                config('app.social.github'),
                config('app.social.linkedin'),
                config('app.social.twitter'),
            ],
            'jobTitle' => config('app.author_title', 'Full Stack Developer'),
            'worksFor' => [
                '@type' => 'Organization',
                'name' => config('app.company_name'),
            ],
        ];
    }

    /**
     * Get cached sitemap or generate new one
     */
    public function getCachedSitemap(): string
    {
        return Cache::remember('sitemap', now()->addHours(6), function () {
            return $this->generateSitemap();
        });
    }

    /**
     * Clear sitemap cache
     */
    public function clearSitemapCache(): void
    {
        Cache::forget('sitemap');
    }
}
