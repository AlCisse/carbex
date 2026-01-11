# Implementation Plan: Carbex MVP Platform

**Branch**: `001-carbex-mvp-platform` | **Date**: 2025-12-30 | **Spec**: [spec.md](./spec.md)
**Reference**: Plateforme SaaS bilan carbone PME **augmentée par l'IA**
**Constitution**: v3.0 — IA-Native

## Summary

Carbex est une solution SaaS de comptabilité carbone pour les PME, **augmentée par l'intelligence artificielle**. L'objectif est de permettre aux entreprises de mesurer, analyser et réduire leur bilan carbone selon les standards GHG Protocol, ISO 14064 et ADEME, **tout en réduisant de 80% le temps de saisie grâce à l'IA**.

**Différenciateur clé**: Premier outil de bilan carbone IA-augmenté pour PME en France.

**Approche fonctionnelle** : Interface guidée par scope (1, 2, 3) avec saisie manuelle des données d'activité **assistée par IA**, calcul automatique des émissions via facteurs ADEME, **extraction automatique de factures**, plan de transition avec objectifs SBTi et **recommandations IA personnalisées**, génération de rapports conformes (BEGES, CSRD, GHG Protocol).

**Technical approach**: Laravel 12 monolith avec Livewire 3 pour UI réactive, PostgreSQL pour persistance, Redis pour queues/cache, Meilisearch pour recherche des facteurs d'émission, **Claude API (Anthropic) pour assistant IA**.

## Fonctionnalités Principales

Carbex offre une interface intuitive et complète pour la comptabilité carbone :
- Navigation par scope (sidebar avec % progression)
- Dashboard avec cercle de progression et équivalents carbone
- Saisie des émissions par catégorie (1.1, 1.2, 3.3, etc.)
- Base de facteurs d'émission (ADEME, IMPACTS, EF reference)
- Plan de transition (actions de réduction)
- Trajectoire SBTi (objectifs par scope)
- Gestion des bilans par année
- Rapports (Word, ADEME, GHG)

**Et les dépasser avec l'IA** :
- 🤖 Assistant IA conversationnel (style Greenly EcoPilot)
- 📄 Extraction automatique de factures (style CarbonAnalytics)
- 💡 Recommandations d'actions personnalisées
- 🏢 Module fournisseurs Scope 3 (style Watershed)
- 🏆 Badges durabilité et gamification (style TrackZero)

---

## Analyse Concurrentielle

| Outil | Cible | Prix | Ce qu'on adopte | Ce qu'on évite |
|-------|-------|------|-----------------|----------------|
| **Greenly** | PME/ETI | 539€-12k€ | EcoPilot AI, 300k facteurs | Prix élevé |
| **Watershed** | Enterprise | >50k$ | Audit-grade, Supplier engagement | Prix prohibitif |
| **Climatiq** | Devs | Freemium | API REST, Free tier | API-only |
| **TrackZero** | PME UK | £995-2995 | 5 Piliers, Badges, Multi-sites | Prix sans free tier |
| **CarbonAnalytics** | Enterprise | N/A | IA extraction 80% automation | Enterprise only |
| **Concurrents FR** | PME FR | 0-600€ | UX simplifiée, ADEME natif | Pas d'IA |

**Positionnement Carbex**: Simplicité PME + IA = Best value France

## Technical Context

**Language/Version**: PHP 8.4+ avec Laravel 12.x

**Primary Dependencies**:
- Laravel Livewire 3.x (reactive UI)
- Laravel Sanctum 4.x (API auth)
- TailwindCSS 4.x + Alpine.js 3.x (frontend)
- ApexCharts 4.x (visualizations)
- Filament 3.x (admin panel)
- Laravel Cashier (Stripe billing)
- **Anthropic SDK** (Claude API - Assistant IA)

**Storage**:
- PostgreSQL 17 (primary database)
- Redis 7.4+ (cache, queues, sessions)
- Meilisearch 1.11+ (emission factor search)

**Testing**:
- PHPUnit / Pest PHP
- Laravel Dusk (browser tests)

**Performance Goals**:
- Dashboard load: < 2 seconds
- Search response: < 50ms
- PDF generation: < 30 seconds

**Constraints**:
- GDPR compliance (EU data residency)
- Multi-language support (FR, DE, EN)
- < 120 EUR/month infrastructure

## Constitution Check

*GATE: Alignement avec la constitution v3.0 (Carbex IA-Native)*

