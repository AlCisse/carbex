# LinsCarbon — Constitution du Projet

> **Document fondateur v4.0 — Janvier 2025**
> Plateforme SaaS de bilan carbone pour PME **augmentee par l'IA**
> **Marche prioritaire : Allemagne (DE)**

---

## 1. Vision et Objectifs

### 1.1 Mission

LinsCarbon est une plateforme SaaS permettant aux PME europeennes de realiser leur bilan carbone de maniere guidee et structuree selon les standards GHG Protocol, ISO 14064 et les reglementations nationales (ADEME FR, UBA DE). **Notre differenciateur cle : l'integration native d'un assistant IA multi-providers qui reduit de 80% le temps de saisie et offre des recommandations personnalisees.**

### 1.2 Objectifs

1. **Offrir une interface intuitive** pour la comptabilite carbone PME
2. **Guider l'utilisateur** a travers les 3 scopes d'emissions (15 categories GHG)
3. **Calculer automatiquement** les emissions avec facteurs multi-sources
4. **Generer des rapports** conformes (BEGES, CSRD 2025, GHG Protocol, ISO 14064-1)
5. **Augmenter par l'IA** : Assistant intelligent multi-providers (Claude, GPT-4, Gemini, DeepSeek)
6. **Automatiser la collecte** : Open Banking PSD2 (Bridge FR, FinAPI DE) + Energie (Enedis, GRDF)

### 1.3 Marches Cibles

| Marche | Priorite | Reglementation | Facteurs |
|--------|----------|----------------|----------|
| **Allemagne** | P0 | CSRD, LkSG | UBA |
| France | P1 | BEGES, CSRD | ADEME |
| UK | P2 | SECR | GHG Protocol |

---

## 2. Etat d'Implementation (Janvier 2025)

### 2.1 Statistiques Globales

| Composant | Implemente |
|-----------|------------|
| Modeles Eloquent | 50+ |
| Controllers API | 17 |
| Composants Livewire | 55+ |
| Services | 60+ |
| Migrations DB | 44 |
| Routes Web | 50+ |
| Routes API | 100+ |

### 2.2 Fonctionnalites Implementees

#### Core (100%)
- [x] Authentification multi-tenant (Sanctum)
- [x] Gestion Organisation/Sites/Users
- [x] Roles (owner, admin, manager, viewer)
- [x] Invitations utilisateurs
- [x] 2FA support
- [x] SSO/SAML configuration
- [x] API Keys avec scopes

#### Bilan Carbone (100%)
- [x] Dashboard complet (KPIs, tendances, comparaisons)
- [x] Scope 1/2/3 - 15 categories GHG Protocol
- [x] Saisie manuelle activites
- [x] Import CSV/Excel/FEC
- [x] Base facteurs multi-sources (ADEME, UBA, Ecoinvent)
- [x] Calcul emissions avec incertitudes (ISO 14064-1)
- [x] Equivalents carbone (vols, voitures, arbres)

#### Open Banking (100%)
- [x] Bridge.io (France) - PSD2 compliant
- [x] FinAPI (Allemagne) - PSD2 compliant
- [x] Sync automatique transactions
- [x] Webhooks temps reel
- [x] Categorisation IA par code MCC
- [x] Validation utilisateur

#### Energie (100%)
- [x] Enedis (electricite France)
- [x] GRDF (gaz France)
- [x] Donnees consommation time-series
- [x] ISO 50001 compliance (baselines, targets, audits)

#### IA Multi-Providers (100%)
- [x] Claude (Anthropic) - provider par defaut
- [x] GPT-4 (OpenAI)
- [x] Gemini (Google)
- [x] DeepSeek
- [x] Chat conversationnel
- [x] Extraction documents (OCR)
- [x] Categorisation transactions
- [x] Recommandations actions
- [x] Generation narratifs rapports
- [x] Quotas par plan

#### Fournisseurs Scope 3 (100%)
- [x] Gestion fournisseurs
- [x] Systeme invitations
- [x] Portail fournisseur public
- [x] Collecte donnees emissions
- [x] Agregation emissions

#### Plan de Transition (100%)
- [x] Actions avec cout/difficulte/impact
- [x] Trajectoire SBTi
- [x] Objectifs reduction par scope
- [x] Visualisation trajectoire

#### Rapports (100%)
- [x] PDF generation
- [x] Word generation (DOCX)
- [x] Export ADEME
- [x] Export GHG Protocol
- [x] Caching et download tracking

