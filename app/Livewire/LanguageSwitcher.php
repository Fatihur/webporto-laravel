<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLocale(string $locale): void
    {
        $availableLocales = array_keys(config('translation.available_locales', ['en' => []]));

        if (!in_array($locale, $availableLocales)) {
            return;
        }

        $this->currentLocale = $locale;
        session(['locale' => $locale]);

        // Get current URL path
        $currentPath = request()->path();

        // Remove existing locale prefix if present
        $currentPathWithoutLocale = preg_replace('/^(en|id)\//', '', $currentPath);

        // Build new URL with locale
        if ($locale === config('app.fallback_locale', 'en')) {
            // Use non-localized URL for default locale
            $newUrl = $currentPathWithoutLocale === '' ? '/' : '/' . $currentPathWithoutLocale;
        } else {
            // Add locale prefix
            $newUrl = '/' . $locale . ($currentPathWithoutLocale === '' ? '' : '/' . $currentPathWithoutLocale);
        }

        $this->redirect($newUrl);
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'locales' => config('translation.available_locales', []),
        ]);
    }
}
