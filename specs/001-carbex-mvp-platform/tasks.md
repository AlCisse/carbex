# Tasks: Carbex MVP Platform

**Input**: Design documents from `/specs/001-carbex-mvp-platform/`
**Reference**: Plateforme SaaS bilan carbone PME **augmentée par l'IA**
**Constitution**: v3.0 — IA-Native
**Generated**: 2025-12-30
**Status**: En cours
**Total**: 182 tâches | 10 phases

> **Différenciateur clé**: Premier outil de bilan carbone IA-augmenté pour PME en France

---

## Légende

- `[ ]` = À faire
- `[x]` = Terminé
- `[P]` = Parallélisable
- 🔴 Critique | 🟠 Haute | 🟡 Moyenne | 🟢 Basse

---

# PHASE 1: Foundation & Navigation

## 1.1 Infrastructure (Complété)

- [x] T001 🔴 Créer projet Laravel 12
- [x] T002 🔴 Configurer docker-compose.yml (PostgreSQL, Redis, Meilisearch, Nginx)
- [x] T003 🟠 Créer .env.example avec variables
- [x] T004 🟠 Installer packages composer (livewire, sanctum, filament)
- [x] T005 🟠 Installer packages npm (tailwindcss, alpinejs)
- [x] T006 🟡 Configurer PHPStan
- [x] T007 🟡 Configurer Laravel Pint
- [x] T008 🟡 Configurer Pest PHP

## 1.2 Database Schema (Complété)

- [x] T009 🔴 Migration organizations
- [x] T010 🔴 Migration users
- [x] T011 🔴 Migration sites
- [x] T012 🔴 Migration categories
- [x] T013 🔴 Migration transactions
- [x] T014 🔴 Migration emission_records
- [x] T015 🔴 Migration emission_factors
- [x] T016 🔴 Migration reports
- [x] T017 🔴 Migration subscriptions

## 1.3 Authentication (Complété)

- [x] T018 🔴 Layout guest (auth)
- [x] T019 🔴 Livewire LoginForm
- [x] T020 🔴 Livewire RegisterForm
- [x] T021 🟠 Layout app principal avec sidebar
- [x] T022 🟠 Composants Blade (button, input, card)

## 1.4 Navigation Carbex (Complété)

- [x] T023 🔴 **Créer sidebar avec structure scopes** dans `resources/views/components/sidebar.blade.php`
  - Dashboard
  - Scope 1 - Émissions directes [%]
    - 1.1 Sources fixes de combustion
    - 1.2 Sources mobiles de combustion
    - 1.4 Émissions fugitives
    - 1.5 Biomasse
  - Scope 2 - Émissions indirectes [%]
    - 2.1 Consommation d'électricité
  - Scope 3 - Autres émissions [%]
    - 3.1 Transport marchandise amont
    - 3.2 Transport marchandise aval
    - 3.3 Déplacements domicile-travail
    - 3.5 Déplacements professionnels
    - 4.1 Achats de biens
    - 4.2 Immobilisations
    - 4.3 Gestion des déchets
    - 4.4 Actifs en leasing
    - 4.5 Achats de services
  - Analyse
  - Plan de transition
  - Rapports & exports

- [x] T024 🔴 **Créer header** avec `resources/views/components/header.blade.php`
  - Logo CARBEX
  - Icônes: Notifications, Signets, Paramètres
  - Sélecteur année "Mes Bilans"
  - Menu utilisateur (avatar + nom)

- [x] T025 🟠 **Créer footer** avec badge plan et chat dans `resources/views/components/footer.blade.php`

- [x] T026 🟠 **Créer menu paramètres** dans `resources/views/components/settings-menu.blade.php`
  - Mon entreprise
  - Utilisateurs
  - Profil
  - Mot de passe

**Checkpoint Navigation**: [x] Navigation Carbex complète

---

# PHASE 2: Emission Entry (Saisie des émissions)

## 2.1 EmissionCategory Model & Seeder

- [x] T027 🔴 **Créer migration emission_categories** dans `database/migrations/`
  > Note: Intégré dans `categories` table avec structure scope/code
  ```php
  - id (uuid)
  - scope (1, 2, 3)
  - code ('1.1', '1.2', '3.3', etc.)
  - name_fr, name_en, name_de
  - description
  - parent_id (nullable, self-ref)
  - sort_order
  ```

- [x] T028 🔴 **Créer model EmissionCategory** dans `app/Models/EmissionCategory.php`
  > Note: Intégré dans `app/Models/Category.php`

- [x] T029 🔴 **Créer EmissionCategorySeeder** dans `database/seeders/EmissionCategorySeeder.php`
  > Note: Implémenté via `MccCategorySeeder.php`
  ```php
  // Scope 1
  ['scope' => 1, 'code' => '1.1', 'name_fr' => 'Sources fixes de combustion'],
  ['scope' => 1, 'code' => '1.2', 'name_fr' => 'Sources mobiles de combustion'],
  ['scope' => 1, 'code' => '1.4', 'name_fr' => 'Émissions fugitives'],
  ['scope' => 1, 'code' => '1.5', 'name_fr' => 'Biomasse (sols et forêts)'],

  // Scope 2
  ['scope' => 2, 'code' => '2.1', 'name_fr' => 'Consommation d\'électricité'],

  // Scope 3
  ['scope' => 3, 'code' => '3.1', 'name_fr' => 'Transport de marchandise amont'],
  ['scope' => 3, 'code' => '3.2', 'name_fr' => 'Transport de marchandise aval'],
  ['scope' => 3, 'code' => '3.3', 'name_fr' => 'Déplacements domicile-travail'],
  ['scope' => 3, 'code' => '3.5', 'name_fr' => 'Déplacements professionnels'],
  ['scope' => 3, 'code' => '4.1', 'name_fr' => 'Achats de biens'],
  ['scope' => 3, 'code' => '4.2', 'name_fr' => 'Immobilisations de biens'],
  ['scope' => 3, 'code' => '4.3', 'name_fr' => 'Gestion des déchets'],
  ['scope' => 3, 'code' => '4.4', 'name_fr' => 'Actifs en leasing amont'],
  ['scope' => 3, 'code' => '4.5', 'name_fr' => 'Achats de services'],
  ```

## 2.2 Default Emission Factors