#### Conformite CSRD/ESG (100%)
- [x] ESRS 2 disclosure manager
- [x] Climate Transition Plan editor
- [x] EU Taxonomy reporting (2020/852)
- [x] Value Chain Due Diligence (LkSG/CSDDD)
- [x] Double materiality assessment
- [x] ISO 14064-1 tracking
- [x] ISO 50001 energy management

#### Gamification (100%)
- [x] Systeme badges
- [x] Employee engagement
- [x] Partage public badges (LinkedIn)
- [x] Verification tokens

#### Facturation Stripe (100%)
- [x] Plans avec limites
- [x] Checkout
- [x] Webhooks Stripe
- [x] Gestion abonnements
- [x] Factures

#### Internationalisation (100%)
- [x] 3 langues : DE, EN, FR
- [x] Selecteur de langue
- [x] Traductions completes

---

## 3. Architecture Technique

### 3.1 Stack Technique

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                              │
├─────────────────────────────────────────────────────────────┤
│  Livewire 3    │  Alpine.js 3   │  Tailwind CSS 4          │
│  (Full-page)   │  (Interactif)  │  (Styling)               │
├─────────────────────────────────────────────────────────────┤
│                        BACKEND                               │
├─────────────────────────────────────────────────────────────┤
│  Laravel 12    │  PHP 8.4+      │  Sanctum (Auth)          │
│  Filament 3    │  Horizon       │  Scout (Search)          │
│  (Admin)       │  (Queues)      │  (MeiliSearch)           │
├─────────────────────────────────────────────────────────────┤
│                      DATABASE                                │
├─────────────────────────────────────────────────────────────┤
│  PostgreSQL    │  Redis         │  MeiliSearch             │
│  (Primary)     │  (Cache/Queue) │  (Full-text)             │
├─────────────────────────────────────────────────────────────┤
│                    INTEGRATIONS                              │
├─────────────────────────────────────────────────────────────┤
│  Bridge.io     │  FinAPI        │  Enedis/GRDF             │
│  (Banking FR)  │  (Banking DE)  │  (Energy FR)             │
├─────────────────────────────────────────────────────────────┤
│  Claude        │  GPT-4         │  Gemini/DeepSeek         │
│  (Anthropic)   │  (OpenAI)      │  (Google/DeepSeek)       │
├─────────────────────────────────────────────────────────────┤
│  Stripe        │  Postmark      │  Sentry                  │
│  (Payments)    │  (Email)       │  (Monitoring)            │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Infrastructure (OVH)

```
┌─────────────────────────────────────────────────────────────┐
│                     OVH VPS                                  │
├─────────────────────────────────────────────────────────────┤
│  Docker + Docker Compose                                     │
│  ├── Traefik v3 (Reverse Proxy + Let's Encrypt SSL)        │
│  ├── PHP-FPM 8.4                                            │
│  ├── Nginx                                                   │
│  ├── PostgreSQL 16                                          │
│  ├── Redis 7                                                 │
│  ├── MeiliSearch                                            │
│  ├── Horizon (Queue Worker)                                 │
│  └── Mailpit (Dev)                                          │
├─────────────────────────────────────────────────────────────┤
│  OVH Object Storage (S3-compatible)                         │
│  GitHub Actions (CI/CD)                                      │
│  Backups automatiques                                        │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Modeles de Donnees Principaux

### 4.1 Core Business

```php
Organization
├── id (uuid), name, legal_name, slug
├── address, city, postal_code, country
├── sector, employee_count, revenue
├── settings (JSON), csrd_applicable
└── Relationships: users, sites, assessments, subscriptions

User
├── id, organization_id, email, password
├── first_name, last_name, role
├── locale, timezone, two_factor_enabled
└── Relationships: organization, aiConversations

Site
├── id, organization_id, name, country
├── floor_area, employee_count
├── renewable_percentage
└── Relationships: organization, emissionRecords

Assessment (Bilan)
├── id, organization_id, year
├── revenue, employee_count
├── status (draft/active/completed)
└── Relationships: organization, emissionRecords
```

### 4.2 Emissions

```php
EmissionRecord
├── id, organization_id, site_id, assessment_id
├── scope (1/2/3), category_code
├── emission_factor_id, quantity, unit
├── co2e_kg, ch4_kg, n2o_kg (GHG gases)
├── uncertainty_percent (ISO 14064-1)
└── Relationships: organization, site, emissionFactor

EmissionFactor
├── id, source (ademe/uba/ecoinvent/custom)
├── name, description, category
├── co2e_per_unit, unit, region
├── valid_from, valid_to
└── Indexed by MeiliSearch

