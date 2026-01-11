# Carbex - Guide Développeur

## Introduction

Carbex est une plateforme SaaS de comptabilité carbone pour les PME françaises. Ce guide fournit toutes les informations nécessaires pour contribuer au projet.

## Stack Technique

- **Backend**: Laravel 12 + PHP 8.3
- **Frontend**: Livewire 3 + Alpine.js + Tailwind CSS
- **Base de données**: MySQL 8.0
- **Cache**: Redis
- **Recherche**: Meilisearch (optionnel)
- **Files d'attente**: Redis Queue
- **Paiements**: Stripe via Laravel Cashier
- **Tests**: PHPUnit + Laravel Dusk

## Installation

### Prérequis

- Docker & Docker Compose
- Node.js 20+ (pour le build front-end)
- Composer 2.x

### Configuration

```bash
# Cloner le repository
git clone git@github.com:carbex/carbex.git
cd carbex

# Copier la configuration
cp .env.example .env

# Démarrer les conteneurs Docker
docker-compose up -d

# Installer les dépendances PHP
docker-compose exec app composer install

# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Exécuter les migrations
docker-compose exec app php artisan migrate

# Seeder la base de données (développement)
docker-compose exec app php artisan db:seed

# Installer les dépendances Node.js
npm install && npm run build
```

### Accès

- **Application**: http://localhost:8000
- **Documentation API**: http://localhost:8000/docs/api
- **Mailpit (emails)**: http://localhost:8025

## Architecture

### Structure du Projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/           # Contrôleurs API REST
│   │   └── Webhooks/      # Handlers de webhooks
│   ├── Middleware/
│   └── Requests/          # Form Requests
├── Livewire/              # Composants Livewire
│   ├── Dashboard/
│   ├── Emissions/
│   ├── Reports/
│   └── ...
├── Models/                # Modèles Eloquent
├── Services/              # Services métier
│   ├── Banking/           # Intégration bancaire
│   ├── Dashboard/         # KPIs et métriques
│   ├── Emissions/         # Calculs d'émissions
│   └── Reports/           # Génération de rapports
├── Jobs/                  # Jobs asynchrones
└── Events/                # Events et Listeners
```

### Modèles Principaux

| Modèle | Description |
|--------|-------------|
| `Organization` | Entreprise cliente |
| `User` | Utilisateur avec rôle (admin, user) |
| `Site` | Site géographique d'une organisation |
| `Assessment` | Bilan carbone annuel |
| `Category` | Catégorie d'émission (GHG Protocol) |
| `EmissionRecord` | Enregistrement d'émission |
| `EmissionFactor` | Facteur d'émission (tCO2e/unité) |
| `Transaction` | Transaction bancaire |
| `Report` | Rapport généré (BEGES, PDF, Excel) |

### Scopes GHG Protocol

- **Scope 1**: Émissions directes (combustion, véhicules de société)
- **Scope 2**: Émissions indirectes liées à l'énergie (électricité, chauffage)
- **Scope 3**: Autres émissions indirectes (achats, déplacements, fret)

## Développement

### Conventions de Code

- **PSR-12** pour le style PHP
- **Pint** pour le formatage automatique
- **PHPStan** niveau 6 pour l'analyse statique

```bash
# Formater le code
docker-compose exec app ./vendor/bin/pint

