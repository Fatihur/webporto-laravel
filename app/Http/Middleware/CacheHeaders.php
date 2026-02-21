<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $publicRouteNames = [
            'home',
            'projects.category',
            'projects.show',
            'blog.index',
            'blog.show',
            'contact.index',
        ];

        // Skip for authenticated users, admin routes, and Livewire routes
        if (Auth::check() || $request->is('admin/*') || $request->is('livewire*')) {
            $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

            return $response;
        }

        // Static assets - cache for 1 year (immutable)
        if ($request->is('build/*') || $request->is('storage/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', now()->addYear()->toRfc7231String());

            return $response;
        }

        if ($request->routeIs('sitemap', 'robots')) {
            $response->headers->set('Vary', 'Accept-Encoding');

            return $response;
        }

        // Add lightweight preload headers only on the homepage.
        if ($request->routeIs('home')) {
            $response->headers->set(
                'Link',
                '<https://fonts.gstatic.com/s/inter/v18/UcCo3FwrK3iLTcviYwY.woff2>; rel=preload; as=font; type=font/woff2; crossorigin',
                false
            );
        }

        // Public pages - cache for 5 minutes with stale-while-revalidate
        if ($request->routeIs($publicRouteNames)) {
            $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=300, stale-while-revalidate=3600');
            $response->headers->set('Vary', 'Accept-Encoding');

            return $response;
        }

        // Default - no caching
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate');

        return $response;
    }
}
