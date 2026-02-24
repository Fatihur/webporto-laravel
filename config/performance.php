<?php

return [
    'web_vitals' => [
        'budgets' => [
            'LCP' => ['good' => 2500, 'needs_improvement' => 4000],
            'INP' => ['good' => 200, 'needs_improvement' => 500],
            'CLS' => ['good' => 0.1, 'needs_improvement' => 0.25],
        ],
        'retention_days' => 30,
    ],

    'alerts' => [
        'ai_blog_failed_last_hour' => 1,
        'ai_blog_fail_rate_7d_percent' => 25,
    ],

    'cache' => [
        'home' => [
            'featured_projects_fresh' => 900,
            'featured_projects_stale' => 3600,
            'experiences_fresh' => 3600,
            'experiences_stale' => 21600,
            'site_contact_fresh' => 300,
            'site_contact_stale' => 1200,
        ],
        'blog' => [
            'list_default_fresh' => 600,
            'list_default_stale' => 1800,
            'list_filtered_fresh' => 180,
            'list_filtered_stale' => 600,
            'detail_fresh' => 600,
            'detail_stale' => 1800,
            'related_fresh' => 600,
            'related_stale' => 1800,
            'categories_fresh' => 1800,
            'categories_stale' => 21600,
        ],
        'search' => [
            'results_seconds' => 120,
        ],
    ],
];
