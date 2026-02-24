<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebVitalsRequest;
use App\Models\WebVitalMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class WebVitalsController extends Controller
{
    public function store(StoreWebVitalsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $path = $this->normalizePath($validated['path']);
        $metric = (string) $validated['metric'];
        $value = (float) $validated['value'];

        WebVitalMetric::create([
            'path' => $path,
            'metric' => $metric,
            'value' => $value,
            'rating' => $validated['rating'] ?? $this->determineRating($metric, $value),
            'page_group' => $this->resolvePageGroup($path),
            'device_type' => $this->resolveDeviceType((string) $request->userAgent()),
            'connection_type' => $validated['connection_type'] ?? null,
            'user_agent_hash' => hash('sha256', (string) $request->userAgent()),
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        return response()->json(['recorded' => true], 202);
    }

    private function normalizePath(string $path): string
    {
        $normalized = trim(parse_url($path, PHP_URL_PATH) ?: '/');

        if ($normalized === '') {
            return '/';
        }

        if (! Str::startsWith($normalized, '/')) {
            return '/'.$normalized;
        }

        return $normalized;
    }

    private function resolvePageGroup(string $path): string
    {
        return match (true) {
            $path === '/' => 'home',
            $path === '/blog' => 'blog_list',
            Str::startsWith($path, '/blog/') => 'blog_detail',
            $path === '/contact' => 'contact',
            Str::startsWith($path, '/project/') => 'project_detail',
            Str::startsWith($path, '/projects/') => 'project_category',
            Str::startsWith($path, '/admin') => 'admin',
            default => 'other',
        };
    }

    private function determineRating(string $metric, float $value): string
    {
        $budget = config("performance.web_vitals.budgets.{$metric}");

        if (! is_array($budget)) {
            return 'needs-improvement';
        }

        if ($value <= (float) ($budget['good'] ?? 0)) {
            return 'good';
        }

        if ($value <= (float) ($budget['needs_improvement'] ?? 0)) {
            return 'needs-improvement';
        }

        return 'poor';
    }

    private function resolveDeviceType(string $userAgent): string
    {
        $ua = Str::lower($userAgent);

        if (Str::contains($ua, ['mobile', 'android', 'iphone'])) {
            return 'mobile';
        }

        if (Str::contains($ua, ['tablet', 'ipad'])) {
            return 'tablet';
        }

        return 'desktop';
    }
}
