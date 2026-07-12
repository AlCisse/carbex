<?php

namespace App\Providers;

use App\Services\AI\AIManager;
use App\Services\AI\FactorRAGService;
use App\Services\Search\EmbeddingService;
use App\Services\Search\SemanticSearchService;
use App\Services\Search\USearchClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register USearch client as singleton
        $this->app->singleton(USearchClient::class, function ($app) {
            return new USearchClient();
        });

        // Register Embedding service as singleton
        $this->app->singleton(EmbeddingService::class, function ($app) {
            return new EmbeddingService(
                $app->make(USearchClient::class)
            );
        });

        // Register Semantic search service
        $this->app->singleton(SemanticSearchService::class, function ($app) {
            return new SemanticSearchService(
                $app->make(USearchClient::class),
                $app->make(EmbeddingService::class)
            );
        });

        // Register FactorRAGService with all dependencies
        $this->app->singleton(FactorRAGService::class, function ($app) {
            return new FactorRAGService(
                $app->make(AIManager::class),
                $app->make(SemanticSearchService::class),
                $app->make(USearchClient::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Enable strict mode for Eloquent in non-production
        Model::shouldBeStrict(! $this->app->isProduction());

        // Prevent lazy loading in development
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
