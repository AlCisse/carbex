# Data Model: LinsCarbon MVP Platform

**Feature**: 001-linscarbon-mvp-platform
**Updated**: 2025-01-12
**Database**: PostgreSQL 16 / MySQL 8.0
**Reference**: Constitution LinsCarbon v4.0 - German Market Priority

---

## Entity Overview

| Domain | Entities | Count |
|--------|----------|-------|
| **Core Business** | Organization, User, Site, Assessment | 4 |
| **Emissions** | EmissionRecord, EmissionFactor, Category, Activity | 4 |
| **Banking** | BankConnection, BankAccount, Transaction, MerchantRule | 4 |
| **Energy** | EnergyConnection, EnergyConsumption, EnergyBaseline, EnergyTarget | 4 |
| **AI** | AIConversation, AISetting, UploadedDocument | 3 |
| **Suppliers** | Supplier, SupplierInvitation, SupplierProduct, SupplierEmission | 4 |
| **Compliance** | Esrs2Disclosure, ClimateTransitionPlan, DoubleMaterialityAssessment, TaxonomyReport, DueDiligenceAssessment | 5 |
| **Planning** | Action, ReductionTarget | 2 |
| **API** | ApiKey, Webhook, WebhookDelivery | 3 |
| **Auth** | SsoConfiguration, SsoLoginAttempt | 2 |
| **Billing** | Subscription, Invoice | 2 |
| **Reports** | Report, BlogPost | 2 |
| **Total** | | **39** |

