# Data Model: Carbex MVP Platform

**Feature**: 001-carbex-mvp-platform
**Date**: 2025-12-30
**Database**: PostgreSQL 17
**Reference**: Constitution Carbex v3.0 - Section 7

---

## Entity Relationship Diagram

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│   Organization  │───┬───│      Site       │       │      User       │
│                 │   │   │                 │       │                 │
│ id (uuid)       │   │   │ id              │       │ id              │
│ name            │   │   │ organization_id │◄──────│ organization_id │
│ legal_name      │   │   │ name            │       │ email           │
│ slug            │   │   │ address         │       │ first_name      │
│ country         │   │   │ country         │       │ last_name       │
│ sector_id       │   │   └─────────────────┘       │ role            │
│ settings (json) │   │                             │ is_active       │
└────────┬────────┘   │                             └─────────────────┘
         │            │
         │            │   ┌─────────────────┐       ┌─────────────────┐
         │            └───│   Assessment    │       │  EmissionRecord │
         │                │     (Bilan)     │───────│                 │
         │                │                 │       │ id              │
         │                │ id              │       │ assessment_id   │
         │                │ organization_id │       │ scope           │
         │                │ year            │       │ ghg_category    │
         │                │ revenue         │       │ quantity        │
         │                │ employee_count  │       │ co2e_kg         │
         │                │ status          │       │ factor_id       │
         │                └─────────────────┘       └────────┬────────┘
         │                                                   │
         │                ┌─────────────────┐                │
         │                │ EmissionFactor  │◄───────────────┘
         │                │                 │
         │                │ id              │
         │                │ source          │
         │                │ name            │
         │                │ factor_kg_co2e  │
         │                │ unit            │
         │                │ country         │
         │                └─────────────────┘
         │
         │                ┌─────────────────┐       ┌─────────────────┐
         ├────────────────│     Action      │       │ ReductionTarget │
         │                │ (Transition)    │       │  (Trajectoire)  │
         │                │                 │       │                 │
         │                │ id              │       │ id              │
         │                │ organization_id │       │ organization_id │
         │                │ title           │       │ baseline_year   │
         │                │ description     │       │ target_year     │
         │                │ status          │       │ scope_1_reduction│
         │                │ due_date        │       │ scope_2_reduction│
         │                │ co2_reduction_%│       │ scope_3_reduction│
         │                │ difficulty      │       └─────────────────┘
         │                └─────────────────┘
         │
         │                ┌─────────────────┐       ┌─────────────────┐
         │                │  BankConnection │       │   Transaction   │
         │                │                 │───────│                 │
         │                │ id              │       │ id              │
         └────────────────│ organization_id │       │ bank_conn_id    │
                          │ provider        │       │ external_id     │
                          │ access_token    │       │ date            │
                          │ status          │       │ amount          │
                          └─────────────────┘       │ mcc_code        │
                                                    └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  Subscription   │       │ EmissionCategory│       │     Report      │
│                 │       │                 │       │                 │
│ id              │       │ id              │       │ id              │
│ organization_id │       │ scope           │       │ organization_id │
│ plan            │       │ code ("1.1")    │       │ type            │
│ status          │       │ name_fr/en/de   │       │ period          │
│ stripe_id       │       │ parent_id       │       │ file_path       │
└─────────────────┘       └─────────────────┘       └─────────────────┘
```

---

## Core Domain Entities (Constitution v3.0)

### Organization

The primary tenant entity representing a company using Carbex.

```php
// app/Models/Organization.php

Schema::create('organizations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');                      // Display name
    $table->string('legal_name')->nullable();    // Raison sociale
    $table->string('slug')->unique();            // URL-friendly identifier
    $table->text('address')->nullable();         // Numéro et rue
    $table->string('city')->nullable();
    $table->string('postal_code', 20)->nullable();
    $table->char('country', 2)->default('FR');   // ISO 3166-1 alpha-2
    $table->uuid('sector_id')->nullable();       // NACE sector reference
    $table->json('settings')->nullable();        // Organization preferences
    $table->string('fiscal_year_start')->default('01-01'); // MM-DD
    $table->string('timezone')->default('Europe/Paris');
    $table->string('locale', 5)->default('fr_FR');
    $table->string('currency', 3)->default('EUR');
    $table->timestamps();
    $table->softDeletes();

    $table->index('country');
    $table->index('slug');
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| id | UUID | auto | Primary key |
| name | string(255) | required, max:255 | Company display name |
| legal_name | string(255) | nullable | Raison sociale officielle |
| slug | string | required, unique | URL identifier |
| country | char(2) | required | ISO country code |
| sector_id | uuid | nullable | Industry sector reference |
| settings | json | nullable | Custom org settings |