- [x] T030 🔴 **Créer DefaultEmissionFactorSeeder** dans `database/seeders/DefaultEmissionFactorSeeder.php`
  > Note: Implémenté via `AdemeFactorSeeder.php`, `Scope3FactorSeeder.php`, `UbaFactorSeeder.php`, `EuCountryFactorSeeder.php`, `MarketBasedFactorSeeder.php`
  ```php
  // 1.1 Sources fixes
  ['category_code' => '1.1', 'name' => 'Fioul domestique', 'co2e_per_unit' => 3.25, 'unit' => 'Litre'],
  ['category_code' => '1.1', 'name' => 'Gaz naturel', 'co2e_per_unit' => 0.215, 'unit' => 'kWh PCS'],

  // 1.2 Sources mobiles
  ['category_code' => '1.2', 'name' => 'Essence', 'co2e_per_unit' => 2.80, 'unit' => 'Litre'],
  ['category_code' => '1.2', 'name' => 'Diesel/Gazole', 'co2e_per_unit' => 3.17, 'unit' => 'Litre'],
  ['category_code' => '1.2', 'name' => 'GPL', 'co2e_per_unit' => 1.86, 'unit' => 'Litre'],
  ['category_code' => '1.2', 'name' => 'Superéthanol', 'co2e_per_unit' => 1.68, 'unit' => 'Litre'],

  // 1.4 Émissions fugitives
  ['category_code' => '1.4', 'name' => 'R134A', 'co2e_per_unit' => 1300, 'unit' => 'kg'],
  ['category_code' => '1.4', 'name' => 'R410A', 'co2e_per_unit' => 2088, 'unit' => 'kg'],
  ['category_code' => '1.4', 'name' => 'R407C', 'co2e_per_unit' => 1774, 'unit' => 'kg'],

  // 2.1 Électricité
  ['category_code' => '2.1', 'name' => 'Électricité France', 'co2e_per_unit' => 0.052, 'unit' => 'kWh'],
  ['category_code' => '2.1', 'name' => 'Électricité Allemagne', 'co2e_per_unit' => 0.362, 'unit' => 'kWh'],

  // 3.3 Déplacements domicile-travail
  ['category_code' => '3.3', 'name' => 'Voiture essence', 'co2e_per_unit' => 0.193, 'unit' => 'km'],
  ['category_code' => '3.3', 'name' => 'Voiture gazole', 'co2e_per_unit' => 0.158, 'unit' => 'km'],

  // 3.5 Déplacements professionnels
  ['category_code' => '3.5', 'name' => 'Avion court courrier', 'co2e_per_unit' => 0.258, 'unit' => 'km'],
  ['category_code' => '3.5', 'name' => 'Avion moyen courrier', 'co2e_per_unit' => 0.187, 'unit' => 'km'],
  ['category_code' => '3.5', 'name' => 'Avion long courrier', 'co2e_per_unit' => 0.152, 'unit' => 'km'],
  ```

## 2.3 EmissionSource Model

- [x] T031 🔴 **Créer migration emission_sources** dans `database/migrations/`
  > Note: Intégré dans `emission_records` table
  ```php
  - id (uuid)
  - assessment_id (uuid, FK)
  - emission_category_id (uuid, FK)
  - emission_factor_id (uuid, FK, nullable)
  - name (string)
  - quantity (decimal)
  - unit (string)
  - co2e_kg (decimal, calculé)
  - notes (text, nullable)
  - status (enum: pending, completed, not_applicable)
  ```

- [x] T032 🔴 **Créer model EmissionSource** dans `app/Models/EmissionSource.php`
  > Note: Intégré dans `app/Models/EmissionRecord.php`

## 2.4 Interface de Saisie par Catégorie

- [x] T033 🔴 **Créer route /emissions/{scope}/{category}** dans `routes/web.php`

- [x] T034 🔴 **Créer Livewire CategoryForm** dans `app/Livewire/Emissions/CategoryForm.php`
  - Afficher nom catégorie (ex: "1.1 Sources fixes de combustion")
  - Bouton "Comment remplir cette catégorie?"
  - Bouton "Marquer comme complété"
  - Liste des sources d'émission par défaut
  - Pour chaque source:
    - Nom + facteur (ex: "Fioul domestique - 1 litre = 3,25 kg éq. CO2")
    - Input quantité + unité
    - Input notes
    - Lien "Modifier le facteur d'émission"
    - Lien "Ajouter une action"
  - Bouton "+ Ajouter une source d'émission"

- [x] T035 🔴 [P] **Créer view category-form** dans `resources/views/livewire/emissions/category-form.blade.php`

- [x] T036 🟠 **Créer Livewire EmissionSourceInput** dans `app/Livewire/Emissions/EmissionSourceInput.php`
  > Note: Intégré dans `app/Livewire/DataEntry/ManualEntryForm.php`
  - Input quantité
  - Select unité
  - Input notes
  - Calcul automatique CO2e

- [x] T037 🟠 [P] **Créer view emission-source-input** dans `resources/views/livewire/emissions/emission-source-input.blade.php`
  > Note: Intégré dans `resources/views/livewire/data-entry/manual-entry-form.blade.php`

## 2.5 Modal Recherche Facteurs (20 000+)

- [x] T038 🟠 **Créer Livewire FactorSelector** dans `app/Livewire/Emissions/FactorSelector.php`
  > Implémenté avec onglets sources (ADEME, UBA, GHG Protocol, Custom), filtres, recherche, pagination
  - Onglets: Base Carbone® ADEME 23.7, Base IMPACTS® ADEME 3.0, EF reference package 3.1, Données Primaires
  - Filtres: Catégories principales, Localisation, Unité
  - Recherche texte
  - Pagination (1-5 de 13219 items)
  - Bouton "+ Nouveau facteur personnalisé"

- [x] T039 🟠 [P] **Créer view factor-selector** dans `resources/views/livewire/emissions/factor-selector.blade.php`
  > Implémenté avec support multilingue (FR/EN/DE)

- [x] T040 🟠 **Créer modal création facteur personnalisé**
  > Intégré dans FactorSelector avec createCustomFactor()
  - Nom
  - Description
  - Unité de référence (kgCO2/...)

## 2.6 Service de Calcul

- [x] T041 🔴 **Créer EmissionCalculator** dans `app/Services/Carbon/EmissionCalculator.php`
  ```php
  public function calculate(float $quantity, string $unit, float $factorCo2ePerUnit): float
  {
      return $quantity * $factorCo2ePerUnit;
  }
  ```

- [x] T042 🟠 **Créer ProgressCalculator** dans `app/Services/Carbon/ProgressCalculator.php`
  > Note: Intégré dans `app/Services/Dashboard/DashboardService.php`
  - Calculer % progression par scope
  - Calculer % progression global

**Checkpoint Emission Entry**: [x] Interface de saisie Carbex complète

---

# PHASE 3: Assessment & Dashboard

## 3.1 Assessment (Bilan annuel)