---

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              ORGANIZATION (Tenant Root)                          │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                  │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐      │
│  │    User     │    │    Site     │    │ Assessment  │    │Subscription │      │
│  │             │    │             │    │             │    │             │      │
│  │ email       │    │ name        │    │ year        │    │ plan        │      │
│  │ role        │    │ country     │    │ status      │    │ stripe_id   │      │
│  │ 2fa_enabled │    │ floor_area  │    │ revenue     │    │ trial_ends  │      │
│  └──────┬──────┘    └──────┬──────┘    └──────┬──────┘    └─────────────┘      │
│         │                  │                  │                                  │
│         │    ┌─────────────┴─────────────┐    │                                  │
│         │    │                           │    │                                  │
│         ▼    ▼                           ▼    ▼                                  │
│  ┌─────────────┐                  ┌─────────────┐                                │
│  │EmissionRecord│◄────────────────│EmissionFactor│                               │
│  │             │                  │             │                                │
│  │ scope 1/2/3 │                  │ source      │                                │
│  │ category    │                  │ co2e_per_unit│                               │
│  │ quantity    │                  │ country     │                                │
│  │ co2e_kg     │                  │ uncertainty │                                │
│  └─────────────┘                  └─────────────┘                                │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           BANKING (Open Banking PSD2)                    │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │BankConnection│───▶│ BankAccount │───▶│ Transaction │                  │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ provider    │    │ iban        │    │ amount      │                  │    │
│  │  │ bridge/finapi│   │ balance     │    │ mcc_code    │                  │    │
│  │  │ status      │    │ currency    │    │ confidence  │                  │    │
│  │  └─────────────┘    └─────────────┘    └─────────────┘                  │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           ENERGY (Enedis/GRDF + ISO 50001)               │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │EnergyConnection│─▶│EnergyConsumption│  │EnergyBaseline│               │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ provider    │    │ period      │    │ year        │◄──┐              │    │
│  │  │ enedis/grdf │    │ kwh         │    │ total_kwh   │   │              │    │
│  │  │ meter_id    │    │ meter_reading│   │ enpi_value  │   │              │    │
│  │  └─────────────┘    └─────────────┘    └──────┬──────┘   │              │    │
│  │                                               │          │              │    │
│  │                                               ▼          │              │    │
│  │                                        ┌─────────────┐   │              │    │
│  │                                        │EnergyTarget │───┘              │    │
│  │                                        │             │                  │    │
│  │                                        │ target_year │                  │    │
│  │                                        │ reduction_% │                  │    │
│  │                                        └─────────────┘                  │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           SUPPLIERS (Scope 3 Upstream)                   │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │  Supplier   │───▶│SupplierProduct│  │SupplierEmission│               │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ name        │    │ name        │    │ year        │                  │    │
│  │  │ country     │    │ emission_factor│ │ scope_1/2/3 │                  │    │
│  │  │ status      │    │ unit        │    │ total       │                  │    │
│  │  └──────┬──────┘    └─────────────┘    └─────────────┘                  │    │
│  │         │                                                               │    │
│  │         ▼                                                               │    │
│  │  ┌─────────────┐                                                        │    │
│  │  │SupplierInvitation│                                                   │    │
│  │  │             │                                                        │    │
│  │  │ email       │                                                        │    │
│  │  │ token       │                                                        │    │
│  │  │ status      │                                                        │    │
│  │  └─────────────┘                                                        │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           AI (Multi-Provider)                            │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │AIConversation│   │  AISetting  │    │UploadedDocument│               │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ messages[]  │    │ provider    │    │ path        │                  │    │
│  │  │ provider    │    │ claude/gpt4 │    │ extracted_data│                │    │
│  │  │ tokens_used │    │ quotas      │    │ status      │                  │    │
│  │  └─────────────┘    └─────────────┘    └─────────────┘                  │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           COMPLIANCE (CSRD/ISO)                          │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │Esrs2Disclosure│  │ClimateTransitionPlan│ │DoubleMateriality│          │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ requirement │    │ base_year   │    │ year        │                  │    │
│  │  │ content     │    │ targets[]   │    │ impact_matrix│                 │    │
│  │  │ status      │    │ actions[]   │    │ financial_matrix│              │    │
│  │  └─────────────┘    └─────────────┘    └─────────────┘                  │    │
│  │                                                                         │    │
│  │  ┌─────────────┐    ┌─────────────┐                                     │    │
│  │  │TaxonomyReport│   │DueDiligenceAssessment│                            │    │
│  │  │             │    │             │                                     │    │
│  │  │ year        │    │ year        │                                     │    │
│  │  │ turnover_%  │    │ lksg_status │                                     │    │
│  │  │ capex_%     │    │ csddd_status│                                     │    │
│  │  │ opex_%      │    │ risk_score  │                                     │    │
│  │  └─────────────┘    └─────────────┘                                     │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           API & WEBHOOKS                                 │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐                  │    │
│  │  │   ApiKey    │    │   Webhook   │───▶│WebhookDelivery│                │    │
│  │  │             │    │             │    │             │                  │    │
│  │  │ key_hash    │    │ url         │    │ event       │                  │    │
│  │  │ scopes[]    │    │ events[]    │    │ payload     │                  │    │
│  │  │ rate_limit  │    │ secret      │    │ response    │                  │    │
│  │  └─────────────┘    └─────────────┘    └─────────────┘                  │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────────┐    │
│  │                           SSO/SAML                                       │    │
│  ├─────────────────────────────────────────────────────────────────────────┤    │
│  │  ┌─────────────┐    ┌─────────────┐                                     │    │
│  │  │SsoConfiguration│ │SsoLoginAttempt│                                   │    │
│  │  │             │    │             │                                     │    │
│  │  │ idp_url     │    │ user_id     │                                     │    │
│  │  │ certificate │    │ status      │                                     │    │
│  │  │ entity_id   │    │ ip_address  │                                     │    │
│  │  └─────────────┘    └─────────────┘                                     │    │
│  └─────────────────────────────────────────────────────────────────────────┘    │
│                                                                                  │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐      │
│  │   Action    │    │ReductionTarget│  │   Report    │    │   Invoice   │      │
│  │             │    │             │    │             │    │             │      │
│  │ title       │    │ baseline_year│   │ type        │    │ amount      │      │
│  │ status      │    │ target_year │    │ file_path   │    │ stripe_id   │      │
│  │ difficulty  │    │ sbti_aligned│    │ format      │    │ status      │      │
│  └─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘      │
│                                                                                  │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## Core Domain Entities

### Organization

The primary tenant entity representing a company using LinsCarbon.

```php
Schema::create('organizations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('legal_name')->nullable();
    $table->string('slug')->unique();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('postal_code', 20)->nullable();
    $table->char('country', 2)->default('DE');      // German market P0
    $table->string('sector')->nullable();
    $table->unsignedInteger('employee_count')->nullable();
    $table->decimal('revenue', 15, 2)->nullable();
    $table->json('settings')->nullable();
    $table->boolean('csrd_applicable')->default(false);
    $table->string('default_currency', 3)->default('EUR');
    $table->string('fiscal_year_start')->default('01-01');
    $table->string('timezone')->default('Europe/Berlin');
    $table->string('locale', 5)->default('de_DE');
    $table->timestamps();
    $table->softDeletes();

    $table->index(['country', 'sector']);
});
```

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| name | string | Company display name |
| legal_name | string | Official legal name |
| slug | string | URL-friendly identifier |
| country | char(2) | ISO 3166-1 alpha-2 (DE default) |
| sector | string | Industry sector |
| employee_count | uint | Number of employees |
| revenue | decimal | Annual revenue in EUR |
| csrd_applicable | boolean | CSRD reporting required |
| default_currency | char(3) | Default currency (EUR) |

**Relationships**:
- hasMany: User, Site, Assessment, EmissionRecord, BankConnection, EnergyConnection, Supplier, Action, ReductionTarget, Report, ApiKey, Webhook, AIConversation
- hasOne: Subscription, SsoConfiguration, AISetting

