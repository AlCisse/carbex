# Carbex Developer Guide

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Development Setup](#development-setup)
4. [Code Structure](#code-structure)
5. [Key Features](#key-features)
6. [Testing](#testing)
7. [Contributing](#contributing)

---

## Project Overview

Carbex is a multi-tenant SaaS platform for carbon footprint tracking and management. It enables organizations to:

- Connect bank accounts and automatically categorize transactions
- Calculate GHG emissions (Scope 1, 2, and 3) following the GHG Protocol
- Generate compliance reports (BEGES, CSRD, custom)
- Set and track science-based targets (SBTi)
- Manage supplier emissions data

### Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: React + TypeScript (Inertia.js)
- **Database**: PostgreSQL 15+
- **Cache/Queue**: Redis
- **Search**: Meilisearch (optional)
- **File Storage**: S3-compatible storage

---

## Architecture

### Multi-Tenant Design

Carbex uses a single-database multi-tenant architecture with the `organization_id` column for tenant isolation.

```
┌─────────────────────────────────────────────────────┐
│                    Application                       │
├─────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐ │
│  │   Org A     │  │   Org B     │  │   Org C     │ │
│  │  (Tenant)   │  │  (Tenant)   │  │  (Tenant)   │ │
│  └─────────────┘  └─────────────┘  └─────────────┘ │
├─────────────────────────────────────────────────────┤
│                 Shared Database                      │
│  (organization_id column for isolation)             │
└─────────────────────────────────────────────────────┘
```

### Key Traits

- **BelongsToOrganization**: Automatically scopes queries to the current tenant
- **HasUuids**: All models use UUID primary keys

### Service Layer Pattern

Business logic is organized into service classes:

```
app/Services/
├── Banking/
│   ├── BankConnectionService.php
│   ├── TransactionCategorizationService.php
│   └── Providers/
│       └── BridgeApiClient.php
├── Emissions/
│   ├── EmissionCalculator.php
│   └── EmissionFactorService.php
├── Reports/
│   ├── ReportGenerator.php
│   └── Templates/
├── Suppliers/
│   ├── SupplierInvitationService.php
│   └── SupplierEmissionAggregator.php
└── Trajectory/
    ├── SbtiTargetCalculator.php
    └── RecommendationEngine.php
```

---

## Development Setup

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- PostgreSQL 15+
- Redis

### Installation

```bash
# Clone repository
git clone https://github.com/your-org/carbex.git
cd carbex

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Docker Setup

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Access the application
open http://localhost:8000
```

### Environment Variables

Key environment variables:

```env
# Application
APP_ENV=local
APP_DEBUG=true

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carbex
DB_USERNAME=carbex
DB_PASSWORD=secret

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Banking API (Bridge)
BRIDGE_API_URL=https://api.bridgeapi.io
BRIDGE_CLIENT_ID=your_client_id
BRIDGE_CLIENT_SECRET=your_client_secret

# Stripe (Billing)
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## Code Structure

### Models

```
app/Models/
├── Organization.php      # Tenant model
├── User.php              # User (belongs to org)
├── Site.php              # Physical locations
├── BankConnection.php    # Bank integrations
├── BankAccount.php       # Linked accounts
├── Transaction.php       # Financial transactions
├── Emission.php          # Calculated emissions
├── EmissionFactor.php    # Carbon factors
├── Report.php            # Generated reports
├── Supplier.php          # Supplier data
├── Webhook.php           # Webhook configs
└── ApiKey.php            # API authentication
```

### Controllers

```
app/Http/Controllers/
├── Api/V1/              # REST API controllers
│   ├── AuthController.php
│   ├── EmissionController.php
│   ├── TransactionController.php
│   └── ...
├── Banking/             # Bank connection flows
├── Dashboard/           # Dashboard endpoints
├── External/            # External API (API key auth)
└── Suppliers/           # Supplier portal
```

### Events & Listeners

```
app/Events/
├── EmissionCalculated.php
├── ReportGenerated.php
├── BankSyncCompleted.php
└── TransactionSynced.php

app/Listeners/
├── CalculateTransactionEmissions.php
├── UpdateDashboardCache.php
├── SendEmissionToWebhook.php
└── NotifyUserOfReportCompletion.php
```

### Jobs

```
app/Jobs/
├── SyncBankTransactions.php
├── ProcessPendingEmissions.php
├── GenerateReport.php
├── DispatchWebhook.php
└── SendSupplierReminders.php
```

---

## Key Features

### 1. Bank Transaction Sync

```php
// Initiate connection
$service = new BankConnectionService();
$url = $service->initiateConnection($organization, 'bridge');

// After OAuth callback
$connection = $service->handleCallback($organization, $authCode);

// Sync transactions (runs hourly via scheduler)
$synced = $service->syncTransactions($connection);
```

### 2. Emission Calculation

```php
// Calculate emissions for a transaction
$calculator = new EmissionCalculator();
$emission = $calculator->calculateFromTransaction($transaction);

// Or calculate from activity data
$emission = $calculator->calculate([
    'category' => 'electricity',
    'quantity' => 1500,
    'unit' => 'kWh',
    'country' => 'FR',
]);
```

### 3. Report Generation

```php
// Generate a carbon footprint report
$generator = new ReportGenerator();
$report = $generator->generate([
    'type' => 'carbon_footprint',
    'period_start' => '2024-01-01',
    'period_end' => '2024-12-31',
    'format' => 'pdf',
]);
```

### 4. SBTi Target Calculation

```php
$calculator = new SbtiTargetCalculator();
$targets = $calculator->calculateTargets(
    $organization,
    baseYearEmissions: 1500.0,
    baseYear: 2024,
    targetYear: 2030,
    ambition: SbtiTargetCalculator::AMBITION_1_5C
);
```

### 5. Supplier Portal

```php
// Invite supplier
$service = new SupplierInvitationService();
$invitation = $service->invite($supplier, $requestedYear);

// Public portal accepts data via token
// GET /supplier-portal/{token}
// POST /supplier-portal/{token}/submit
```

---

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/EmissionCalculatorTest.php
```

### Test Structure

```
tests/
├── Feature/
│   ├── Api/
│   │   ├── EmissionApiTest.php
│   │   └── TransactionApiTest.php
│   ├── Banking/
│   │   └── BankConnectionTest.php
│   └── Reports/
│       └── ReportGenerationTest.php
├── Unit/
│   ├── Services/
│   │   └── EmissionCalculatorTest.php
│   └── Models/
│       └── OrganizationTest.php
└── TestCase.php
```

### Factory Pattern

```php
// Create test data
$organization = Organization::factory()->create();
$user = User::factory()->for($organization)->create();
$transaction = Transaction::factory()
    ->for($organization)
    ->categorized()
    ->create();
```

---

## Contributing

### Code Style

Follow PSR-12 coding standards. Use Laravel Pint for formatting:

```bash
./vendor/bin/pint
```

### Git Workflow

1. Create feature branch: `git checkout -b feature/your-feature`
2. Make changes and commit with conventional commits
3. Run tests: `php artisan test`
4. Submit pull request

### Commit Messages

Use conventional commits:

```
feat: add supplier emission aggregation
fix: correct Scope 3 calculation for travel
docs: update API documentation
refactor: extract emission factor service
test: add unit tests for currency converter
```

### Pull Request Checklist

- [ ] Tests pass
- [ ] Code formatted with Pint
- [ ] Documentation updated
- [ ] Migrations are reversible
- [ ] No hardcoded secrets

---

## Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Refresh database
php artisan migrate:fresh --seed

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Queue worker
php artisan queue:work --queue=high,default,low

# Schedule worker (local dev)
php artisan schedule:work

# Generate API documentation
php artisan scribe:generate
```

---

## Support

- **Documentation**: https://docs.carbex.app
- **Issues**: https://github.com/your-org/carbex/issues
- **Email**: dev@carbex.app