**Relationships**:
- hasMany: Site, User, Assessment, Action, ReductionTarget, BankConnection, Report
- hasOne: Subscription

---

### User

Platform user with role-based access control (Constitution v3.0 Section 7).

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                                // bigint auto-increment
    $table->uuid('organization_id');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('first_name');
    $table->string('last_name');
    $table->enum('role', ['owner', 'admin', 'member', 'viewer'])->default('member');
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('last_login_at')->nullable();
    $table->string('last_login_ip', 45)->nullable();
    $table->string('locale', 5)->default('fr_FR');
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->index(['organization_id', 'role']);
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| email | string | required, email, unique | Login identifier |
| first_name | string | required | User's first name |
| last_name | string | required | User's last name |
| role | enum | required | Access level |
| is_active | boolean | default:true | Account status |

**Role Permissions**:

| Role | Description | Permissions |
|------|-------------|-------------|
| owner | Organization owner | All permissions, billing, delete org |
| admin | Administrator | Users management, all data |
| member | Team member | Data entry, view reports |
| viewer | Read-only | View only, no modifications |

---

### Assessment (Bilan)

Annual carbon assessment entity - core of the emission tracking workflow.

```php
Schema::create('assessments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('year');
    $table->decimal('revenue', 15, 2)->nullable();        // Chiffre d'affaires
    $table->unsignedInteger('employee_count')->nullable(); // Nombre de collaborateurs
    $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
    $table->json('progress')->nullable();                  // Completion tracking per scope
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->unique(['organization_id', 'year']);
    $table->index(['organization_id', 'status']);
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| year | year | required | Assessment year (e.g., 2024) |
| revenue | decimal | nullable, min:0 | Annual revenue in EUR |
| employee_count | uint | nullable, min:0 | FTE count |
| status | enum | required | Assessment lifecycle state |
| progress | json | nullable | Per-category completion tracking |

**Status Lifecycle**:
```
draft → active (user starts assessment)
active → completed (all categories done)
completed → active (reopened for edits)
```

**Relationships**:
- belongsTo: Organization
- hasMany: EmissionRecord

---

### EmissionCategory

GHG Protocol emission categories (Constitution v3.0 Section 2.1).

```php
Schema::create('emission_categories', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->tinyInteger('scope');                    // 1, 2, or 3
    $table->string('code', 10);                      // "1.1", "3.3", etc.
    $table->string('name');                          // French default
    $table->string('name_en')->nullable();
    $table->string('name_de')->nullable();
    $table->text('description')->nullable();
    $table->uuid('parent_id')->nullable();           // Self-referential for subcategories
    $table->string('icon')->nullable();              // Icon class for UI
    $table->boolean('is_active')->default(true);
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();

    $table->foreign('parent_id')
        ->references('id')->on('emission_categories')
        ->onDelete('set null');
    $table->unique(['scope', 'code']);
    $table->index('scope');
});
```

**Seeded Categories (Constitution v3.0 Section 2.1)**:

| Scope | Code | Name (FR) |
|-------|------|-----------|
| 1 | 1.1 | Sources fixes de combustion |
| 1 | 1.2 | Sources mobiles de combustion |
| 1 | 1.4 | Emissions fugitives |
| 1 | 1.5 | Biomasse (sols et forets) |
| 2 | 2.1 | Consommation d'electricite |
| 3 | 3.1 | Transport de marchandise amont |
| 3 | 3.2 | Transport de marchandise aval |
| 3 | 3.3 | Deplacements domicile-travail |
| 3 | 3.5 | Deplacements professionnels |
| 3 | 4.1 | Achats de biens |
| 3 | 4.2 | Immobilisations de biens |
| 3 | 4.3 | Gestion des dechets |
| 3 | 4.4 | Actifs en leasing amont |
| 3 | 4.5 | Achats de services |

---

### EmissionRecord (EmissionSource)

Individual emission entry within an assessment category.

```php
Schema::create('emission_records', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('assessment_id')->nullable();
    $table->uuid('site_id')->nullable();
    $table->uuid('emission_factor_id')->nullable();

    // Classification
    $table->tinyInteger('scope');                    // 1, 2, or 3
    $table->string('ghg_category', 10);              // "1.1", "3.3", etc.

    // Activity data
    $table->decimal('quantity', 15, 4);              // Amount consumed
    $table->string('unit', 20);                      // kWh, L, km, EUR, kg

    // Calculated emissions
    $table->decimal('co2e_kg', 15, 4);               // Total CO2e in kg
    $table->decimal('factor_value', 15, 10);         // Factor used (audit trail)
    $table->string('factor_unit', 50)->nullable();   // kgCO2e/kWh, etc.
    $table->string('factor_source', 50)->nullable(); // ademe, uba, custom

    // Metadata
    $table->text('notes')->nullable();
    $table->enum('source_type', ['manual', 'bank_sync', 'csv_import', 'ocr']);
    $table->enum('calculation_method', ['spend_based', 'activity_based', 'hybrid']);
    $table->enum('data_quality', ['measured', 'calculated', 'estimated']);
    $table->boolean('is_estimated')->default(false);
    $table->date('date')->nullable();
    $table->date('period_start')->nullable();
    $table->date('period_end')->nullable();
    $table->enum('status', ['pending', 'completed', 'not_applicable'])->default('pending');
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->foreign('emission_factor_id')
        ->references('id')->on('emission_factors')
        ->onDelete('set null');

    $table->index(['organization_id', 'scope', 'ghg_category']);
    $table->index(['assessment_id', 'scope']);
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| scope | tinyint | in:1,2,3 | GHG Protocol scope |
| ghg_category | string | required | Category code (e.g., "1.1") |
| quantity | decimal | required, min:0 | Activity quantity |
| unit | string | required | Unit of measure |
| co2e_kg | decimal | calculated | Total emissions in kg CO2e |
| factor_value | decimal | required | Factor used for audit |
| status | enum | default:pending | Entry completion status |

**Relationships**:
- belongsTo: Organization, Assessment, Site, EmissionFactor

---

### EmissionFactor

Reference data for emission calculations (Constitution v3.0 Section 2.7).

```php
Schema::create('emission_factors', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('category_id')->nullable();

    // Source identification
    $table->string('source');                        // ademe, uba, ghg_protocol, custom
    $table->string('source_id')->nullable();
    $table->string('source_url')->nullable();

    // Multilingual names
    $table->string('name');                          // Default (French)
    $table->string('name_en')->nullable();
    $table->string('name_de')->nullable();
    $table->text('description')->nullable();

    // Factor values (kgCO2e per unit)
    $table->decimal('factor_kg_co2e', 15, 10);
    $table->decimal('factor_kg_co2', 15, 10)->nullable();
    $table->decimal('factor_kg_ch4', 15, 10)->nullable();
    $table->decimal('factor_kg_n2o', 15, 10)->nullable();

    // Unit
    $table->string('unit');                          // kWh, L, km, EUR, kg, t, m3

    // Uncertainty
    $table->decimal('uncertainty_percent', 5, 2)->nullable();

    // Scope & classification
    $table->tinyInteger('scope')->nullable();        // 1, 2, or 3
    $table->string('ghg_category')->nullable();
    $table->string('sector')->nullable();

    // Geographic scope
    $table->string('country', 2)->nullable();        // ISO country code
    $table->string('region')->nullable();

    // Validity
    $table->date('valid_from')->nullable();
    $table->date('valid_until')->nullable();

    // Methodology
    $table->string('methodology')->nullable();       // location-based, market-based

    // Status
    $table->boolean('is_active')->default(true);

    // Additional data
    $table->json('metadata')->nullable();

    $table->timestamps();

    // Indexes
    $table->index('source');
    $table->index('country');
    $table->index('scope');
    $table->index('unit');
    $table->index('is_active');
    $table->unique(['source', 'source_id']);
});
```

**Sources (Constitution v3.0 Section 2.7)**:

| Source | Description | Count |
|--------|-------------|-------|
| ademe | Base Carbone ADEME 23.7 | ~13,000 |
| uba | Umweltbundesamt (DE) | ~3,000 |
| ghg_protocol | GHG Protocol standards | ~2,000 |
| custom | User-created factors | Variable |

---

### Action (Plan de transition)

Reduction action entity for transition planning (Constitution v3.0 Section 2.8).

```php
Schema::create('actions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('category_id')->nullable();         // Related emission category
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', ['todo', 'in_progress', 'completed'])->default('todo');
    $table->date('due_date')->nullable();
    $table->decimal('co2_reduction_percent', 5, 2)->nullable();  // % reduction target
    $table->decimal('estimated_cost', 15, 2)->nullable();        // EUR
    $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
    $table->unsignedInteger('priority')->default(0);
    $table->uuid('assigned_to')->nullable();         // User ID
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->foreign('assigned_to')
        ->references('id')->on('users')
        ->onDelete('set null');

    $table->index(['organization_id', 'status']);
    $table->index(['organization_id', 'due_date']);
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| title | string | required | Action title |
| status | enum | required | Current state |
| due_date | date | nullable | Target completion date |
| co2_reduction_percent | decimal | 0-100 | Expected impact |
| estimated_cost | decimal | nullable | Implementation cost in EUR |
| difficulty | enum | required | Implementation complexity |

**Relationships**:
- belongsTo: Organization, User (assigned_to)

---

### ReductionTarget (Trajectoire SBTi)

Reduction targets aligned with SBTi recommendations (Constitution v3.0 Section 2.9).

```php
Schema::create('reduction_targets', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->year('baseline_year');                   // Reference year
    $table->year('target_year');                     // Target achievement year
    $table->decimal('scope_1_reduction', 5, 2);      // % reduction target
    $table->decimal('scope_2_reduction', 5, 2);
    $table->decimal('scope_3_reduction', 5, 2);
    $table->boolean('is_sbti_aligned')->default(false);
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');

    $table->unique(['organization_id', 'baseline_year', 'target_year']);
    $table->index('organization_id');
});
```

| Field | Type | Validation | Notes |
|-------|------|------------|-------|
| baseline_year | year | required | Reference year for calculation |
| target_year | year | required, after:baseline_year | Goal year |
| scope_1_reduction | decimal | 0-100 | SBTi recommends 4.2%/year |
| scope_2_reduction | decimal | 0-100 | SBTi recommends 4.2%/year |
| scope_3_reduction | decimal | 0-100 | SBTi recommends 2.5%/year |

**SBTi Recommendations** (Constitution v3.0 Section 2.9):
- Scope 1 & 2: Minimum 4.2% annual reduction
- Scope 3: Minimum 2.5% annual reduction
- Aligned with Paris Agreement 1.5C target

---

## Implementation Entities

### Site

Physical location for multi-site organizations.

```php
Schema::create('sites', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('name');
    $table->text('address')->nullable();
    $table->char('country', 2);
    $table->string('city')->nullable();
    $table->string('postal_code', 20)->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->decimal('surface_m2', 12, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->index(['organization_id', 'is_active']);
});
```

---

### BankConnection

OAuth connection to Open Banking provider for automatic transaction import.

```php
Schema::create('bank_connections', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('provider');                      // bridge, finapi
    $table->string('provider_connection_id')->nullable();
    $table->text('access_token')->nullable();        // Encrypted
    $table->text('refresh_token')->nullable();       // Encrypted
    $table->timestamp('token_expires_at')->nullable();
    $table->string('bank_name')->nullable();
    $table->string('account_iban', 34)->nullable();  // Masked
    $table->enum('status', ['pending', 'active', 'expired', 'revoked']);
    $table->timestamp('last_sync_at')->nullable();
    $table->json('sync_errors')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->index(['organization_id', 'status']);
    $table->unique(['provider', 'provider_connection_id']);
});
```

---

### Transaction

Financial transaction imported from bank or manual entry.

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('bank_connection_id')->nullable();
    $table->string('external_id')->nullable();
    $table->date('date');
    $table->decimal('amount', 15, 2);                // Negative = expense
    $table->char('currency', 3)->default('EUR');
    $table->string('merchant_name')->nullable();
    $table->string('mcc_code', 4)->nullable();       // Merchant Category Code
    $table->text('description')->nullable();
    $table->enum('source', ['bank_sync', 'csv_import', 'manual']);
    $table->uuid('category_id')->nullable();
    $table->decimal('confidence', 3, 2)->nullable(); // AI categorization 0.00-1.00
    $table->boolean('validated')->default(false);
    $table->uuid('validated_by')->nullable();
    $table->timestamp('validated_at')->nullable();
    $table->json('raw_data')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->index(['organization_id', 'date']);
    $table->index(['organization_id', 'validated']);
});
```

