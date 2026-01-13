<?php

declare(strict_types=1);

/**
 * uSearch Semantic Search Configuration
 *
 * Configuration for the uSearch vector search microservice
 * used for semantic/AI-powered search in Carbex.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | uSearch API URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the uSearch microservice. In Docker, this will be
    | the service name (usearch). In production, use the full URL.
    |
    */
    'url' => env('USEARCH_URL', 'http://localhost:8001'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | The API key used to authenticate requests to the uSearch microservice.
    | This should be a secure random string in production.
    |
    */
    'api_key' => env('USEARCH_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for uSearch API responses.
    | Semantic search should be fast, so keep this reasonably low.
    |
    */
    'timeout' => env('USEARCH_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait when establishing connection.
    |
    */
    'connect_timeout' => env('USEARCH_CONNECT_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Default Index
    |--------------------------------------------------------------------------
    |
    | The default index to use for searches if none is specified.
    |
    */
    'default_index' => env('USEARCH_DEFAULT_INDEX', 'emission_factors'),

    /*
    |--------------------------------------------------------------------------
    | Indexes Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each vector index used in the application.
    | Each index can have different dimensions based on the embedding model.
    |
    */
    'indexes' => [
        'emission_factors' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
            'description' => 'Emission factors for carbon calculations',
        ],
        'transactions' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
            'description' => 'Bank transactions for categorization',
        ],
        'documents' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
            'description' => 'Uploaded documents for AI extraction',
        ],
        'actions' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
            'description' => 'Reduction actions for recommendations',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Embedding Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for embedding generation. Embeddings can be generated
    | either by the uSearch microservice or locally in Laravel.
    |
    */
    'embeddings' => [
        // Generate embeddings via uSearch microservice (recommended)
        'use_microservice' => env('USEARCH_USE_MICROSERVICE_EMBEDDINGS', true),

        // Provider to use if generating locally (openai, anthropic)
        'provider' => env('USEARCH_EMBEDDING_PROVIDER', 'openai'),

        // Model for embedding generation
        'model' => env('USEARCH_EMBEDDING_MODEL', 'text-embedding-3-small'),

        // Batch size for bulk embedding operations
        'batch_size' => env('USEARCH_EMBEDDING_BATCH_SIZE', 100),

        // Cache embeddings in Redis (reduces API calls)
        'cache_enabled' => env('USEARCH_EMBEDDING_CACHE', true),

        // Cache TTL in seconds (7 days default)
        'cache_ttl' => env('USEARCH_EMBEDDING_CACHE_TTL', 604800),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for semantic search operations.
    |
    */
    'search' => [
        // Default number of results to return
        'default_top_k' => 10,

        // Maximum results allowed
        'max_top_k' => 100,

        // Default minimum similarity score (0-1)
        'default_min_score' => 0.5,

        // Enable hybrid search (combine text + semantic)
        'hybrid_enabled' => env('USEARCH_HYBRID_ENABLED', true),

        // Weight for semantic results in hybrid search (0-1)
        'semantic_weight' => 0.7,

        // Weight for text results in hybrid search (0-1)
        'text_weight' => 0.3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for retrying failed requests to the uSearch microservice.
    |
    */
    'retry' => [
        'times' => env('USEARCH_RETRY_TIMES', 3),
        'sleep_ms' => env('USEARCH_RETRY_SLEEP_MS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure logging for uSearch operations.
    |
    */
    'logging' => [
        'enabled' => env('USEARCH_LOGGING_ENABLED', true),
        'channel' => env('USEARCH_LOG_CHANNEL', 'stack'),
    ],
];
