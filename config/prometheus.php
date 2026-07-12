<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Prometheus Exporter Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the Prometheus metrics exporter for Laravel.
    |
    */

    'enabled' => env('PROMETHEUS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Metrics Endpoint
    |--------------------------------------------------------------------------
    |
    | The route where Prometheus can scrape metrics from.
    |
    */

    'route' => env('PROMETHEUS_ROUTE', '/metrics'),

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "memory", "redis", "apc", "apcu"
    |
    */

    'storage' => env('PROMETHEUS_STORAGE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | When using the redis storage driver, specify the connection to use.
    |
    */

    'redis_connection' => env('PROMETHEUS_REDIS_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Default Labels
    |--------------------------------------------------------------------------
    |
    | Labels that will be added to all metrics.
    |
    */

    'default_labels' => [
        'app' => env('APP_NAME', 'linscarbon'),
        'env' => env('APP_ENV', 'production'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Collectors
    |--------------------------------------------------------------------------
    |
    | Configure which collectors should be enabled.
    |
    */

    'collectors' => [
        'request_duration' => true,
        'request_count' => true,
        'queue_jobs' => true,
        'database_queries' => true,
        'cache_operations' => true,
        'http_client' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Metrics
    |--------------------------------------------------------------------------
    |
    | Define custom application metrics.
    |
    */

    'metrics' => [
        // Business metrics
        'linscarbon_emissions_calculated_total' => [
            'type' => 'counter',
            'help' => 'Total number of emissions calculated',
            'labels' => ['scope', 'country'],
        ],

        'linscarbon_transactions_processed_total' => [
            'type' => 'counter',
            'help' => 'Total number of transactions processed',
            'labels' => ['status', 'source'],
        ],

        'linscarbon_bank_syncs_total' => [
            'type' => 'counter',
            'help' => 'Total number of bank synchronizations',
            'labels' => ['provider', 'status'],
        ],

        'linscarbon_reports_generated_total' => [
            'type' => 'counter',
            'help' => 'Total number of reports generated',
            'labels' => ['type', 'format'],
        ],

        'linscarbon_api_requests_total' => [
            'type' => 'counter',
            'help' => 'Total API requests',
            'labels' => ['endpoint', 'method', 'status'],
        ],

        // Gauge metrics
        'linscarbon_active_organizations' => [
            'type' => 'gauge',
            'help' => 'Number of active organizations',
            'labels' => ['plan'],
        ],

        'linscarbon_pending_transactions' => [
            'type' => 'gauge',
            'help' => 'Number of transactions pending review',
            'labels' => [],
        ],

        'linscarbon_queue_size' => [
            'type' => 'gauge',
            'help' => 'Current queue size',
            'labels' => ['queue'],
        ],

        // Histogram metrics
        'linscarbon_emission_calculation_duration_seconds' => [
            'type' => 'histogram',
            'help' => 'Duration of emission calculations',
            'labels' => ['scope'],
            'buckets' => [0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10],
        ],

        'linscarbon_report_generation_duration_seconds' => [
            'type' => 'histogram',
            'help' => 'Duration of report generation',
            'labels' => ['type'],
            'buckets' => [1, 5, 10, 30, 60, 120, 300, 600],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Histogram Buckets
    |--------------------------------------------------------------------------
    |
    | Default buckets for histogram metrics.
    |
    */

    'default_buckets' => [
        'request_duration' => [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10],
        'database_query' => [0.001, 0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1],
    ],
];
