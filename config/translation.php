<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    */
    'available_locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
        ],
        'id' => [
            'name' => 'Indonesian',
            'native' => 'Bahasa Indonesia',
            'flag' => '🇮🇩',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    */
    'default_locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Google Translate Configuration
    |--------------------------------------------------------------------------
    */
    'google_translate' => [
        'enabled' => env('GOOGLE_TRANSLATE_ENABLED', true),
        'rate_limit' => env('GOOGLE_TRANSLATE_RATE_LIMIT', 100), // requests per minute
        'cache_translations' => env('GOOGLE_TRANSLATE_CACHE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Storage
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'driver' => 'file', // file or database
        'path' => lang_path(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Content Translation
    |--------------------------------------------------------------------------
    */
    'dynamic_content' => [
        'enabled' => true,
        'table_prefix' => 'translations',
        'cache_duration' => 86400 * 30, // 30 days
    ],
];
