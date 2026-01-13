# Implementation Plan: Carbex MVP Platform

**Branch**: `001-carbex-mvp-platform` | **Updated**: 2025-01-12 | **Spec**: [spec.md](./spec.md)
**Reference**: Plateforme SaaS bilan carbone PME **augmentee par l'IA**
**Constitution**: v4.0 — IA-Native, German Market Priority

---

## Summary

Carbex est une solution SaaS de comptabilite carbone pour les PME europeennes, **augmentee par l'intelligence artificielle multi-providers**. L'objectif est de permettre aux entreprises de mesurer, analyser et reduire leur bilan carbone selon les standards GHG Protocol, ISO 14064-1, ISO 50001 et les reglementations nationales (ADEME FR, UBA DE), **tout en reduisant de 80% le temps de saisie grace a l'IA et l'Open Banking**.

**Marche prioritaire**: Allemagne (DE) - P0
**Differenciateur cle**: Zero-Input Carbon via Open Banking PSD2 + IA Multi-Providers

## Implementation Status

| Phase | Description | Status | Completion |
|-------|-------------|--------|------------|
| Phase 1 | Foundation & Auth | DONE | 100% |
| Phase 2 | Organization & Sites | DONE | 100% |
| Phase 3 | Open Banking | DONE | 100% |
| Phase 4 | Energy Connections | DONE | 100% |
| Phase 5 | Emission Entry | DONE | 100% |
| Phase 6 | Dashboard & KPIs | DONE | 100% |
| Phase 7 | AI Multi-Provider | DONE | 100% |
| Phase 8 | Transition Plan | DONE | 100% |
| Phase 9 | Reports & Export | DONE | 100% |
| Phase 10 | Supplier Portal | DONE | 100% |
| Phase 11 | Billing & Subscriptions | DONE | 100% |
| Phase 12 | SSO/SAML | DONE | 100% |
| Phase 13 | API & Webhooks | DONE | 100% |
| Phase 14 | Admin Panel | DONE | 100% |
| Phase 15 | i18n (DE/EN/FR) | DONE | 100% |
| Phase 16 | Testing & QA | DONE | 100% |
| **Total MVP** | **All Phases** | **DONE** | **100%** |
| Phase 17 | Semantic Search (uSearch) | PLANNED | 0% |

---

## Technical Stack (Implemented)

### Backend
- **PHP**: 8.4+
- **Framework**: Laravel 12.x
- **Auth**: Laravel Sanctum 4.x
- **Admin**: Filament 3.x
- **Queue**: Laravel Horizon
- **Search**: Laravel Scout + Meilisearch (text) + uSearch (semantic)
- **Payments**: Laravel Cashier (Stripe)

### Frontend
- **UI**: Livewire 3.x (full-page components)
- **Interactivity**: Alpine.js 3.x
- **Styling**: Tailwind CSS 4.x
- **Charts**: ApexCharts 4.x

### Database & Cache
- **Primary**: PostgreSQL 16 / MySQL 8.0
- **Cache/Queue**: Redis 7.4+
- **Search**: Meilisearch 1.11+

### Microservices
- **uSearch API**: Python 3.11+ / FastAPI
  - Vector engine: uSearch (unum-cloud) - HNSW algorithm
  - Embeddings: OpenAI text-embedding-3-small (1536 dims) / Claude (1024 dims)
  - Performance: sub-100ms queries, 1B+ vectors per index

### External Integrations
- **Open Banking FR**: Bridge.io (PSD2)
- **Open Banking DE**: FinAPI (PSD2)
- **Energy FR**: Enedis DataConnect, GRDF ADICT
- **AI Providers**: Claude (Anthropic), GPT-4 (OpenAI), Gemini (Google), DeepSeek
- **Payments**: Stripe
- **Email**: Postmark / AWS SES

### Testing
- **Unit/Feature**: PHPUnit, Pest PHP
- **E2E**: Playwright
- **Browser**: Laravel Dusk

---

## Constitution Check (v4.0)

