<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Resolve locale from request
     *
     * @param Request $request
     * @return string
     */
    protected function resolveLocale(Request $request): string
    {
        $availableLocales = array_keys(config('translation.available_locales', ['en' => []]));
        $defaultLocale = config('app.fallback_locale', 'en');

        // 1. Check URL parameter (e.g., /id/blog)
        if ($request->route('locale') && in_array($request->route('locale'), $availableLocales)) {
            return $request->route('locale');
        }

        // 2. Check session
        if (Session::has('locale') && in_array(Session::get('locale'), $availableLocales)) {
            return Session::get('locale');
        }

        // 3. Check browser preference
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLocales = explode(',', $acceptLanguage);
            foreach ($preferredLocales as $preferredLocale) {
                $localeCode = substr($preferredLocale, 0, 2);
                if (in_array($localeCode, $availableLocales)) {
                    return $localeCode;
                }
            }
        }

        return $defaultLocale;
    }
}
