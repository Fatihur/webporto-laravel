<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Newsletter
    |--------------------------------------------------------------------------
    |
    | When enabled, a newsletter will be automatically sent to all active
    | subscribers whenever a new blog post or project is published.
    |
    */
    'auto_send' => env('NEWSLETTER_AUTO_SEND', true),

    /*
    |--------------------------------------------------------------------------
    | Send Delay
    |--------------------------------------------------------------------------
    |
    | Delay in minutes before sending auto-newsletter after content is published.
    | This allows you to cancel if needed.
    |
    */
    'send_delay' => env('NEWSLETTER_SEND_DELAY', 0),

    /*
    |--------------------------------------------------------------------------
    | From Settings
    |--------------------------------------------------------------------------
    |
    | Override the default from address and name for newsletter emails.
    |
    */
    'from' => [
        'address' => env('NEWSLETTER_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'name' => env('NEWSLETTER_FROM_NAME', env('MAIL_FROM_NAME')),
    ],
];