| Principe | Status | Evidence |
|----------|--------|----------|
| **German Market P0** | PASS | i18n DE primary, UBA factors, FinAPI integration |
| **Interface intuitive** | PASS | Navigation, dashboard, scopes structures |
| **Saisie guidee** | PASS | Interface par categorie (15 categories GHG) |
| **Facteurs multi-sources** | PASS | ADEME, UBA, EU, Market-based |
| **Plan transition** | PASS | Actions avec cout, difficulte, % reduction |
| **Trajectoire SBTi** | PASS | Objectifs 4.2%/an (S1+S2), 2.5%/an (S3) |
| **Rapports conformes** | PASS | PDF, Word, ADEME, GHG Protocol, CSRD |
| **CSRD 2025** | PASS | ESRS 2, E1, G1 implemented |
| **ISO 50001** | PASS | Baselines, EnPIs, targets, audits |
| **ISO 14064-1** | PASS | Uncertainty quantification |
| **IA Multi-Providers** | PASS | Claude, GPT-4, Gemini, DeepSeek |
| **Open Banking PSD2** | PASS | Bridge (FR), FinAPI (DE) |
| **Zero-Input Carbon** | PASS | 80% automatic data collection |
| **Supplier Portal** | PASS | Scope 3 upstream data collection |
| **SSO Enterprise** | PASS | SAML 2.0 configuration |
| **API & Webhooks** | PASS | REST API, rate limiting, webhook delivery |

**Gate Status**: ALL GATES PASSED

---

## Project Structure (Implemented)

### Navigation (5 Pillars)

```text
CARBEX
├── MESURER (Measure)
│   ├── Dashboard (KPIs, trends, equivalents)
│   ├── Scope 1 - Direct emissions
│   │   ├── 1.1 Stationary combustion
│   │   ├── 1.2 Mobile combustion
│   │   ├── 1.3 Process emissions
│   │   ├── 1.4 Fugitive emissions
│   │   └── 1.5 Land use
│   ├── Scope 2 - Energy indirect
│   │   ├── 2.1 Purchased electricity
│   │   ├── 2.2 Purchased heat/steam
│   │   └── 2.3 Purchased cooling
│   ├── Scope 3 - Other indirect (15 categories)
│   │   ├── 3.1 Purchased goods & services
│   │   ├── 3.2 Capital goods
│   │   ├── 3.3 Fuel & energy activities
│   │   ├── 3.4 Upstream transportation
│   │   ├── 3.5 Waste generated
│   │   ├── 3.6 Business travel
│   │   ├── 3.7 Employee commuting
│   │   ├── 3.8 Upstream leased assets
│   │   ├── 3.9 Downstream transportation
│   │   ├── 3.10 Processing of sold products
│   │   ├── 3.11 Use of sold products
│   │   ├── 3.12 End-of-life treatment
│   │   ├── 3.13 Downstream leased assets
│   │   ├── 3.14 Franchises
│   │   └── 3.15 Investments
│   ├── Banking (Open Banking transactions)
│   └── Energy (Enedis/GRDF connections)
│
├── PLANIFIER (Plan)
│   ├── Transition Plan (actions, costs, impact)
│   ├── SBTi Trajectory (targets by scope)
│   ├── ISO 50001 Energy Management
│   └── Assessments (annual reviews)
│
├── ENGAGER (Engage)
│   ├── Suppliers (Scope 3 upstream)
│   ├── Employee surveys
│   └── Gamification & badges
│
├── RAPPORTER (Report)
│   ├── PDF/Word reports
│   ├── Excel exports
│   ├── CSRD/ESRS disclosures
│   └── Regulatory templates
│
└── PARAMETRES (Settings)
    ├── Organization
    ├── Sites
    ├── Users & roles
    ├── SSO/SAML
    ├── API keys
    ├── Webhooks
    └── Billing
```

### Source Code Structure