| Principe | Status | Évidence |
|----------|--------|----------|
| **Interface intuitive** | PASS | Navigation, dashboard, scopes structurés |
| **Saisie guidée** | PASS | Interface par catégorie (1.1, 1.2, 3.3...) |
| **Facteurs ADEME** | PASS | Import Base Carbone, IMPACTS, EF reference |
| **Plan transition** | PASS | Actions avec coût, difficulté, % réduction |
| **Trajectoire SBTi** | PASS | Objectifs 4.2%/an (S1+S2), 2.5%/an (S3) |
| **Rapports conformes** | PASS | Word, ADEME, GHG Protocol |
| **Multi-bilans** | PASS | Gestion par année avec CA et effectifs |
| **Plans tarifaires** | PASS | Gratuit, Premium (400€), Avancé (1200€) |
| **🆕 IA-Native** | PASS | Claude API, Assistant conversationnel, Extraction factures |
| **🆕 Simplicité > Features** | PASS | UX 5min onboarding, PME First |
| **🆕 Conformité FR** | PASS | ADEME, BEGES, CSRD prioritaires |

**Principes Architecturaux (v3.0)**:
1. IA-Native, pas IA-Ajoutée
2. Simplicité > Fonctionnalités
3. PME First (validation pour 10 employés)
4. Conformité FR (ADEME, BEGES, CSRD)
5. Open by Default (API publique prévue)

**Gate Status**: ALL GATES PASSED

---

## Project Structure

### Navigation Carbex

```text
CARBEX
├── Dashboard
│   ├── Cercle de progression (%)
│   ├── Équivalents carbone (A/R Paris-NY, Tours Terre, Nuits hôtel)
│   ├── Progression évaluation
│   └── Vidéos tutoriels
├── Scope 1 - Émissions directes [%]
│   ├── 1.1 Sources fixes de combustion
│   ├── 1.2 Sources mobiles de combustion
│   ├── 1.4 Émissions fugitives
│   └── 1.5 Biomasse (sols et forêts)
├── Scope 2 - Émissions indirectes liées à l'énergie [%]
│   └── 2.1 Consommation d'électricité
├── Scope 3 - Autres émissions indirectes [%]
│   ├── 3.1 Transport marchandise amont
│   ├── 3.2 Transport marchandise aval
│   ├── 3.3 Déplacements domicile-travail
│   ├── 3.5 Déplacements professionnels
│   ├── 4.1 Achats de biens
│   ├── 4.2 Immobilisations de biens
│   ├── 4.3 Gestion des déchets
│   ├── 4.4 Actifs en leasing amont
│   └── 4.5 Achats de services
├── Analyse
├── Plan de transition
└── Rapports & exports
```

### Source Code Structure

