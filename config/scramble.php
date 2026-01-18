<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     */
    'api_path' => 'api/v1',

    /*
     * Your API domain. By default, app domain is used.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api/docs/openapi.json',

    /*
     * OpenAPI info block
     */
    'info' => [
        'title' => env('APP_NAME', 'LinsCarbon API'),
        'version' => '1.0.0',
        'description' => 'LinsCarbon Carbon Footprint Tracking Platform API.

Track and manage your organization\'s carbon emissions through:
- Bank transaction synchronization
- Energy consumption tracking
- Emissions calculation by scope (1, 2, 3)
- Automated reporting (BEGES, GHG Protocol)
- Multi-site management',
        'contact' => [
            'name' => 'LinsCarbon Support',
            'email' => 'support@linscarbon.app',
        ],
        'license' => [
            'name' => 'Proprietary',
        ],
    ],

    /*
     * The list of servers (URLs) that will be used to make example requests.
     */
    'servers' => null,

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    /*
     * Extensions configuration
     */
    'extensions' => [],

    /*
     * Security schemes
     */
    'security_schemes' => [
        'bearerAuth' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'sanctum',
            'description' => 'Authentication using Laravel Sanctum bearer tokens',
        ],
        'apiKey' => [
            'type' => 'apiKey',
            'in' => 'header',
            'name' => 'X-API-Key',
            'description' => 'API Key for external integrations',
        ],
    ],

    /*
     * Tags for organizing endpoints
     */
    'ui' => [
        'try_it_out' => [
            'enabled' => true,
        ],
    ],
];