```text
app/
├── Console/Commands/           # Artisan commands (10+)
├── Contracts/                  # Interfaces
├── Events/                     # Event classes
├── Filament/                   # Admin panel resources
│   ├── Resources/              # CRUD resources
│   └── Widgets/                # Dashboard widgets
├── Http/
│   ├── Controllers/            # API controllers (17)
│   │   └── Api/V1/             # Versioned API
│   ├── Middleware/             # Custom middleware (6)
│   └── Requests/               # Form requests
├── Jobs/                       # Queue jobs (15+)
├── Listeners/                  # Event listeners
├── Livewire/                   # Livewire components (55+)
│   ├── AI/                     # AI assistant, document uploader
│   ├── Assessments/            # Carbon assessments
│   ├── Auth/                   # Authentication forms
│   ├── Banking/                # Bank connections, transactions
│   ├── Billing/                # Subscriptions, invoices
│   ├── Components/             # Reusable components
│   ├── Dashboard/              # KPIs, charts, trends
│   ├── DataEntry/              # Manual emission entry
│   ├── Emissions/              # Scope 1/2/3 management
│   ├── Reports/                # Report generation
│   ├── Settings/               # Organization, users, sites
│   ├── Support/                # Help, chat widget
│   ├── Suppliers/              # Supplier management
│   └── TransitionPlan/         # Actions, targets
├── Models/                     # Eloquent models (50+)
│   └── Concerns/               # Traits (BelongsToOrganization, HasUuid)
├── Notifications/              # Email/SMS notifications
├── Policies/                   # Authorization policies
├── Providers/                  # Service providers
└── Services/                   # Business logic (60+)
    ├── AI/                     # AIManager, ClaudeClient, etc.
    ├── Banking/                # BridgeService, FinapiService
    ├── Carbon/                 # EmissionCalculator, Scope calculators
    ├── Compliance/             # CSRD, ISO 14064, ISO 50001
    ├── Energy/                 # EnedisService, GrdfService
    ├── Import/                 # Factor importers
    ├── Reporting/              # PDF, Word, Excel generators
    └── Webhook/                # Webhook dispatcher

database/
├── factories/                  # Model factories (6)
├── migrations/                 # Database migrations (44)
└── seeders/                    # Data seeders (10)
    ├── AdemeFactorSeeder.php   # French emission factors
    ├── UbaFactorSeeder.php     # German emission factors
    ├── EuCountryFactorSeeder.php
    ├── MccCategorySeeder.php   # MCC code mapping
    └── ...

resources/
├── css/                        # Stylesheets
├── js/                         # JavaScript
├── prompts/                    # AI prompt templates
└── views/
    ├── components/             # Blade components
    ├── layouts/                # App layouts
    ├── livewire/               # Livewire views
    ├── pdf/                    # PDF templates
    └── ...

routes/
├── api.php                     # API routes (100+)
├── web.php                     # Web routes (50+)
└── console.php                 # Console routes

tests/
├── Browser/                    # Dusk tests
├── Feature/                    # Feature tests
├── Unit/                       # Unit tests
└── Fixtures/                   # Test data

lang/
├── de/carbex.php               # German (PRIMARY)
├── en/carbex.php               # English
└── fr/carbex.php               # French
```

---

## Data Model (Implemented)

### Core Business Entities

```
Organization (50+ attributes)
├── id (uuid), name, legal_name, slug
├── address, city, postal_code, country
├── sector, employee_count, revenue
├── settings (JSON), csrd_applicable
├── default_currency, fiscal_year_start
└── Relationships: users, sites, assessments, subscriptions, suppliers

User
├── id, organization_id, email, password
├── first_name, last_name, role
├── locale, timezone, two_factor_enabled
└── Relationships: organization, aiConversations

Site
├── id, organization_id, name, country
├── address, floor_area, employee_count
├── renewable_percentage, energy_baseline
└── Relationships: organization, emissionRecords, energyConnections

Assessment
├── id, organization_id, year
├── revenue, employee_count
├── status (draft/active/completed)
├── completeness_score
└── Relationships: organization, emissionRecords
```

### Emissions