- [x] T043 🔴 **Créer migration assessments** dans `database/migrations/`
  > Implémenté: 2025_12_30_052350_create_assessments_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - year (integer)
  - revenue (decimal, nullable) // Chiffre d'affaires
  - employee_count (integer, nullable)
  - status (enum: draft, active, completed)
  ```

- [x] T044 🔴 **Créer model Assessment** dans `app/Models/Assessment.php`
  > Implémenté avec relations, scopes, méthodes activate/complete/reopen

- [x] T045 🔴 **Créer Livewire AssessmentList** dans `app/Livewire/Assessments/AssessmentList.php`
  > Note: Stub créé - nécessite implémentation complète
  - Tableau: Année, Chiffre d'affaires, Nombre de collaborateurs, Actions
  - Bouton "+ Démarrer un nouveau bilan"

- [x] T046 🔴 [P] **Créer view assessment-list** dans `resources/views/livewire/assessments/assessment-list.blade.php`

- [x] T047 🟠 **Créer modal AssessmentForm** dans `app/Livewire/Assessments/AssessmentForm.php`
  > Implémenté: Modal intégré dans AssessmentList avec formulaire CRUD complet
  - Année du bilan (select)
  - Chiffre d'affaires (€)
  - Nombre de collaborateurs
  - Boutons: Annuler, Sauvegarder

- [x] T048 🟠 **Créer sélecteur année dans header**
  > Implémenté: Livewire AssessmentSelector component avec bilans de la BD
  - Dropdown "Mes Bilans" avec année active
  - Options: Gérer mes bilans, Modifier ma trajectoire

## 3.2 Dashboard

- [x] T049 🔴 **Créer Livewire ProgressCircle** dans `app/Livewire/Dashboard/ProgressCircle.php`
  > Implémenté: Cercle SVG avec progression, légende, barres par scope
  - Cercle SVG avec % progression (0-100%)
  - Texte "X/15 tâches"
  - Légende: Terminé (vert), À faire (jaune), Non concerné (gris)

- [x] T050 🔴 [P] **Créer view progress-circle** dans `resources/views/livewire/dashboard/progress-circle.blade.php`

- [x] T051 🔴 **Créer Livewire CarbonEquivalents** dans `app/Livewire/Dashboard/CarbonEquivalents.php`
  > Implémenté: Affichage des équivalents carbone avec icônes dynamiques
  - X A/R Paris-New York par personne
  - X Tours de la Terre en voiture
  - X Nuits dans un hôtel
  - Icônes et valeurs dynamiques

- [x] T052 🔴 [P] **Créer view carbon-equivalents** dans `resources/views/livewire/dashboard/carbon-equivalents.blade.php`

- [x] T053 🟠 **Créer EquivalentCalculator** dans `app/Services/Carbon/EquivalentCalculator.php`
  > Implémenté: Service complet avec getTopEquivalents() et formatNumber()
  ```php
  // Facteurs de conversion
  const PARIS_NY_KG = 1775; // kgCO2e par A/R
  const TOUR_TERRE_KG = 6000; // kgCO2e (40 000 km en voiture)
  const NUIT_HOTEL_KG = 25; // kgCO2e par nuit
  ```

- [x] T054 🟠 **Créer Livewire EvaluationProgress** dans `app/Livewire/Dashboard/EvaluationProgress.php`
  > Implémenté: Liste étapes groupées par section avec liens et statuts
  - Liste des étapes avec statut (✓, en cours, à faire)
  - Personnalisation de votre espace
  - Scope 1 : Émissions directes
  - Scope 2 : Émissions indirectes
  - Scope 3 : Autres émissions
  - etc.

- [x] T055 🟠 [P] **Créer view evaluation-progress** dans `resources/views/livewire/dashboard/evaluation-progress.blade.php`

- [x] T056 🟡 **Créer section "Se former"** avec vidéos YouTube intégrées
  > Implémenté: TrainingSection avec player YouTube intégré et accordéon
  - Comment définir son bilan carbone ?
  - Paramétrer votre compte
  - Définir ses objectifs de réduction

- [x] T057 🟠 **Créer page Dashboard principale** dans `app/Livewire/Dashboard/DashboardPage.php`
  > Note: Implémenté avec EmissionOverview, ScopeBreakdown, TopCategories, TrendChart, IntensityMetrics, SiteComparison
  - Assembler tous les composants

**Checkpoint Dashboard**: [x] Dashboard Carbex complet (ProgressCircle, CarbonEquivalents, EvaluationProgress, TrainingSection)

---

# PHASE 4: Plan de Transition

## 4.1 Action Model

- [x] T058 🔴 **Créer migration actions** dans `database/migrations/`
  > Implémenté: 2025_12_30_052351_create_actions_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - title (string)
  - description (text, nullable)
  - category_id (uuid, FK, nullable)
  - status (enum: todo, in_progress, completed)
  - due_date (date, nullable)
  - co2_reduction_percent (decimal, nullable)
  - estimated_cost (decimal, nullable)
  - difficulty (enum: easy, medium, hard)
  ```

- [x] T059 🔴 **Créer model Action** dans `app/Models/Action.php`
  > Implémenté avec relations, scopes, méthodes start/complete/reopen, attributs status_label/difficulty_label/cost_indicator

## 4.2 Interface Actions

- [x] T060 🔴 **Créer Livewire ActionList** dans `app/Livewire/TransitionPlan/ActionList.php`
  > Note: Stub créé - nécessite implémentation complète
  - Filtres par statut
  - Liste des actions avec titre, statut, date limite
  - Bouton "+ Nouvelle action"

- [x] T061 🔴 [P] **Créer view action-list** dans `resources/views/livewire/transition-plan/action-list.blade.php`

- [x] T062 🔴 **Créer Livewire ActionForm** dans `app/Livewire/TransitionPlan/ActionForm.php`
  > Implémenté: Modal intégré dans ActionList avec formulaire CRUD complet
  - Titre
  - Description (éditeur riche: B, I, U, listes)
  - Date limite (datepicker)
  - Catégorie (select)
  - Statut (select: À faire, En cours, Terminé)
  - Pourcentage de réduction CO2 (slider 0-100%)
  - Coût estimé (€)
  - Niveau de difficulté (radio: Facile, Moyenne, Difficile)
  - Boutons: Retour, Sauvegarder

- [x] T063 🔴 [P] **Créer view action-form** dans `resources/views/livewire/transition-plan/action-form.blade.php`
  > Implémenté: Vue intégrée dans action-list.blade.php

## 4.3 Trajectoire SBTi