---

### User

Platform user with role-based access control.

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->uuid('organization_id');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('first_name');
    $table->string('last_name');
    $table->enum('role', ['owner', 'admin', 'manager', 'viewer'])->default('viewer');
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->boolean('two_factor_enabled')->default(false);
    $table->text('two_factor_secret')->nullable();
    $table->string('locale', 5)->default('de_DE');
    $table->string('timezone')->default('Europe/Berlin');
    $table->timestamp('last_login_at')->nullable();
    $table->string('last_login_ip', 45)->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'role']);
});
```

**Role Permissions**:

| Role | Description | Permissions |
|------|-------------|-------------|
| owner | Organization owner | All permissions, billing, delete org |
| admin | Administrator | User management, all data, settings |
| manager | Team manager | Data entry, reports, no billing |
| viewer | Read-only | View only, no modifications |

---

### Site

Physical location for multi-site organizations.

```php
Schema::create('sites', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('name');
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('postal_code', 20)->nullable();
    $table->char('country', 2);
    $table->decimal('floor_area', 12, 2)->nullable();      // m2
    $table->unsignedInteger('employee_count')->nullable();
    $table->decimal('renewable_percentage', 5, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'is_active']);
});
```

**Relationships**:
- belongsTo: Organization
- hasMany: EmissionRecord, EnergyConnection, EnergyBaseline

---

### Assessment

Annual carbon assessment entity.

```php
Schema::create('assessments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');
    $table->decimal('revenue', 15, 2)->nullable();
    $table->unsignedInteger('employee_count')->nullable();
    $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
    $table->decimal('completeness_score', 5, 2)->nullable();
    $table->json('progress')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'year']);
});
```

**Status Lifecycle**:
```
draft → active (user starts assessment)
active → completed (all categories done)
completed → active (reopened for edits)
```

---

## Emission Entities

### EmissionRecord

Individual emission entry with ISO 14064-1 uncertainty tracking.

```php
Schema::create('emission_records', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('assessment_id')->nullable();
    $table->uuid('site_id')->nullable();
    $table->uuid('emission_factor_id')->nullable();

    // GHG Protocol classification
    $table->tinyInteger('scope');                         // 1, 2, or 3
    $table->tinyInteger('ghg_category');                  // 1-15 for Scope 3
    $table->string('category_code', 10)->nullable();      // "1.1", "3.6", etc.

    // Activity data
    $table->decimal('quantity', 15, 4);
    $table->string('unit', 20);

    // Calculated emissions (ISO 14064-1)
    $table->decimal('co2e_kg', 15, 4);
    $table->decimal('co2_kg', 15, 4)->nullable();
    $table->decimal('ch4_kg', 15, 8)->nullable();
    $table->decimal('n2o_kg', 15, 8)->nullable();
    $table->decimal('uncertainty_percent', 5, 2)->nullable();

    // Factor audit trail
    $table->decimal('factor_value', 15, 10);
    $table->string('factor_unit', 50)->nullable();
    $table->string('factor_source', 50)->nullable();

    // Metadata
    $table->enum('data_source', ['manual', 'banking', 'energy', 'supplier', 'csv_import', 'ai_extraction']);
    $table->enum('calculation_method', ['activity_based', 'spend_based', 'hybrid']);
    $table->enum('data_quality', ['measured', 'calculated', 'estimated'])->default('estimated');
    $table->date('period_start')->nullable();
    $table->date('period_end')->nullable();
    $table->text('notes')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->foreign('emission_factor_id')->references('id')->on('emission_factors')->onDelete('set null');

    $table->index(['organization_id', 'scope', 'ghg_category']);
    $table->index(['assessment_id', 'scope']);
});
```

| Field | Type | Description |
|-------|------|-------------|
| scope | tinyint | GHG Protocol scope (1, 2, 3) |
| ghg_category | tinyint | Scope 3 category (1-15) |
| quantity | decimal | Activity quantity |
| co2e_kg | decimal | Total CO2 equivalent in kg |
| uncertainty_percent | decimal | ISO 14064-1 uncertainty % |
| data_source | enum | Origin of data |
| data_quality | enum | Quality classification |

---

### EmissionFactor

Reference emission factors from multiple sources (ADEME, UBA, etc.).

```php
Schema::create('emission_factors', function (Blueprint $table) {
    $table->uuid('id')->primary();

    // Source identification
    $table->string('source');                             // ademe, uba, ecoinvent, ghg_protocol, custom
    $table->string('source_id')->nullable();
    $table->string('source_url')->nullable();

    // Multilingual names
    $table->string('name');
    $table->string('name_en')->nullable();
    $table->string('name_de')->nullable();
    $table->text('description')->nullable();

    // Factor values (kgCO2e per unit)
    $table->decimal('co2e_per_unit', 15, 10);
    $table->decimal('co2_per_unit', 15, 10)->nullable();
    $table->decimal('ch4_per_unit', 15, 10)->nullable();
    $table->decimal('n2o_per_unit', 15, 10)->nullable();

    // Unit and scope
    $table->string('unit', 20);
    $table->tinyInteger('scope')->nullable();
    $table->string('ghg_category')->nullable();
    $table->string('sector')->nullable();

    // Geographic scope
    $table->string('country', 2)->nullable();
    $table->string('region')->nullable();

    // Uncertainty (ISO 14064-1)
    $table->decimal('uncertainty_percent', 5, 2)->nullable();

    // Validity period
    $table->date('valid_from')->nullable();
    $table->date('valid_to')->nullable();

    // Methodology
    $table->enum('methodology', ['location_based', 'market_based'])->nullable();

    $table->boolean('is_active')->default(true);
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->index(['source', 'country', 'is_active']);
    $table->unique(['source', 'source_id']);
});
```

**Factor Sources**:

| Source | Description | Count |
|--------|-------------|-------|
| ademe | Base Carbone ADEME (France) | ~13,000 |
| uba | Umweltbundesamt (Germany) | ~3,000 |
| ecoinvent | Ecoinvent database | ~5,000 |
| ghg_protocol | GHG Protocol defaults | ~2,000 |
| eu_grid | EU electricity grid factors | ~30 |
| custom | User-created factors | Variable |

---

### Category

GHG Protocol emission categories (15 Scope 3 categories).

```php
Schema::create('categories', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->tinyInteger('scope');
    $table->tinyInteger('ghg_category');                  // 1-15 for Scope 3
    $table->string('code', 10);
    $table->string('name_de');                            // German primary
    $table->string('name_en')->nullable();
    $table->string('name_fr')->nullable();
    $table->text('description')->nullable();
    $table->string('icon')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();

    $table->unique(['scope', 'code']);
});
```

**GHG Protocol Categories**:

| Scope | Cat | Code | Name (DE) |
|-------|-----|------|-----------|
| 1 | - | 1.1 | Stationare Verbrennung |
| 1 | - | 1.2 | Mobile Verbrennung |
| 1 | - | 1.3 | Prozessemissionen |
| 1 | - | 1.4 | Fluchtive Emissionen |
| 1 | - | 1.5 | Landnutzung |
| 2 | - | 2.1 | Eingekaufter Strom |
| 2 | - | 2.2 | Eingekaufte Warme |
| 2 | - | 2.3 | Eingekaufte Kuhlung |
| 3 | 1 | 3.1 | Eingekaufte Waren & Dienstleistungen |
| 3 | 2 | 3.2 | Investitionsguter |
| 3 | 3 | 3.3 | Brennstoff- & energiebezogene Aktivitaten |
| 3 | 4 | 3.4 | Vorgelagerter Transport |
| 3 | 5 | 3.5 | Abfall |
| 3 | 6 | 3.6 | Geschaftsreisen |
| 3 | 7 | 3.7 | Pendeln der Mitarbeiter |
| 3 | 8 | 3.8 | Vorgelagerte gemietete Anlagen |
| 3 | 9 | 3.9 | Nachgelagerter Transport |
| 3 | 10 | 3.10 | Verarbeitung verkaufter Produkte |
| 3 | 11 | 3.11 | Nutzung verkaufter Produkte |
| 3 | 12 | 3.12 | End-of-Life Behandlung |
| 3 | 13 | 3.13 | Nachgelagerte gemietete Anlagen |
| 3 | 14 | 3.14 | Franchises |
| 3 | 15 | 3.15 | Investitionen |

---

## Banking Entities (Open Banking PSD2)

### BankConnection

OAuth connection to Open Banking provider.

```php
Schema::create('bank_connections', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->enum('provider', ['bridge', 'finapi']);       // FR: Bridge, DE: FinAPI
    $table->string('external_id')->nullable();
    $table->text('access_token')->nullable();             // Encrypted
    $table->text('refresh_token')->nullable();            // Encrypted
    $table->timestamp('token_expires_at')->nullable();
    $table->string('bank_name')->nullable();
    $table->enum('status', ['pending', 'active', 'expired', 'revoked', 'error']);
    $table->timestamp('last_sync_at')->nullable();
    $table->json('sync_errors')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['provider', 'external_id']);
});
```

### BankAccount

Individual bank account within a connection.

```php
Schema::create('bank_accounts', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('bank_connection_id');
    $table->string('external_id');
    $table->string('iban', 34)->nullable();
    $table->string('name')->nullable();
    $table->decimal('balance', 15, 2)->nullable();
    $table->char('currency', 3)->default('EUR');
    $table->string('account_type')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('bank_connection_id')->references('id')->on('bank_connections')->onDelete('cascade');
});
```

### Transaction

Financial transaction with AI categorization.

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('bank_account_id')->nullable();
    $table->string('external_id')->nullable();
    $table->date('transaction_date');
    $table->decimal('amount', 15, 2);
    $table->char('currency', 3)->default('EUR');
    $table->string('merchant_name')->nullable();
    $table->string('mcc_code', 4)->nullable();
    $table->text('description')->nullable();
    $table->enum('source', ['bank_sync', 'csv_import', 'manual']);

    // AI Categorization
    $table->uuid('category_id')->nullable();
    $table->decimal('confidence_score', 3, 2)->nullable();
    $table->string('ai_provider')->nullable();

    // Validation
    $table->boolean('validated')->default(false);
    $table->uuid('validated_by')->nullable();
    $table->timestamp('validated_at')->nullable();

    $table->json('raw_data')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'transaction_date']);
    $table->index(['organization_id', 'validated']);
});
```