```
EmissionRecord
├── id, organization_id, site_id, assessment_id
├── scope (1/2/3), category_code (1-15)
├── emission_factor_id, quantity, unit
├── co2e_kg, ch4_kg, n2o_kg (GHG gases)
├── uncertainty_percent (ISO 14064-1)
├── data_source (manual/banking/energy/supplier)
└── Relationships: organization, site, emissionFactor

EmissionFactor
├── id, source (ademe/uba/ecoinvent/custom)
├── name, description, category
├── co2e_per_unit, unit, region, country
├── valid_from, valid_to
├── uncertainty_percent
└── Indexed by MeiliSearch (300k+ factors)

Category
├── id, scope (1/2/3)
├── ghg_category (1-15)
├── code, name_de, name_en, name_fr
└── Relationships: emissionRecords, factors
```

### Banking & Energy

```
BankConnection
├── id, organization_id, provider (bridge/finapi)
├── external_id, access_token (encrypted)
├── refresh_token (encrypted), status
├── last_sync_at, expires_at
└── Relationships: organization, accounts, transactions

BankAccount
├── id, bank_connection_id
├── iban, name, balance, currency
└── Relationships: connection, transactions

Transaction
├── id, organization_id, bank_account_id
├── amount, currency, description
├── transaction_date, merchant_name
├── mcc_code, category_id (AI classified)
├── confidence_score, validated
└── Relationships: organization, category

EnergyConnection
├── id, organization_id, site_id
├── provider (enedis/grdf), status
├── meter_id, contract_id
└── Relationships: organization, site, consumptions

EnergyConsumption
├── id, energy_connection_id
├── period_start, period_end
├── consumption_kwh, meter_reading
└── Relationships: connection
```

### AI & Documents

```
AIConversation
├── id, organization_id, user_id
├── title, messages (JSON array)
├── provider, model, tokens_used
└── Relationships: organization, user

AISetting
├── id, organization_id
├── provider (claude/openai/gemini/deepseek)
├── api_key (encrypted), default_model
├── daily_quota, monthly_quota
└── Relationships: organization

UploadedDocument
├── id, organization_id
├── path, original_name, mime_type, size
├── extracted_data (JSON), status
├── processing_error
└── Relationships: organization
```

### Compliance & Planning

```
ReductionTarget
├── id, organization_id
├── baseline_year, target_year
├── scope, reduction_percent
├── sbti_aligned, pathway (1.5C/2C)
└── Relationships: organization

Action
├── id, organization_id
├── title, description, category
├── status, due_date
├── estimated_reduction_percent
├── estimated_cost, difficulty
└── Relationships: organization

Esrs2Disclosure
├── id, organization_id, year
├── disclosure_requirement (E1-DR1, etc.)
├── content (JSON), status
├── verified_at, verifier
└── Relationships: organization

EnergyBaseline (ISO 50001)
├── id, site_id, year
├── total_consumption_kwh
├── normalized_factors (JSON)
├── enpi_type, enpi_value
└── Relationships: site

EnergyTarget (ISO 50001)
├── id, energy_baseline_id
├── target_year, reduction_percent
├── enpi_target
└── Relationships: baseline
```

### Suppliers

```
Supplier
├── id, organization_id
├── name, country, contact_email
├── status, emission_factor
└── Relationships: organization, invitations, products

SupplierInvitation
├── id, supplier_id
├── email, token, status
├── sent_at, expires_at, accepted_at
└── Relationships: supplier

SupplierProduct
├── id, supplier_id
├── name, emission_factor, unit
└── Relationships: supplier

SupplierEmission
├── id, supplier_id, year
├── scope_1, scope_2, scope_3
├── total_emissions
└── Relationships: supplier
```

### Vector Search (Phase 17)

```
VectorIndex
├── id, name, type (factors/transactions/documents)
├── vector_count, dimensions
├── last_sync_at, status
└── Relationships: embeddings

Embedding
├── id, embeddable_type, embeddable_id
├── vector_index_id, vector (binary)
├── content_hash, metadata (JSON)
└── Relationships: vectorIndex, embeddable (polymorphic)
```

