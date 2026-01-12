# Carbex Development Guidelines

> Carbon footprint SaaS platform for European SMEs
> **Priority Market**: Germany (DE)

Last updated: 2025-01-12

## Tech Stack

- **Backend**: PHP 8.4+ / Laravel 12.x
- **Frontend**: Livewire 3 + Alpine.js + Tailwind CSS 4
- **Database**: MySQL 8.0 / PostgreSQL
- **Cache/Queue**: Redis + Laravel Horizon
- **Search**: Laravel Scout (Meilisearch)
- **Admin**: Filament 3.x
- **Payments**: Laravel Cashier (Stripe)
- **Testing**: PHPUnit + Pest + Playwright (E2E)

## Project Structure

```text
app/
├── Console/Commands/      # Artisan commands
├── Contracts/             # Interfaces
├── Events/                # Event classes
├── Filament/              # Admin panel resources
├── Http/
│   ├── Controllers/       # API controllers (17)
│   ├── Middleware/        # Custom middleware
│   └── Requests/          # Form requests
├── Jobs/                  # Queue jobs
├── Listeners/             # Event listeners
├── Livewire/              # Livewire components (55+)
├── Models/                # Eloquent models (50+)
├── Notifications/         # Notification classes
├── Policies/              # Authorization policies
├── Providers/             # Service providers
└── Services/              # Business logic (60+)

config/                    # Configuration files
database/
├── factories/             # Model factories
├── migrations/            # Database migrations
└── seeders/               # Database seeders

resources/
├── css/                   # Stylesheets
├── js/                    # JavaScript
├── prompts/               # AI prompts
└── views/                 # Blade templates

routes/
├── api.php                # API routes
├── web.php                # Web routes
└── console.php            # Console routes

tests/
├── Browser/               # Dusk browser tests
├── Feature/               # Feature tests
├── Unit/                  # Unit tests
└── Fixtures/              # Test fixtures

lang/
├── de/                    # German translations (PRIMARY)
├── en/                    # English translations
└── fr/                    # French translations
```

## Commands

### Setup & Installation

```bash
# Initial setup (install deps, generate key, migrate, build assets)
composer setup

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Run database seeders
php artisan db:seed

# Full fresh migration with seeders
php artisan migrate:fresh --seed
```

### Development

```bash
# Start all dev services (server, queue, logs, vite)
composer dev

# Start Laravel server only
php artisan serve

# Start Vite dev server only
npm run dev

# Build assets for production
npm run build

# Watch queue jobs
php artisan queue:listen

# Monitor logs in real-time
php artisan pail

# Start Horizon dashboard
php artisan horizon

# Clear all caches
php artisan optimize:clear

# Cache config, routes, views
php artisan optimize
```

### Testing

```bash
# Run all PHP tests
composer test

# Run PHPUnit directly
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run Playwright E2E tests
npm run test:e2e

# Run E2E with UI mode
npm run test:e2e:ui

# Run E2E in headed browser
npm run test:e2e:headed

# Debug E2E tests
npm run test:e2e:debug

# Run Laravel Dusk tests
php artisan dusk
```

### Code Quality

```bash
# Format code with Pint
./vendor/bin/pint

# Run static analysis with PHPStan
./vendor/bin/phpstan analyse

# Check for issues without fixing
./vendor/bin/pint --test
```

### Database

```bash
# Create a new migration
php artisan make:migration create_example_table

# Rollback last migration
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:fresh

# Seed specific seeder
php artisan db:seed --class=TestDataSeeder

# Available seeders
php artisan db:seed --class=AdemeFactorSeeder      # French emission factors
php artisan db:seed --class=UbaFactorSeeder        # German emission factors
php artisan db:seed --class=EuCountryFactorSeeder  # EU country factors
php artisan db:seed --class=MccCategorySeeder      # MCC code mapping
php artisan db:seed --class=CountrySeeder          # Country data
```

### Artisan Generators