### MerchantRule

Custom categorization rules for recurring merchants.

```php
Schema::create('merchant_rules', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('merchant_pattern');
    $table->uuid('category_id');
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

---

## Energy Entities (ISO 50001)

### EnergyConnection

Connection to energy provider API (Enedis/GRDF).

```php
Schema::create('energy_connections', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('site_id')->nullable();
    $table->enum('provider', ['enedis', 'grdf']);
    $table->string('external_id')->nullable();
    $table->string('meter_id')->nullable();
    $table->string('contract_id')->nullable();
    $table->text('access_token')->nullable();
    $table->enum('status', ['pending', 'active', 'expired', 'revoked']);
    $table->timestamp('last_sync_at')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
});
```

### EnergyConsumption

Energy consumption data from providers.

```php
Schema::create('energy_consumptions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('energy_connection_id');
    $table->date('period_start');
    $table->date('period_end');
    $table->decimal('consumption_kwh', 15, 4);
    $table->decimal('meter_reading', 15, 4)->nullable();
    $table->enum('energy_type', ['electricity', 'gas']);
    $table->json('raw_data')->nullable();
    $table->timestamps();

    $table->foreign('energy_connection_id')->references('id')->on('energy_connections')->onDelete('cascade');
    $table->index(['energy_connection_id', 'period_start']);
});
```

### EnergyBaseline (ISO 50001)

Energy baseline for ISO 50001 compliance.

```php
Schema::create('energy_baselines', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('site_id');
    $table->year('year');
    $table->decimal('total_consumption_kwh', 15, 4);
    $table->json('normalized_factors')->nullable();       // Temperature, production, etc.
    $table->string('enpi_type')->nullable();              // kWh/m2, kWh/employee, etc.
    $table->decimal('enpi_value', 15, 6)->nullable();
    $table->timestamps();

    $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
    $table->unique(['site_id', 'year']);
});
```

### EnergyTarget (ISO 50001)

Energy reduction targets.

```php
Schema::create('energy_targets', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('energy_baseline_id');
    $table->year('target_year');
    $table->decimal('reduction_percent', 5, 2);
    $table->decimal('enpi_target', 15, 6)->nullable();
    $table->timestamps();

    $table->foreign('energy_baseline_id')->references('id')->on('energy_baselines')->onDelete('cascade');
});
```

---

## Supplier Entities (Scope 3 Upstream)

### Supplier

External supplier in supply chain.

```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('name');
    $table->string('country', 2)->nullable();
    $table->string('contact_name')->nullable();
    $table->string('contact_email')->nullable();
    $table->enum('status', ['pending', 'invited', 'active', 'inactive']);
    $table->decimal('annual_spend', 15, 2)->nullable();
    $table->decimal('emission_factor', 15, 10)->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