---

### Subscription

Billing subscription managed via Stripe (Constitution v3.0 Section 3).

```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->string('stripe_id')->unique();
    $table->string('stripe_customer_id');
    $table->enum('plan', ['gratuit', 'premium', 'avance', 'enterprise']);
    $table->enum('status', ['trialing', 'active', 'past_due', 'canceled', 'unpaid']);
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('current_period_start');
    $table->timestamp('current_period_end');
    $table->timestamp('canceled_at')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
});
```

**Plan Limits (Constitution v3.0 Section 3.2)**:

| Plan | Prix | Users | Sites | Features |
|------|------|-------|-------|----------|
| Gratuit | 0 | 1 | 1 | Essai 15 jours |
| Premium | 400/an | 5 | 3 | Reporting, Dashboard, Export |
| Avance | 1200/an | illimite | illimite | Multi-entites, Support prioritaire |
| Enterprise | Devis | illimite | illimite | Sur-mesure, SLA |

---

### Report

Generated carbon footprint report (Constitution v3.0 Section 2.11).

```php
Schema::create('reports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('organization_id');
    $table->uuid('assessment_id')->nullable();
    $table->uuid('generated_by');
    $table->enum('type', ['summary_pdf', 'beges', 'csrd', 'ghg_protocol', 'ademe']);
    $table->date('period_start');
    $table->date('period_end');
    $table->string('file_path');
    $table->string('file_name');
    $table->unsignedBigInteger('file_size');
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->foreign('organization_id')
        ->references('id')->on('organizations')
        ->onDelete('cascade');
    $table->index(['organization_id', 'type']);
});
```