```text
app/
├── Http/
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   │   ├── ProgressCircle.php
│   │   │   ├── CarbonEquivalents.php
│   │   │   ├── EvaluationProgress.php
│   │   │   └── TutorialVideos.php
│   │   ├── Emissions/
│   │   │   ├── ScopeNavigation.php
│   │   │   ├── CategoryForm.php
│   │   │   ├── EmissionSourceInput.php
│   │   │   └── FactorSelector.php
│   │   ├── TransitionPlan/
│   │   │   ├── ActionList.php
│   │   │   ├── ActionForm.php
│   │   │   └── TrajectoryForm.php
│   │   ├── Assessments/
│   │   │   ├── AssessmentList.php
│   │   │   └── AssessmentForm.php
│   │   ├── Reports/
│   │   │   ├── ReportList.php
│   │   │   └── ReportGenerator.php
│   │   ├── Settings/
│   │   │   ├── OrganizationSettings.php
│   │   │   ├── UserManagement.php
│   │   │   └── ProfileSettings.php
│   │   ├── AI/                            # 🆕 Composants IA
│   │   │   ├── ChatWidget.php             # Assistant conversationnel
│   │   │   ├── EmissionHelper.php         # Aide saisie intelligente
│   │   │   ├── DocumentUploader.php       # Upload factures
│   │   │   └── ActionRecommender.php      # Recommandations
│   │   ├── Scope3/                        # 🆕 Module fournisseurs
│   │   │   └── SupplierManagement.php
│   │   └── Promote/                       # 🆕 Badges durabilité
│   │       └── BadgeShowcase.php
├── Models/
│   ├── User.php
│   ├── Organization.php
│   ├── Assessment.php              # Bilan annuel
│   ├── EmissionCategory.php        # 1.1, 1.2, 3.3, etc.
│   ├── EmissionSource.php          # Saisie utilisateur
│   ├── EmissionFactor.php          # Base ADEME
│   ├── Action.php                  # Plan de transition
│   ├── ReductionTarget.php         # Trajectoire SBTi
│   ├── Report.php
│   ├── Subscription.php
│   ├── AIConversation.php          # 🆕 Historique conversations IA
│   ├── UploadedDocument.php        # 🆕 Documents uploadés (factures)
│   ├── Supplier.php                # 🆕 Fournisseurs Scope 3
│   ├── SustainabilityBadge.php     # 🆕 Badges durabilité
│   └── ApiKey.php                  # 🆕 Clés API publique
├── Services/
│   ├── Carbon/
│   │   ├── EmissionCalculator.php
│   │   ├── FactorRepository.php
│   │   ├── EquivalentCalculator.php
│   │   ├── ProgressCalculator.php
│   │   └── SupplierScoreCalculator.php    # 🆕 Score fournisseurs
│   ├── Reporting/
│   │   ├── WordReportGenerator.php
│   │   ├── AdemeExporter.php
│   │   └── GhgExporter.php
│   ├── Import/
│   │   ├── AdemeFactorImporter.php
│   │   ├── ImpactsFactorImporter.php
│   │   └── EfReferenceImporter.php
│   └── AI/                                 # 🆕 Services IA
│       ├── AIService.php                   # Client Claude API
│       ├── PromptLibrary.php               # Prompts système
│       ├── EmissionClassifier.php          # Classification intelligente
│       ├── DocumentExtractor.php           # Extraction factures
│       ├── ActionRecommendationEngine.php  # Recommandations
│       └── ReportNarrativeGenerator.php    # Narratifs IA
└── Jobs/
    ├── CalculateEmissions.php
    ├── GenerateReport.php
    └── ImportFactors.php

database/
├── migrations/
│   ├── create_organizations_table.php
│   ├── create_users_table.php
│   ├── create_assessments_table.php
│   ├── create_emission_categories_table.php
│   ├── create_emission_sources_table.php
│   ├── create_emission_factors_table.php
│   ├── create_actions_table.php
│   ├── create_reduction_targets_table.php
│   └── create_reports_table.php
└── seeders/
    ├── EmissionCategorySeeder.php  # Structure scopes
    ├── EmissionFactorSeeder.php    # Base ADEME
    └── SectorSeeder.php            # Secteurs d'activité

resources/views/
├── livewire/
│   ├── dashboard/
│   ├── emissions/
│   ├── transition-plan/
│   ├── assessments/
│   ├── reports/
│   └── settings/
├── layouts/
│   ├── app.blade.php              # Layout principal avec sidebar
│   └── guest.blade.php            # Layout auth
├── components/
│   ├── sidebar.blade.php
│   ├── header.blade.php
│   ├── progress-circle.blade.php
│   └── footer.blade.php
└── pdf/
    └── reports/
```

---

## Data Model

### Core Entities

```
Organization
├── id (uuid)
├── name, legal_name, slug
├── address, city, postal_code, country
├── sector_id
├── settings (JSON)
└── Relationships: users, assessments, actions, targets

Assessment (Bilan annuel)
├── id (uuid)
├── organization_id
├── year
├── revenue (CA)
├── employee_count
├── status (draft, active, completed)
└── Relationships: emission_sources

EmissionCategory (Structure des scopes)
├── id (uuid)
├── scope (1, 2, 3)
├── code ("1.1", "1.2", "3.3", etc.)
├── name_fr, name_en, name_de
├── description
├── parent_id (nullable, self-ref)
└── Relationships: emission_sources, factors

EmissionSource (Saisie utilisateur)
├── id (uuid)
├── assessment_id
├── emission_category_id
├── emission_factor_id
├── quantity
├── unit
├── co2e_kg (calculé)
├── notes
├── status (pending, completed, not_applicable)
└── Relationships: assessment, category, factor

EmissionFactor (Base ADEME + autres)
├── id (uuid)
├── source (ademe, impacts, ef_reference, custom)
├── name, description
├── category, subcategory
├── co2e_per_unit
├── unit
├── region
├── valid_from, valid_to
└── metadata (JSON)

Action (Plan de transition)
├── id (uuid)
├── organization_id
├── title, description
├── category_id
├── status (todo, in_progress, completed)
├── due_date
├── co2_reduction_percent
├── estimated_cost
├── difficulty (easy, medium, hard)
└── Relationships: organization, category

ReductionTarget (Trajectoire SBTi)
├── id (uuid)
├── organization_id
├── baseline_year
├── target_year
├── scope_1_reduction (%)
├── scope_2_reduction (%)
├── scope_3_reduction (%)
└── Relationships: organization
```