### SupplierInvitation

Invitation for suppliers to submit emission data.

```php
Schema::create('supplier_invitations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('supplier_id');
    $table->string('email');
    $table->string('token', 64)->unique();
    $table->enum('status', ['pending', 'sent', 'accepted', 'expired']);
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('expires_at');
    $table->timestamp('accepted_at')->nullable();
    $table->timestamps();

    $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
});
```

### SupplierProduct

Product with emission factor from supplier.

```php
Schema::create('supplier_products', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('supplier_id');
    $table->string('name');
    $table->string('sku')->nullable();
    $table->decimal('emission_factor', 15, 10);
    $table->string('unit', 20);
    $table->timestamps();

    $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
});
```

### SupplierEmission

Annual emission data submitted by supplier.

```php
Schema::create('supplier_emissions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('supplier_id');
    $table->year('year');
    $table->decimal('scope_1', 15, 4)->nullable();
    $table->decimal('scope_2', 15, 4)->nullable();
    $table->decimal('scope_3', 15, 4)->nullable();
    $table->decimal('total_emissions', 15, 4);
    $table->boolean('verified')->default(false);
    $table->timestamps();

    $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
    $table->unique(['supplier_id', 'year']);
});
```

---

## AI Entities (Multi-Provider)

### AIConversation

