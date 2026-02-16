<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugBlogContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug untuk blog save requests
        if ($request->is('livewire/update') && str_contains($request->getContent(), 'blog')) {
            $content = $request->getContent();
            \Log::debug('Livewire Blog Request:', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'content_preview' => substr($content, 0, 2000),
                'content_length' => strlen($content),
            ]);
        }

        return $next($request);
    }
}
