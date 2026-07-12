# Developer Quickstart: LinsCarbon MVP Platform

**Feature**: 001-linscarbon-mvp-platform
**Date**: 2025-12-28

---

## Prerequisites

- PHP 8.4+
- Composer 2.x
- Node.js 20+ and npm
- Docker and Docker Compose
- Git

## 1. Clone and Setup

```bash
# Clone repository
git clone git@github.com:linscarbon/linscarbon-app.git
cd linscarbon-app

# Copy environment file
cp .env.example .env

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Generate application key
php artisan key:generate
```

## 2. Docker Services

Start PostgreSQL, Redis, and Meilisearch:

```bash
# Start services
docker compose up -d

# Verify services
docker compose ps
```

**docker-compose.yml** should include:

```yaml
services:
  postgres:
    image: postgres:17
    ports:
      - "5432:5432"
    environment:
      POSTGRES_DB: linscarbon
      POSTGRES_USER: linscarbon
      POSTGRES_PASSWORD: secret
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7.4-alpine
    ports:
      - "6379:6379"

  meilisearch:
    image: getmeili/meilisearch:v1.11
    ports:
      - "7700:7700"
    environment:
      MEILI_MASTER_KEY: masterKey
    volumes:
      - meilisearch_data:/meili_data

volumes:
  postgres_data:
  meilisearch_data:
```

## 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed emission factors and categories
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=EmissionFactorSeeder

# (Optional) Seed test organization with sample data
php artisan db:seed --class=TestDataSeeder
```

## 4. Environment Configuration

Update `.env` with:

```env
# Application
APP_NAME=LinsCarbon
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=linscarbon
DB_USERNAME=linscarbon
DB_PASSWORD=secret

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey

# External Services (use test/sandbox keys)
BRIDGE_CLIENT_ID=test_client_id
BRIDGE_CLIENT_SECRET=test_secret
BRIDGE_SANDBOX=true

FINAPI_CLIENT_ID=test_client_id
FINAPI_CLIENT_SECRET=test_secret
FINAPI_SANDBOX=true

ANTHROPIC_API_KEY=your_claude_api_key

STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=eu-west-3
AWS_BUCKET=linscarbon-dev
TEXTRACT_REGION=eu-west-1

MAIL_MAILER=log
```

## 5. Build Assets

```bash
# Development (with HMR)
npm run dev

# Production build
npm run build
```

## 6. Start Application

```bash
# Start Laravel development server
php artisan serve

# In separate terminal: Start queue worker
php artisan queue:work

# In separate terminal: Start Horizon (queue monitoring)
php artisan horizon
```

Access the application at: http://localhost:8000

## 7. Admin Panel

Filament admin panel is available at: http://localhost:8000/admin

Create a super admin user:

```bash
php artisan make:filament-user
```

## 8. Running Tests

```bash
# All tests
php artisan test

# With coverage
php artisan test --coverage

# Specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Specific test file
php artisan test tests/Feature/Banking/BridgeSyncTest.php

# Browser tests (requires Chrome/Chromium)
php artisan dusk
```

## 9. Code Quality

```bash
# PHPStan static analysis
./vendor/bin/phpstan analyse

# Laravel Pint code style
./vendor/bin/pint

# Check code style without fixing
./vendor/bin/pint --test
```

## 10. Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild Meilisearch index
php artisan scout:flush "App\Models\EmissionFactor"
php artisan scout:import "App\Models\EmissionFactor"

# View queue jobs
php artisan queue:monitor

# Simulate bank sync (with mock data)
php artisan linscarbon:sync-bank --mock

# Import emission factors from ADEME
php artisan linscarbon:import-factors ademe

# Generate OpenAPI documentation
php artisan scramble:export > docs/openapi.json
```

## 11. Mock External Services

For local development without real API credentials:

### Bridge (Open Banking FR)

```php
// config/banking.php
'bridge' => [
    'mock' => env('BRIDGE_MOCK', true),
],
```

When `BRIDGE_MOCK=true`, the BridgeService returns sample transactions from `tests/Fixtures/bridge_transactions.json`.

### Claude AI

```php
// config/services.php
'anthropic' => [
    'mock' => env('ANTHROPIC_MOCK', true),
],
```

When `ANTHROPIC_MOCK=true`, the CategorizationService uses rule-based MCC matching only.

### Stripe

Use Stripe CLI for webhook testing:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

## 12. Project Structure

```
linscarbon-app/
├── app/
│   ├── Console/Commands/      # Artisan commands
│   ├── Http/
│   │   ├── Controllers/Api/   # API controllers
│   │   └── Livewire/          # Livewire components
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic
│   │   ├── Banking/           # Open Banking integrations
│   │   ├── Carbon/            # Emission calculations
│   │   └── AI/                # Claude integration
│   └── Jobs/                  # Queue jobs
├── config/
│   ├── linscarbon.php             # App-specific config
│   ├── countries.php          # Country settings
│   └── banking.php            # Banking providers
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/livewire/        # Livewire blade templates
├── routes/
│   ├── api.php                # API routes
│   └── web.php                # Web routes
└── tests/
    ├── Feature/               # Integration tests
    ├── Unit/                  # Unit tests
    └── Fixtures/              # Test data files
```

## 13. Common Development Tasks

### Add a New Emission Category

1. Add migration for category table update
2. Update `CategorySeeder` with new category
3. Add MCC code mapping in `config/mcc_mapping.php`
4. Update AI prompt template if needed

### Add a New Banking Provider

1. Create provider service implementing `BankingProviderInterface`
2. Add config in `config/banking.php`
3. Register in `BankingServiceProvider`
4. Add OAuth routes and callback handler

### Add a New Report Type

1. Create report builder in `App\Services\Reporting`
2. Add Blade template in `resources/views/pdf/reports`
3. Register in `ReportBuilder` factory
4. Add to `ReportType` enum

## 14. Deployment Checklist

Before deploying to staging/production:

- [ ] Run full test suite
- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false`
- [ ] Configure real API credentials (Bridge, Finapi, Claude, Stripe)
- [ ] Set up Stripe webhooks
- [ ] Configure Cloudflare WAF rules
- [ ] Set up monitoring (Sentry, Prometheus)
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `npm run build`
- [ ] Import production emission factors

## 15. Troubleshooting

### Queue jobs not processing

```bash
# Check Redis connection
redis-cli ping

# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed
```

### Meilisearch not returning results

```bash
# Check health
curl http://localhost:7700/health

# Reindex
php artisan scout:flush "App\Models\EmissionFactor"
php artisan scout:import "App\Models\EmissionFactor"
```

### OAuth callback failing

- Verify `APP_URL` matches callback URL registered with provider
- Check HTTPS requirement (use ngrok for local testing)
- Verify client credentials in `.env`

---

## Resources

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire 3 Documentation](https://livewire.laravel.com/)
- [Filament 3 Documentation](https://filamentphp.com/docs)
- [Bridge API Documentation](https://docs.bridgeapi.io/)
- [Finapi API Documentation](https://docs.finapi.io/)
- [Claude API Documentation](https://docs.anthropic.com/claude/reference)
