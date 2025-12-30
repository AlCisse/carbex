<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware aliases for subscription features
        $middleware->alias([
            'plan.limit' => \App\Http\Middleware\CheckPlanLimits::class,
            'feature' => \App\Http\Middleware\CheckFeatureAccess::class,
            'subscribed' => \App\Http\Middleware\EnsureActiveSubscription::class,
            'org.scope' => \App\Http\Middleware\EnsureOrganizationScope::class,
            'api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'api.rate' => \App\Http\Middleware\ApiRateLimiter::class,
            'ai.access' => \App\Http\Middleware\CheckAIAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