---

## Implementation Phases

### Phase 1: Foundation (En cours)

**Objectif**: Structure de base et navigation

- [x] Setup Docker (nginx, php-fpm, postgres, redis, meilisearch)
- [x] Authentification (login, register, forgot password)
- [x] Multi-tenant (Organization, User)
- [x] Layout principal avec sidebar
- [ ] Dashboard structure de base
- [ ] Navigation par scope avec % progression

**Livewire Components**:
- `Dashboard\ProgressCircle` - Cercle de progression
- `Dashboard\CarbonEquivalents` - Équivalents visuels
- `Emissions\ScopeNavigation` - Sidebar avec scopes

### Phase 2: Emission Entry

**Objectif**: Saisie des émissions par catégorie

- [ ] Seeder EmissionCategory (structure scopes)
- [ ] Import facteurs ADEME (Base Carbone)
- [ ] Interface saisie par catégorie (1.1, 1.2, etc.)
- [ ] Recherche facteurs (Meilisearch)
- [ ] Calcul automatique CO2e
- [ ] Statut par catégorie (complété, non applicable)

**Livewire Components**:
- `Emissions\CategoryForm` - Formulaire par catégorie
- `Emissions\EmissionSourceInput` - Saisie quantité + unité
- `Emissions\FactorSelector` - Modal recherche facteurs

### Phase 3: Assessment & Dashboard

**Objectif**: Bilans annuels et tableau de bord

- [ ] Gestion des bilans (créer, mettre à jour)
- [ ] Sélecteur d'année dans header
- [ ] Dashboard temps réel
  - Cercle progression
  - Équivalents carbone
  - Répartition par scope
  - Tendances mensuelles
- [ ] Section "Se former" (vidéos YouTube)

**Livewire Components**:
- `Assessments\AssessmentList` - Liste bilans
- `Assessments\AssessmentForm` - Modal création/édition
- `Dashboard\TrendChart` - Graphique tendances

### Phase 4: Transition Plan

**Objectif**: Actions de réduction et trajectoire

- [ ] Liste des actions
- [ ] Formulaire action (titre, description, coût, difficulté, % réduction)
- [ ] Trajectoire SBTi
  - Année référence / cible
  - Objectifs par scope
  - Visualisation graphique
- [ ] Calcul écart vs trajectoire

**Livewire Components**:
- `TransitionPlan\ActionList` - Liste actions
- `TransitionPlan\ActionForm` - Formulaire action
- `TransitionPlan\TrajectoryForm` - Objectifs SBTi
- `TransitionPlan\TrajectoryChart` - Graphique trajectoire

### Phase 5: Reports & Export

**Objectif**: Génération de rapports conformes

- [ ] Bilan complet émissions (Word)
  - Conforme ISO 14064, ISO 14067, GHG Protocol
- [ ] Tableau déclaration ADEME
  - Format bilans.ges.ademe.fr
- [ ] Tableau déclaration GHG
  - Protocole WBCSD/WRI
- [ ] Historique rapports générés

**Services**:
- `Reporting\WordReportGenerator`
- `Reporting\AdemeExporter`
- `Reporting\GhgExporter`

### Phase 6: Settings & Billing

**Objectif**: Paramètres et plans tarifaires

- [ ] Paramètres organisation
- [ ] Gestion utilisateurs
  - Inviter collaborateur
  - Activer/désactiver
  - Limite selon plan
- [ ] Profil et mot de passe
- [ ] Plans tarifaires
  - Gratuit (15 jours)
  - Premium (400€/an)
  - Avancé (1200€/an)
- [ ] Intégration Stripe

### Phase 7: Polish & Testing

**Objectif**: Tests et traductions

- [ ] Traductions complètes (FR, EN, DE)
- [ ] Tests Feature (CRUD models)
- [ ] Tests Unit (calculators, services)
- [ ] Tests Browser (flows critiques)
- [ ] Documentation API (Scramble)

### Phase 8: Site Marketing Public

**Objectif**: Pages publiques et SEO

- [ ] Layout marketing (header, footer)
- [ ] Landing page (hero, avantages, stats)
- [ ] Page tarifs publique
- [ ] Blog (posts, SEO)
- [ ] Pages légales (CGV, CGU, mentions)
- [ ] SEO (meta tags, sitemap, robots.txt)

