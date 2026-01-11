# Carbex — Constitution du Projet

> **Document fondateur v3.0 — Décembre 2024**
> Plateforme SaaS de bilan carbone pour PME **augmentée par l'IA**

---

## 1. Vision et Objectifs

### 1.1 Mission

Carbex est une plateforme SaaS permettant aux PME de réaliser leur bilan carbone de manière guidée et structurée selon les standards GHG Protocol, ISO 14064 et ADEME. **Notre différenciateur clé : l'intégration native d'un assistant IA (LLM) qui réduit de 80% le temps de saisie et offre des recommandations personnalisées.**

### 1.2 Objectifs

1. **Offrir une interface intuitive** pour la comptabilité carbone PME
2. **Guider l'utilisateur** à travers les 3 scopes d'émissions
3. **Calculer automatiquement** les émissions à partir des facteurs ADEME
4. **Générer des rapports** conformes (BEGES, CSRD, GHG Protocol)
5. **Augmenter par l'IA** : Assistant intelligent pour recommandations, aide à la saisie, et extraction automatique de données

---

## 2. Architecture Fonctionnelle

### 2.1 Navigation Principale (Sidebar)

```
EMPREINTE CARBONE
├── Dashboard
├── Scope 1 - Émissions directes [%]
│   ├── 1.1 Sources fixes de combustion
│   ├── 1.2 Sources mobiles de combustion
│   ├── 1.4 Émissions fugitives
│   └── 1.5 Biomasse (sols et forêts)
├── Scope 2 - Émissions indirectes liées à l'énergie [%]
│   └── 2.1 Consommation d'électricité
├── Scope 3 - Autres émissions indirectes [%]
│   ├── 3.1 Transport de marchandise amont
│   ├── 3.2 Transport de marchandise aval
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

### 2.2 Dashboard

#### Composants :

1. **Cercle de progression**
   - Affiche le % d'avancement (0/15 tâches → 0%)
   - Indicateurs : Terminé (vert), À faire (jaune), Non concerné (gris)

2. **Équivalents carbone** (visualisation)
   - X A/R Paris-New York par personne
   - X Tours de la Terre en voiture (véhicule thermique)
   - X Nuits dans un hôtel (consommation annuelle)

3. **Progression de l'évaluation**
   - Personnalisation de votre espace ✓
   - Collecte des données (6 étapes)
     - Scope 1 : Émissions directes
     - Scope 2 : Émissions indirectes
     - Scope 3 : Autres émissions
     - etc.

4. **Section "Se former"**
   - Vidéos tutoriels YouTube intégrées
   - Comment définir son bilan carbone ?
   - Paramétrer votre compte Carbex
   - Définir ses objectifs de réduction

### 2.3 Scope 1 - Émissions Directes

#### 1.1 Sources fixes de combustion
| Source | Facteur | Unité |
|--------|---------|-------|
| Fioul domestique | 3,25 kg éq. CO2 | Litre |
| Gaz naturel | 0,215 kg éq. CO2 | kWh PCS |

#### 1.2 Sources mobiles de combustion
| Source | Facteur | Unité |
|--------|---------|-------|
| Essence | 2,80 kg éq. CO2 | Litre |
| Diesel/Gazole | 3,17 kg éq. CO2 | Litre |
| GPL | 1,86 kg éq. CO2 | Litre |
| Superéthanol | 1,68 kg éq. CO2 | Litre |

#### 1.4 Émissions fugitives
| Source | Facteur | Unité |
|--------|---------|-------|
| R134A | 1 300 kg éq. CO2 | kg |
| R410A | 2 088 kg éq. CO2 | kg |
| R407C | 1 774 kg éq. CO2 | kg |

#### 1.5 Biomasse (sols et forêts)
- Changement d'affectation des sols direct (forêt vers prairie)
- Facteurs par hectare/an

### 2.4 Scope 2 - Émissions Indirectes (Énergie)

#### 2.1 Consommation d'électricité
| Pays | Facteur (location-based) |
|------|-------------------------|
| France | 0,052 kgCO2e/kWh |
| Allemagne | 0,362 kgCO2e/kWh |

### 2.5 Scope 3 - Autres Émissions Indirectes

#### 3.1 Transport de marchandise amont
- CO2 - données transporteur (kgCO2)

#### 3.2 Transport de marchandise aval
- CO2 - données transporteur (kgCO2)

#### 3.3 Déplacements domicile-travail
| Mode | Facteur | Unité |
|------|---------|-------|
| Voiture motorisation essence | Variable | km parcouru |
| Voiture motorisation gazole | Variable | km parcouru |
| Voiture motorisation GPL | Variable | km parcouru |
| Voiture motorisation superéthanol | Variable | km parcouru |

#### 3.5 Déplacements professionnels
| Mode | Type |
|------|------|
| Avion court courrier | < 1000 km |
| Avion moyen courrier | 1000-3500 km |
| Avion long courrier | > 3500 km |

#### 4.1 Achats de biens
- Produits manufacturés, matières premières

#### 4.2 Immobilisations de biens
- Équipements, machines, bâtiments

#### 4.3 Gestion des déchets
- CO2 - données fournisseur

#### 4.4 Actifs en leasing amont
- Équipements loués

#### 4.5 Achats de services
- Services externalisés

### 2.6 Interface de Saisie des Émissions

Chaque source d'émission affiche :

```
┌─────────────────────────────────────────────────────────────┐
│ 2024 > [Organisation]                                        │
│ 1.1 Sources fixes de combustion                              │
│                                                              │
│ [Comment remplir cette catégorie?] [Marquer comme complété] │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ Fioul domestique                                             │
│ 1 litre = 3,25 kg éq. CO2                                   │
│                                                              │
│ Quantité          Notes                                      │
│ [________] Litre  [_________________________]               │
│                                                              │
│ [✏ Modifier le facteur d'émission] [+ Ajouter une action]  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ Gaz naturel                                                  │
│ 1 kWh PCS = 0,215 kg éq. CO2                                │
│                                                              │
│ Quantité          Notes                                      │
│ [________] kWh    [_________________________]               │
│                                                              │
│ [✏ Modifier le facteur d'émission] [+ Ajouter une action]  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ [+ Ajouter une source d'émission]                           │
│    Explorez plus de 20 000 facteurs d'émission              │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 2.7 Base de Données des Facteurs d'Émission

#### Onglets disponibles :
1. **Base Carbone® ADEME 23.7** - Référence française
2. **Base IMPACTS® ADEME 3.0** - Impacts environnementaux
3. **EF reference package 3.1** - Standards européens
4. **Données Primaires** - Facteurs personnalisés

#### Filtres :
- **Catégories principales** : Forêts, Métaux, Chimie, Transport, etc.
- **Localisation** : France continentale, Europe, Global
- **Unité** : kgCO2e/ha.an, kgCO2e/kg, kgCO2e/kWh, etc.

#### Recherche :
- Champ de recherche libre
- Affichage : 1 - 5 de 13219 items
- Pagination

#### Création de facteur personnalisé :
```
Nom : [________________________]
Description : [_________________]
Unité de référence : [kgCO2/] [ex: km]
[Sauvegarder]
```

### 2.8 Plan de Transition (Actions)

#### Liste des actions :
- Statut : À faire / En cours / Terminé
- Bouton : "+ Nouvelle action"

#### Formulaire de création/édition :
```
┌─────────────────────────────────────────────────────────────┐
│ Créer ou éditer une action                                   │
│ Nouvelle action ...                                          │
├─────────────────────────────────────────────────────────────┤
│ Titre                                                        │
│ [_______________________________________]                    │
│                                                              │
│ Description                                                  │
│ [B] [I] [U] [≡] [≡] [≡] [🔗]                                │
│ [                                        ]                   │
│ [                                        ]                   │
│                                                              │
│ Date limite          Catégorie              Statut           │
│ [jj/mm/aaaa] 📅     [Non catégorisé ▼]     [À faire ▼]      │
│                                                              │
│ ⬤ POURCENTAGE DE    💰 COÛT               ⚠ DIFFICULTÉ     │
│   RÉDUCTION CO2       [___] €€€              Facile          │
│   [●────────────]     €   €€  €€€€          ○ Moyenne       │
│   X%                                         ○ Difficile     │
│                                                              │
│                              [← Retour] [💾 Sauvegarder]    │
└─────────────────────────────────────────────────────────────┘
```

### 2.9 Trajectoire de Réduction (SBTi)

#### Page "Modifier ma trajectoire"

**Explication SBTi :**
> Dans une approche globale et absolue, la **Science Based Targets initiative** (SBTi) recommande de viser une réduction annuelle d'au moins **4,2%** des émissions de gaz à effet de serre pour les **scopes 1 et 2**, et de **2,5%** pour le **scope 3**, afin d'être aligné avec l'objectif de l'Accord de Paris, qui vise à limiter le réchauffement climatique à **1,5°C**.

#### Formulaire objectifs :
```
[+ Ajouter un nouvel objectif]

Année de référence : [Choisissez une année ▼]
Année cible : [Choisissez une année ▼]

Réduction cible scope 1 : [____] %
Réduction cible scope 2 : [____] %
Réduction cible scope 3 : [____] %

[Ajouter] [Annuler]
```

### 2.10 Gestion des Bilans

#### Page "Mes Bilans"

```
┌─────────────────────────────────────────────────────────────┐
│ Mes Bilans                     [+ Démarrer un nouveau bilan]│
├──────┬────────────────┬────────────────────┬────────────────┤
│ 📅   │ Chiffre        │ Nombre de          │                │
│ Année│ d'affaires     │ collaborateurs     │ Mettre à jour  │
├──────┼────────────────┼────────────────────┼────────────────┤
│ 2024 │ 0€             │ 0                  │ [Mettre à jour]│
└──────┴────────────────┴────────────────────┴────────────────┘
```

#### Modal "Démarrer un nouveau bilan" :
```
Année du bilan : [Sélectionner une année ▼]
Chiffre d'affaires : [0] €
Nombre de collaborateurs : [0]
[Annuler] [Sauvegarder]
```

### 2.11 Rapports & Exports

#### Types de rapports :

1. **Bilan complet des émissions carbone**
   - Format Word modifiable
   - Structuré et rigoureux
   - Conforme ISO 14064, ISO 14067, GHG Protocol
   - Exigences du bilan réglementaire GES

2. **Tableau de déclaration ADEME**
   - Export pour bilans.ges.ademe.fr
   - Plateforme dédiée ADEME
   - Format administratif français

3. **Tableau de déclaration GHG**
   - Protocole WBCSD/WRI
   - Reconnu mondialement
   - Standard international

Chaque rapport a un bouton **[Voir]** pour génération.

### 2.12 Paramètres Organisation

```
Nom d'organisation (Raison Sociale) : [____________________]
Numéro et nom de rue : [____________________]
Complément adresse : [____________________]
Code Postal : [____________________]
Ville : [____________________]
Pays : [____________________]
Secteur d'activité : [Sélectionnez un secteur ▼]
                                        [Sauvegarder]
```

### 2.13 Gestion des Utilisateurs

#### Page "Utilisateurs"

Header bleu avec stats :
- 👥 X Utilisateurs
- 📈 X Limite de votre offre
- [+ Inviter un collaborateur]

#### Tableau utilisateurs :
| Utilisateur | Prénom | Nom | Statut | Actions |
|-------------|--------|-----|--------|---------|
| email@... | Prénom | Nom | 🟢 Actif | [Éditer] [🗑] |

#### Modal "Inviter un collaborateur" :
```
Email : [exemple@mail.com]
Prénom : [Jean]
Nom : [Dupont]
[Annuler] [Envoyer l'invitation]
```

#### Modal "Éditer" :
```
Adresse email : [email@...]
Prénom : [____] Nom : [____]
Compte activé : [🔘 ON/OFF]
[Annuler] [💾 Sauvegarder]
```

### 2.14 Multi-Entités (Plan Avancé)

Modal "Gérer vos entités" :
> 🔓 Débloquez plus de fonctionnalités
> Accédez à la gestion multi-entités / multi-sites en passant au plan Avancé et simplifiez votre pilotage carbone à grande échelle.
> [Passez au plan Avancé]

> Vous êtes consultant et accompagnez plusieurs clients ? Profitez de notre offre exclusive Consultants et gérez facilement tous leurs bilans carbone depuis une seule plateforme.
> ✉ Contactez-nous pour en savoir plus.

---

## 3. Plans Tarifaires

### 3.1 Grille tarifaire

| Plan | Prix | Description | Support |
|------|------|-------------|---------|
| **Gratuit** | 0€ | Essai 15 jours pour démarrer votre démarche carbone | Support technique |
| **Premium** | 400 €/an HT | Pour gérer efficacement votre transition | Support fonctionnel |
| **Avancé** | 1200 €/an HT | Solution complète avec accompagnement expert | Support prioritaire |
| **Enterprise** | Sur devis | Solution sur-mesure pour les grandes organisations | Support dédié |
| **Pro/Partenaire** | Sur devis | Pour les professionnels du conseil en transition | Support partenaire |

### 3.2 Fonctionnalités par plan

#### Gratuit
- Fonctionnalités de base
- Accès limité (15 jours)
- 1 utilisateur

#### Premium
- Tout le Gratuit +
- Reporting automatique (Word)
- Export Excel formats standards (ADEME, GHG...)
- Modélisation et suivi de trajectoire carbone
- Dashboard / KPI
- Gestion d'un plan de transition
- Jusqu'à 5 utilisateurs

#### Avancé
- Tout le Premium +
- Accompagnement expert
- Analyse approfondie
- Support méthodologique avancé
- Multi-entités / Multi-sites
- Utilisateurs illimités

#### Enterprise
- Tout l'Avancé +
- Solution sur-mesure
- Intégrations personnalisées
- SLA garanti

### 3.3 Toggle facturation
- **Mensuel (sans engagement)** - Flexibilité maximale
- **Annuel** - Économie de ~17%

### 3.4 Modal de paiement
```
Choisir votre offre
[Premium] [Avancé]

Période de facturation : [Annuel 🔘] [Mensuel]
Code promo : [____________] [Tester]

Total : XXX eur/an HT
Abonnement annuel

[Aller au paiement]
```

---

## 4. Site Marketing Public

### 4.1 Navigation Principale (Header)
```
🌱 EMPREINTE CARBONE.org | Outil | Pour qui? | Base carbone ▼ | Tarifs | Blog | Contact | [Se connecter]
```

### 4.2 Page d'Accueil

#### Hero Section
- Titre accrocheur
- CTA principal: "Essai gratuit"

#### Section "Notre outil"
- Boutons: [Notre solution] [Méthodologie de calcul] [Notre offre]
- Description: Interface intuitive, estimation précise des émissions

#### 4 Avantages clés
| Icône | Avantage |
|-------|----------|
| ✓ | Conformité réglementaire |
| ✓ | Rapidité d'analyse |
| ✓ | Personnalisation |
| ✓ | Visualisation claire |

#### Statistiques
- **70%** d'entreprises qui prennent une longueur d'avance
- **30%** Réduction des coûts opérationnels obtenue grâce aux actions carbone
- **67%** des entreprises constatent une réduction significative de leurs émissions dès la première année

### 4.3 Section "Pourquoi nous choisir?"

| Icône | Titre | Description |
|-------|-------|-------------|
| ⭐ | Mesurez votre impact | Réalisez facilement votre premier bilan carbone complet (Scope 1, 2, 3), sans expert et sans engagement |
| 📈 | Pilotez votre transition | Suivez vos émissions dans le temps, fixez des objectifs de réduction et construisez un plan d'action concret |
| 📋 | Répondez aux obligations | Générez automatiquement vos rapports RSE, CSRD ou ESG, et démontrez votre conformité aux réglementations |

### 4.4 Clients de Référence
Logos: **SUEZ**, **VAUBAN**, **NEODD**, **ADEME**

### 4.5 Section "Pour qui?"

| Cible | Description |
|-------|-------------|
| **PME** | Mesurer l'empreinte carbone pour optimiser les coûts, répondre à la réglementation, et améliorer l'image de l'entreprise |
| **ETI** | Suivre l'impact global, réduire les émissions sur plusieurs sites et répondre aux demandes des clients tout en se préparant aux audits |
| **GE** (Grandes Entreprises) | Gérer l'empreinte carbone mondiale, respecter les normes internationales, et optimiser les stratégies de réduction des émissions tout en communiquant sur la RSE |

### 4.6 Témoignages Clients
```
"Le support expert nous a été précieux pour affiner nos interprétations.
La possibilité d'importer automatiquement nos FEC et de gérer plusieurs
sites a fait toute la différence. C'est un outil robuste et pro."

— Aicha Benhamou, Directrice Développement Durable chez Terres & Saveurs
```

### 4.7 Blog
Section "Notre Blog" avec articles récents:
- Compensation Carbone : Guide Complet
- Le marketing de la preuve : au-delà du greenwashing
- Bilan carbone territorial : de la connaissance à l'action

Bouton: [Voir Plus]

### 4.8 Footer

```
┌────────────────────────────────────────────────────────────────────────┐
│ 🌱 EMPREINTE                                                           │
│    CARBONE.org                                                         │
│                                                                        │
│ Informations      │ Ressources              │ Découvrir               │
│ ─────────────     │ ──────────              │ ─────────               │
│ CGV               │ Blog                    │ Outil                   │
│ CGU               │ Bilan carbone des       │ Pour qui ?              │
│ Nos engagements   │   entreprises           │ Tarifs                  │
│ Partenariat       │ Bilan carbone des       │ Nos gestes climat       │
│ Carrière          │   produits              │                         │
│ Contact           │ Nos tutos               │                         │
│ Mentions légales  │                         │                         │
│                                                                        │
│                Copyright © Carbex 2024. All rights reserved            │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Interface Utilisateur (App)

### 5.1 Header

```
┌─────────────────────────────────────────────────────────────┐
│ 🌱 EMPREINTE    🔔  📑  ⚙️  │ 📅 2024 ▼  │ 👤 Prénom ▼   │
│    CARBONE.org              │ Mes Bilans │   Nom         │
└─────────────────────────────────────────────────────────────┘
```

### 5.2 Menu Paramètres (⚙️)
- 📊 Mon entreprise
- 👥 Utilisateurs
- 🔧 Profil
- 🔒 Mot de passe

### 5.3 Menu Utilisateur (👤)
- Avatar avec initiales
- Nom complet
- [Gérer vos entités]
- [↪ Déconnexion]

### 5.4 Sélecteur d'année
- Mes Bilans
- Année active (ex: 2024)
- [Gérer mes bilans]
- [Modifier ma trajectoire]

### 5.5 Footer (App)
```
┌─────────────────────────────────────────────────────────────┐
│ ┌─────────────────┐                                         │
│ │ ESSAI GRATUIT   │                                         │
│ │ Plan Premium    │                        [💬 En ligne]   │
│ │ 15 jours restants│                                        │
│ │ [✨ Mettre à niveau]│                                     │
│ └─────────────────┘                                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. Stack Technique

### Backend
- **Framework** : Laravel 12
- **PHP** : 8.4+
- **Base de données** : PostgreSQL (UUID)
- **Cache/Queue** : Redis
- **Search** : Meilisearch

### Frontend
- **Admin Panel** : Filament 3
- **CSS** : Tailwind CSS
- **JS** : Alpine.js, Livewire 3

### Infrastructure
- **Containers** : Docker (nginx, php-fpm, postgres, redis, meilisearch, mailpit)
- **Web Server** : Nginx

---

## 7. Modèles de Données

### Organization
```php
- id (uuid)
- name, legal_name, slug
- address, city, postal_code, country
- sector_id
- settings (JSON)
- created_at, updated_at, deleted_at
```

### User
```php
- id (bigint)
- organization_id (uuid)
- email, password
- first_name, last_name
- role (owner, admin, member, viewer)
- is_active (boolean)
- email_verified_at
- last_login_at, last_login_ip
```

### Assessment (Bilan)
```php
- id (uuid)
- organization_id (uuid)
- year (integer)
- revenue (decimal)
- employee_count (integer)
- status (draft, active, completed)
```

### EmissionCategory
```php
- id (uuid)
- scope (1, 2, 3)
- code (ex: "1.1", "3.3")
- name_fr, name_en, name_de
- description
- parent_id (self-referential)
```

### EmissionSource
```php
- id (uuid)
- assessment_id (uuid)
- emission_category_id (uuid)
- emission_factor_id (uuid)
- quantity (decimal)
- unit (string)
- co2e_kg (decimal, calculé)
- notes (text)
- status (pending, completed, not_applicable)
```

### EmissionFactor
```php
- id (uuid)
- source (ademe, impacts, ef_reference, custom)
- name, description
- category, subcategory
- co2e_per_unit (decimal)
- unit (string)
- region (string)
- valid_from, valid_to (date)
- metadata (JSON)
```

### Action (Plan de transition)
```php
- id (uuid)
- organization_id (uuid)
- title, description
- category_id
- status (todo, in_progress, completed)
- due_date (date)
- co2_reduction_percent (decimal)
- estimated_cost (decimal)
- difficulty (easy, medium, hard)
```

### ReductionTarget (Trajectoire)
```php
- id (uuid)
- organization_id (uuid)
- baseline_year (integer)
- target_year (integer)
- scope_1_reduction (decimal, %)
- scope_2_reduction (decimal, %)
- scope_3_reduction (decimal, %)
```

---

## 8. Priorités d'Implémentation

### Phase 1 - MVP (En cours)
- [x] Authentification / Multi-tenant
- [x] Structure de navigation (sidebar)
- [ ] Dashboard avec progression
- [ ] Scope 1 - Saisie des émissions
- [ ] Scope 2 - Saisie des émissions
- [ ] Scope 3 - Saisie des émissions
- [ ] Base facteurs ADEME (import)

### Phase 2 - Core Features
- [ ] Calcul automatique des émissions
- [ ] Plan de transition (actions)
- [ ] Trajectoire SBTi
- [ ] Gestion des bilans par année
- [ ] Rapports PDF/Word

### Phase 3 - Advanced
- [ ] Multi-entités
- [ ] Système de facturation (Stripe)
- [ ] Export BEGES/CSRD
- [ ] API publique

---

## 9. Conventions de Code

- **Langue code** : Anglais
- **Langue UI** : Français (défaut), Anglais, Allemand
- **Naming** : snake_case (DB), camelCase (PHP), kebab-case (routes)
- **Tests** : PHPUnit, Pest
- **Standards** : PSR-12, Laravel conventions

---

## 10. Analyse Concurrentielle et Positionnement

### 10.1 Outils Analysés

| Outil | Cible | Prix | Points Forts | Faiblesses |
|-------|-------|------|--------------|------------|
| **Greenly** | PME/ETI | 539€-12k€/an | EcoPilot AI, 300k facteurs, accompagnement expert | Prix élevé, pas d'API |
| **Watershed** | Enterprise | >50k$/an | 60+ intégrations, audit-grade, Scope 3 avancé | Prix prohibitif, pas adapté FR |
| **Climatiq** | Développeurs | Freemium | API REST, 80+ sources, ISO 14067 validé | Pas d'UI, technique only |
| **TrackZero** | PME UK | £995-2995/an | 5 piliers (Measure/Plan/Engage/Report/Promote), Supply Chain, B Corp, ISO 27001, 4.8/5 | Prix élevé (1160€+), pas d'IA, focus UK (SECR/ESOS), pas de free tier |
| **CarbonAnalytics** | Enterprise | Non communiqué | IA extraction, 99% accuracy revendiquée | Moins mature, Scope 3 basique |
| **Concurrents FR** | PME FR | 0€-600€/an | UX simplifiée, ADEME natif, prix compétitif | Pas d'IA, intégrations limitées |

### 10.2 Positionnement Carbex

**Carbex = Simplicité PME + Intelligence Artificielle**

```
                           ┌─────────────────────────────────────────┐
                           │             ENTERPRISE                   │
                           │         Watershed, SAP                   │
                           │         (>50k€/an)                       │
        ┌──────────────────┼─────────────────────────────────────────┤
        │                  │                                          │
 SIMPLE │   Concurrents    │         Greenly                         │ COMPLEXE
  UX    │   FR basiques    │         (539-12k€)                      │   UX
        │                  │                                          │
        │   ★ CARBEX ★     │                                          │
        │   (IA-native)    │                                          │
        │                  │                                          │
        └──────────────────┼─────────────────────────────────────────┤
                           │              PME                         │
                           │         (0-1000€/an)                     │
                           └─────────────────────────────────────────┘
```

### 10.3 Différenciateurs Clés

| # | Différenciateur | Vs Concurrence | Valeur Client |
|---|----------------|----------------|---------------|
| 1 | **Assistant IA natif** | Greenly payant, autres n'ont pas | -80% temps saisie |
| 2 | **Prix PME + IA** | Greenly trop cher, concurrents sans IA | Best value FR |
| 3 | **Extraction auto factures** | CarbonAnalytics enterprise only | Autonomie totale |
| 4 | **API publique (futur)** | Climatiq API-only, autres fermés | Intégrations dev |
| 5 | **ADEME + IA recommandations** | Aucun concurrent combine les deux | Pertinence FR |

---

## 11. Rôle du LLM dans Carbex

### 11.1 Cas d'Usage de l'Assistant IA

| Cas d'Usage | Description | Priorité |
|-------------|-------------|----------|
| **Aide à la saisie** | Suggestion de catégories, auto-complétion, détection d'erreurs | 🔴 P0 |
| **Extraction factures** | Upload PDF → parsing automatique des données carbone | 🔴 P0 |
| **Recommandations actions** | Suggestions personnalisées de réduction basées sur le profil | 🔴 P0 |
| **Explication pédagogique** | Vulgarisation des résultats, tutoriels contextuels | 🟡 P1 |
| **Chatbot support** | Réponses aux questions méthodologiques (GHG, BEGES) | 🟡 P1 |
| **Benchmark intelligent** | Comparaison anonyme secteur avec insights | 🟢 P2 |
| **Génération rapports** | Rédaction automatique des narratifs de rapport | 🟢 P2 |

### 11.2 Architecture LLM

```
┌─────────────────────────────────────────────────────────────┐
│                     CARBEX APPLICATION                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │   Frontend   │───▶│   Backend    │───▶│  LLM Service │  │
│  │   (Vue/React)│    │   (Laravel)  │    │   (Claude)   │  │
│  └──────────────┘    └──────────────┘    └──────────────┘  │
│         │                   │                    │          │
│         ▼                   ▼                    ▼          │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │  Chat Widget │    │  AI Service  │    │   Prompts    │  │
│  │  (Livewire)  │    │  Controller  │    │   Library    │  │
│  └──────────────┘    └──────────────┘    └──────────────┘  │
│                             │                               │
│                             ▼                               │
│                    ┌──────────────┐                        │
│                    │ RAG Context  │                        │
│                    │ (ADEME data) │                        │
│                    └──────────────┘                        │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 11.3 Prompts Système (Exemples)

#### Assistant Saisie
```
Tu es l'assistant Carbex pour la saisie des émissions carbone.
Contexte: {user_sector}, {current_scope}, {previous_entries}
Tâche: Aide l'utilisateur à catégoriser "{user_input}" dans la bonne source d'émission.
Utilise la Base Carbone ADEME pour suggérer le facteur le plus pertinent.
```

#### Recommandations Actions
```
Tu es l'assistant Carbex pour les recommandations de réduction carbone.
Profil: {sector}, {employee_count}, {annual_emissions_by_scope}
Émissions principales: {top_5_emission_sources}
Tâche: Propose 5 actions de réduction prioritaires avec:
- Impact estimé (% réduction)
- Coût indicatif (€€€)
- Difficulté (facile/moyen/difficile)
- Délai de mise en œuvre
```

### 11.4 Modèle LLM Recommandé

| Critère | Recommandation |
|---------|----------------|
| **Modèle** | Claude 3.5 Sonnet (Anthropic) |
| **Raison** | Meilleur rapport qualité/prix, excellent en français |
| **Alternative** | GPT-4 Turbo, Mistral Large |
| **Fallback** | Claude Haiku (économique) |
| **Hébergement** | API Anthropic directe |

---

## 12. Décisions Produit Issues de l'Analyse

### 12.1 À Adopter (Best Practices Concurrence)

| Source | Fonctionnalité | Implémentation Carbex |
|--------|----------------|----------------------|
| Greenly | EcoPilot AI | Assistant IA conversationnel intégré |
| Greenly | 300k facteurs | Import complet Base Carbone ADEME |
| Watershed | Audit-grade data | Traçabilité complète + logs |
| Watershed | Supplier engagement | Module questionnaires fournisseurs |
| CarbonAnalytics | 80% automation | Extraction IA factures/données |
| Best practices | UX simplifiée | Conserver UX 5min onboarding |
| Climatiq | Free tier | Plan Starter généreux (3 bilans) |
| TrackZero | 5 Piliers (Measure/Plan/Engage/Report/Promote) | Navigation alternative optionnelle |
| TrackZero | Badges durabilité + partage LinkedIn | Module "Promouvoir" avec assets marketing |
| TrackZero | Gestion multi-sites avec comparaison | Dashboard comparatif sites + recommandations |
| TrackZero | Conformité étendue (CSRD, ISO) | Templates rapports CSRD, ISO 14064-1 |
| TrackZero | Engagement équipes | Quiz carbone, challenges internes |

### 12.2 À Éviter (Pièges Identifiés)

| Piège | Concurrent | Risque | Décision Carbex |
|-------|-----------|--------|-----------------|
| Prix enterprise | Watershed | Exclut PME | Garder tarifs <1200€/an |
| Complexité UX | Greenly | Abandons | Simplicité first |
| API-only | Climatiq | Non-technique exclus | UI + API optionnelle |
| Pas d'IA | Concurrents FR | Différenciation nulle | IA native obligatoire |
| Over-engineering | Tous | Time to market | MVP first, itérer |
| Prix sans free tier | TrackZero (£995 min) | Barrière entrée PME | Garder plan gratuit 15j |
| Focus réglementaire UK | TrackZero (SECR/ESOS) | Inadapté FR | Focus BEGES/ADEME/CSRD |

### 12.3 Opportunités Non Exploitées

| Opportunité | Potentiel | Roadmap |
|-------------|-----------|---------|
| 1er bilan carbone IA-augmenté FR | ⭐⭐⭐⭐⭐ | Phase 2 |
| Gamification réduction | ⭐⭐⭐⭐ | Phase 3 |
| Partenariats experts-comptables | ⭐⭐⭐⭐ | Phase 3 |
| App mobile PWA | ⭐⭐⭐ | Phase 4 |
| Benchmark communautaire PME | ⭐⭐⭐ | Phase 4 |

---

## 13. Principes Architecturaux

### 13.1 Principes Fondamentaux

1. **IA-Native, pas IA-Ajoutée** : L'IA n'est pas un add-on mais un pilier central
2. **Simplicité > Fonctionnalités** : Moins de features, mieux exécutées
3. **PME First** : Chaque décision validée pour une PME de 10 employés
4. **Conformité FR** : ADEME, BEGES, CSRD sont les standards prioritaires
5. **Open by Default** : API publique prévue dès la conception

### 13.2 Trade-offs Acceptés

| Choix | Au détriment de | Justification |
|-------|-----------------|---------------|
| Simplicité UX | Features avancées | Cible PME non-experte |
| Claude API | LLM self-hosted | Time to market, qualité |
| PostgreSQL | Scale infini | Suffisant pour 10k clients |
| Monolithe Laravel | Microservices | Complexité inutile MVP |
| ADEME only | Facteurs globaux | Marché FR prioritaire |
