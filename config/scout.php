<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "meilisearch", "database", "collection", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'algolia'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | This option allows you to control the index prefix that will be used
    | on the underlying model indexes when they are created. You may set
    | a custom prefix here if you wish, however, it must be set prior
    | to model creation, as Scout does not provide a renaming facility.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database Transactions
    |--------------------------------------------------------------------------
    |
    | This configuration option determines if your data will only be synced
    | with your search indexes after every open database transaction has
    | been committed, thus preventing any discarded data from syncing.
    |
    */

    'after_commit' => false,

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options allow you to control the maximum chunk size when you are
    | mass importing data into the search engine. This allows you to fine
    | tune each of these chunk sizes based on the power of the servers.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows to control whether to keep soft deleted records in
    | the search indexes. Maintaining soft deleted records can be useful
    | if your application still needs to search for the records later.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    |
    | This option allows you to control whether to notify the search engine
    | of the user performing the search. This is sometimes useful if the
    | engine supports any analytics based on this application's users.
    |
    | Supported engines: "algolia"
    |
    */

    'identify' => env('SCOUT_IDENTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Algolia configuration. Algolia is a cloud
    | hosted search engine which provides great performance. Just make
    | sure you have the proper application ID and secret configured.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_API_KEY', env('ALGOLIA_SECRET', '')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Meilisearch configuration. Meilisearch is an
    | open source search engine with great performance. Just make sure you
    | have the proper application ID and secret configured.
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY', null),
        'index-settings' => [
            // 'users' => [
            //     'filterableAttributes'=> ['id', 'name', 'email'],
            // ],
        ],
    ],

];