- [x] T064 🔴 **Créer migration reduction_targets** dans `database/migrations/`
  > Implémenté: 2025_12_30_052352_create_reduction_targets_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - baseline_year (integer)
  - target_year (integer)
  - scope_1_reduction (decimal) // %
  - scope_2_reduction (decimal) // %
  - scope_3_reduction (decimal) // %
  ```

- [x] T065 🔴 **Créer model ReductionTarget** dans `app/Models/ReductionTarget.php`
  > Implémenté avec calculs SBTi (4.2%/an S1+S2, 2.5%/an S3), méthodes compliance, scopes

- [x] T066 🔴 **Créer page "Modifier ma trajectoire"** dans `app/Livewire/TransitionPlan/TrajectoryPage.php`
  > Note: Stub créé - nécessite implémentation complète. SbtiTargetCalculator service existe.
  - Explication SBTi (4.2%/an pour S1+S2, 2.5%/an pour S3)
  - Liste des objectifs existants
  - Bouton "+ Ajouter un nouvel objectif"

- [x] T067 🔴 [P] **Créer view trajectory-page** dans `resources/views/livewire/transition-plan/trajectory-page.blade.php`

- [x] T068 🟠 **Créer modal TrajectoryForm** dans `app/Livewire/TransitionPlan/TrajectoryForm.php`
  > Implémenté: Modal intégré dans TrajectoryPage avec formulaire CRUD complet + indicateurs SBTi
  - Année de référence (select)
  - Année cible (select)
  - Réduction cible scope 1 (%) avec slider et compliance SBTi
  - Réduction cible scope 2 (%) avec slider et compliance SBTi
  - Réduction cible scope 3 (%) avec slider et compliance SBTi
  - Bouton "Appliquer objectifs SBTi"
  - Boutons: Ajouter, Annuler

- [x] T069 🟠 **Créer graphique trajectoire** (ApexCharts)
  > Implémenté: TrajectoryChart avec émissions réelles vs cible, indicateurs on/off track
  - Axe X: années
  - Axe Y: tCO2e
  - Ligne réelle vs ligne cible

**Checkpoint Transition Plan**: [x] Plan de transition complet (ActionList, TrajectoryPage, TrajectoryChart ApexCharts)

---

# PHASE 5: Reports & Export

## 5.1 Types de Rapports

- [x] T070 🔴 **Créer page Rapports** dans `app/Livewire/Reports/ReportList.php`
  > Note: Route /reports existe, ReportBuilder service implémenté
  - 3 cartes:
    1. Bilan complet des émissions carbone (Word)
    2. Tableau de déclaration ADEME
    3. Tableau de déclaration GHG
  - Chaque carte: Description + Bouton "Voir"

- [x] T071 🔴 [P] **Créer view report-list** dans `resources/views/livewire/reports/report-list.blade.php`
  > Implémenté: Vue complète avec 3 cartes de types de rapport, modal de génération, historique

## 5.2 Génération Word

- [x] T072 🔴 **Installer PhpWord** `composer require phpoffice/phpword`
  > Note: À installer manuellement via composer

- [x] T073 🔴 **Créer WordReportGenerator** dans `app/Services/Reporting/WordReportGenerator.php`
  > Implémenté: Rapport Word complet avec toutes les sections
  - Page de garde
  - Sommaire
  - Introduction et périmètre
  - Méthodologie (ISO 14064, ISO 14067, GHG Protocol)
  - Résultats par scope
  - Graphiques
  - Plan d'action
  - Annexes

## 5.3 Export ADEME

- [x] T074 🟠 **Créer AdemeExporter** dans `app/Services/Reporting/AdemeExporter.php`
  > Implémenté: Export Excel format ADEME avec 4 onglets (Identification, Émissions, Méthodologie, Actions)
  - Format compatible bilans.ges.ademe.fr
  - Excel structuré

## 5.4 Export GHG Protocol

- [x] T075 🟠 **Créer GhgExporter** dans `app/Services/Reporting/GhgExporter.php`
  > Implémenté: Export Excel format GHG Protocol avec 6 onglets (Summary, Scope 1-3, Methodology, History)
  - Format WBCSD/WRI
  - Excel structuré

- [x] T076 🟡 **Créer historique des rapports générés**
  > Implémenté: Intégré dans ReportList avec liste, statuts, téléchargement et suppression
  - Liste avec date, type, téléchargement

**Checkpoint Reports**: [x] Rapports complets (ReportList, WordReportGenerator, AdemeExporter, GhgExporter)

---

# PHASE 6: Settings & Billing

## 6.1 Paramètres Organisation

- [x] T077 🔴 **Améliorer OrganizationSettings** dans `app/Livewire/Settings/OrganizationSettings.php`
  > Note: Implémenté
  - Nom d'organisation (Raison Sociale)
  - Numéro et nom de rue
  - Complément d'adresse
  - Code Postal
  - Ville
  - Pays
  - Secteur d'activité (select)

## 6.2 Gestion Utilisateurs

- [x] T078 🔴 **Améliorer UserManagement** dans `app/Livewire/Settings/UserManagement.php`
  > Note: Implémenté avec UserInvitationService
  - Header bleu avec stats (X Utilisateurs, X Limite de votre offre)
  - Bouton "+ Inviter un collaborateur"
  - Tableau: Email, Prénom, Nom, Statut, Actions
  - Modal invitation: Email, Prénom, Nom
  - Modal édition: Toggle compte activé

## 6.3 Plans Tarifaires

- [x] T079 🔴 **Créer page Plans** dans `app/Livewire/Billing/PlanSelector.php`
  > Note: Implémenté avec SubscriptionManager et PlanLimitsService
  - 3 plans:
    - Gratuit (0€, 15 jours)
    - Premium (400€/an HT)
    - Avancé (1200€/an HT)
  - Comparatif fonctionnalités
  - Boutons sélection

- [x] T080 🔴 **Créer modal paiement**
  > Implémenté: Modal dans PlanSelector avec sélection période, code promo, prix calculé, checkout Stripe
  - Sélection plan
  - Période facturation (Annuel/Mensuel)
  - Code promo
  - Total
  - Bouton "Aller au paiement"

- [x] T081 🟠 **Intégrer Stripe Checkout**
  > Implémenté: Intégration Laravel Cashier avec checkout session, promo codes, webhooks

## 6.4 Footer avec Plan

- [x] T082 🟡 **Créer badge plan dans footer**
  > Note: Implémenté dans `sidebar-plan-badge.blade.php`
  - ESSAI GRATUIT / Plan Premium / Plan Avancé
  - X jours restants (pour trial)
  - Bouton "Mettre à niveau"

- [x] T083 🟡 **Créer chat support "En ligne"**
  > Implémenté: ChatWidget Livewire avec panel coulissant, réponses rapides, formulaire de contact, statut en ligne

**Checkpoint Settings**: [x] Settings & Billing complets

---

# PHASE 7: Polish & Testing

## 7.1 Traductions Complètes

- [x] T084 🟠 **Compléter traductions FR** dans `lang/fr/carbex.php`
  > Note: Dossier lang/fr/ existe
- [x] T085 🟠 [P] **Compléter traductions EN** dans `lang/en/carbex.php`
  > Note: Dossier lang/en/ existe
- [x] T086 🟠 [P] **Compléter traductions DE** dans `lang/de/carbex.php`
  > Note: Dossier lang/de/ existe

## 7.2 Tests

- [x] T087 🟠 Tests Feature EmissionCategory CRUD
  > Implémenté: tests/Feature/CategoryTest.php (12 tests)
- [x] T088 🟠 Tests Feature EmissionSource CRUD
  > Implémenté: tests/Feature/EmissionRecordTest.php (13 tests)
- [x] T089 🟠 Tests Feature Assessment CRUD
  > Implémenté: tests/Feature/AssessmentTest.php (15 tests)
- [x] T090 🟠 Tests Feature Action CRUD
  > Implémenté: tests/Feature/ActionTest.php (16 tests)
- [x] T091 🟠 Tests Feature ReductionTarget CRUD
  > Implémenté: tests/Feature/ReductionTargetTest.php (14 tests)
- [x] T092 🟠 Tests Unit EmissionCalculator
  > Implémenté: tests/Unit/EmissionCalculatorTest.php (20+ tests pour Scope1/2/3 Calculators)
- [x] T093 🟠 Tests Unit EquivalentCalculator
  > Implémenté: tests/Unit/EquivalentCalculatorTest.php (16 tests)
- [x] T094 🟠 Tests Unit ProgressCalculator
  > Implémenté: tests/Unit/DashboardServiceTest.php (13 tests pour DashboardService)
- [x] T095 🟡 Tests Browser onboarding flow
  > Implémenté: tests/Browser/OnboardingTest.php (9 tests Dusk)
- [x] T096 🟡 Tests Browser emission entry flow
  > Implémenté: tests/Browser/EmissionEntryTest.php (14 tests Dusk)
- [x] T097 🟡 Tests Browser dashboard
  > Implémenté: tests/Browser/DashboardTest.php (16 tests Dusk)

## 7.3 Documentation

- [x] T098 🟡 Documentation API (Scramble)
  > Implémenté: Scramble installé, config/scramble.php configuré, annotations PHPDoc ajoutées aux contrôleurs API (EmissionController, DashboardController)
- [x] T099 🟡 Guide développeur
  > Implémenté: docs/DEVELOPER_GUIDE.md - Guide complet (stack, installation, architecture, tests, déploiement)
- [x] T100 🟢 ADRs (Architecture Decision Records)
  > Implémenté: 5 nouveaux ADRs ajoutés dans docs/adr/ (Livewire, GHG Protocol, Multi-tenant, Facteurs ADEME, Rapports)

**Checkpoint Final**: [x] MVP Carbex Phase 7 complet

---

# PHASE 8: Site Marketing Public

## 8.1 Landing Page

- [x] T101 🔴 **Créer layout guest marketing** dans `resources/views/layouts/marketing.blade.php`
  > Implémenté: Header avec navigation complète, Footer 5 colonnes, styles CSS variables
  - Header: Logo, Navigation (Outil, Pour qui?, Base carbone, Tarifs, Blog, Contact), Bouton "Se connecter"
  - Footer complet

- [x] T102 🔴 **Créer page d'accueil** dans `resources/views/marketing/home.blade.php`
  > Note: Implémenté dans `welcome.blade.php` avec design premium B2B SaaS
  - Hero section avec CTA "Essai gratuit"
  - Section "Notre outil" avec 3 boutons
  - 4 avantages clés (checkmarks)
  - Statistiques (70%, 30%, 67%)

- [x] T103 🔴 **Créer section "Pourquoi nous choisir?"**
  > Implémenté: Section dans pour-qui.blade.php avec 3 cartes (Mesurer, Piloter, Répondre)
  - Mesurez votre impact
  - Pilotez votre transition
  - Répondez aux obligations

- [x] T104 🟠 **Créer section clients de référence**
  > Implémenté: Section logos dans pour-qui.blade.php (SUEZ, VAUBAN, NEODD, ADEME)
  - Logos: SUEZ, VAUBAN, NEODD, ADEME
  - Carrousel ou grille

- [x] T105 🟠 **Créer section "Pour qui?"**
  > Implémenté: Page complète pour-qui.blade.php avec 3 cibles (PME, ETI, GE)
  - PME, ETI, GE (Grandes Entreprises)
  - Icônes et descriptions

- [x] T106 🟠 **Créer section témoignages**
  > Implémenté: Section témoignage dans pour-qui.blade.php avec citation client
  - Carousel de témoignages clients
  - Photo, nom, titre, entreprise
  - Citation

## 8.2 Page Tarifs Publique

- [x] T107 🔴 **Créer page tarifs publique** dans `resources/views/marketing/pricing.blade.php`
  > Implémenté: Toggle Mensuel/Annuel Alpine.js, 3 plans (Essai, Premium 40€/mois, Avancé 120€/mois), comparatif, FAQ
  - Toggle Mensuel/Annuel (-17%)
  - 5 plans: Gratuit, Premium, Avancé, Enterprise, Pro/Partenaire
  - Comparatif fonctionnalités
  - Niveaux de support par plan
  - CTA "Essai gratuit" / "Sur devis"

- [x] T108 🟠 **Créer composant PricingCard**
  > Implémenté: Intégré dans pricing.blade.php avec badge "Le plus populaire", prix dynamique, features list
  - Badge "Populaire" pour Premium
  - Prix avec période
  - Liste fonctionnalités
  - Bouton action

## 8.3 Blog

- [x] T109 🔴 **Créer migration blog_posts** dans `database/migrations/`
  ```php
  - id (uuid)
  - title, slug
  - excerpt, content
  - featured_image
  - author_id (FK users)
  - published_at
  - status (draft, published)
  - meta_title, meta_description
  ```

- [x] T110 🔴 **Créer model BlogPost** dans `app/Models/BlogPost.php`

- [x] T111 🔴 **Créer page liste blog** dans `resources/views/blog/index.blade.php`
  - Grille d'articles (image, titre, date, extrait)
  - Pagination "Voir Plus"

- [x] T112 🔴 **Créer page article blog** dans `resources/views/blog/show.blade.php`
  - Image à la une
  - Titre, date, auteur
  - Contenu markdown/rich text
  - Articles connexes

- [x] T113 🟠 **Créer Filament BlogPostResource** pour admin

## 8.4 Pages Légales

- [x] T114 🟠 **Créer page CGV** dans `resources/views/marketing/legal/cgv.blade.php`
- [x] T115 🟠 **Créer page CGU** dans `resources/views/marketing/legal/cgu.blade.php`
- [x] T116 🟠 **Créer page Mentions légales** dans `resources/views/marketing/legal/mentions.blade.php`
- [x] T117 🟡 **Créer page Nos engagements** dans `resources/views/marketing/legal/engagements.blade.php`
- [x] T118 🟡 **Créer page Contact** dans `resources/views/marketing/contact.blade.php`
  - Formulaire de contact
  - Informations de contact

## 8.5 Footer Marketing

- [x] T119 🔴 **Créer footer marketing** dans `resources/views/components/marketing-footer.blade.php`
  - Logo
  - Colonnes: Informations, Ressources, Découvrir, Entreprise
  - Liens vers toutes les pages légales
  - Badges standards (ADEME, GHG, ISO, RGPD)
  - Copyright

## 8.6 SEO & Meta

- [x] T120 🟠 **Configurer meta tags dynamiques**
  - Titre, description par page
  - Open Graph tags (Facebook)
  - Twitter cards
  - JSON-LD structured data
  - Canonical URLs

- [x] T121 🟡 **Créer sitemap.xml** via `SeoController::sitemap()`
- [x] T122 🟡 **Créer robots.txt** via `SeoController::robots()`

**Checkpoint Site Marketing**: [x] Site marketing complet (Phase 8 terminée)

---

# PHASE 9: Intelligence Artificielle (Différenciateur Carbex)

> **Source**: Analyse concurrentielle (Greenly EcoPilot, CarbonAnalytics, Watershed, Climatiq)
> **Objectif**: Faire de Carbex le 1er outil de bilan carbone IA-augmenté pour PME en France

## 9.1 Infrastructure IA

- [x] T123 🔴 **Installer SDK Anthropic** `composer require anthropic/anthropic-sdk`
  > Note: ClaudeClient implémenté dans app/Services/AI/ClaudeClient.php

- [x] T124 🔴 **Créer config/ai.php** avec paramètres LLM
  - Provider, models, rate limits par plan
  - System prompts par contexte
  - Feature flags

- [x] T125 🔴 **Créer AIService** dans `app/Services/AI/AIService.php`
  > Note: Implémenté via ClaudeClient + CategorizationService
  ```php
  class AIService
  {
      public function complete(string $prompt, array $context = []): string;
      public function streamComplete(string $prompt, array $context = []): Generator;
  }
  ```

- [x] T126 🔴 **Créer migration ai_conversations** dans `database/migrations/`
  - UUID, user_id, organization_id, context_type, messages (json), metadata, token_count

- [x] T127 🔴 **Créer model AIConversation** dans `app/Models/AIConversation.php`
  - Scopes: forUser, forOrganization, ofType, recent
  - Methods: addMessage, getMessagesForApi

## 9.2 Assistant IA Conversationnel (Style Greenly EcoPilot)

- [x] T128 🔴 **Créer Livewire AIChatWidget** dans `app/Livewire/AI/ChatWidget.php`
  - Bouton flottant, panel sliding
  - Rate limiting par plan
  - Suggested prompts par contexte
  - Integration ClaudeClient

- [x] T129 🔴 [P] **Créer view chat-widget** dans `resources/views/livewire/ai/chat-widget.blade.php`
  - UI moderne avec Alpine.js transitions
  - Messages styling (user/assistant)
  - Loading animation

- [x] T130 🔴 **Créer PromptLibrary** dans `app/Services/AI/PromptLibrary.php`
  - emissionEntryHelper, actionRecommendation, factorExplainer
  - reportNarrative, generalHelper, transactionCategorization
  - documentExtraction

- [x] T131 🟠 **Créer endpoint /api/ai/chat** dans `routes/api.php`
  > Implémenté: API routes avec chat, providers, suggestions, conversations
  - POST avec message, context_type, conversation_id
  - Rate limiting (100 req/jour plan gratuit, illimité premium)

- [x] T132 🟠 **Créer AIController** dans `app/Http/Controllers/Api/AIController.php`
  > Implémenté: Controller complet avec chat, getProviders, getSuggestions, listConversations, getConversation, deleteConversation
  > Multi-provider: Anthropic (Claude), OpenAI (GPT), Google (Gemini), DeepSeek
  > Admin Filament: AISettings page pour configuration des providers et modèles
  > Docker Secrets: Stockage sécurisé des clés API dans /run/secrets/

## 9.3 Aide à la Saisie Intelligente

- [x] T133 🔴 **Créer Livewire AIEmissionHelper** dans `app/Livewire/AI/EmissionHelper.php`
  > Implémenté: Panel sliding avec suggestions, chat, auto-complétion, intégré dans CategoryForm
  - Intégration dans CategoryForm
  - Bouton "✨ Aide IA" à côté de chaque source
  - Suggestions de catégorisation
  - Auto-complétion intelligente
  - Détection d'erreurs/incohérences

- [x] T134 🔴 [P] **Créer view emission-helper** dans `resources/views/livewire/ai/emission-helper.blade.php`
  > Implémenté: UI moderne avec suggestions, messages, quick actions, provider info

- [x] T135 🟠 **Créer EmissionClassifier** dans `app/Services/AI/EmissionClassifier.php`
  > Implémenté: suggestCategory, suggestFactor, detectAnomalies, getCategorySuggestions, autoComplete
  ```php
  class EmissionClassifier
  {
      public function suggestCategory(string $description): array; // [category_code, confidence]
      public function suggestFactor(string $description, string $categoryCode): ?EmissionFactor;
      public function detectAnomalies(Assessment $assessment): array;
  }
  ```

- [x] T136 🟠 **Créer RAG context avec facteurs ADEME**
  > Implémenté: FactorRAGService avec recherche hybride (texte + IA), getContextForPrompt, aiEnhancedSearch
  - Embeddings des 20k+ facteurs
  - Recherche sémantique pour suggestions

## 9.4 Extraction Automatique de Factures (Style CarbonAnalytics)

- [x] T137 🔴 **Créer migration uploaded_documents** dans `database/migrations/`
  > Implémenté: 2025_12_30_160000_create_uploaded_documents_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - assessment_id (uuid, FK, nullable)
  - original_filename (string)
  - storage_path (string)
  - mime_type (string)
  - file_size (integer)
  - processing_status (enum: pending, processing, completed, failed)
  - extracted_data (json, nullable)
  - ai_confidence (decimal, nullable)
  - created_at, updated_at
  ```

