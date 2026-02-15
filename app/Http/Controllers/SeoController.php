<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    /**
     * Generate and return sitemap.xml
     */
    public function sitemap(): Response
    {
        $sitemap = $this->seoService->getCachedSitemap();

        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=21600'); // 6 hours
    }

    /**
     * Generate and return robots.txt
     */
    public function robots(): Response
    {
        $robots = $this->seoService->generateRobotsTxt();

        return response($robots)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400'); // 24 hours
    }
}
