<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch application locale
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        $availableLocales = array_keys(config('translation.available_locales', ['en' => []]));

        if (!in_array($locale, $availableLocales)) {
            abort(404);
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Redirect to the previous URL or home
        $redirectUrl = $request->input('redirect', route('home'));

        // Remove locale prefix from current URL if present
        $currentPath = parse_url($redirectUrl, PHP_URL_PATH) ?? '/';
        $currentPathWithoutLocale = preg_replace('/^\/(en|id)\//', '/', $currentPath);
        $currentPathWithoutLocale = preg_replace('/^\/(en|id)$/', '/', $currentPathWithoutLocale);

        // Build new URL with locale
        if ($locale === config('app.fallback_locale', 'en')) {
            // Use non-localized URL for default locale
            $newUrl = $currentPathWithoutLocale;
        } else {
            // Add locale prefix
            $newUrl = '/' . $locale . ($currentPathWithoutLocale === '/' ? '' : $currentPathWithoutLocale);
        }

        return redirect($newUrl);
    }
}