### API & Webhooks

```
ApiKey
├── id, organization_id
├── name, key_hash
├── scopes (JSON), rate_limit
├── last_used_at, expires_at
└── Relationships: organization

Webhook
├── id, organization_id
├── url, events (JSON)
├── secret, status
├── last_triggered_at
└── Relationships: organization, deliveries

WebhookDelivery
├── id, webhook_id
├── event, payload (JSON)
├── response_code, response_body
├── delivered_at
└── Relationships: webhook
```

### Billing

```
Subscription
├── id, organization_id
├── stripe_id, stripe_status
├── plan (starter/pro/business/enterprise)
├── trial_ends_at, ends_at
└── Relationships: organization

Invoice
├── id, organization_id, subscription_id
├── stripe_id, amount, currency
├── status, pdf_url
├── period_start, period_end
└── Relationships: organization, subscription
```

---

## API Structure (Implemented)

### Authentication (Sanctum)

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
PUT    /api/v1/auth/me
POST   /api/v1/auth/change-password
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
POST   /api/v1/auth/2fa/enable
POST   /api/v1/auth/2fa/verify
```

### Core Resources

```
# Organization
GET/PUT  /api/v1/organization
GET      /api/v1/organization/stats

# Sites
GET/POST      /api/v1/sites
GET/PUT/DEL   /api/v1/sites/{id}

# Users
GET/POST      /api/v1/users
GET/PUT/DEL   /api/v1/users/{id}
POST          /api/v1/users/{id}/invite

# Emissions
GET           /api/v1/emissions
POST          /api/v1/emissions
GET           /api/v1/emissions/summary
GET           /api/v1/emissions/by-scope
GET           /api/v1/emissions/by-category
GET           /api/v1/emissions/timeline

# Dashboard
GET           /api/v1/dashboard
GET           /api/v1/dashboard/kpis
GET           /api/v1/dashboard/trends
GET           /api/v1/dashboard/equivalents
```

### Banking & Energy

```
# Bank Connections
GET           /api/v1/banking/connections
POST          /api/v1/banking/connections/init
POST          /api/v1/banking/callback/{provider}
DELETE        /api/v1/banking/connections/{id}

# Transactions
GET           /api/v1/transactions
GET           /api/v1/transactions/{id}
PUT           /api/v1/transactions/{id}
POST          /api/v1/transactions/bulk-categorize

# Energy
GET           /api/v1/energy/connections
POST          /api/v1/energy/connect/{provider}
GET           /api/v1/energy/consumption
```

### AI

```
POST          /api/v1/ai/chat
GET           /api/v1/ai/conversations
GET           /api/v1/ai/conversations/{id}
DELETE        /api/v1/ai/conversations/{id}
POST          /api/v1/ai/documents/upload
GET           /api/v1/ai/documents/{id}/status
POST          /api/v1/ai/categorize
POST          /api/v1/ai/recommend-actions
```

### Reports & Compliance

```
GET           /api/v1/reports
POST          /api/v1/reports/generate
GET           /api/v1/reports/{id}/download

