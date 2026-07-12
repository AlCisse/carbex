<?php

/**
 * CORS Configuration
 *
 * SECURITY: Strict CORS policy to prevent cross-origin attacks.
 * Only explicitly allowed origins can access the API.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
     * SECURITY: Paths that should have CORS headers applied.
     * Only API routes need CORS - web routes use session-based auth.
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
     * SECURITY: Allowed HTTP methods.
     * Restricting to necessary methods only.
     */
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
     * SECURITY: Allowed origins.
     * In production, this should be a specific list of domains.
     * Use env variable to configure per environment.
     *
     * Examples:
     * - ['https://app.linscarbon.de', 'https://linscarbon.de']
     * - ['*'] for development only (NOT recommended for production)
     */
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', env('APP_URL', 'http://localhost'))),

    /*
     * Patterns for allowed origins (regex support).
     */
    'allowed_origins_patterns' => [],

    /*
     * SECURITY: Allowed request headers.
     * Only headers needed for API communication are allowed.
     */
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-API-Key',
    ],

    /*
     * Headers exposed to the browser.
     */
    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'Retry-After',
    ],

    /*
     * Max age for preflight request cache (in seconds).
     * 24 hours reduces preflight requests.
     */
    'max_age' => 86400,

    /*
     * SECURITY: Whether cookies/credentials can be sent cross-origin.
     * Only enable if needed for stateful SPA authentication.
     */
    'supports_credentials' => true,

];