### Phase 9: Intelligence Artificielle 🤖 (Différenciateur)

**Objectif**: Premier outil bilan carbone IA-augmenté FR

**Infrastructure IA**:
- [ ] Installer SDK Anthropic
- [ ] Config AI (model, tokens, temperature)
- [ ] AIService (client Claude API)
- [ ] PromptLibrary (prompts système)
- [ ] Migration ai_conversations

**Assistant Conversationnel** (style Greenly EcoPilot):
- [ ] ChatWidget Livewire (bouton flottant, panel sliding)
- [ ] API endpoint /api/ai/chat
- [ ] Streaming réponses
- [ ] Rate limiting par plan

**Livewire Components IA**:
- `AI\ChatWidget` - Assistant conversationnel
- `AI\EmissionHelper` - Aide saisie intelligente
- `AI\DocumentUploader` - Upload factures
- `AI\ActionRecommender` - Recommandations

**Aide Saisie Intelligente**:
- [ ] EmissionClassifier (suggestion catégories)
- [ ] Auto-complétion intelligente
- [ ] Détection anomalies
- [ ] RAG context facteurs ADEME

**Extraction Factures** (style CarbonAnalytics):
- [ ] DocumentExtractor (PDF, images, Excel)
- [ ] Claude Vision pour OCR
- [ ] Job ProcessDocumentExtraction
- [ ] Mapping vers EmissionSources

**Recommandations Actions**:
- [ ] ActionRecommendationEngine
- [ ] Top 5 actions prioritaires
- [ ] Estimation impact (% réduction, coût)
- [ ] Base données actions types par secteur

**Module Fournisseurs Scope 3** (style Watershed):
- [ ] Migration suppliers
- [ ] SupplierManagement Livewire
- [ ] Questionnaires automatisés
- [ ] SupplierScoreCalculator

**API Publique** (style Climatiq):
- [ ] Migration api_keys
- [ ] Endpoints /api/v1/factors, /api/v1/calculate
- [ ] Documentation Scramble
- [ ] Page gestion API keys

### Phase 10: Fonctionnalités Avancées (TrackZero)

**Objectif**: Features avancées inspirées TrackZero

**Badges Durabilité**:
- [ ] Migration sustainability_badges
- [ ] BadgeShowcase Livewire
- [ ] Partage LinkedIn
- [ ] Générateur assets marketing

**Multi-Sites Amélioré**:
- [ ] SiteComparison Livewire
- [ ] Dashboard comparatif
- [ ] Import CSV sites

**Conformité Étendue**:
- [ ] Templates CSRD
- [ ] Templates ISO 14001/14064-1
- [ ] Checklist conformité dynamique

**Engagement Équipes**:
- [ ] Quiz carbone interactif
- [ ] Challenges réduction
- [ ] Emails automatiques engagement

---

## Emission Categories (Seeder)

```php
// Scope 1 - Émissions directes
['scope' => 1, 'code' => '1.1', 'name' => 'Sources fixes de combustion'],
['scope' => 1, 'code' => '1.2', 'name' => 'Sources mobiles de combustion'],
['scope' => 1, 'code' => '1.4', 'name' => 'Émissions fugitives'],
['scope' => 1, 'code' => '1.5', 'name' => 'Biomasse (sols et forêts)'],

// Scope 2 - Émissions indirectes liées à l'énergie
['scope' => 2, 'code' => '2.1', 'name' => 'Consommation d\'électricité'],

// Scope 3 - Autres émissions indirectes
['scope' => 3, 'code' => '3.1', 'name' => 'Transport de marchandise amont'],
['scope' => 3, 'code' => '3.2', 'name' => 'Transport de marchandise aval'],
['scope' => 3, 'code' => '3.3', 'name' => 'Déplacements domicile-travail'],
['scope' => 3, 'code' => '3.5', 'name' => 'Déplacements professionnels'],
['scope' => 3, 'code' => '4.1', 'name' => 'Achats de biens'],
['scope' => 3, 'code' => '4.2', 'name' => 'Immobilisations de biens'],
['scope' => 3, 'code' => '4.3', 'name' => 'Gestion des déchets'],
['scope' => 3, 'code' => '4.4', 'name' => 'Actifs en leasing amont'],
['scope' => 3, 'code' => '4.5', 'name' => 'Achats de services'],
```

---

## Default Emission Factors