Chat history with AI assistant.

```php
Schema::create('ai_conversations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->foreignId('user_id')->nullable();
    $table->string('title')->nullable();
    $table->json('messages');                             // Array of {role, content, timestamp}
    $table->string('provider');                           // claude, openai, gemini, deepseek
    $table->string('model')->nullable();
    $table->unsignedInteger('tokens_used')->default(0);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'user_id']);
});
```

### AISetting

Organization AI configuration.

```php
Schema::create('ai_settings', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id')->unique();
    $table->enum('provider', ['claude', 'openai', 'gemini', 'deepseek'])->default('claude');
    $table->string('default_model')->nullable();
    $table->text('api_key')->nullable();                  // Encrypted, for custom keys
    $table->unsignedInteger('daily_quota')->default(50);
    $table->unsignedInteger('monthly_quota')->default(1000);
    $table->decimal('temperature', 2, 1)->default(0.7);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

### UploadedDocument

Document uploaded for AI processing.

```php
Schema::create('uploaded_documents', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->foreignId('user_id')->nullable();
    $table->string('path');
    $table->string('original_name');
    $table->string('mime_type');
    $table->unsignedBigInteger('size');
    $table->enum('type', ['invoice', 'report', 'spreadsheet', 'other']);
    $table->json('extracted_data')->nullable();
    $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
    $table->text('processing_error')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

---

## Compliance Entities (CSRD/ISO)

### Esrs2Disclosure

CSRD ESRS 2 disclosure requirement.

```php
Schema::create('esrs2_disclosures', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');
    $table->string('disclosure_requirement');             // E1-DR1, E1-DR2, etc.
    $table->json('content');
    $table->enum('status', ['draft', 'review', 'approved', 'published']);
    $table->timestamp('verified_at')->nullable();
    $table->string('verifier')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'year', 'disclosure_requirement']);
});
```

### ClimateTransitionPlan

CSRD Climate Transition Plan.

```php
Schema::create('climate_transition_plans', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id')->unique();
    $table->year('base_year');
    $table->year('target_year');
    $table->json('targets');                              // By scope, by year
    $table->json('actions');                              // Planned actions
    $table->json('milestones')->nullable();
    $table->boolean('sbti_committed')->default(false);
    $table->enum('pathway', ['1.5C', '2C', 'well_below_2C'])->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

### DoubleMaterialityAssessment

CSRD Double Materiality Assessment.

```php
Schema::create('double_materiality_assessments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');
    $table->json('impact_matrix');                        // Environmental & social impacts
    $table->json('financial_matrix');                     // Financial risks & opportunities
    $table->json('stakeholder_input')->nullable();
    $table->json('material_topics');                      // Identified material topics
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'year']);
});
```

### TaxonomyReport

EU Taxonomy alignment report for CSRD compliance.

```php
Schema::create('taxonomy_reports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');
    $table->decimal('turnover_aligned_percent', 5, 2)->nullable();
    $table->decimal('capex_aligned_percent', 5, 2)->nullable();
    $table->decimal('opex_aligned_percent', 5, 2)->nullable();
    $table->decimal('turnover_eligible_percent', 5, 2)->nullable();
    $table->decimal('capex_eligible_percent', 5, 2)->nullable();
    $table->decimal('opex_eligible_percent', 5, 2)->nullable();
    $table->json('eligible_activities')->nullable();       // List of eligible activities
    $table->json('aligned_activities')->nullable();        // List of aligned activities
    $table->enum('status', ['draft', 'review', 'approved', 'published'])->default('draft');
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'year']);
});
```

| Field | Type | Description |
|-------|------|-------------|
| turnover_aligned_percent | decimal | % of turnover from taxonomy-aligned activities |
| capex_aligned_percent | decimal | % of CapEx taxonomy-aligned |
| opex_aligned_percent | decimal | % of OpEx taxonomy-aligned |
| eligible_activities | JSON | List of taxonomy-eligible economic activities |
| aligned_activities | JSON | List of fully aligned activities with DNSH criteria |

### DueDiligenceAssessment

LkSG (German Supply Chain Act) and CSDDD (EU Due Diligence) compliance assessment.

```php
Schema::create('due_diligence_assessments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');

    // LkSG (German Supply Chain Due Diligence Act)
    $table->enum('lksg_status', ['not_applicable', 'pending', 'in_progress', 'compliant', 'non_compliant'])->default('not_applicable');
    $table->decimal('lksg_progress_percent', 5, 2)->nullable();
    $table->json('lksg_measures')->nullable();             // Implemented measures

    // CSDDD (EU Corporate Sustainability Due Diligence Directive)
    $table->enum('csddd_status', ['not_applicable', 'pending', 'in_progress', 'compliant', 'non_compliant'])->default('not_applicable');
    $table->decimal('csddd_progress_percent', 5, 2)->nullable();
    $table->json('csddd_measures')->nullable();

    // Risk assessments
    $table->decimal('supplier_risk_score', 5, 2)->nullable();    // 0-100
    $table->json('human_rights_assessment')->nullable();
    $table->json('environmental_assessment')->nullable();
    $table->unsignedInteger('suppliers_assessed')->default(0);
    $table->unsignedInteger('high_risk_suppliers')->default(0);

    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'year']);
});
```

| Field | Type | Description |
|-------|------|-------------|
| lksg_status | enum | German Supply Chain Act compliance status |
| csddd_status | enum | EU Due Diligence Directive status |
| supplier_risk_score | decimal | Overall supply chain risk score (0-100) |
| human_rights_assessment | JSON | Human rights due diligence findings |
| environmental_assessment | JSON | Environmental due diligence findings |
| suppliers_assessed | uint | Number of suppliers assessed |
| high_risk_suppliers | uint | Number of high-risk suppliers identified |

**LkSG/CSDDD Status Values**:

| Status | Description |
|--------|-------------|
| not_applicable | Regulation does not apply to this organization |
| pending | Assessment not yet started |
| in_progress | Due diligence measures being implemented |
| compliant | All requirements met |
| non_compliant | Compliance gaps identified |

---

## Planning Entities

### Action

Reduction action for transition planning.

```php
Schema::create('actions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('category_id')->nullable();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
    $table->date('start_date')->nullable();
    $table->date('due_date')->nullable();
    $table->decimal('estimated_reduction_percent', 5, 2)->nullable();
    $table->decimal('actual_reduction_percent', 5, 2)->nullable();
    $table->decimal('estimated_cost', 15, 2)->nullable();
    $table->decimal('actual_cost', 15, 2)->nullable();
    $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
    $table->unsignedInteger('priority')->default(0);
    $table->foreignId('assigned_to')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'status']);
});
```

### ReductionTarget

SBTi-aligned reduction targets.

```php
Schema::create('reduction_targets', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('baseline_year');
    $table->year('target_year');
    $table->tinyInteger('scope');                         // 1, 2, or 3
    $table->decimal('reduction_percent', 5, 2);
    $table->boolean('sbti_aligned')->default(false);
    $table->enum('pathway', ['1.5C', '2C', 'well_below_2C'])->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique(['organization_id', 'baseline_year', 'target_year', 'scope']);
});
```

**SBTi Recommendations**:
- Scope 1 & 2: Minimum 4.2% annual reduction (1.5C pathway)
- Scope 3: Minimum 2.5% annual reduction
- Net-zero by 2050

---

## API & Webhook Entities

### ApiKey

API access credential.

```php
Schema::create('api_keys', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('name');
    $table->string('key_hash', 64);
    $table->string('key_prefix', 8);                      // For identification
    $table->json('scopes');                               // ['emissions:read', 'reports:write']
    $table->unsignedInteger('rate_limit')->default(100); // Requests per minute
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->unique('key_hash');
});
```

### Webhook

Webhook configuration.

```php
Schema::create('webhooks', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('url');
    $table->json('events');                               // ['emission.created', 'report.generated']
    $table->string('secret', 64);
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_triggered_at')->nullable();
    $table->unsignedInteger('failure_count')->default(0);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

### WebhookDelivery

Webhook delivery attempt log.

```php
Schema::create('webhook_deliveries', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('webhook_id');
    $table->string('event');
    $table->json('payload');
    $table->unsignedSmallInteger('response_code')->nullable();
    $table->text('response_body')->nullable();
    $table->unsignedInteger('duration_ms')->nullable();
    $table->timestamp('delivered_at')->nullable();
    $table->unsignedTinyInteger('attempt')->default(1);
    $table->timestamps();

    $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
    $table->index(['webhook_id', 'created_at']);
});
```

**Webhook Events**:

| Event | Description |
|-------|-------------|
| emission.created | New emission record created |
| emission.updated | Emission record updated |
| assessment.completed | Assessment marked complete |
| report.generated | Report generated |
| transaction.synced | New transactions imported |
| supplier.invited | Supplier invitation sent |
| supplier.responded | Supplier submitted data |

---

## SSO/SAML Entities

### SsoConfiguration

SAML 2.0 SSO configuration.

```php
Schema::create('sso_configurations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id')->unique();
    $table->string('idp_entity_id');
    $table->string('idp_sso_url');
    $table->string('idp_slo_url')->nullable();
    $table->text('idp_certificate');
    $table->string('sp_entity_id');
    $table->json('attribute_mapping');                    // IdP attr → LinsCarbon user fields
    $table->boolean('is_active')->default(false);
    $table->boolean('auto_provision')->default(true);
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

### SsoLoginAttempt

SSO login attempt audit log.

```php
Schema::create('sso_login_attempts', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->foreignId('user_id')->nullable();
    $table->string('email')->nullable();
    $table->enum('status', ['success', 'failed', 'error']);
    $table->string('error_message')->nullable();
    $table->string('ip_address', 45);
    $table->string('user_agent')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'created_at']);
});
```

---

## Billing Entities

### Subscription

Stripe subscription via Laravel Cashier.

```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('stripe_id')->unique();
    $table->string('stripe_status');
    $table->enum('plan', ['trial', 'starter', 'business', 'premium', 'enterprise']);
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('ends_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

**Plan Limits**:

| Plan | Users | Sites | Bank Connections | AI Queries/day |
|------|-------|-------|------------------|----------------|
| Trial | 1 | 1 | 1 | 50 |
| Starter | 3 | 2 | 2 | 50 |
| Business | 10 | 5 | 5 | 100 |
| Premium | Unlimited | Unlimited | 10 | Unlimited |
| Enterprise | Custom | Custom | Custom | Custom |

### Invoice

Billing invoice.

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('subscription_id')->nullable();
    $table->string('stripe_id')->unique();
    $table->decimal('amount', 10, 2);
    $table->char('currency', 3)->default('EUR');
    $table->enum('status', ['draft', 'open', 'paid', 'void', 'uncollectible']);
    $table->string('pdf_url')->nullable();
    $table->date('period_start');
    $table->date('period_end');
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
});
```

---

## Report Entities

### Report

Generated carbon footprint report.

```php
Schema::create('reports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('assessment_id')->nullable();
    $table->foreignId('generated_by');
    $table->enum('type', ['summary', 'detailed', 'beges', 'csrd', 'ghg_protocol', 'ademe', 'iso_14064']);
    $table->enum('format', ['pdf', 'docx', 'xlsx', 'csv']);
    $table->date('period_start');
    $table->date('period_end');
    $table->string('file_path');
    $table->string('file_name');
    $table->unsignedBigInteger('file_size');
    $table->unsignedInteger('download_count')->default(0);
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
    $table->index(['organization_id', 'type']);
});
```

**Report Types**:

| Type | Description | Formats |
|------|-------------|---------|
| summary | Executive summary | PDF, DOCX |
| detailed | Full emission breakdown | PDF, XLSX |
| beges | BEGES format (France) | XLSX, CSV |
| csrd | CSRD/ESRS format | PDF, DOCX |
| ghg_protocol | GHG Protocol format | XLSX |
| ademe | ADEME export | CSV |
| iso_14064 | ISO 14064-1 format | PDF |

---

## Indexes Summary

### Performance-Critical Indexes

| Query Pattern | Index |
|---------------|-------|
| Dashboard by org/year | `emission_records(organization_id, scope, assessment_id)` |
| Transactions by date | `transactions(organization_id, transaction_date)` |
| Unvalidated transactions | `transactions(organization_id, validated)` |
| Factor search | `emission_factors(source, country, is_active)` |
| Energy consumption | `energy_consumptions(energy_connection_id, period_start)` |
| Webhook deliveries | `webhook_deliveries(webhook_id, created_at)` |
| SSO audit | `sso_login_attempts(organization_id, created_at)` |

---

## Data Integrity Rules

1. **Multi-tenancy**: All queries scoped by `organization_id`
2. **Cascading deletes**: Organization deletion cascades to all child entities
3. **Soft deletes**: Organization, User, Site, Subscription use soft deletes
4. **Factor immutability**: EmissionFactor records are append-only
5. **Audit trail**: EmissionRecord stores factor_value at calculation time
6. **Token encryption**: All OAuth tokens encrypted at application level (AES-256)
7. **Unique constraints**: One assessment per org/year, one subscription per org
8. **ISO 14064-1**: Uncertainty tracking on all emission records

---

## Migration Count

| Category | Migrations |
|----------|------------|
| Core (users, orgs, sites) | 6 |
| Emissions | 5 |
| Banking | 4 |
| Energy | 4 |
| Suppliers | 4 |
| AI | 3 |
| Compliance | 5 |
| Planning | 2 |
| API/Webhooks | 3 |
| SSO | 2 |
| Billing | 2 |
| Reports | 2 |
| **Total** | **46** |

---

> **Document generated**: January 2025
> **Database**: PostgreSQL 16 / MySQL 8.0
> **Total Entities**: 39