# Analyse statique
docker-compose exec app ./vendor/bin/phpstan analyse
```

### Branches Git

- `main`: Production stable
- `develop`: Développement en cours
- `feature/*`: Nouvelles fonctionnalités
- `fix/*`: Corrections de bugs

### Commits

Format recommandé:
```
type(scope): description courte

Description détaillée si nécessaire

Co-Authored-By: Nom <email>
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

## Tests

### Tests Unitaires et Fonctionnels

```bash
# Exécuter tous les tests
docker-compose exec app php artisan test

# Tests en parallèle
docker-compose exec app php artisan test --parallel

# Tests avec couverture
docker-compose exec app php artisan test --coverage
```

### Tests Navigateur (Dusk)

```bash
# Installer ChromeDriver
docker-compose exec app php artisan dusk:chrome-driver

# Exécuter les tests Dusk
docker-compose exec app php artisan dusk
```

### Structure des Tests

```
tests/
├── Feature/           # Tests d'intégration
│   ├── CategoryTest.php
│   ├── EmissionRecordTest.php
│   └── ...
├── Unit/              # Tests unitaires
│   ├── EmissionCalculatorTest.php
│   ├── DashboardServiceTest.php
│   └── ...
└── Browser/           # Tests Dusk (E2E)
    ├── OnboardingTest.php
    ├── DashboardTest.php
    └── ...
```

## API

### Authentification

L'API utilise Laravel Sanctum pour l'authentification:

```bash
# Obtenir un token
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

# Réponse
{
  "token": "1|abc123...",
  "user": {...}
}
```

Utiliser le token dans les requêtes:
```
Authorization: Bearer 1|abc123...
```

### Documentation OpenAPI

La documentation interactive est disponible à `/docs/api` (Scramble).

### Endpoints Principaux

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/v1/emissions` | Liste des émissions |
| GET | `/api/v1/emissions/summary` | Résumé par scope |
| GET | `/api/v1/dashboard` | Données du tableau de bord |
| GET | `/api/v1/reports` | Liste des rapports |
| POST | `/api/v1/reports` | Générer un rapport |

## Services Métier

### EmissionCalculator

Calcule les émissions CO2e à partir des données d'activité:

```php
use App\Services\Emissions\EmissionCalculator;

$calculator = app(EmissionCalculator::class);

// Calcul Scope 1 (carburant)
$co2e = $calculator->calculateScope1([
    'fuel_type' => 'diesel',
    'quantity' => 1000, // litres
    'unit' => 'L',
]);

// Calcul Scope 2 (électricité)
$co2e = $calculator->calculateScope2([
    'quantity' => 50000, // kWh
    'country' => 'FR',
]);

// Calcul Scope 3 (achats)
$co2e = $calculator->calculateScope3([
    'amount' => 10000, // EUR
    'category_code' => '3.1', // Achats de biens
]);
```

### DashboardService

Fournit les KPIs et métriques pour le tableau de bord:

```php
use App\Services\Dashboard\DashboardService;

$service = app(DashboardService::class);

$kpis = $service->getKpis($organizationId, $siteId, $startDate, $endDate);
$breakdown = $service->getScopeBreakdown($organizationId);
$trend = $service->getMonthlyTrend($organizationId);
```

### ReportGenerator

Génère des rapports aux formats PDF, Excel, BEGES:

```php
use App\Services\Reports\ReportGenerator;

$generator = app(ReportGenerator::class);

// Génération asynchrone
GenerateReportJob::dispatch($assessment, 'pdf', $options);

// Génération synchrone
$report = $generator->generate($assessment, [
    'format' => 'pdf',
    'template' => 'beges',
    'language' => 'fr',
]);
```

## Intégrations

### Connexion Bancaire

L'application supporte Bridge et FinAPI pour la synchronisation bancaire:

```php
// Configuration dans .env
BANKING_PROVIDER=bridge
BRIDGE_CLIENT_ID=xxx
BRIDGE_CLIENT_SECRET=xxx
```

### Stripe (Paiements)

Configuration via Laravel Cashier:

```php
// Plans disponibles
- starter: 49€/mois (1 site, 2 utilisateurs)
- pro: 149€/mois (5 sites, 10 utilisateurs)
- enterprise: 399€/mois (illimité)
```

## Jobs et Queues

### Jobs Principaux

| Job | Description |
|-----|-------------|
| `SyncBankTransactionsJob` | Synchronisation bancaire |
| `CalculateEmissionsJob` | Calcul des émissions |
| `GenerateReportJob` | Génération de rapports |
| `SendReminderEmailJob` | Envoi de rappels |

### Exécution des Workers

```bash
# Démarrer le worker
docker-compose exec app php artisan queue:work

# Monitorer les jobs
docker-compose exec app php artisan queue:monitor default
```

## Déploiement

### Variables d'Environnement

Variables requises en production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.carbex.fr

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=carbex
DB_USERNAME=...
DB_PASSWORD=...

REDIS_HOST=...
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Commandes Post-Déploiement

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan queue:restart
```

## Troubleshooting

### Problèmes Courants

**Les émissions ne se calculent pas**
- Vérifier que les facteurs d'émission sont chargés: `php artisan db:seed --class=EmissionFactorSeeder`
- Vérifier les logs: `storage/logs/laravel.log`

**Erreurs de cache**
- Vider le cache: `php artisan cache:clear`
- Régénérer le cache: `php artisan config:cache`

**Jobs bloqués**
- Vérifier Redis: `redis-cli ping`
- Redémarrer les workers: `php artisan queue:restart`

## Contact

- **Équipe technique**: tech@carbex.fr
- **Documentation API**: https://app.carbex.fr/docs/api
- **Support**: support@carbex.fr