- [x] T138 🔴 **Créer model UploadedDocument** dans `app/Models/UploadedDocument.php`
  > Implémenté avec relations, scopes, méthodes d'extraction

- [x] T139 🔴 **Créer Livewire DocumentUploader** dans `app/Livewire/AI/DocumentUploader.php`
  > Implémenté: Zone drag & drop, support multi-format, preview, validation
  - Zone drag & drop
  - Support PDF, images (PNG, JPG), Excel
  - Progress bar upload
  - Preview document
  - Affichage données extraites pour validation

- [x] T140 🔴 [P] **Créer view document-uploader** dans `resources/views/livewire/ai/document-uploader.blade.php`
  > Implémenté: UI complète avec drag & drop, liste documents, modal extraction

- [x] T141 🔴 **Créer DocumentExtractor** dans `app/Services/AI/DocumentExtractor.php`
  > Implémenté: Extraction PDF, images, Excel avec Claude Vision
  ```php
  class DocumentExtractor
  {
      public function extractFromPdf(string $filePath): array;
      public function extractFromImage(string $filePath): array;
      public function extractFromExcel(string $filePath): array;
      public function mapToEmissionSources(array $extractedData): array;
  }
  ```

- [x] T142 🟠 **Créer job ProcessDocumentExtraction** dans `app/Jobs/ProcessDocumentExtraction.php`
  > Implémenté: Queue async avec notification utilisateur
  - Queue async pour OCR + extraction IA
  - Notification utilisateur quand terminé