**Report Types**:

| Type | Description | Format |
|------|-------------|--------|
| summary_pdf | Bilan complet des emissions | PDF/Word |
| beges | Tableau declaration ADEME | Excel |
| csrd | Rapport CSRD/ESG | PDF |
| ghg_protocol | GHG Protocol format | Excel |
| ademe | Export bilans.ges.ademe.fr | CSV |

---

## Indexes Summary

### Performance-Critical Queries

| Query Pattern | Index |
|---------------|-------|
| Dashboard totals by org/year | `emission_records(organization_id, scope, assessment_id)` |
| Assessment completion | `emission_records(assessment_id, scope, ghg_category)` |
| Factor lookup | `emission_factors(source, country, scope, is_active)` |
| Action tracking | `actions(organization_id, status, due_date)` |
| Trajectory comparison | `reduction_targets(organization_id, baseline_year)` |
| Transaction validation | `transactions(organization_id, validated)` |

---

## Data Integrity Rules

1. **Cascading deletes**: Organization deletion cascades to all child entities
2. **Soft deletes**: Organization, Site, User use soft deletes for audit trail
3. **Factor immutability**: EmissionFactor records are append-only (new versions, not updates)
4. **Audit trail**: EmissionRecord stores factor_value at calculation time
5. **Token encryption**: BankConnection tokens encrypted at application level
6. **Unique assessments**: One assessment per organization per year
7. **Status constraints**: Assessment status transitions are validated

---

## Entity Summary

| Entity | Purpose | Constitution Ref |
|--------|---------|------------------|
| Organization | Tenant company | Section 7 |
| User | Platform user | Section 7 |
| Assessment | Annual carbon bilan | Section 7, 2.10 |
| EmissionCategory | GHG categories (1.1, 3.3...) | Section 7, 2.1 |
| EmissionRecord | Individual emission entry | Section 7 (EmissionSource) |
| EmissionFactor | Reference emission factors | Section 7, 2.7 |
| Action | Transition plan actions | Section 7, 2.8 |
| ReductionTarget | SBTi trajectory targets | Section 7, 2.9 |
| Site | Physical location | Implementation |
| BankConnection | Open Banking OAuth | Implementation |
| Transaction | Financial transaction | Implementation |
| Subscription | Billing (Stripe) | Section 3 |
| Report | Generated reports | Section 2.11 |
