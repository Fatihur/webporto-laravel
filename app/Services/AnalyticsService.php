<?php

namespace App\Services;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get demographics data for specified period.
     *
     * @param int $days Number of days to look back
     * @return array
     */
    public function getDemographics(int $days = 30): array
    {
        return [
            'countries' => $this->getCountries($days),
            'cities' => $this->getCities($days),
            'daily_views' => $this->getDailyViews($days),
            'top_pages' => $this->getTopPages($days),
            'total_views' => $this->getTotalViews($days),
            'unique_visitors' => $this->getUniqueVisitors($days),
            'popular_content' => $this->getPopularContent($days),
        ];
    }

    /**
     * Get countries with view counts.
     */
    protected function getCountries(int $days): array
    {
        return PageView::where('created_at', '>=', now()->subDays($days))
            ->select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get cities with view counts.
     */
    protected function getCities(int $days): array
    {
        return PageView::where('created_at', '>=', now()->subDays($days))
            ->select('country', 'city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('country', 'city')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get daily view counts.
     */
    protected function getDailyViews(int $days): array
    {
        return PageView::where('created_at', '>=', now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get top pages by view count.
     */
    protected function getTopPages(int $days): array
    {
        return PageView::where('created_at', '>=', now()->subDays($days))
            ->select('viewable_type', 'viewable_id', DB::raw('count(*) as total'))
            ->groupBy('viewable_type', 'viewable_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->viewable_type,
                    'id' => $item->viewable_id,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get total view count.
     */
    protected function getTotalViews(int $days): int
    {
        return PageView::where('created_at', '>=', now()->subDays($days))->count();
    }

    /**
     * Get unique visitor count.
     */
    protected function getUniqueVisitors(int $days): int
    {
        return PageView::where('created_at', '>=', now()->subDays($days))
            ->distinct('session_id')
            ->count('session_id');
    }

    /**
     * Get popular content (projects and blogs).
     */
    public function getPopularContent(int $days = 30, int $limit = 5): array
    {
        $projects = PageView::where('created_at', '>=', now()->subDays($days))
            ->where('viewable_type', 'App\Models\Project')
            ->select('viewable_id', DB::raw('count(*) as total'))
            ->groupBy('viewable_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'project',
                    'id' => $item->viewable_id,
                    'total' => $item->total,
                ];
            })
            ->toArray();

        $blogs = PageView::where('created_at', '>=', now()->subDays($days))
            ->where('viewable_type', 'App\Models\Blog')
            ->select('viewable_id', DB::raw('count(*) as total'))
            ->groupBy('viewable_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'blog',
                    'id' => $item->viewable_id,
                    'total' => $item->total,
                ];
            })
            ->toArray();

        return [
            'projects' => $projects,
            'blogs' => $blogs,
        ];
    }
}