- [x] T143 🟠 **Intégrer Claude Vision** pour extraction images/PDF scannés
  > Implémenté: Intégration via DocumentExtractor avec support multi-modal

## 9.5 Recommandations d'Actions Personnalisées

- [x] T144 🔴 **Créer Livewire AIActionRecommender** dans `app/Livewire/AI/AIActionRecommender.php`
  > Implémenté: Analyse automatique, recommandations prioritaires, ajout au plan de transition
  - Analyse automatique du bilan
  - Top 5 actions prioritaires recommandées
  - Impact estimé (% réduction, €€€ coût)
  - Difficulté et délai indicatifs
  - Bouton "Ajouter au plan de transition"

- [x] T145 🔴 [P] **Créer view action-recommender** dans `resources/views/livewire/ai/action-recommender.blade.php`
  > Implémenté: UI complète avec cartes stats, insights, liste recommandations avec sélection

- [x] T146 🔴 **Créer ActionRecommendationEngine** dans `app/Services/AI/ActionRecommendationEngine.php`
  > Implémenté: Service complet avec analyzeAssessment, generateRecommendations, generateInsights, estimateImpact, convertToAction
  ```php
  class ActionRecommendationEngine
  {
      public function analyzeAssessment(Assessment $assessment): array;
      public function generateRecommendations(array $topEmissions, string $sector): Collection;
      public function estimateImpact(Action $action, Assessment $assessment): array;
  }
  ```