```php
// Scope 1 - 1.1 Sources fixes
['category' => '1.1', 'name' => 'Fioul domestique', 'co2e' => 3.25, 'unit' => 'Litre'],
['category' => '1.1', 'name' => 'Gaz naturel', 'co2e' => 0.215, 'unit' => 'kWh PCS'],

// Scope 1 - 1.2 Sources mobiles
['category' => '1.2', 'name' => 'Essence', 'co2e' => 2.80, 'unit' => 'Litre'],
['category' => '1.2', 'name' => 'Diesel/Gazole', 'co2e' => 3.17, 'unit' => 'Litre'],
['category' => '1.2', 'name' => 'GPL', 'co2e' => 1.86, 'unit' => 'Litre'],
['category' => '1.2', 'name' => 'Superéthanol', 'co2e' => 1.68, 'unit' => 'Litre'],

// Scope 1 - 1.4 Émissions fugitives
['category' => '1.4', 'name' => 'R134A', 'co2e' => 1300, 'unit' => 'kg'],
['category' => '1.4', 'name' => 'R410A', 'co2e' => 2088, 'unit' => 'kg'],
['category' => '1.4', 'name' => 'R407C', 'co2e' => 1774, 'unit' => 'kg'],

// Scope 2 - 2.1 Électricité
['category' => '2.1', 'name' => 'Électricité France', 'co2e' => 0.052, 'unit' => 'kWh'],
['category' => '2.1', 'name' => 'Électricité Allemagne', 'co2e' => 0.362, 'unit' => 'kWh'],

// Scope 3 - 3.3 Déplacements domicile-travail
['category' => '3.3', 'name' => 'Voiture essence', 'co2e' => 0.193, 'unit' => 'km'],
['category' => '3.3', 'name' => 'Voiture gazole', 'co2e' => 0.158, 'unit' => 'km'],
['category' => '3.3', 'name' => 'Voiture GPL', 'co2e' => 0.142, 'unit' => 'km'],

// Scope 3 - 3.5 Déplacements professionnels
['category' => '3.5', 'name' => 'Avion court courrier', 'co2e' => 0.258, 'unit' => 'km'],
['category' => '3.5', 'name' => 'Avion moyen courrier', 'co2e' => 0.187, 'unit' => 'km'],
['category' => '3.5', 'name' => 'Avion long courrier', 'co2e' => 0.152, 'unit' => 'km'],
```

---

## Plans Tarifaires

| Plan | Prix | Limites | Fonctionnalités |
|------|------|---------|-----------------|
| **Gratuit** | 0€ | 15 jours, 1 user | Saisie émissions, Dashboard basique, **100 req IA/jour** |
| **Premium** | 400€/an | 3 users | + Rapports Word, Trajectoire SBTi, Plan transition, **IA illimitée**, Extraction factures |
| **Avancé** | 1200€/an | Illimité | + Multi-entités, Support dédié, Export CSRD, **API publique**, Module fournisseurs |

---

## External Dependencies

| Dependency | Type | Usage |
|------------|------|-------|
| Base Carbone ADEME | Required | Facteurs d'émission FR |
| Meilisearch | Required | Recherche facteurs |
| Stripe | Required | Paiements |
| PhpWord | Required | Génération rapports Word |
| **Anthropic API** | Required | Assistant IA (Claude 3.5 Sonnet) |
| **Claude Vision** | Optional | OCR extraction factures |

---

## Next Steps

1. **Immédiat**: T027-T042 (Emission Entry - Saisie des émissions)
2. **Court terme**: T043-T057 (Assessment & Dashboard)
3. **Moyen terme**: T101-T122 (Site Marketing Public)
4. **Différenciateur IA**: T123-T165 (Intelligence Artificielle - Avantage concurrentiel)
5. **Avancé**: T166-T182 (Fonctionnalités TrackZero - Badges, Multi-sites, Conformité)

---

## Statistiques Tâches

| Phase | Tâches | Status |
|-------|--------|--------|
| Phase 1: Foundation & Navigation | 26 | Complété |
| Phase 2: Emission Entry | 16 | À faire |
| Phase 3: Assessment & Dashboard | 15 | À faire |
| Phase 4: Plan de Transition | 12 | À faire |
| Phase 5: Reports & Export | 7 | À faire |
| Phase 6: Settings & Billing | 7 | À faire |
| Phase 7: Polish & Testing | 17 | À faire |
| Phase 8: Site Marketing Public | 22 | À faire |
| Phase 9: Intelligence Artificielle | 43 | À faire |
| Phase 10: Fonctionnalités TrackZero | 17 | À faire |
| **Total** | **182** | |

Run `/speckit.tasks` to view or update implementation tasks.
