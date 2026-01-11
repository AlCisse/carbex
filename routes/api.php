<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BankConnectionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmissionController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\ApiKeyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API v1 routes for Carbex platform
| All routes are prefixed with /api/v1
|
*/

// Health check (public)
Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

/*
|--------------------------------------------------------------------------
| Guest Routes (unauthenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
        Route::post('/reset-password', [PasswordResetController::class, 'reset']);
        Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware(['signed'])
            ->name('verification.verify');
    });

    // Webhooks (with signature verification - no CSRF)
    Route::prefix('webhooks')->group(function () {
        Route::post('/stripe', [\App\Http\Controllers\Webhooks\StripeWebhookController::class, 'handle']);
        Route::post('/bridge', [\App\Http\Controllers\Webhooks\BankingWebhookController::class, 'handleBridge']);
        Route::post('/finapi', [\App\Http\Controllers\Webhooks\BankingWebhookController::class, 'handleFinapi']);
    });

    // Public emission factors (for reference)
    Route::get('/emission-factors', [CategoryController::class, 'emissionFactors']);
    Route::get('/categories', [CategoryController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {

        // Auth management
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/me', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])
                ->middleware(['throttle:6,1']);
        });

        // Organization management
        Route::prefix('organization')->group(function () {
            Route::get('/', [OrganizationController::class, 'current']);
            Route::put('/', [OrganizationController::class, 'update']);
            Route::get('/stats', [OrganizationController::class, 'stats']);
        });

        // Sites management
        Route::apiResource('sites', SiteController::class);

        // Users management (admin only)
        Route::apiResource('users', UserController::class);
        Route::post('/users/{user}/invite', [UserController::class, 'invite']);
        Route::post('/users/{user}/resend-invite', [UserController::class, 'resendInvite']);

        // Bank connections
        Route::prefix('banking')->group(function () {
            Route::get('/connections', [BankConnectionController::class, 'index']);
            Route::post('/connections/initiate', [BankConnectionController::class, 'initiate']);
            Route::post('/connections/callback', [BankConnectionController::class, 'callback']);
            Route::delete('/connections/{connection}', [BankConnectionController::class, 'destroy']);
            Route::post('/connections/{connection}/sync', [BankConnectionController::class, 'sync']);

            Route::get('/accounts', [BankAccountController::class, 'index']);
            Route::put('/accounts/{account}', [BankAccountController::class, 'update']);
            Route::post('/accounts/{account}/toggle-sync', [BankAccountController::class, 'toggleSync']);
        });

        // Transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::get('/pending-review', [TransactionController::class, 'pendingReview']);
            Route::get('/stats', [TransactionController::class, 'stats']);
            Route::get('/{transaction}', [TransactionController::class, 'show']);
            Route::put('/{transaction}/categorize', [TransactionController::class, 'categorize']);
            Route::put('/{transaction}/validate', [TransactionController::class, 'validate']);
            Route::put('/{transaction}/exclude', [TransactionController::class, 'exclude']);
            Route::post('/bulk-categorize', [TransactionController::class, 'bulkCategorize']);
            Route::post('/import', [TransactionController::class, 'import']);
        });

        // Emissions
        Route::prefix('emissions')->group(function () {
            Route::get('/', [EmissionController::class, 'index']);
            Route::get('/summary', [EmissionController::class, 'summary']);
            Route::get('/by-scope', [EmissionController::class, 'byScope']);
            Route::get('/by-category', [EmissionController::class, 'byCategory']);
            Route::get('/by-site', [EmissionController::class, 'bySite']);
            Route::get('/timeline', [EmissionController::class, 'timeline']);
            Route::get('/comparison', [EmissionController::class, 'comparison']);
        });

        // Dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/kpis', [DashboardController::class, 'kpis']);
            Route::get('/scope-breakdown', [DashboardController::class, 'scopeBreakdown']);
            Route::get('/trends', [DashboardController::class, 'trends']);
            Route::get('/categories', [DashboardController::class, 'categories']);
            Route::get('/sites', [DashboardController::class, 'sites']);
            Route::get('/intensity', [DashboardController::class, 'intensity']);
            Route::post('/cache/invalidate', [DashboardController::class, 'invalidateCache']);
        });

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/', [ReportController::class, 'generate']);
            Route::get('/{report}', [ReportController::class, 'show']);
            Route::get('/{report}/download', [ReportController::class, 'download']);
            Route::delete('/{report}', [ReportController::class, 'destroy']);
        });

        // Energy Connections (Enedis, GRDF)
        Route::prefix('energy')->group(function () {
            Route::get('/connections', [\App\Http\Controllers\Api\EnergyController::class, 'connections']);
            Route::post('/connect', [\App\Http\Controllers\Api\EnergyController::class, 'initiate']);
            Route::post('/callback', [\App\Http\Controllers\Api\EnergyController::class, 'callback']);
            Route::put('/connections/{connection}', [\App\Http\Controllers\Api\EnergyController::class, 'updateConnection']);
            Route::delete('/connections/{connection}', [\App\Http\Controllers\Api\EnergyController::class, 'disconnect']);
            Route::post('/connections/{connection}/sync', [\App\Http\Controllers\Api\EnergyController::class, 'sync']);
            Route::get('/consumption', [\App\Http\Controllers\Api\EnergyController::class, 'consumption']);
            Route::get('/summary', [\App\Http\Controllers\Api\EnergyController::class, 'summary']);
        });

        // Subscription & Billing
        Route::prefix('subscription')->group(function () {
            Route::get('/', [SubscriptionController::class, 'show']);
            Route::get('/plans', [SubscriptionController::class, 'plans']);
            Route::post('/checkout', [SubscriptionController::class, 'checkout']);
            Route::post('/portal', [SubscriptionController::class, 'portal']);
            Route::post('/change-plan', [SubscriptionController::class, 'changePlan']);
            Route::post('/cancel', [SubscriptionController::class, 'cancel']);
            Route::post('/resume', [SubscriptionController::class, 'resume']);
            Route::post('/trial', [SubscriptionController::class, 'startTrial']);
            Route::get('/invoices', [SubscriptionController::class, 'invoices']);
            Route::get('/invoices/{invoice}/download', [SubscriptionController::class, 'downloadInvoice']);
        });

        // API Keys Management
        Route::prefix('api-keys')->group(function () {
            Route::get('/scopes', [ApiKeyController::class, 'scopes']);
            Route::get('/', [ApiKeyController::class, 'index']);
            Route::post('/', [ApiKeyController::class, 'store']);
            Route::get('/{apiKey}', [ApiKeyController::class, 'show']);
            Route::put('/{apiKey}', [ApiKeyController::class, 'update']);
            Route::delete('/{apiKey}', [ApiKeyController::class, 'destroy']);
            Route::post('/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate']);
        });

        // AI Assistant
        Route::prefix('ai')->group(function () {
            // Routes with AI quota check
            Route::middleware('ai.access')->group(function () {
                Route::post('/chat', [\App\Http\Controllers\Api\AIController::class, 'chat']);
            });

            // Routes without quota (no AI consumption)
            Route::get('/providers', [\App\Http\Controllers\Api\AIController::class, 'getProviders']);
            Route::get('/suggestions', [\App\Http\Controllers\Api\AIController::class, 'getSuggestions']);
            Route::get('/conversations', [\App\Http\Controllers\Api\AIController::class, 'listConversations']);
            Route::get('/conversations/{id}', [\App\Http\Controllers\Api\AIController::class, 'getConversation']);
            Route::delete('/conversations/{id}', [\App\Http\Controllers\Api\AIController::class, 'deleteConversation']);
        });

        // Outgoing Webhooks Management
        Route::prefix('webhooks')->name('webhooks.')->group(function () {
            Route::get('/events', [WebhookController::class, 'events']);
            Route::get('/', [WebhookController::class, 'index']);
            Route::post('/', [WebhookController::class, 'store']);
            Route::get('/{webhook}', [WebhookController::class, 'show']);
            Route::put('/{webhook}', [WebhookController::class, 'update']);
            Route::delete('/{webhook}', [WebhookController::class, 'destroy']);
            Route::post('/{webhook}/regenerate-secret', [WebhookController::class, 'regenerateSecret']);
            Route::post('/{webhook}/test', [WebhookController::class, 'test']);
            Route::get('/{webhook}/deliveries', [WebhookController::class, 'deliveries']);
            Route::post('/{webhook}/deliveries/{delivery}/retry', [WebhookController::class, 'retryDelivery']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | API Key Authenticated Routes (External API access)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['api.key', 'api.rate'])->group(function () {
        // Public API endpoints for external integrations
        Route::prefix('external')->group(function () {
            // Read emissions data
            Route::get('/emissions', [EmissionController::class, 'index']);
            Route::get('/emissions/summary', [EmissionController::class, 'summary']);
            Route::get('/emissions/by-scope', [EmissionController::class, 'byScope']);

            // Read organization data
            Route::get('/organization', [OrganizationController::class, 'current']);
            Route::get('/sites', [SiteController::class, 'index']);

            // Read reports
            Route::get('/reports', [ReportController::class, 'index']);
            Route::get('/reports/{report}', [ReportController::class, 'show']);
        });
    });
});