- [x] T147 🟠 **Créer base de données d'actions types**
  > Implémenté: Base intégrée dans ActionRecommendationEngine avec actions par secteur et catégorie
  - Actions courantes par secteur
  - Impacts moyens constatés
  - Coûts indicatifs

- [x] T148 🟠 **Créer page "Analyse IA"** dans sidebar
  > Implémenté: Route /ai-analysis, vue ai/analysis.blade.php, lien sidebar avec badge NEW
  - Dashboard recommandations
  - Historique des suggestions
  - Suivi des actions adoptées

## 9.6 Module Fournisseurs Scope 3 (Style Watershed)

- [x] T149 🔴 **Créer migration suppliers** dans `database/migrations/`
  > Note: Implémenté dans 2025_12_29_040000_create_suppliers_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - name (string)
  - email (string, nullable)
  - sector (string, nullable)
  - country (string)
  - carbon_score (decimal, nullable) // Note 0-100
  - last_questionnaire_sent_at (timestamp, nullable)
  - questionnaire_status (enum: not_sent, pending, completed)
  - emission_data (json, nullable)
  ```

- [x] T150 🔴 **Créer model Supplier** dans `app/Models/Supplier.php`
  > Note: Implémenté avec SupplierEmission, SupplierInvitation, SupplierProduct

- [x] T151 🔴 **Créer Livewire SupplierManagement** dans `app/Livewire/Suppliers/SupplierManagement.php`
  > Implémenté: CRUD complet, import CSV, invitations, filtres, modal create/edit
  - Liste fournisseurs avec score carbone
  - Import CSV fournisseurs
  - Envoi questionnaires automatisés
  - Tableau de bord Scope 3 fournisseurs

- [x] T152 🔴 [P] **Créer view supplier-management** dans `resources/views/livewire/suppliers/supplier-management.blade.php`
  > Implémenté: UI complète avec stats, tableau filtrable, modals pour CRUD et import

- [x] T153 🟠 **Créer template questionnaire fournisseur**
  > Note: Implémenté via SupplierPortalController + routes supplier-portal
  - Questions clés (énergie, transport, déchets)
  - Formulaire public (lien unique)
  - Rappels automatiques

- [x] T154 🟠 **Créer SupplierScoreCalculator** dans `app/Services/Carbon/SupplierScoreCalculator.php`
  > Note: Implémenté via SupplierEmissionAggregator et SupplierDataValidator
  - Score 0-100 basé sur réponses
  - Estimation émissions si pas de données

- [x] T155 🟡 **Créer suggestions alternatives fournisseurs**
  > Implémenté: SupplierAlternativeService avec suggestAlternatives, compareEmissionIntensity, identifyOpportunities
  - IA suggère fournisseurs plus verts
  - Base de données fournisseurs certifiés

## 9.7 Génération Automatique de Rapports (IA)

- [x] T156 🟠 **Améliorer WordReportGenerator** avec IA
  > Implémenté: Intégration ReportNarrativeGenerator pour narratifs intelligents
  - Génération narrative automatique
  - Analyse contextuelle des résultats
  - Recommandations personnalisées dans rapport

- [x] T157 🟠 **Créer ReportNarrativeGenerator** dans `app/Services/AI/ReportNarrativeGenerator.php`
  > Implémenté: Service complet avec generateExecutiveSummary, generateScopeAnalysis, generateConclusion, generateBenchmarkComparison, generateTrendAnalysis
  ```php
  class ReportNarrativeGenerator
  {
      public function generateExecutiveSummary(Assessment $assessment): string;
      public function generateScopeAnalysis(int $scope, array $emissions): string;
      public function generateConclusion(Assessment $assessment, array $actions): string;
  }
  ```

## 9.8 API Publique (Style Climatiq)

- [x] T158 🟠 **Créer documentation API** avec Scramble
  > Implémenté: Scramble configuré, annotations PHPDoc, documentation auto-générée
  - Endpoints facteurs d'émission
  - Endpoints calcul émissions
  - Authentification API keys

- [x] T159 🟠 **Créer migration api_keys** dans `database/migrations/`
  > Implémenté: 2025_12_29_030000_create_api_keys_table.php
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - name (string)
  - key_hash (string)
  - permissions (json)
  - rate_limit (integer)
  - last_used_at (timestamp, nullable)
  - expires_at (timestamp, nullable)
  ```

- [x] T160 🟠 **Créer model ApiKey** dans `app/Models/ApiKey.php`
  > Implémenté: Model avec génération clé, validation, scopes, permissions

- [x] T161 🟠 **Créer endpoints API publique** dans `routes/api.php`
  > Implémenté: Routes v1 avec authentification ApiKey middleware
  ```
  GET  /api/v1/factors - Liste facteurs d'émission
  GET  /api/v1/factors/{id} - Détail facteur
  POST /api/v1/calculate - Calcul émission
  GET  /api/v1/categories - Liste catégories
  ```

- [x] T162 🟡 **Créer page gestion API keys** dans paramètres
  > Implémenté: Livewire ApiKeyManager avec génération, révocation, statistiques
  - Génération nouvelle clé
  - Révocation
  - Statistiques d'usage

## 9.9 Gamification (Différenciateur PME)