```bash
# Create Livewire component
php artisan make:livewire ComponentName

# Create model with migration and factory
php artisan make:model ModelName -mf

# Create controller
php artisan make:controller Api/ExampleController --api

# Create form request
php artisan make:request StoreExampleRequest

# Create job
php artisan make:job ProcessExample

# Create event and listener
php artisan make:event ExampleCreated
php artisan make:listener HandleExampleCreated

# Create policy
php artisan make:policy ExamplePolicy --model=Example

# Create Filament resource
php artisan make:filament-resource Example
```

### API Documentation

```bash
# Generate API docs (Scramble)
php artisan scramble:export

# View API docs at /docs/api
```

### Maintenance

```bash
# Clear specific caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild Meilisearch indexes
php artisan scout:flush "App\Models\Transaction"
php artisan scout:import "App\Models\Transaction"

# Process failed jobs
php artisan queue:retry all

# Prune old data
php artisan model:prune
```

## Code Style

### PHP (PSR-12 + Laravel conventions)

- Use strict types: `declare(strict_types=1);`
- Type hints for parameters and return types
- Use constructor property promotion
- Follow Laravel naming conventions:
  - Models: singular, PascalCase (`EmissionRecord`)
  - Controllers: PascalCase + Controller (`EmissionController`)
  - Migrations: snake_case with timestamp
  - Config keys: snake_case

### Livewire Components

- Full-page components in `app/Livewire/`
- Views in `resources/views/livewire/`
- Use wire:model for two-way binding
- Dispatch events with `$this->dispatch()`

### Translations

- **NO hardcoded text** - German market requires full i18n
- All text must use `__('carbex.section.key')` pattern
- Translation files in `lang/{de,en,fr}/carbex.php`
- German (de) is the primary language

### Database

- Use UUIDs for public-facing IDs
- Soft deletes for important models
- Multi-tenant via `organization_id` foreign key
- Use `BelongsToOrganization` trait for scoping

## Environment Variables

Key environment variables (see `.env.example`):

```env
# Database
DB_CONNECTION=mysql
DB_DATABASE=carbex

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Banking APIs
BRIDGE_CLIENT_ID=
BRIDGE_CLIENT_SECRET=
FINAPI_CLIENT_ID=
FINAPI_CLIENT_SECRET=

# Energy APIs
ENEDIS_CLIENT_ID=
ENEDIS_CLIENT_SECRET=
GRDF_CLIENT_ID=
GRDF_CLIENT_SECRET=

# AI Providers
ANTHROPIC_API_KEY=
OPENAI_API_KEY=
GOOGLE_AI_API_KEY=
DEEPSEEK_API_KEY=

# Payments
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Search
MEILISEARCH_HOST=
MEILISEARCH_KEY=
```

## Key Models

| Model | Purpose |
|-------|---------|
| `Organization` | Multi-tenant root entity |
| `Site` | Physical location |
| `User` | Authentication & authorization |
| `BankConnection` | Open Banking OAuth |
| `Transaction` | Financial records |
| `EmissionRecord` | Carbon calculations |
| `EmissionFactor` | CO2e conversion factors |
| `Subscription` | Stripe billing |
| `Supplier` | Supply chain entities |
| `Report` | Generated documents |

## API Endpoints

Base URL: `/api/v1/`

| Endpoint | Description |
|----------|-------------|
| `/organizations` | Organization CRUD |
| `/sites` | Site management |
| `/emissions` | Emission records |
| `/transactions` | Transaction data |
| `/reports` | Report generation |
| `/bank-connections` | Banking integrations |
| `/energy-connections` | Energy APIs |
| `/suppliers` | Supplier portal |
| `/webhooks` | Webhook configuration |

## Recent Changes

- 2025-01-12: Updated spec.md and constitution.md to v4.0
- 2025-01-11: Added Playwright E2E tests
- 2025-01-10: Full i18n cleanup (no hardcoded text)
- 2025-12-30: Added AI multi-provider support
- 2025-12-29: Implemented supplier portal

<!-- MANUAL ADDITIONS START -->
<!-- MANUAL ADDITIONS END -->