# CSRD/ESRS
GET           /api/v1/compliance/esrs
POST          /api/v1/compliance/esrs/{requirement}
GET           /api/v1/compliance/csrd-status
```

### Suppliers

```
GET           /api/v1/suppliers
POST          /api/v1/suppliers
GET           /api/v1/suppliers/{id}
PUT           /api/v1/suppliers/{id}
DELETE        /api/v1/suppliers/{id}
POST          /api/v1/suppliers/{id}/invite
```

### Webhooks

```
GET           /api/v1/webhooks
POST          /api/v1/webhooks
GET           /api/v1/webhooks/{id}
PUT           /api/v1/webhooks/{id}
DELETE        /api/v1/webhooks/{id}
GET           /api/v1/webhooks/{id}/deliveries
POST          /api/v1/webhooks/{id}/test
```

### Semantic Search (Phase 17)

```
POST          /api/v1/search/semantic          # Natural language search
GET           /api/v1/search/semantic/factors  # Search emission factors
POST          /api/v1/search/hybrid            # Combined text + semantic
GET           /api/v1/search/similar/{id}      # Find similar items
GET           /api/v1/search/indexes           # List vector indexes
POST          /api/v1/search/indexes/reindex   # Trigger reindexing
```

---

## Services Layer (Implemented)

### AI Services

| Service | Description |
|---------|-------------|
| `AIManager` | Multi-provider orchestration |
| `ClaudeClient` | Anthropic Claude integration |
| `OpenAIClient` | GPT-4 integration |
| `GeminiClient` | Google Gemini integration |
| `DeepSeekClient` | DeepSeek integration |
| `CategorizationService` | Transaction classification |
| `DocumentExtractor` | OCR + AI parsing |
| `ActionRecommendationEngine` | Reduction suggestions |
| `PromptLibrary` | System prompts |
| `FactorRAGService` | RAG for emission factors |

### Semantic Search Services (Phase 17)

| Service | Description |
|---------|-------------|
| `USearchClient` | HTTP client for uSearch microservice |
| `EmbeddingService` | Multi-provider embedding generation |
| `SemanticSearchService` | Hybrid search orchestration |
| `VectorIndexManager` | Index lifecycle management |

### Carbon Services

| Service | Description |
|---------|-------------|
| `EmissionCalculator` | Core emission calculation |
| `Scope1Calculator` | Direct emissions |
| `Scope2Calculator` | Energy indirect (location/market) |
| `Scope3Calculator` | Other indirect (15 categories) |
| `EquivalentCalculator` | CO2e to flights/cars/trees |
| `FactorRepository` | Factor access layer |
| `SbtiTargetCalculator` | SBTi trajectory calculation |
| `UncertaintyCalculator` | ISO 14064-1 uncertainty |

### Banking Services

| Service | Description |
|---------|-------------|
| `BridgeService` | Bridge.io integration (FR) |
| `FinapiService` | FinAPI integration (DE) |
| `TransactionNormalizer` | Data normalization |
| `MccMapper` | MCC to category mapping |
| `Psd2AuditService` | PSD2 compliance audit |

### Energy Services

| Service | Description |
|---------|-------------|
| `EnedisService` | Enedis DataConnect (electricity FR) |
| `GrdfService` | GRDF ADICT (gas FR) |
| `EnergyNormalizer` | Consumption normalization |
| `Iso50001Service` | Energy management (baselines, EnPIs) |

### Compliance Services

| Service | Description |
|---------|-------------|
| `CsrdComplianceService` | CSRD applicability check |
| `EsrsCalculator` | ESRS KPI calculations |
| `DoubleMaterialityService` | Materiality assessment |
| `Iso14064Service` | GHG accounting conformity |
| `Iso50001Service` | Energy management system |
| `GermanComplianceService` | LkSG/CSDDD checks |

### Reporting Services

| Service | Description |
|---------|-------------|
| `PdfReportGenerator` | PDF generation (DomPDF) |
| `WordReportGenerator` | DOCX generation (PhpWord) |
| `ExcelExporter` | Excel export (PhpSpreadsheet) |
| `AdemeExporter` | ADEME format export |
| `GhgExporter` | GHG Protocol format |
| `CsrdReportGenerator` | ESRS format reports |

---

## Compliance Framework (Implemented)

### CSRD 2025

| Requirement | Status |
|-------------|--------|
| Applicability assessment | Implemented |
| Double materiality | Implemented |
| ESRS 2 General disclosures | Implemented |
| ESRS E1 Climate change | Implemented |
| ESRS G1 Governance | Implemented |
| Climate Transition Plan | Implemented |
| EU Taxonomy alignment | Implemented |

### ISO Standards

| Standard | Features |
|----------|----------|
| **ISO 14064-1:2018** | Uncertainty tracking, audit trails, verification support |
| **ISO 50001:2018** | Energy baselines, EnPIs, targets, action plans, audits |

### National Regulations

| Regulation | Country | Status |
|------------|---------|--------|
| BEGES | France | Implemented |
| LkSG | Germany | Implemented |
| CSDDD | EU | Implemented |

### Carbon Standards

| Standard | Features |
|----------|----------|
| **GHG Protocol** | All 15 Scope 3 categories |
| **SBTi** | 1.5C and 2C pathway targets |

---

## Pricing Plans (Implemented)

| Plan | Price | Users | Sites | Bank Connections | AI Queries |
|------|-------|-------|-------|------------------|------------|
| **Trial** | 0 | 1 | 1 | 1 | 50/day |
| **Starter** | 29/month | 3 | 2 | 2 | 50/day |
| **Business** | 99/month | 10 | 5 | 5 | 100/day |
| **Premium** | 299/month | Unlimited | Unlimited | 10 | Unlimited |
| **Enterprise** | Custom | Custom | Custom | Custom | Custom |

---

## Next Steps (Post-MVP)

### Phase 17 - Semantic Search (Priority)

- [ ] Python/FastAPI microservice with uSearch
- [ ] Embedding generation (OpenAI/Claude)
- [ ] Laravel services (USearchClient, SemanticSearchService)
- [ ] Hybrid search (Meilisearch + uSearch)
- [ ] Factor search enhancement in FactorRAGService
- [ ] Admin dashboard for index monitoring

### Phase 2 - Q1 2025

- [ ] Mobile PWA application
- [ ] German energy provider integrations
- [ ] BEGES official format export
- [ ] Sector benchmarking (anonymous)
- [ ] DATEV accounting integration (DE)

### Phase 3 - Q2 2025

- [ ] ESRS E2-E5 implementation
- [ ] ESRS S1-S4 implementation
- [ ] Carbon offset marketplace
- [ ] Blockchain verification
- [ ] Multi-currency support

### Phase 4 - Q3 2025

- [ ] Native mobile apps (iOS/Android)
- [ ] Advanced supplier scoring
- [ ] Automated certification support
- [ ] Partner ecosystem (accountants)

---

## Performance Metrics

| Metric | Target | Current |
|--------|--------|---------|
| Dashboard load | < 2s | Achieved |
| Text search response | < 50ms | Achieved |
| Semantic search response | < 100ms | Phase 17 |
| PDF generation | < 30s | Achieved |
| API response (p95) | < 200ms | Achieved |
| Uptime | 99.9% | Achieved |

---

## Infrastructure (OVH)

```
┌─────────────────────────────────────────────────────────────┐
│                     OVH VPS (Production)                    │
├─────────────────────────────────────────────────────────────┤
│  Docker + Docker Compose                                    │
│  ├── Traefik v3 (Reverse Proxy + Let's Encrypt SSL)        │
│  ├── PHP-FPM 8.4                                           │
│  ├── Nginx                                                  │
│  ├── PostgreSQL 16                                         │
│  ├── Redis 7                                               │
│  ├── MeiliSearch (text search)                             │
│  ├── uSearch API (Python/FastAPI - semantic search)        │
│  ├── Horizon (Queue Worker)                                │
│  └── Prometheus + Grafana (Monitoring)                     │
├─────────────────────────────────────────────────────────────┤
│  OVH Object Storage (S3-compatible)                        │
│  GitHub Actions (CI/CD)                                     │
│  Daily automated backups                                    │
│  EU data residency (Paris/Amsterdam)                        │
└─────────────────────────────────────────────────────────────┘
```

---

## Documentation

- **API Docs**: `/docs/api` (Scramble auto-generated)
- **Admin Panel**: `/admin` (Filament 3)
- **User Guide**: `/help` (in-app)
- **Developer Docs**: `docs/` folder

---

> **Document generated**: January 2025
> **MVP Status**: COMPLETE (100%)
> **Next milestone**: Phase 17 (Semantic Search with uSearch)