Activity
├── id, organization_id, category_id
├── period_start, period_end
├── quantity, unit, notes
└── Relationships: organization, category
```

### 4.3 Banking

```php
BankConnection
├── id, organization_id, provider (bridge/finapi)
├── external_id, access_token (encrypted)
├── refresh_token (encrypted), status
└── Relationships: organization, accounts, transactions

Transaction
├── id, organization_id, bank_account_id
├── amount, currency, description
├── mcc_code, category_id (AI classified)
├── validated, validation_date
└── Relationships: organization, category
```

### 4.4 AI

```php
AIConversation
├── id, organization_id, user_id
├── title, messages (JSON)
├── provider, model
└── Relationships: organization, user

AISetting
├── id, organization_id
├── provider, api_key (encrypted)
├── default_model, temperature
└── Relationships: organization

UploadedDocument
├── id, organization_id
├── path, mime_type, size
├── extracted_data (JSON), status
└── Relationships: organization
```

### 4.5 Compliance

```php
Esrs2Disclosure
├── id, organization_id, year
├── disclosure_requirement, content
├── status, verified_at
└── Relationships: organization

ClimateTransitionPlan
├── id, organization_id
├── base_year, target_year
├── targets (JSON), actions (JSON)
└── Relationships: organization

EuTaxonomyReport
├── id, organization_id, year
├── eligible_revenue, aligned_revenue
├── activities (JSON)
└── Relationships: organization
```

---

## 5. API Structure

### 5.1 Authentication (Sanctum)

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
PUT    /api/v1/auth/me
POST   /api/v1/auth/change-password
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
```

### 5.2 Core Resources

```
# Organization
GET/PUT  /api/v1/organization
GET      /api/v1/organization/stats

# Sites
GET/POST      /api/v1/sites
GET/PUT/DEL   /api/v1/sites/{id}

# Users
GET/POST      /api/v1/users
POST          /api/v1/users/{id}/invite

# Emissions
GET           /api/v1/emissions
GET           /api/v1/emissions/summary
GET           /api/v1/emissions/by-scope
GET           /api/v1/emissions/by-category
GET           /api/v1/emissions/timeline

# Dashboard
GET           /api/v1/dashboard
GET           /api/v1/dashboard/kpis
GET           /api/v1/dashboard/trends
```

### 5.3 Integrations

```
# Banking
GET           /api/v1/banking/connections
POST          /api/v1/banking/connections/init
POST          /api/v1/banking/callbacks
GET           /api/v1/banking/accounts
GET/PUT       /api/v1/transactions
POST          /api/v1/transactions/bulk-categorize

# Energy
GET           /api/v1/energy/connections
POST          /api/v1/energy/connect
GET           /api/v1/energy/consumption

# AI
POST          /api/v1/ai/chat
GET           /api/v1/ai/providers
GET           /api/v1/ai/conversations
```

### 5.4 External API (API Key Auth)

```
GET    /api/v1/external/emissions
GET    /api/v1/external/emissions/summary
GET    /api/v1/external/organization
GET    /api/v1/external/sites
GET    /api/v1/external/reports
```

---

## 6. Services Layer

### 6.1 AI Services

| Service | Description |
|---------|-------------|
| `AIManager` | Orchestration multi-providers |
| `ClaudeClient` | Integration Anthropic |
| `ActionRecommendationEngine` | Suggestions reduction |
| `CategorizationService` | Classification transactions |
| `DocumentExtractor` | OCR + parsing documents |
| `FactorRAGService` | RAG pour facteurs emission |
| `PromptLibrary` | Templates prompts |

### 6.2 Carbon Services

| Service | Description |
|---------|-------------|
| `EmissionCalculator` | Calcul emissions core |
| `Scope1Calculator` | Emissions directes |
| `Scope2Calculator` | Energie achetee |
| `Scope3Calculator` | Emissions indirectes |
| `EquivalentCalculator` | Conversions CO2e |
| `FactorRepository` | Acces facteurs |
| `SbtiTargetCalculator` | Objectifs SBTi |

### 6.3 Banking Services

| Service | Description |
|---------|-------------|
| `BridgeService` | Integration Bridge.io (FR) |
| `FinapiService` | Integration FinAPI (DE) |
| `TransactionNormalizer` | Normalisation data |
| `Psd2AuditService` | Audit PSD2 compliance |

### 6.4 Compliance Services

