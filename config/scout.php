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
    | Supported: "algolia", "meilisearch", "typesense",
    |            "database", "collection", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'meilisearch'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', 'linscarbon_'),

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

    'after_commit' => true,

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
    | if your application still needs to search for the deleted records.
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
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Meilisearch settings. Meilisearch is an open
    | source search engine alternative designed for applications that need
    | powerful, fast, and typo-tolerant searching capabilities.
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            // Emission Factors Index
            \App\Models\EmissionFactor::class => [
                'filterableAttributes' => [
                    'scope',
                    'country',
                    'unit',
                    'source',
                    'category_id',
                    'is_active',
                    'methodology',
                ],
                'sortableAttributes' => [
                    'factor_kg_co2e',
                    'name',
                    'scope',
                    'valid_from',
                    'valid_until',
                ],
                'searchableAttributes' => [
                    'name',
                    'name_en',
                    'name_de',
                    'source_id',
                ],
                'rankingRules' => [
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'sort',
                    'exactness',
                ],
                'typoTolerance' => [
                    'enabled' => true,
                    'minWordSizeForTypos' => [
                        'oneTypo' => 4,
                        'twoTypos' => 8,
                    ],
                ],
                'pagination' => [
                    'maxTotalHits' => 1000,
                ],
            ],

            // Categories Index
            \App\Models\Category::class => [
                'filterableAttributes' => [
                    'scope',
                    'parent_id',
                    'is_active',
                    'ghg_category',
                    'scope_3_category',
                ],
                'sortableAttributes' => [
                    'name',
                    'code',
                    'scope',
                ],
                'searchableAttributes' => [
                    'name',
                    'name_en',
                    'name_de',
                    'code',
                    'description',
                ],
            ],

            // Transactions Index
            \App\Models\Transaction::class => [
                'filterableAttributes' => [
                    'organization_id',
                    'category_id',
                    'type',
                    'is_excluded',
                    'date',
                ],
                'sortableAttributes' => [
                    'date',
                    'amount',
                ],
                'searchableAttributes' => [
                    'description',
                    'clean_description',
                    'counterparty_name',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a cloud hosted
    | search engine which works great with Scout out of the box. Just plug
    | in your application ID and admin API key to get started searching.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Typesense Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Typesense settings. Typesense is an open
    | source search engine designed as an alternative to commercial search
    | engines like Algolia or Elasticsearch. It offers fast and easy setup.
    |
    */

    'typesense' => [
        'client-settings' => [
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'localhost'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'path' => env('TYPESENSE_PATH', ''),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'nearest_node' => [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
            'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
            'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
            'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
            'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
        ],
        'model-settings' => [
            // EmissionFactor::class => [
            //     'collection-schema' => [
            //         'fields' => [
            //             ['name' => 'id', 'type' => 'string'],
            //             ['name' => 'name', 'type' => 'string'],
            //         ],
            //         'default_sorting_field' => 'name',
            //     ],
            //     'search-parameters' => [
            //         'query_by' => 'name',
            //     ],
            // ],
        ],
    ],
];