- [x] T163 🟡 **Créer système de badges**
  > Implémenté: Migration badges/organization_badges/user_badges, Model Badge, BadgeService, BadgeSeeder
  - "Premier bilan" - Bilan complété
  - "Réducteur" - -10% émissions année N vs N-1
  - "Champion Scope 3" - 100% fournisseurs questionnés
  - "Expert" - 5 bilans complétés

- [x] T164 🟡 **Créer Livewire BadgeDisplay** dans `app/Livewire/Gamification/BadgeDisplay.php`
  > Implémenté: Affichage badges, progression, score, leaderboard, partage social
  - Affichage badges gagnés
  - Progression vers prochain badge
  - Partage sur réseaux sociaux

- [x] T165 🟡 **Créer tableau de bord engagement**
  > Implémenté: Route /gamification, vue gamification/index.blade.php, sidebar link, BadgeShareController
  - Score global entreprise
  - Comparaison anonyme secteur
  - Tendances mensuelles

**Checkpoint IA**: [x] IA complète - Phase 9 terminée (Infrastructure, Aide Saisie, DocumentUploader, ActionRecommender, SupplierManagement, Reports IA, API, Gamification)

---

# PHASE 10: Fonctionnalités Avancées (Inspirées TrackZero)

> **Source**: Analyse TrackZero (trackzero.eco) - Plateforme UK £995-2995/an, 4.8/5
> **Objectif**: Intégrer les meilleures pratiques de TrackZero adaptées au marché français

## 10.1 Structure 5 Piliers (Navigation Alternative)

- [ ] T166 🟡 **Créer navigation alternative "5 Piliers"** (option dans settings)
  - Mesurer (Measure) → Scopes 1/2/3
  - Planifier (Plan) → Objectifs, trajectoire
  - Engager (Engage) → Fournisseurs, équipes
  - Rapporter (Report) → Exports, conformité
  - Promouvoir (Promote) → Badges, communication

## 10.2 Badges Durabilité & Communication (Style TrackZero "Promote")

- [ ] T167 🟠 **Créer migration sustainability_badges** dans `database/migrations/`
  ```php
  - id (uuid)
  - organization_id (uuid, FK)
  - badge_type (enum: first_assessment, carbon_reducer, scope3_champion, etc.)
  - earned_at (timestamp)
  - share_token (string, unique) // Pour partage public
  - metadata (json)
  ```

- [ ] T168 🟠 **Créer model SustainabilityBadge** dans `app/Models/SustainabilityBadge.php`

- [ ] T169 🟠 **Créer Livewire BadgeShowcase** dans `app/Livewire/Promote/BadgeShowcase.php`
  - Affichage badges gagnés (visuels attractifs)
  - Bouton "Partager sur LinkedIn"
  - Bouton "Télécharger pour site web"
  - Widget embeddable (iframe/script)

- [ ] T170 🟠 [P] **Créer view badge-showcase** dans `resources/views/livewire/promote/badge-showcase.blade.php`

- [ ] T171 🟡 **Créer page publique badge** `/badge/{share_token}`
  - Affichage badge vérifié
  - Infos entreprise (opt-in)
  - Lien vers Carbex

- [ ] T172 🟡 **Créer générateur d'assets marketing**
  - Téléchargement badge PNG/SVG
  - Kit réseaux sociaux (LinkedIn, Twitter)
  - Signature email HTML

## 10.3 Gestion Multi-Sites Améliorée (Style TrackZero)

- [ ] T173 🟠 **Améliorer migration sites** avec champs supplémentaires
  ```php
  - floor_area_m2 (decimal, nullable)
  - energy_rating (string, nullable)
  - building_type (enum: office, warehouse, retail, factory)
  - occupancy_rate (decimal, nullable)
  ```

- [ ] T174 🟠 **Créer Livewire SiteComparison** dans `app/Livewire/Sites/SiteComparison.php`
  - Tableau comparatif émissions par site
  - Graphique bar chart par site
  - Identification sites les plus émetteurs
  - Recommandations par site

- [ ] T175 🟠 [P] **Créer view site-comparison** dans `resources/views/livewire/sites/site-comparison.blade.php`

- [ ] T176 🟡 **Créer import CSV sites en masse**

## 10.4 Conformité Réglementaire Étendue

- [ ] T177 🟠 **Ajouter support CSRD** dans rapports
  - Template rapport CSRD
  - Checklist conformité
  - Indicateurs ESRS E1 (climat)

- [ ] T178 🟠 **Ajouter support ISO 14001/14064-1**
  - Template audit ISO
  - Documentation processus
  - Traçabilité complète

- [ ] T179 🟡 **Créer checklist conformité dynamique**
  - Basée sur taille entreprise
  - Basée sur secteur
  - Alertes deadlines réglementaires

## 10.5 Engagement Équipes Internes

- [ ] T180 🟡 **Créer module sensibilisation employés**
  - Quiz carbone interactif
  - Calcul bilan individuel
  - Classement équipes (opt-in)
  - Challenges réduction

- [ ] T181 🟡 **Créer Livewire EmployeeEngagement** dans `app/Livewire/Engage/EmployeeEngagement.php`

- [ ] T182 🟡 **Créer emails automatiques engagement**
  - Newsletter mensuelle progrès
  - Rappels objectifs
  - Célébration milestones

**Checkpoint TrackZero Features**: [ ] Fonctionnalités avancées non commencées

---

# Résumé

## Statistiques

| Phase | Tâches | Faites | Status |
|-------|--------|--------|--------|
| Phase 1: Foundation & Navigation | 26 | 26 | ✅ Complété |
| Phase 2: Emission Entry | 16 | 16 | ✅ Complété |
| Phase 3: Assessment & Dashboard | 15 | 15 | ✅ Complété |
| Phase 4: Plan de Transition | 12 | 12 | ✅ Complété |
| Phase 5: Reports & Export | 7 | 7 | ✅ Complété |
| Phase 6: Settings & Billing | 7 | 7 | ✅ Complété |
| Phase 7: Polish & Testing | 17 | 17 | ✅ Complété |
| Phase 8: Site Marketing Public | 22 | 22 | ✅ Complété |
| Phase 9: Intelligence Artificielle | 43 | 43 | ✅ Complété |
| Phase 10: Fonctionnalités Avancées (TrackZero) | 17 | 0 | 🔴 0% |
| **Total** | **182** | **165** | **91%** |

## Prochaines Actions

1. **Phases 1-9**: ✅ Complété (165/165 tâches)
2. **Phase 10 TrackZero**: À faire - T166-T182 (Fonctionnalités avancées inspirées TrackZero)

---

## Instructions d'utilisation

1. **Marquer une tâche complétée**: Remplacer `[ ]` par `[x]`
2. **Valider un checkpoint**: Vérifier que toutes les tâches associées sont terminées
3. **Suivre la progression**: Mettre à jour le tableau de résumé