| Service | Description |
|---------|-------------|
| `CsrdComplianceService` | Applicabilite CSRD |
| `EsrsCalculator` | KPIs ESRS |
| `DoubleMaterialityService` | Evaluation materialite |
| `Iso14064Service` | Conformite ISO 14064-1 |
| `Iso50001Service` | Management energie |
| `GermanComplianceService` | LkSG/CSDDD |

---

## 7. Navigation (5 Piliers)

```
LINSCARBON
├── MESURER (Measure)
│   ├── Dashboard
│   ├── Scope 1/2/3 (par categorie)
│   ├── Banking (transactions)
│   └── Sites (comparaison)
│
├── PLANIFIER (Plan)
│   ├── Plan de transition
│   ├── Trajectoire SBTi
│   └── Bilans (assessments)
│
├── ENGAGER (Engage)
│   ├── Fournisseurs
│   └── Employes
│
├── RAPPORTER (Report)
│   ├── Rapports & Exports
│   └── Conformite (CSRD)
│
└── PROMOUVOIR (Promote)
    ├── Badges
    └── Partage
```

---

## 8. Plans Tarifaires

| Plan | Prix | Users | Sites | Connections | IA |
|------|------|-------|-------|-------------|-----|
| **Trial** | 0 | 1 | 1 | 1 | 0 |
| **Starter** | 29/mois | 3 | 2 | 2 | 50/j |
| **Business** | 99/mois | 10 | 5 | 5 | 100/j |
| **Premium** | 299/mois | Illimite | Illimite | 10 | Illimite |
| **Enterprise** | Devis | Custom | Custom | Custom | Custom |

---

## 9. Securite et Conformite

### 9.1 Securite

- **Auth** : Laravel Sanctum (tokens + sessions)
- **2FA** : TOTP support
- **SSO** : SAML 2.0 configuration
- **Encryption** : Tokens bancaires chiffres (AES-256)
- **Rate Limiting** : API throttling
- **Audit Logs** : Activity tracking complet

### 9.2 Conformite

| Standard | Implementation |
|----------|----------------|
| **RGPD/DSGVO** | Export data, deletion, DPO contact |
| **PSD2** | Open Banking certified (Bridge, FinAPI) |
| **ISO 14064-1** | Uncertainty quantification |
| **ISO 50001** | Energy management system |
| **CSRD 2025** | ESRS 2 disclosures |
| **GHG Protocol** | Scope 1/2/3 methodology |

---

## 10. Conventions de Code

### 10.1 Standards

- **Langue code** : Anglais
- **Langue UI** : Allemand (defaut), Anglais, Francais
- **Naming DB** : snake_case
- **Naming PHP** : camelCase (methodes), PascalCase (classes)
- **Naming Routes** : kebab-case
- **Standards** : PSR-12, Laravel conventions

### 10.2 Tests

- **Unit/Feature** : PHPUnit, Pest
- **E2E** : Playwright
- **Coverage cible** : 80%
- **Factories** : Toutes les entites

### 10.3 Traductions

- Fichiers : `lang/{de,en,fr}/linscarbon.php`
- Pattern : `{{ __('linscarbon.section.key') }}`
- **Aucun texte hardcode** dans les vues

---

## 11. Roadmap

### Phase 1 - MVP (COMPLETE)
- [x] Core multi-tenant
- [x] Dashboard complet
- [x] Emissions Scope 1/2/3
- [x] Base facteurs multi-sources
- [x] Open Banking (Bridge, FinAPI)
- [x] IA multi-providers
- [x] Rapports PDF/Word
- [x] Facturation Stripe
- [x] i18n DE/EN/FR

### Phase 2 - Q1 2025
- [ ] App mobile PWA
- [ ] Benchmark sectoriel anonymise
- [ ] Integration comptable (DATEV DE)
- [ ] Supplier scoring automatique

### Phase 3 - Q2 2025
- [ ] API publique documentee
- [ ] Partenariats experts-comptables
- [ ] Certifications ISO automatisees
- [ ] Marketplace actions reduction

---

## 12. Contacts et Ressources

### DPO (Datenschutzbeauftragter)
- Email : dpo@linscarbon.de
- Autorite : BfDI (www.bfdi.bund.de)

### Support
- Email : support@linscarbon.de
- Chat : Widget integre (IA + humain)

### Legal
- Impressum : /mentions-legales
- Datenschutz : /privacy
- AGB : /cgu

---

> **Document genere automatiquement**
> Derniere mise a jour : Janvier 2025
> Version : 4.0
