<?php

/**
 * Carbex - French Translations
 */

return [

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    'app_name' => 'Carbex',
    'tagline' => 'Bilan carbone automatique pour PME',
    'welcome' => 'Bienvenue sur Carbex',
    'dashboard' => 'Tableau de bord',
    'settings' => 'Paramètres',
    'profile' => 'Profil',
    'logout' => 'Déconnexion',
    'login' => 'Connexion',
    'register' => 'Inscription',
    'save' => 'Enregistrer',
    'cancel' => 'Annuler',
    'delete' => 'Supprimer',
    'edit' => 'Modifier',
    'create' => 'Créer',
    'back' => 'Retour',
    'next' => 'Suivant',
    'previous' => 'Précédent',
    'search' => 'Rechercher',
    'filter' => 'Filtrer',
    'reset_filters' => 'Réinitialiser les filtres',
    'export' => 'Exporter',
    'import' => 'Importer',
    'download' => 'Télécharger',
    'upload' => 'Téléverser',

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [
        'ai' => 'IA',
        'loading' => 'Chargement...',
        'saving' => 'Enregistrement...',
        'processing' => 'Traitement...',
        'view_details' => 'Voir les détails',
        'view_all' => 'Voir tout',
        'select' => 'Sélectionner',
        'inactive' => 'Inactif',
        'active' => 'Actif',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'delete' => 'Supprimer',
        'edit' => 'Modifier',
        'create' => 'Créer',
        'back' => 'Retour',
        'next' => 'Suivant',
        'reset' => 'Réinitialiser',
        'date' => 'Date',
        'description' => 'Description',
        'quantity' => 'Quantité',
        'unit' => 'Unité',
        'amount_optional' => 'Montant (optionnel)',
        'calculating' => 'Calcul...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home Page
    |--------------------------------------------------------------------------
    */

    'home' => [
        'title' => 'Carbex - Plateforme de Bilan Carbone pour PME',
        'meta_description' => 'Pilotez votre empreinte carbone et décidez avec impact. La plateforme IA qui transforme les obligations carbone en décisions stratégiques pour les PME.',
        'badge' => 'Carbon Intelligence for SMEs',
        'csrd_badge' => 'Conforme CSRD 2025',

        // Navigation
        'nav' => [
            'features' => 'Fonctionnalités',
            'pricing' => 'Tarifs',
            'resources' => 'Ressources',
            'login' => 'Connexion',
            'start' => 'Commencer',
        ],

        // Hero section
        'hero' => [
            'title_line1' => 'Pilotez votre',
            'title_line2' => 'empreinte carbone.',
            'subtitle' => 'La plateforme IA qui transforme les obligations carbone en décisions stratégiques pour les PME.',
            'cta_primary' => 'Commencer gratuitement',
            'cta_secondary' => 'Voir comment ça marche',
            'no_commitment' => 'Sans engagement · 10 min · Données sécurisées',
            'badges' => 'Base ADEME · GHG Protocol · CSRD Ready',
        ],

        // Dashboard preview
        'preview' => [
            'total_footprint' => 'Empreinte totale',
            'vs_previous_year' => 'vs année précédente',
            'monthly_evolution' => 'Évolution mensuelle',
        ],

        // Features section
        'features' => [
            'title' => 'Comment ça marche',
            'subtitle' => '3 étapes simples pour piloter votre empreinte carbone',

            'step1' => [
                'title' => 'Mesurez automatiquement',
                'description' => 'Importez vos factures PDF, exports comptables ou fichiers Excel. Notre IA extrait et calcule vos émissions selon les normes GHG Protocol.',
                'item1' => 'Import PDF, Excel, ERP',
                'item2' => '20 000+ facteurs ADEME',
                'item3' => 'Scope 1, 2, 3 automatique',
            ],

            'step2' => [
                'title' => 'Comprenez avec l\'IA',
                'description' => 'Posez vos questions en langage naturel. Notre IA analyse vos données et identifie les leviers de réduction.',
                'item1' => 'Analyse de vos données',
                'item2' => 'Leviers de réduction',
                'item3' => 'Conformité CSRD/BEGES',
                'ai_question' => 'Quels sont mes principaux postes d\'émission ?',
                'ai_answer_title' => 'Vos 3 principaux postes :',
                'ai_answer1' => 'Achats de biens (42%)',
                'ai_answer2' => 'Déplacements (28%)',
                'ai_answer3' => 'Électricité (18%)',
            ],

            'step3' => [
                'title' => 'Réduisez concrètement',
                'description' => 'Recevez des recommandations personnalisées avec estimation d\'impact CO₂ et ROI.',
                'item1' => 'Actions par impact',
                'item2' => 'ROI et économies',
                'item3' => 'Rapports CSRD',
                'action1_title' => 'Flotte électrique',
                'action1_impact' => '-180 tCO₂e',
                'action1_details' => 'ROI : 24 mois · Économies : 12k€/an',
                'action2_title' => 'Énergie verte',
                'action2_impact' => '-120 tCO₂e',
                'action2_details' => 'ROI : 6 mois · Économies : 3k€/an',
            ],

            'upload' => [
                'file1_name' => 'facture-edf-2024.pdf',
                'file1_category' => 'Scope 2 · Électricité',
                'file1_status' => 'Traité',
                'file2_name' => 'export-comptable.xlsx',
                'file2_category' => 'Scope 3 · Achats',
                'file2_status' => 'En cours',
            ],
        ],

        // Stats section
        'stats' => [
            'title' => 'Pourquoi agir maintenant',
            'subtitle' => 'Le bilan carbone devient un avantage compétitif',
            'stat1_value' => '90%',
            'stat1_label' => 'des émissions PME viennent du Scope 3',
            'stat2_value' => '67%',
            'stat2_label' => 'des acheteurs préfèrent les entreprises responsables',
            'stat3_value' => '85%',
            'stat3_label' => 'des PME font des économies après leur bilan',
        ],

        // Pricing section
        'pricing' => [
            'title' => 'Tarifs simples',
            'subtitle' => 'Commencez gratuitement',

            'free' => [
                'name' => 'Gratuit',
                'price' => '0€',
                'period' => 'pour toujours',
                'feature1' => '5 imports',
                'feature2' => '1 rapport',
                'feature3' => 'Sans IA',
                'cta' => 'Commencer',
            ],

            'premium_monthly' => [
                'name' => 'Premium',
                'price' => '39€',
                'period' => 'par mois',
                'feature1' => 'IA (quota mensuel)',
                'feature2' => 'Imports illimités',
                'feature3' => '5 utilisateurs',
                'cta' => 'Choisir',
            ],

            'premium_annual' => [
                'name' => 'Premium',
                'price' => '400€',
                'period' => 'par an',
                'discount' => '-15%',
                'feature1' => 'IA illimitée',
                'feature2' => 'Imports illimités',
                'feature3' => '5 utilisateurs',
                'cta' => 'Choisir',
            ],

            'enterprise' => [
                'name' => 'Entreprise',
                'price' => '840€',
                'period' => 'par an',
                'old_price' => '1200€',
                'discount' => '-30%',
                'feature1' => 'Tout Premium +',
                'feature2' => 'Users illimités',
                'feature3' => 'API + Support',
                'cta' => 'Contacter',
            ],
        ],

        // CTA section
        'cta' => [
            'title' => 'Prêt à commencer ?',
            'subtitle' => 'Lancez votre premier bilan carbone en 10 minutes.',
            'button' => 'Commencer gratuitement',
            'note' => 'Sans engagement · Sans carte bancaire',
        ],

        // Footer
        'footer' => [
            'tagline' => 'Bilan carbone pour PME',
            'description' => 'Plateforme de bilan carbone pour PME. Simple, précis et assisté par l\'IA.',
            'product' => 'Produit',
            'resources' => 'Ressources',
            'documentation' => 'Documentation',
            'csrd_guide' => 'Guide CSRD',
            'legal' => 'Légal',
            'privacy' => 'Confidentialité',
            'terms' => 'CGU',
            'compliance' => 'ADEME · GHG Protocol · RGPD',
            'information' => 'Informations',
            'terms_sale' => 'CGV',
            'terms_use' => 'CGU',
            'commitments' => 'Nos engagements',
            'legal_notice' => 'Mentions légales',
            'contact' => 'Contact',
            'blog' => 'Blog',
            'guides' => 'Guides pratiques',
            'carbon_footprint' => 'Bilan carbone entreprise',
            'csrd_regulation' => 'Réglementation CSRD',
            'discover' => 'Découvrir',
            'features' => 'Fonctionnalités',
            'for_whom' => 'Pour qui ?',
            'pricing' => 'Tarifs',
            'free_trial' => 'Essai gratuit',
            'company' => 'Entreprise',
            'partnership' => 'Partenariat',
            'careers' => 'Carrières',
            'press' => 'Espace presse',
            'gdpr' => 'RGPD',
            'compliant' => 'Conforme',
            'all_rights_reserved' => 'Tous droits réservés.',
            'cookies' => 'Cookies',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Entry / Manual Entry
    |--------------------------------------------------------------------------
    */

    'data_entry' => [
        'title' => 'Saisie manuelle',
        'subtitle' => 'Enregistrez les activités non capturées par les transactions bancaires.',
        'success' => 'Activité enregistrée avec succès ! Les émissions ont été calculées et ajoutées à votre bilan carbone.',
        'activity_type' => 'Type d\'activité',
        'energy' => 'Énergie',
        'travel' => 'Déplacements',
        'purchases' => 'Achats',
        'waste' => 'Déchets',
        'freight' => 'Fret',
        'site' => 'Site',
        'select_site' => 'Sélectionner un site...',
        'emission_category' => 'Catégorie d\'émission',
        'select_category' => 'Sélectionner une catégorie...',
        'description_placeholder' => 'Ex: Consommation électricité T1 2025, Voyage professionnel Paris-Berlin...',
        // Travel fields
        'origin' => 'Origine',
        'destination' => 'Destination',
        'origin_placeholder' => 'Ex: Paris, CDG',
        'destination_placeholder' => 'Ex: Berlin, TXL',
        'travel_class' => 'Classe de voyage',
        'standard' => 'Standard',
        'economy' => 'Économique',
        'business' => 'Affaires',
        'first_class' => 'Première classe',
        'passengers' => 'Nombre de passagers',
        // Energy fields
        'fuel_type' => 'Type de carburant/énergie',
        'not_specified' => 'Non spécifié',
        'grid_electricity' => 'Électricité réseau',
        'renewable_electricity' => 'Électricité renouvelable',
        'natural_gas' => 'Gaz naturel',
        'diesel' => 'Diesel',
        'petrol' => 'Essence',
        'lpg' => 'GPL',
        'heating_oil' => 'Fioul domestique',
        // Calculation
        'calculate' => 'Calculer les émissions',
        'calculation_result' => 'Résultat du calcul des émissions',
        'co2e_kg' => 'CO₂e (kg)',
        'co2e_tonnes' => 'CO₂e (tonnes)',
        'scope' => 'Scope',
        'methodology' => 'Méthodologie',
        'emission_factor' => 'Facteur d\'émission :',
        'save_activity' => 'Enregistrer l\'activité',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'login_title' => 'Connectez-vous à votre compte',
        'login_subtitle' => 'Gérez votre empreinte carbone',
        'login_button' => 'Se connecter',
        'email' => 'Adresse e-mail',
        'password' => 'Mot de passe',
        'remember_me' => 'Se souvenir de moi',
        'forgot_password' => 'Mot de passe oublié ?',
        'no_account' => 'Pas encore de compte ?',
        'register_link' => 'Créer un compte',
        'register_title' => 'Créer votre compte',
        'register_subtitle' => 'Commencez votre bilan carbone',
        'register_button' => 'S\'inscrire',
        'name' => 'Nom complet',
        'confirm_password' => 'Confirmer le mot de passe',
        'already_have_account' => 'Déjà un compte ?',
        'login_link' => 'Se connecter',
        'reset_password' => 'Réinitialiser le mot de passe',
        'send_reset_link' => 'Envoyer le lien',
        'reset_link_sent' => 'Un lien de réinitialisation a été envoyé.',
        'step_account' => 'Compte',
        'step_organization' => 'Organisation',
        'organization_name' => 'Nom de l\'organisation',
        'country' => 'Pays',
        'sector' => 'Secteur d\'activité',
        'select_sector' => 'Sélectionnez un secteur',
        'organization_size' => 'Taille de l\'entreprise',
        'select_size' => 'Sélectionnez une taille',
        'employees' => 'employés',
        'password_requirements' => 'Minimum 8 caractères',
        'accept_terms_html' => 'J\'accepte les <a href="/legal/terms" class="text-green-600 hover:underline">conditions d\'utilisation</a>',
        'accept_privacy_html' => 'J\'accepte la <a href="/legal/privacy" class="text-green-600 hover:underline">politique de confidentialité</a>',
        'create_account' => 'Créer mon compte',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'dashboard' => 'Tableau de bord',
        'emissions' => 'Émissions',
        'transactions' => 'Transactions',
        'banking' => 'Banques',
        'reports' => 'Rapports',
        'settings' => 'Paramètres',
        'help' => 'Aide',
        'notifications' => 'Notifications',
        'bookmarks' => 'Signets',
        'my_profile' => 'Mon profil',
        'logout' => 'Déconnexion',
    ],

    'navigation' => [
        'dashboard' => 'Tableau de bord',
        'emissions' => 'Émissions',
        'transactions' => 'Transactions',
        'banking' => 'Banques',
        'reports' => 'Rapports',
        'settings' => 'Paramètres',
        'profile' => 'Profil',
        'logout' => 'Déconnexion',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title' => 'Tableau de bord',
        'add_emission' => 'Ajouter une émission',
        'overview_for' => 'Aperçu de l\'empreinte carbone pour :organization',
        'refresh_data' => 'Actualiser les données',
        'total_emissions' => 'Émissions totales',
        'this_month' => 'Ce mois',
        'this_year' => 'Cette année',
        'vs_last_month' => 'vs mois dernier',
        'vs_last_year' => 'vs année dernière',
        'scope_breakdown' => 'Répartition par scope',
        'category_breakdown' => 'Répartition par catégorie',
        'trend' => 'Tendance',
        'top_emitters' => 'Principaux émetteurs',
        'recent_transactions' => 'Transactions récentes',
        'pending_validation' => 'En attente de validation',
        'no_transactions' => 'Aucune transaction',
        'connect_bank_prompt' => 'Connectez vos comptes bancaires pour commencer à suivre vos émissions.',
        // Progress Circle
        'progress_title' => 'Progression du bilan',
        'completed' => 'complété',
        'of' => 'sur',
        'categories' => 'catégories',
        'legend_completed' => 'Terminé',
        'legend_todo' => 'À faire',
        'legend_na' => 'Non concerné',
        // Carbon Equivalents
        'equivalents_title' => 'Équivalents carbone',
        'equivalents_subtitle' => 'Vos émissions représentent...',
        'no_emissions' => 'Aucune émission enregistrée pour cette période.',
        // Emission Overview
        'direct_emissions' => 'Émissions directes',
        'indirect_energy' => 'Énergie indirecte',
        'value_chain' => 'Chaîne de valeur',
        'transaction_coverage' => 'Couverture des transactions',
        'categorized_of_total' => ':categorized sur :total catégorisées',
        'pending_count' => ':count en attente',
        // Scope Breakdown
        'emissions_by_scope' => 'Émissions par scope',
        'total' => 'Total',
        'records' => 'enregistrements',
        'no_data' => 'Aucune donnée',
        'scope1_desc' => 'Émissions directes des sources détenues (flotte, chauffage)',
        'scope2_desc' => 'Émissions indirectes de l\'énergie achetée',
        'scope3_desc' => 'Toutes les autres émissions indirectes de la chaîne de valeur',
        // Trend Chart
        'emission_trends' => 'Tendances des émissions',
        'no_trend_data' => 'Aucune donnée de tendance disponible',
        'trend_data_hint' => 'Les données d\'émission apparaîtront ici une fois les transactions traitées.',
        // Top Categories
        'top_categories' => 'Principales catégories d\'émission',
        'treemap' => 'Treemap',
        'bar_chart' => 'Graphique en barres',
        'no_category_data' => 'Aucune donnée de catégorie',
        'category_data_hint' => 'Les catégories apparaîtront une fois les transactions catégorisées.',
        'transactions' => 'transactions',
        // Intensity Metrics
        'emission_intensity' => 'Intensité des émissions',
        'intensity_help' => 'Les métriques d\'intensité aident à comparer les émissions par rapport à la taille de l\'entreprise ou aux dépenses',
        'per_employee' => 'Par employé',
        'per_revenue' => 'Par chiffre d\'affaires',
        'per_area' => 'Par surface',
        // Site Comparison
        'emissions_by_site' => 'Émissions par site',
        'no_site_data' => 'Aucune donnée de site',
        // Filters
        'filter_by_site' => 'Filtrer par site',
        'all_sites' => 'Tous les sites',
        'custom_range' => 'Plage personnalisée',
        'start_date' => 'Date de début',
        'end_date' => 'Date de fin',
        'quarters' => 'Trimestres',
        'apply' => 'Appliquer',
        // Site Comparison
        'add_sites_prompt' => 'Ajoutez des sites à votre organisation pour voir les comparaisons.',
        'emissions' => 'Émissions',
        // Intensity Metrics
        'per_1000_eur' => 'Par 1 000 € dépensés',
        'emission_intensity_per_1000' => 'intensité carbone par 1 000 €',
        'total_spend' => 'dépenses totales',
        'employees' => 'employés',
        'industry_benchmarks' => 'Références sectorielles (Moyenne)',
        'sme_services' => 'PME (Services)',
        'sme_manufacturing' => 'PME (Industrie)',
        'sme_retail' => 'PME (Commerce)',
        'sme_it' => 'PME (IT)',
        'benchmarks_source' => 'Source : références moyennes ADEME/UBA pour les PME européennes',
        // Scope labels
        'scope_1' => 'Scope 1',
        'scope_2' => 'Scope 2',
        'scope_3' => 'Scope 3',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carbon Equivalents
    |--------------------------------------------------------------------------
    */

    'equivalents' => [
        'paris_ny' => 'A/R Paris-New York',
        'round_trips' => 'voyages',
        'earth_tours' => 'Tours de la Terre en voiture',
        'tours' => 'tours',
        'hotel_nights' => 'Nuits d\'hôtel',
        'nights' => 'nuits',
        'car_km' => 'Kilomètres en voiture',
        'french_person' => 'Empreinte annuelle française',
        'years' => 'années',
        'trees_needed' => 'Arbres nécessaires pour compenser',
        'trees' => 'arbres',
        'streaming' => 'Heures de streaming vidéo',
        'hours' => 'heures',
    ],

    /*
    |--------------------------------------------------------------------------
    | Evaluation Progress
    |--------------------------------------------------------------------------
    */

    'evaluation' => [
        'title' => 'Étapes d\'évaluation',
        'completed' => 'complétées',
        'setup' => 'Configuration',
        'setup_organization' => 'Paramètres de l\'organisation',
        'setup_organization_desc' => 'Nom, secteur d\'activité, pays',
        'setup_sites' => 'Ajouter vos sites',
        'setup_sites_desc' => 'Bureaux, entrepôts, usines...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Training Section
    |--------------------------------------------------------------------------
    */

    'training' => [
        'title' => 'Se former',
        'subtitle' => 'Vidéos et ressources pour maîtriser votre bilan carbone',
        'video1_title' => 'Qu\'est-ce qu\'un bilan carbone ?',
        'video1_desc' => 'Comprendre les fondamentaux des émissions de GES',
        'video2_title' => 'Paramétrer votre compte',
        'video2_desc' => 'Guide de démarrage rapide avec Carbex',
        'video3_title' => 'Définir vos objectifs de réduction',
        'video3_desc' => 'Stratégies et meilleures pratiques SBTi',
        'coming_soon' => 'Bientôt disponible',
        'need_help' => 'Besoin d\'aide personnalisée ?',
        'contact_support' => 'Contacter le support',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    'scopes' => [
        'scope1_name' => 'Émissions directes',
        'scope2_name' => 'Émissions indirectes liées à l\'énergie',
        'scope3_name' => 'Autres émissions indirectes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Units
    |--------------------------------------------------------------------------
    */

    'units' => [
        'tonnes' => 't',
        'kg' => 'kg',
        'g' => 'g',
    ],

    /*
    |--------------------------------------------------------------------------
    | Emissions
    |--------------------------------------------------------------------------
    */

    'emissions' => [
        'title' => 'Émissions',
        'activities' => 'Activités',
        'scope_title' => 'Émissions Scope :scope',
        'scope_label' => 'Scope :scope',
        'scope_coming_soon' => 'Détails du Scope :scope à venir prochainement.',
        'total' => 'Total des émissions',
        'scope_1' => 'Scope 1 - Émissions directes',
        'scope_2' => 'Scope 2 - Énergie',
        'scope_3' => 'Scope 3 - Émissions indirectes',
        'unit' => 'tCO2e',
        'unit_kg' => 'kgCO2e',
        'factor' => 'Facteur d\'émission',
        'source' => 'Source',
        'confidence' => 'Confiance',
        'validated' => 'Validé',
        'pending' => 'En attente',
        'help_category' => 'Comment remplir cette catégorie?',
        'mark_completed' => 'Marquer comme complété',
        'category_completed' => 'Catégorie marquée comme complétée.',
        'sources_title' => 'Sources d\'émission',
        'sources_subtitle' => 'Ajoutez et configurez les sources d\'émission pour cette catégorie',
        'add_source' => 'Ajouter une source',
        'edit_source' => 'Modifier la source',
        'new_source' => 'Nouvelle source d\'émission',
        'no_sources' => 'Aucune source d\'émission',
        'no_sources_hint' => 'Commencez par ajouter une source d\'émission',
        'source_name' => 'Nom de la source',
        'source_name_placeholder' => 'Ex: Électricité bureaux Paris',
        'select_factor' => 'Sélectionner un facteur d\'émission',
        'quantity' => 'Quantité',
        'calculated_emissions' => 'Émissions calculées',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette source ?',
        'validation' => [
            'name_required' => 'Le nom de la source est requis.',
            'quantity_required' => 'La quantité est requise.',
            'factor_required' => 'Veuillez sélectionner un facteur d\'émission.',
        ],
        'categories' => [
            'fuel_gasoline' => 'Essence',
            'fuel_diesel' => 'Diesel',
            'natural_gas' => 'Gaz naturel',
            'electricity' => 'Électricité',
            'business_travel_air' => 'Voyages avion',
            'business_travel_rail' => 'Voyages train',
            'business_travel_hotel' => 'Hôtels',
            'purchased_goods' => 'Achats',
            'cloud_services' => 'Services cloud',
            'restaurant_meals' => 'Restauration',
        ],
        'factors' => [
            'title' => 'Sélectionner un facteur d\'émission',
            'subtitle' => 'Explorez plus de :count facteurs d\'émission',
            'search_placeholder' => 'Rechercher un facteur d\'émission...',
            'reset' => 'Réinitialiser',
            'no_results' => 'Aucun facteur trouvé',
            'no_results_hint' => 'Essayez de modifier vos critères de recherche ou créez un facteur personnalisé.',
            'showing' => 'Affichage de',
            'to' => 'à',
            'of' => 'sur',
            'results' => 'résultats',
            'kg_co2e_per' => 'kgCO2e/',
            'create_custom' => 'Créer un facteur personnalisé',
            'tabs' => [
                'all' => 'Tous',
                'ademe' => 'Base Carbone® ADEME',
                'uba' => 'UBA (Allemagne)',
                'ghg' => 'GHG Protocol',
                'custom' => 'Données Primaires',
            ],
            'filters' => [
                'all_countries' => 'Tous les pays',
                'all_units' => 'Toutes les unités',
            ],
            'countries' => [
                'fr' => 'France',
                'de' => 'Allemagne',
                'eu' => 'Union Européenne',
                'gb' => 'Royaume-Uni',
                'us' => 'États-Unis',
            ],
            'units' => [
                'liter' => 'Litre',
                'tonne' => 'Tonne',
            ],
            'validation' => [
                'name_required' => 'Le nom du facteur est requis.',
                'unit_required' => 'L\'unité est requise.',
                'value_required' => 'La valeur du facteur est requise.',
                'value_numeric' => 'La valeur doit être un nombre.',
            ],
            'custom' => [
                'title' => 'Créer un facteur personnalisé',
                'subtitle' => 'Définissez votre propre facteur d\'émission pour des sources spécifiques.',
                'name' => 'Nom du facteur',
                'name_placeholder' => 'Ex: Électricité photovoltaïque sur site',
                'description' => 'Description (optionnel)',
                'description_placeholder' => 'Décrivez la source et la méthodologie...',
                'unit' => 'Unité',
                'value' => 'Valeur (kgCO2e)',
                'info' => 'Ce facteur personnalisé sera associé à votre organisation et pourra être utilisé dans vos calculs d\'émissions.',
                'create' => 'Créer le facteur',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Banking
    |--------------------------------------------------------------------------
    */

    'banking' => [
        'title' => 'Connexions bancaires',
        'connect' => 'Connecter une banque',
        'callback_title' => 'Connexion bancaire',
        'processing_connection' => 'Traitement de la connexion bancaire...',
        'disconnect' => 'Déconnecter',
        'refresh' => 'Actualiser',
        'last_sync' => 'Dernière synchronisation',
        'status' => [
            'connected' => 'Connectée',
            'disconnected' => 'Déconnectée',
            'error' => 'Erreur',
            'syncing' => 'Synchronisation...',
        ],
        'accounts' => 'Comptes',
        'transactions' => 'Transactions',
        'import_csv' => 'Importer un fichier CSV',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transactions',
        'date' => 'Date',
        'description' => 'Description',
        'amount' => 'Montant',
        'category' => 'Catégorie',
        'emissions' => 'Émissions',
        'status' => 'Statut',
        'validate' => 'Valider',
        'reject' => 'Rejeter',
        'recategorize' => 'Recatégoriser',
        'bulk_validate' => 'Valider la sélection',
        'filters' => [
            'all' => 'Toutes',
            'pending' => 'En attente',
            'validated' => 'Validées',
            'excluded' => 'Exclues',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'title' => 'Rapports & Exports',
        'subtitle' => 'Générez et téléchargez vos bilans carbone dans différents formats',
        'generate' => 'Générer',
        'form_coming_soon' => 'Formulaire de génération de rapport à venir prochainement.',
        'generate_report' => 'Générer le rapport',
        'generate_confirm' => 'Vous allez générer un rapport pour l\'année :year.',
        'download' => 'Télécharger',
        'download_pdf' => 'Télécharger PDF',
        'download_excel' => 'Télécharger Excel',
        'select_year' => 'Sélectionner l\'année',
        'type' => 'Type de rapport',
        'format' => 'Format',
        'period' => 'Période',
        'from' => 'Du',
        'to' => 'Au',

        // Report types
        'carbon_footprint' => 'Bilan Carbone Complet',
        'carbon_footprint_desc' => 'Rapport Word complet avec introduction, méthodologie, résultats par scope, graphiques et plan d\'action.',
        'ademe' => 'Déclaration ADEME',
        'ademe_desc' => 'Format Excel compatible avec la plateforme bilans-ges.ademe.fr pour la déclaration réglementaire.',
        'ghg' => 'GHG Protocol Report',
        'ghg_desc' => 'Export Excel au format WBCSD/WRI conforme aux standards internationaux GHG Protocol.',

        'types' => [
            'monthly' => 'Rapport mensuel',
            'quarterly' => 'Rapport trimestriel',
            'annual' => 'Rapport annuel',
            'beges' => 'BEGES simplifié',
            'custom' => 'Rapport personnalisé',
        ],

        // History
        'history' => 'Historique des rapports',
        'no_reports' => 'Aucun rapport généré',
        'no_reports_desc' => 'Sélectionnez un type de rapport ci-dessus pour commencer.',
        'downloads' => 'téléchargements',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce rapport ?',

        // Status
        'status_completed' => 'Prêt',
        'status_processing' => 'En cours',
        'status_pending' => 'En attente',
        'status_failed' => 'Échec',
        'include_details' => 'Inclure les détails',
        'generating' => 'Génération...',
        'generation_started' => 'La génération du rapport a démarré.',
        'generation_failed' => 'Erreur lors de la génération du rapport.',
        'pending_generation' => 'En attente de génération',
        'ready' => 'Rapport prêt',

        // Report content labels
        'ghg_inventory' => 'Inventaire GHG',
        'scope_breakdown' => 'Répartition par scope',
        'category_analysis' => 'Analyse par catégorie',
        'period_comparison' => 'Comparaison de périodes',

        // PDF Report content
        'pdf' => [
            'carbon_footprint_report' => 'Bilan Carbone',
            'executive_summary' => 'Résumé exécutif',
            'emissions_by_scope' => 'Émissions par scope',
            'top_categories' => 'Principales catégories d\'émissions',
            'emissions_by_site' => 'Émissions par site',
            'scope' => 'Scope',
            'description' => 'Description',
            'emissions' => 'Émissions',
            'category' => 'Catégorie',
            'records' => 'Enregistrements',
            'site' => 'Site',
            'location' => 'Localisation',
            'methodology' => 'Méthodologie',
            'standard' => 'Standard',
            'emission_factors' => 'Facteurs d\'émission',
            'note' => 'Note',
            'report_generated_on' => 'Rapport généré le',
            'compared_to_previous' => 'Par rapport à la période précédente',
            'reduction' => 'réduction',
            'increase' => 'augmentation',
            'stable' => 'Stable',
            'direct_emissions' => 'Émissions directes des sources détenues',
            'indirect_energy_emissions' => 'Émissions indirectes liées à l\'énergie',
            'value_chain_emissions' => 'Émissions de la chaîne de valeur',
            // Methodology section
            'calculation_standards' => 'Standards de calcul',
            'primary_standard' => 'Standard principal',
            'consolidation_approach' => 'Approche de consolidation',
            'operational_control' => 'Contrôle opérationnel',
            'base_year' => 'Année de référence',
            'reporting_period' => 'Période de reporting',
            'emission_factor_sources' => 'Sources des facteurs d\'émission',
            'source' => 'Source',
            'version' => 'Version',
            'applied_to' => 'Appliqué à',
            'all_french_operations' => 'Toutes les opérations françaises',
            'other_countries' => 'Autres pays',
            'scope_definitions' => 'Définitions des scopes',
            'scope1_emissions' => 'Émissions directes',
            'energy_indirect_emissions' => 'Émissions indirectes liées à l\'énergie',
            'scope3_emissions' => 'Émissions de la chaîne de valeur',
            'process_emissions' => 'Émissions de procédés',
            'purchased_electricity' => 'Électricité achetée',
            'data_quality_assessment' => 'Évaluation de la qualité des données',
            'data_type' => 'Type de données',
            'quality_score' => 'Score de qualité',
            'energy_consumption' => 'Consommation d\'énergie',
            'business_travel' => 'Voyages professionnels',
            'bank_transactions' => 'Transactions bancaires',
            'purchased_goods' => 'Biens achetés',
            'estimated_uncertainty' => 'Incertitude estimée globale',
            'estimated_impact' => 'Impact estimé',
            'verification_statement' => 'Attestation de vérification',
            'verified_by' => 'Cet inventaire de gaz à effet de serre a été vérifié par',
            'verification_date' => 'Date de vérification',
            'detailed_emissions_by_scope' => 'Émissions détaillées par scope',
            'calculation_method' => 'Méthode de calcul',
            'electricity_mix' => 'Mix électrique',
            'grid_emission_factor' => 'Facteur d\'émission du réseau',
            'currently_tracking' => 'Suivi actuellement',
            'categories_not_relevant' => 'Catégories jugées non pertinentes',
            'summary_by_scope' => 'Résumé par scope',
            'share_of_total' => 'Part du total',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */

    'organization' => [
        'title' => 'Organisation',
        'name' => 'Nom de l\'entreprise',
        'legal_name' => 'Raison sociale',
        'siret' => 'SIRET',
        'registration_number' => 'Numéro d\'immatriculation',
        'vat_number' => 'Numéro TVA',
        'address' => 'Adresse',
        'address_line_2' => 'Complément d\'adresse',
        'city' => 'Ville',
        'postal_code' => 'Code postal',
        'country' => 'Pays',
        'phone' => 'Téléphone',
        'website' => 'Site web',
        'sector' => 'Secteur d\'activité',
        'size' => 'Taille de l\'entreprise',
        'employees' => 'Nombre d\'employés',
        'fiscal_year' => 'Année fiscale',
        'fiscal_year_start' => 'Début de l\'exercice fiscal',
        'default_currency' => 'Devise par défaut',
        'timezone' => 'Fuseau horaire',
        'vat_rate' => 'Taux de TVA',
        'sites' => 'Sites',
        'add_site' => 'Ajouter un site',
        'general_info' => 'Informations générales',
        'general_info_desc' => 'Informations de base sur votre organisation',
        'legal_info' => 'Informations légales',
        'legal_info_desc' => 'Numéros d\'identification officiels',
        'contact_info' => 'Coordonnées',
        'fiscal_settings' => 'Paramètres fiscaux',
        'fiscal_settings_desc' => 'Année fiscale et devise de référence',
        'country_config' => 'Configuration pays',
        'country_config_desc' => 'Ces paramètres sont déterminés par le pays de votre organisation',
        'currencies' => [
            'eur' => 'EUR - Euro',
            'chf' => 'CHF - Franc suisse',
            'gbp' => 'GBP - Livre sterling',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription
    |--------------------------------------------------------------------------
    */

    'subscription' => [
        'title' => 'Abonnement',
        'current_plan' => 'Plan actuel',
        'upgrade' => 'Passer à un plan supérieur',
        'downgrade' => 'Rétrograder',
        'cancel' => 'Annuler l\'abonnement',
        'trial' => 'Période d\'essai',
        'trial_ends' => 'Fin de l\'essai',
        'plans' => [
            'starter' => 'Starter',
            'business' => 'Business',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
        ],
        'features' => [
            'bank_connections' => 'Connexions bancaires',
            'transactions_month' => 'Transactions/mois',
            'users' => 'Utilisateurs',
            'reports' => 'Rapports',
            'api_access' => 'Accès API',
            'support' => 'Support',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */

    'billing' => [
        'title' => 'Tarifs & Abonnement',
        'subscription' => 'Abonnement',
        'subtitle' => 'Choisissez le plan adapté à vos besoins',
        'current_plan' => 'Plan actuel',
        'monthly' => 'Mensuel',
        'annual' => 'Annuel',
        'per_month' => 'mois HT',
        'per_year' => 'an HT',
        'year' => 'an',
        'currency' => '€',
        'users' => 'utilisateurs',
        'popular' => 'Populaire',
        'save' => 'Économisez',
        'start_trial' => 'Démarrer l\'essai gratuit',
        'choose' => 'Choisir ce plan',
        'trial' => 'Essai',
        'trial_ends' => 'Fin de l\'essai',
        'trial_started' => 'Votre essai gratuit de 15 jours a commencé !',
        'free_trial' => 'Essai Gratuit',
        'days_remaining' => 'jours restants',
        'plan_premium' => 'Plan Premium',
        'plan_advanced' => 'Plan Avancé',

        // Payment modal
        'checkout_title' => 'Finaliser l\'abonnement',
        'billing_period' => 'Période de facturation',
        'promo_code' => 'Code promo',
        'promo_placeholder' => 'Entrez votre code',
        'apply' => 'Appliquer',
        'invalid_promo_code' => 'Code promo invalide',
        'discount' => 'Réduction',
        'annual_savings' => 'Économie annuelle',
        'total' => 'Total',
        'checkout_button' => 'Aller au paiement',
        'processing' => 'Traitement...',
        'cancel' => 'Annuler',
        'secure_payment' => 'Paiement sécurisé par Stripe',
        'checkout_error' => 'Erreur lors de la création du paiement. Veuillez réessayer.',

        // Plans
        'plans' => [
            'free' => [
                'name' => 'Gratuit',
                'description' => 'Pour découvrir la plateforme',
            ],
            'premium' => [
                'name' => 'Premium',
                'description' => 'Pour les PME',
            ],
            'advanced' => [
                'name' => 'Avancé',
                'description' => 'Pour les grandes entreprises',
            ],
        ],

        // Subscription management
        'manage' => 'Gérer mon abonnement',
        'billing_portal' => 'Portail de facturation',
        'update_payment' => 'Mettre à jour le mode de paiement',
        'cancel_subscription' => 'Annuler l\'abonnement',
        'resume_subscription' => 'Reprendre l\'abonnement',
        'subscription_cancelled' => 'Votre abonnement a été annulé.',
        'subscription_resumed' => 'Votre abonnement a été réactivé.',
        'next_billing_date' => 'Prochaine facturation',
        'payment_method' => 'Mode de paiement',
        'invoices' => 'Factures',
        'no_invoices' => 'Aucune facture',
        'download_invoice' => 'Télécharger',
        // Subscription Manager
        'subscription_billing' => 'Abonnement & Facturation',
        'manage_desc' => 'Gérez votre abonnement, consultez vos factures et mettez à jour vos modes de paiement.',
        'subscription_activated' => 'Votre abonnement a été activé avec succès !',
        'checkout_canceled' => 'Le paiement a été annulé. Vous pouvez réessayer quand vous êtes prêt.',
        'on_trial' => 'Vous êtes en période d\'essai',
        'days_remaining' => 'jours restants',
        'trial_ends_on' => 'L\'essai se termine le :date',
        'upgrade_now' => 'Passer au plan supérieur',
        'subscription_ends_on' => 'Votre abonnement se terminera le :date',
        'lose_premium' => 'Vous perdrez l\'accès aux fonctionnalités premium après cette date.',
        'no_subscription' => 'Pas d\'abonnement actif',
        'start_trial_days' => 'Démarrer un essai gratuit de :days jours',
        'usage' => 'Utilisation',
        'bank_connections' => 'Connexions bancaires',
        'sites' => 'Sites',
        'monthly_reports' => 'Rapports mensuels',
        'unlimited' => 'Illimité',
        'available_plans' => 'Plans disponibles',
        'yearly' => 'Annuel',
        'quick_actions' => 'Actions rapides',
        'update_payment_method' => 'Mettre à jour le mode de paiement',
        'update_billing_address' => 'Mettre à jour l\'adresse de facturation',
        'recent_invoices' => 'Factures récentes',
        'no_invoices_yet' => 'Aucune facture pour le moment',
        'paid' => 'Payée',
        'failed' => 'Échouée',
        'need_help' => 'Besoin d\'aide ?',
        'help_choose_plan' => 'Notre équipe est là pour vous aider à choisir le bon plan.',
        'contact_support' => 'Contacter le support',
        'cancel_subscription_title' => 'Annuler l\'abonnement ?',
        'cancel_subscription_desc' => 'Votre abonnement restera actif jusqu\'au :date. Après cela, vous perdrez l\'accès aux fonctionnalités premium.',
        'why_canceling' => 'Pourquoi annulez-vous ? (optionnel)',
        'feedback_placeholder' => 'Vos commentaires nous aident à nous améliorer...',
        'keep_subscription' => 'Garder l\'abonnement',
        'upgrade_to' => 'Passer au plan :plan',
        'redirect_to_payment' => 'Vous serez redirigé vers notre page de paiement sécurisé pour finaliser votre abonnement.',
        'plan' => 'Plan',
        'continue_to_payment' => 'Continuer vers le paiement',
        'unlimited_bank_connections' => 'Connexions bancaires illimitées',
        'unlimited_users' => 'Utilisateurs illimités',
        'api_access' => 'Accès API',
        'upgrade' => 'Passer au plan supérieur',
        'downgrade' => 'Rétrograder',
        'manage_billing' => 'Gérer la facturation',
        'next_billing' => 'Prochaine facturation : :date',
        'month' => 'mois',
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors & Messages
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'saved' => 'Modifications enregistrées.',
        'created' => 'Élément créé avec succès.',
        'updated' => 'Élément mis à jour avec succès.',
        'deleted' => 'Élément supprimé.',
        'error' => 'Une erreur est survenue.',
        'not_found' => 'Élément non trouvé.',
        'unauthorized' => 'Action non autorisée.',
        'validation_required' => 'Veuillez valider les transactions en attente.',
        'sync_started' => 'Synchronisation démarrée.',
        'sync_completed' => 'Synchronisation terminée.',
        'report_generating' => 'Votre rapport est en cours de génération.',
        'report_ready' => 'Votre rapport est prêt.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assessments (Bilans)
    |--------------------------------------------------------------------------
    */

    'assessments' => [
        'title' => 'Mes Bilans Carbone',
        'my_assessments' => 'Mes Bilans',
        'subtitle' => 'Gérez vos bilans carbone annuels',
        'new' => 'Démarrer un nouveau bilan',
        'edit' => 'Modifier le bilan',
        'year' => 'Année',
        'year_label' => 'Bilan :year',
        'revenue' => 'Chiffre d\'affaires',
        'employees' => 'Collaborateurs',
        'status' => 'Statut',
        'progress' => 'Progression',
        'status_draft' => 'Brouillon',
        'status_active' => 'Actif',
        'status_completed' => 'Terminé',
        'activate' => 'Activer ce bilan',
        'activated' => 'Bilan activé avec succès.',
        'empty' => 'Aucun bilan pour le moment',
        'empty_short' => 'Aucun bilan',
        'none' => 'Aucun bilan',
        'create_first' => 'Créer votre premier bilan',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce bilan ?',
        'manage' => 'Gérer mes bilans',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions (Plan de transition)
    |--------------------------------------------------------------------------
    */

    'actions' => [
        'title' => 'Plan de Transition',
        'subtitle' => 'Gérez vos actions de réduction carbone',
        'new' => 'Nouvelle action',
        'edit' => 'Modifier l\'action',
        'filter_all' => 'Toutes',
        'status' => [
            'todo' => 'À faire',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
        ],
        'difficulty' => [
            'easy' => 'Facile',
            'medium' => 'Moyenne',
            'hard' => 'Difficile',
        ],
        'form' => [
            'title' => 'Titre',
            'title_placeholder' => 'Ex: Remplacer la flotte par des véhicules électriques',
            'description' => 'Description',
            'description_placeholder' => 'Décrivez les détails de l\'action à mettre en place...',
            'due_date' => 'Date limite',
            'category' => 'Catégorie d\'émission',
            'category_none' => '-- Aucune catégorie --',
            'status' => 'Statut',
            'estimated_cost' => 'Coût estimé',
            'co2_reduction' => 'Réduction CO2 estimée',
            'difficulty' => 'Niveau de difficulté',
        ],
        'priority' => 'Priorité',
        'assigned_to' => 'Assigné à',
        'empty' => 'Aucune action pour le moment',
        'empty_description' => 'Commencez par créer une action de réduction',
        'create_first' => 'Créer ma première action',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette action ?',
        'status_updated' => 'Statut de l\'action mis à jour.',
        'overdue' => 'En retard',
        'start' => 'Démarrer',
        'complete' => 'Terminer',
        'reopen' => 'Rouvrir',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reduction Targets (Trajectoire)
    |--------------------------------------------------------------------------
    */

    'targets' => [
        'title' => 'Trajectoire de Réduction',
        'subtitle' => 'Définissez vos objectifs de réduction alignés SBTi',
        'trajectory' => 'Modifier ma trajectoire',
        'new' => 'Nouvel objectif',
        'edit' => 'Modifier l\'objectif',
        'baseline_year' => 'Année de référence',
        'target_year' => 'Année cible',
        'scope1_reduction' => 'Réduction Scope 1',
        'scope2_reduction' => 'Réduction Scope 2',
        'scope3_reduction' => 'Réduction Scope 3',
        'sbti_title' => 'Science Based Targets initiative (SBTi)',
        'sbti_description' => 'Pour être aligné avec les objectifs de l\'Accord de Paris (limiter le réchauffement à 1.5°C), les entreprises doivent réduire leurs émissions de :',
        'sbti_scope12_rate' => '4.2% par an',
        'sbti_scope12_label' => 'pour les émissions Scope 1 et Scope 2',
        'sbti_scope3_rate' => '2.5% par an',
        'sbti_scope3_label' => 'pour les émissions Scope 3',
        'sbti_aligned' => 'Aligné SBTi',
        'sbti_not_aligned' => 'Non aligné SBTi',
        'sbti_info' => 'La Science Based Targets initiative (SBTi) recommande une réduction annuelle d\'au moins 4,2% pour les scopes 1 et 2, et de 2,5% pour le scope 3.',
        'apply_sbti' => 'Appliquer les objectifs SBTi',
        'horizon' => 'Horizon :years ans',
        'per_year' => 'an',
        'notes' => 'Notes',
        'notes_placeholder' => 'Notes additionnelles sur cet objectif...',
        'empty' => 'Aucun objectif défini',
        'empty_description' => 'Commencez par définir votre trajectoire de réduction',
        'create_first' => 'Définir ma trajectoire',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet objectif ?',
        'annual_rate' => 'Taux annuel',
        'compliant' => 'Conforme',
        'not_compliant' => 'Non conforme',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trajectory Chart
    |--------------------------------------------------------------------------
    */

    'trajectory' => [
        'chart_title' => 'Trajectoire des Émissions',
        'chart_subtitle' => 'Évolution des émissions réelles vs objectifs de réduction',
        'select_target' => 'Sélectionner un objectif',
        'actual_emissions' => 'Émissions réelles',
        'target_trajectory' => 'Trajectoire cible',
        'target' => 'Cible',
        'axis_years' => 'Années',
        'axis_emissions' => 'Émissions (tCO₂e)',
        'baseline' => 'Référence',
        'reduction' => 'Réduction visée',
        'status' => 'Statut',
        'on_track' => 'En bonne voie',
        'off_track' => 'Hors cible',
        'no_data' => 'Pas de données',
        'today' => 'Aujourd\'hui',
        'years_left' => ':years ans restants',
        'empty' => 'Aucune trajectoire définie',
        'empty_description' => 'Définissez d\'abord vos objectifs de réduction pour visualiser la trajectoire.',
        'create_target' => 'Définir mes objectifs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Chat
    |--------------------------------------------------------------------------
    */

    'support' => [
        'title' => 'Support Carbex',
        'online' => 'En ligne',
        'online_support' => 'Support en ligne',
        'offline' => 'Hors ligne',
        'clear_chat' => 'Effacer la conversation',
        'start_conversation' => 'Commencez la conversation...',
        'quick_help' => 'Besoin d\'aide rapide ?',
        'message_placeholder' => 'Tapez votre message...',

        // Welcome and greetings
        'welcome_message' => 'Bonjour ! Je suis l\'assistant Carbex. Comment puis-je vous aider aujourd\'hui ?',
        'response_greeting' => 'Bonjour ! Ravi de vous voir. Comment puis-je vous aider ?',
        'response_thanks' => 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions.',

        // Quick responses
        'response_pricing' => 'Nous proposons 3 formules : Gratuit (15 jours d\'essai), Premium (400€/an) et Avancé (1200€/an). Le plan Premium est parfait pour les PME avec jusqu\'à 5 utilisateurs. Consultez notre page Tarifs pour plus de détails !',
        'response_import' => 'Vous pouvez importer vos données de plusieurs manières : connexion bancaire directe via notre partenaire, import de fichiers CSV, ou saisie manuelle. Rendez-vous dans Paramètres > Banques pour configurer l\'import.',
        'response_report' => 'Pour générer un rapport, allez dans Rapports & Exports. Vous pouvez créer un bilan complet Word, une déclaration ADEME ou un rapport GHG Protocol. Sélectionnez l\'année et cliquez sur "Générer".',
        'response_emissions' => 'La saisie des émissions se fait par catégorie (Scope 1, 2 et 3). Cliquez sur une catégorie dans le menu, puis ajoutez vos sources d\'émission. Notre base contient plus de 20 000 facteurs d\'émission ADEME.',
        'response_support' => 'Je vais vous mettre en contact avec notre équipe. Merci de remplir le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.',
        'default_response' => 'Je comprends. Pour une assistance personnalisée, vous pouvez contacter notre équipe support ou consulter notre documentation. Puis-je vous aider avec autre chose ?',

        // Contact form
        'contact_form_title' => 'Contacter le support',
        'your_name' => 'Votre nom',
        'name_placeholder' => 'Jean Dupont',
        'your_email' => 'Votre email',
        'email_placeholder' => 'jean@entreprise.fr',
        'submit_request' => 'Envoyer ma demande',
        'contact_submitted' => 'Merci :name ! Nous avons bien reçu votre demande et vous répondrons à :email dans les 24h.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common UI Elements
    |--------------------------------------------------------------------------
    */

    'common' => [
        'loading' => 'Chargement...',
        'saving' => 'Enregistrement...',
        'processing' => 'Traitement...',
        'view_details' => 'Voir les détails',
        'actions' => 'Actions',
        'save' => 'Sauvegarder',
        'cancel' => 'Annuler',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'close' => 'Fermer',
        'confirm' => 'Confirmer',
        'yes' => 'Oui',
        'no' => 'Non',
    ],

    /*
    |--------------------------------------------------------------------------
    | Import Wizard
    |--------------------------------------------------------------------------
    */

    'import' => [
        'upload' => 'Téléverser',
        'map_columns' => 'Mapper les colonnes',
        'validate' => 'Valider',
        'import' => 'Importer',
        'upload_data_file' => 'Téléverser un fichier de données',
        'import_desc' => 'Importez des transactions ou activités depuis des fichiers CSV, Excel ou FEC',
        'import_type' => 'Type d\'import',
        'bank_transactions' => 'Transactions bancaires',
        'csv_export' => 'Export CSV de votre banque',
        'activities' => 'Activités',
        'activities_desc' => 'Énergie, déplacements, achats...',
        'fec_france' => 'FEC (France)',
        'fec_desc' => 'Export comptable français',
        'target_site' => 'Site cible',
        'select_site' => 'Sélectionner un site...',
        'data_file' => 'Fichier de données',
        'drag_drop' => 'Glissez-déposez votre fichier ici, ou',
        'browse' => 'parcourir',
        'file_types' => 'CSV, Excel (.xlsx, .xls) jusqu\'à 10Mo',
        'uploading' => 'Téléversement...',
        'analyze_file' => 'Analyser le fichier',
        'analyzing' => 'Analyse en cours...',
        'map_columns_title' => 'Mapper les colonnes',
        'map_columns_desc' => 'Associez les colonnes de votre fichier aux champs requis',
        'detected' => 'Détecté :',
        'rows' => 'lignes',
        'columns' => 'colonnes',
        'select_column' => '-- Sélectionner une colonne --',
        'sample_data' => 'Aperçu des données',
        'validate_mapping' => 'Valider le mapping',
        'validating' => 'Validation en cours...',
        'validation_results' => 'Résultats de validation',
        'review_before_import' => 'Vérifiez avant d\'importer',
        'total_rows' => 'Total des lignes',
        'valid' => 'Valides',
        'invalid' => 'Invalides',
        'warnings' => 'Avertissements',
        'validation_errors' => 'Erreurs de validation',
        'and_more' => '... et :count de plus',
        'import_rows' => 'Importer :count lignes',
        'starting' => 'Démarrage...',
        'import_started' => 'Import démarré !',
        'processing_background' => 'Vos données sont en cours de traitement en arrière-plan. Vous recevrez une notification une fois terminé.',
        'import_another' => 'Importer un autre fichier',
        'go_to_dashboard' => 'Aller au tableau de bord',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transactions List
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transactions',
        'import_title' => 'Importer des transactions',
        'review_title' => 'Vérifier les transactions',
        'total_transactions' => 'Total des transactions',
        'pending_categorization' => 'Catégorisation en attente',
        'needs_review' => 'À vérifier',
        'validated' => 'Validées',
        'search_placeholder' => 'Rechercher des transactions...',
        'all_categories' => 'Toutes les catégories',
        'all_scopes' => 'Tous les scopes',
        'selected' => 'sélectionnée(s)',
        'validate_all' => 'Tout valider',
        'categorize_as' => 'Catégoriser en...',
        'date' => 'Date',
        'description' => 'Description',
        'category' => 'Catégorie',
        'amount' => 'Montant',
        'emissions' => 'Émissions',
        'actions' => 'Actions',
        'select' => 'Sélectionner...',
        'add_category' => '+ Ajouter une catégorie',
        'low_confidence' => 'Confiance faible',
        'validate' => 'Valider',
        'create_rule' => 'Créer une règle pour ce marchand',
        'exclude' => 'Exclure',
        'exclude_confirm' => 'Exclure cette transaction des émissions ?',
        'include' => 'Inclure',
        'no_transactions' => 'Aucune transaction trouvée correspondant à vos filtres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Banking Connection Wizard
    |--------------------------------------------------------------------------
    */

    'banking_wizard' => [
        'country' => 'Pays',
        'bank' => 'Banque',
        'authorize' => 'Autoriser',
        'connect' => 'Connecter',
        'done' => 'Terminé',
        'select_country' => 'Sélectionnez votre pays',
        'supported_countries' => 'Nous supportons l\'Open Banking en France et en Allemagne',
        'via' => 'via',
        'select_bank' => 'Sélectionnez votre banque',
        'supported_banks' => 'Choisissez parmi les banques supportées',
        'search_banks' => 'Rechercher des banques...',
        'no_banks_found' => 'Aucune banque trouvée correspondant à votre recherche.',
        'authorize_connection' => 'Autoriser la connexion',
        'redirect_to_bank' => 'Vous serez redirigé vers votre banque',
        'secure_connection' => 'Connexion sécurisée (PSD2)',
        'psd2_info' => 'Nous utilisons les réglementations Open Banking (PSD2) pour nous connecter de manière sécurisée à votre banque. Nous ne voyons ni ne stockons jamais vos identifiants bancaires.',
        'what_we_access' => 'Ce à quoi nous accédons :',
        'account_balances' => 'Soldes des comptes',
        'transaction_history' => 'Historique des transactions (90 derniers jours)',
        'transaction_details' => 'Descriptions et catégories des transactions',
        'what_we_dont_access' => 'Ce à quoi nous n\'accédons PAS :',
        'login_credentials' => 'Vos identifiants de connexion',
        'ability_transfers' => 'Capacité à effectuer des virements',
        'investment_details' => 'Détails des investissements personnels',
        'continue_to_bank' => 'Continuer vers la banque',
        'redirecting' => 'Redirection...',
        'redirecting_to_bank' => 'Redirection vers votre banque...',
        'complete_authorization' => 'Veuillez compléter l\'autorisation dans la nouvelle fenêtre.',
        'click_if_not_redirected' => 'Cliquez ici si vous n\'êtes pas redirigé automatiquement',
        'bank_connected' => 'Banque connectée !',
        'sync_in_progress' => 'Vos transactions sont en cours de synchronisation. Cela peut prendre quelques minutes.',
        'connect_another' => 'Connecter une autre banque',
        'connected_banks' => 'Banques connectées',
        'accounts' => 'comptes',
        'synced' => 'Synchronisé',
        'sync_now' => 'Synchroniser maintenant',
        'disconnect' => 'Déconnecter',
        'disconnect_confirm' => 'Êtes-vous sûr de vouloir déconnecter cette banque ?',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Reports - Methodology
    |--------------------------------------------------------------------------
    */

    'methodology' => [
        'title' => 'Méthodologie & Qualité des données',
        'calculation_standards' => 'Standards de calcul',
        'primary_standard' => 'Standard principal',
        'consolidation_approach' => 'Approche de consolidation',
        'operational_control' => 'Contrôle opérationnel',
        'base_year' => 'Année de référence',
        'reporting_period' => 'Période de reporting',
        'emission_factor_sources' => 'Sources des facteurs d\'émission',
        'source' => 'Source',
        'version' => 'Version',
        'applied_to' => 'Appliqué à',
        'all_french_operations' => 'Toutes les opérations françaises',
        'uk_operations' => 'Opérations au Royaume-Uni',
        'other_countries' => 'Autres pays',
        'scope_definitions' => 'Définitions des scopes',
        'direct_emissions' => 'Émissions directes',
        'scope1_desc' => 'Émissions des sources détenues ou contrôlées par l\'organisation.',
        'company_vehicles' => 'Véhicules de société (flotte)',
        'onsite_fuel' => 'Combustion de carburant sur site',
        'fugitive_emissions' => 'Émissions fugitives (réfrigérants)',
        'process_emissions' => 'Émissions de procédés',
        'energy_indirect' => 'Émissions indirectes liées à l\'énergie',
        'scope2_desc' => 'Émissions de l\'électricité, vapeur, chauffage et climatisation achetés.',
        'purchased_electricity' => 'Électricité achetée',
        'district_heating' => 'Chauffage/climatisation urbain',
        'steam' => 'Vapeur',
        'location_based_note' => 'Méthode basée sur la localisation appliquée par défaut ; méthode basée sur le marché disponible sur demande.',
        'value_chain' => 'Émissions de la chaîne de valeur',
        'scope3_desc' => 'Toutes les autres émissions indirectes de la chaîne de valeur.',
        'cat1_purchased' => 'Cat. 1 : Biens et services achetés',
        'cat5_waste' => 'Cat. 5 : Déchets générés par les opérations',
        'cat6_travel' => 'Cat. 6 : Voyages professionnels',
        'cat7_commuting' => 'Cat. 7 : Trajets domicile-travail',
        'cat8_leased' => 'Cat. 8 : Actifs loués en amont',
        'data_quality_assessment' => 'Évaluation de la qualité des données',
        'data_type' => 'Type de données',
        'quality_score' => 'Score de qualité',
        'coverage' => 'Couverture',
        'energy_consumption' => 'Consommation d\'énergie',
        'invoices_meters' => 'Factures / Compteurs intelligents',
        'business_travel' => 'Voyages professionnels',
        'bank_transactions' => 'Transactions bancaires',
        'purchased_goods' => 'Biens achetés',
        'spend_based' => 'Estimation basée sur les dépenses',
        'uncertainty_limitations' => 'Incertitude & Limitations',
        'uncertainty_factors' => 'Les facteurs suivants contribuent à l\'incertitude des calculs d\'émissions :',
        'uncertainty_1' => 'Les facteurs d\'émission sont basés sur des moyennes nationales ou régionales et peuvent ne pas refléter les données spécifiques des fournisseurs.',
        'uncertainty_2' => 'Les calculs basés sur les dépenses pour les biens achetés utilisent des facteurs de conversion monétaire-carbone.',
        'uncertainty_3' => 'Certaines catégories du Scope 3 peuvent utiliser des méthodes d\'estimation lorsque les données primaires ne sont pas disponibles.',
        'estimated_uncertainty' => 'Incertitude globale estimée',
        'exclusions' => 'Exclusions',
        'exclusions_desc' => 'Les sources d\'émission suivantes ont été exclues de cet inventaire :',
        'estimated_impact' => 'Impact estimé',
        'verification_statement' => 'Attestation de vérification',
        'verified_by' => 'Cet inventaire de gaz à effet de serre a été vérifié par',
        'to_standard' => 'selon le standard',
        'verification_date' => 'Date de vérification',
        'ghg_prepared' => 'Cet inventaire de gaz à effet de serre a été préparé selon le GHG Protocol Corporate Standard.',
        'verification_recommended' => 'Une vérification indépendante par un tiers est recommandée pour les rapports externes.',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Reports - Scope Breakdown
    |--------------------------------------------------------------------------
    */

    'scope_breakdown' => [
        'title' => 'Émissions détaillées par scope',
        'direct_emissions' => 'Émissions directes',
        'scope1_desc' => 'Émissions des sources détenues ou directement contrôlées par l\'organisation.',
        'category' => 'Catégorie',
        'emissions_tco2e' => 'Émissions (t CO₂e)',
        'share' => 'Part',
        'trend' => 'Tendance',
        'no_scope1_data' => 'Aucune émission Scope 1 enregistrée pour cette période.',
        'energy_indirect' => 'Émissions indirectes liées à l\'énergie',
        'scope2_desc' => 'Émissions de l\'électricité, vapeur, chauffage et climatisation achetés.',
        'calculation_method' => 'Méthode de calcul',
        'location_based' => 'Basée sur la localisation',
        'market_based' => 'Basée sur le marché',
        'consumption' => 'Consommation',
        'no_scope2_data' => 'Aucune émission Scope 2 enregistrée pour cette période.',
        'electricity_mix' => 'Mix électrique',
        'grid_emission_factor' => 'Facteur d\'émission du réseau',
        'value_chain' => 'Émissions de la chaîne de valeur',
        'scope3_desc' => 'Toutes les autres émissions indirectes de la chaîne de valeur.',
        'ghg_category' => 'Catégorie GHG Protocol',
        'no_scope3_data' => 'Aucune émission Scope 3 enregistrée pour cette période.',
        'scope3_coverage' => 'Couverture Scope 3',
        'currently_tracking' => 'Suivi actuellement',
        'of_15_categories' => 'des 15 catégories du Scope 3',
        'not_relevant' => 'Catégories jugées non pertinentes',
        'summary_by_scope' => 'Résumé par scope',
        'scope' => 'Scope',
        'share_of_total' => 'Part du total',
        'vs_previous' => 'vs Période précédente',
        'total' => 'Total',
    ],

    /*
    |--------------------------------------------------------------------------
    | Documents (AI Extraction)
    |--------------------------------------------------------------------------
    */

    'documents' => [
        'title' => 'Documents',
        'subtitle' => 'Importez vos factures et l\'IA extraira automatiquement les données d\'émissions',
        'upload' => 'Importer un document',
        'new_upload' => 'Nouveau document',
        'list' => 'Documents importés',
        'drop_files' => 'Glissez vos fichiers ici',
        'or_click' => 'ou cliquez pour sélectionner',
        'uploading' => 'Envoi en cours',
        'processing' => 'Traitement IA en cours',
        'process' => 'Traiter le document',
        'type' => 'Type de document',
        'file_required' => 'Veuillez sélectionner un fichier',
        'file_too_large' => 'Le fichier est trop volumineux (max 10 Mo)',
        'invalid_type' => 'Type de fichier non supporté',
        'upload_success' => 'Document envoyé avec succès. Traitement en cours...',
        'upload_error' => 'Erreur lors de l\'envoi',
        'no_documents' => 'Aucun document importé',
        'no_documents_hint' => 'Importez vos factures pour extraire automatiquement les données d\'émissions',
        'validated' => 'Validé',
        'emission_linked' => 'Émission créée',
        'confidence' => 'confiance',
        'view' => 'Voir les détails',
        'validate' => 'Valider',
        'create_emission' => 'Créer l\'émission',
        'reprocess' => 'Retraiter',
        'cannot_reprocess' => 'Ce document ne peut pas être retraité',
        'reprocessing' => 'Document en cours de retraitement...',
        'deleted' => 'Document supprimé',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce document ?',
        'extracted_data' => 'Données extraites',
        'supplier' => 'Fournisseur',
        'date' => 'Date',
        'amount' => 'Montant',
        'category' => 'Catégorie',
        'line_items' => 'Lignes de facture',
        'description' => 'Description',
        'quantity' => 'Quantité',
        'unit' => 'Unité',
        'validate_data' => 'Valider les données extraites',
        'validation_instructions' => 'Vérifiez et corrigez les données extraites si nécessaire',
        'confidence_level' => 'Niveau de confiance IA',
        'confirm_validation' => 'Confirmer la validation',
        'validation_success' => 'Document validé avec succès',
        'fields' => [
            'supplier_name' => 'Fournisseur',
            'date' => 'Date',
            'total_amount' => 'Montant total',
            'invoice_number' => 'N° de facture',
            'document_type' => 'Type de document',
            'suggested_category' => 'Catégorie suggérée',
        ],

        // Document types
        'types' => [
            'invoice' => 'Facture',
            'energy_bill' => 'Facture énergie',
            'fuel_receipt' => 'Ticket carburant',
            'transport_invoice' => 'Facture transport',
            'purchase_order' => 'Bon de commande',
            'bank_statement' => 'Relevé bancaire',
            'expense_report' => 'Note de frais',
            'other' => 'Autre',
        ],

        // Processing statuses
        'statuses' => [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'failed' => 'Échec',
            'needs_review' => 'À réviser',
        ],

        // File formats
        'file_formats' => 'PDF, Images, Excel, CSV (max 10 Mo)',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Assistant
    |--------------------------------------------------------------------------
    */

    'ai' => [
        // Common
        'ai_help' => 'Aide IA',
        'close' => 'Fermer',
        'ask_question' => 'Posez votre question...',
        'not_logged_in' => 'Non connecté',
        'not_available' => 'IA non disponible',

        // Quick prompts
        'quick_prompts' => [
            'emission_sources' => 'Quelles sources d\'émission dois-je inclure ?',
            'consumption_data' => 'Comment trouver mes données de consommation ?',
            'which_unit' => 'Quelle unité utiliser ?',
            'emission_factors' => 'Expliquer les facteurs d\'émission',
        ],

        // Analysis Page
        'analysis_title' => 'Analyse IA',
        'analysis_description' => 'Obtenez des recommandations personnalisées basées sur l\'analyse de vos émissions carbone.',
        'recommendations_title' => 'Recommandations IA',
        'powered_by' => 'Propulsé par :provider',
        'refresh' => 'Actualiser',
        'not_configured' => 'IA non configurée',
        'configure_to_use' => 'Configurez une clé API dans les paramètres pour activer les recommandations IA.',
        'configure_ai' => 'Configurer l\'IA',
        'ready_to_analyze' => 'Prêt à analyser',
        'analyze_description' => 'Cliquez sur le bouton ci-dessous pour lancer l\'analyse de vos émissions et recevoir des recommandations personnalisées.',
        'start_analysis' => 'Lancer l\'analyse',
        'analyzing' => 'Analyse en cours...',
        'analyzing_emissions' => 'Analyse de vos émissions en cours...',
        'recommendations_count' => 'Recommandations',
        'potential_reduction' => 'Réduction potentielle',
        'key_insights' => 'Points clés',
        'recommended_actions' => 'Actions recommandées',
        'add_selected' => 'Ajouter :count action(s)',
        'add_action' => 'Ajouter au plan',
        'no_recommendations' => 'Aucune recommandation disponible',
        'top_emission_categories' => 'Principales catégories d\'émissions',
        'how_it_works' => 'Comment ça marche',
        'how_it_works_description' => 'Notre IA analyse vos données d\'émissions, identifie les opportunités de réduction et propose des actions concrètes adaptées à votre secteur.',
        'data_sources' => 'Sources de données',
        'data_source_emissions' => '• Vos données d\'émissions (Scopes 1, 2, 3)',
        'data_source_sector' => '• Benchmarks sectoriels',
        'data_source_benchmarks' => '• Meilleures pratiques du marché',
        'privacy' => 'Confidentialité',
        'privacy_description' => 'Vos données restent confidentielles et ne sont utilisées que pour générer vos recommandations personnalisées.',

        // Emission Helper
        'helper' => [
            'current_category' => 'Catégorie actuelle',
            'not_configured' => 'IA non configurée',
            'configure_api_key' => 'Configurez une clé API dans les paramètres admin pour activer l\'aide IA.',
            'configure_ai' => 'Configurer l\'IA',
            'suggested_sources' => 'Sources suggérées pour cette catégorie',
            'suggested_category' => 'Catégorie suggérée',
            'use_category' => 'Utiliser cette catégorie',
            'suggested_factor' => 'Facteur d\'émission suggéré',
            'use_factor' => 'Utiliser ce facteur',
            'quick_actions' => 'Actions rapides',
            'how_to_fill' => 'Comment remplir ?',
            'suggest_factor' => 'Suggérer un facteur',
            'suggest_category' => 'Suggérer catégorie',
            'ask_about_category' => 'Posez une question sur cette catégorie',
            'frequent_questions' => 'Questions fréquentes',
        ],

        // Chat Widget
        'chat' => [
            'assistant_name' => 'Assistant Carbex',
            'subtitle' => 'IA - Bilan Carbone',
            'new_conversation' => 'Nouvelle conversation',
            'welcome' => 'Bonjour ! Je suis votre assistant.',
            'welcome_description' => 'Je peux vous aider avec votre bilan carbone, expliquer les facteurs d\'émission, et suggérer des actions de réduction.',
            'powered_by' => 'Propulsé par Claude AI',
            'unlimited' => 'Illimité',
            'remaining' => 'restant',
            'remaining_plural' => 'restants',
            'quota_daily' => 'Quota journalier',
            'quota_monthly' => 'Mensuel',
            'ai_not_available' => 'L\'IA n\'est pas disponible sur votre plan',
            'upgrade_premium' => 'Passer à Premium',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sites - Multi-sites Management (T174-T175)
    |--------------------------------------------------------------------------
    */

    'sites' => [
        'manage_sites' => 'Gérer les sites',
        'add_site' => 'Ajouter un site',
        'add' => 'Ajouter un site',
        'employees' => 'employés',
        'no_sites' => 'Aucun site configuré',
        'no_sites_desc' => 'Ajoutez vos différents sites pour suivre leurs émissions carbone individuellement.',
        'add_first' => 'Ajouter votre premier site',

        'comparison' => [
            'title' => 'Comparaison des sites',
            'subtitle' => 'Analysez et comparez les émissions carbone de vos différents sites',
            'total_sites' => 'Sites actifs',
            'total_emissions' => 'Émissions totales',
            'top_emitter' => 'Plus gros émetteur',
            'average_per_site' => 'Moyenne par site',
            'year' => 'Année',
            'scope' => 'Scope',
            'all_scopes' => 'Tous les scopes',
            'metric' => 'Métrique',
            'metric_total' => 'Émissions totales',
            'metric_per_m2' => 'Par m² de surface',
            'metric_per_employee' => 'Par employé',
            'sort_by' => 'Trier par',
            'sort_emissions_desc' => 'Émissions (décroissant)',
            'sort_emissions_asc' => 'Émissions (croissant)',
            'sort_name_asc' => 'Nom (A-Z)',
            'sort_name_desc' => 'Nom (Z-A)',
            'sort_intensity_desc' => 'Intensité (décroissant)',
            'sort_intensity_asc' => 'Intensité (croissant)',
            'chart_title' => 'Émissions par site',
            'emissions_unit' => 't CO₂e',
            'no_sites' => 'Aucun site configuré',
            'no_sites_description' => 'Ajoutez vos sites pour commencer à comparer leurs émissions',
            'table_title' => 'Détail par site',
            'site' => 'Site',
            'total' => 'Total',
            'intensity' => 'Intensité',
            'share' => 'Part',
            'recommendations' => 'Recommandations',
        ],

        'recommendations' => [
            'high_emitter' => 'Ce site émet :percent% de plus que la moyenne. Prioritaire pour les actions de réduction.',
            'scope1_heavy' => 'Émissions directes élevées. Envisagez des alternatives aux combustibles fossiles.',
            'scope2_heavy' => 'Forte consommation électrique. Considérez des contrats d\'énergie verte ou l\'efficacité énergétique.',
            'high_intensity' => 'Intensité carbone élevée par m². Audit énergétique recommandé.',
            'missing_area' => 'Renseignez la surface pour calculer l\'intensité carbone.',
            'missing_employees' => 'Renseignez le nombre d\'employés pour l\'analyse par personne.',
            'good_performance' => 'Excellente performance ! Ce site est bien en dessous de la moyenne.',
        ],

        // Site fields
        'name' => 'Nom',
        'code' => 'Code',
        'code_hint' => 'Code unique optionnel (ex: PAR-01)',
        'type' => 'Type',
        'address' => 'Adresse',
        'city' => 'Ville',
        'postal_code' => 'Code postal',
        'country' => 'Pays',
        'floor_area' => 'Surface (m²)',
        'energy_rating' => 'DPE',
        'construction_year' => 'Année de construction',
        'heating_type' => 'Type de chauffage',
        'employee_count' => 'Nombre d\'employés',
        'electricity_provider' => 'Fournisseur d\'électricité',
        'renewable_energy' => 'Énergie renouvelable',
        'renewable_percentage' => 'Pourcentage d\'énergie renouvelable',
        'is_primary' => 'Site principal',
        'primary' => 'Principal',
        'set_as_primary' => 'Définir comme site principal',
        'edit' => 'Modifier le site',
        'delete_title' => 'Supprimer le site',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer ce site ? Cette action est irréversible.',

        // Site types
        'types' => [
            'office' => 'Bureau',
            'warehouse' => 'Entrepôt',
            'factory' => 'Usine',
            'store' => 'Magasin',
            'datacenter' => 'Centre de données',
            'other' => 'Autre',
        ],

        // Import (T176)
        'import' => [
            'title' => 'Import CSV de sites',
            'description' => 'Importez plusieurs sites à la fois depuis un fichier CSV.',
            'upload_file' => 'Téléverser le fichier',
            'download_template' => 'Télécharger le modèle',
            'csv_file' => 'Fichier CSV',
            'click_to_upload' => 'Cliquez pour téléverser',
            'has_header' => 'La première ligne contient les en-têtes',
            'map_columns' => 'Mapper les colonnes',
            'skip' => 'Ignorer',
            'column' => 'Colonne',
            'col' => 'Col.',
            'preview' => 'Aperçu des données',
            'rows' => 'lignes',
            'import_button' => 'Importer les sites',
            'importing' => 'Importation en cours...',
            'file_read_error' => 'Impossible de lire le fichier.',
            'name_required' => 'La colonne "Nom" est obligatoire.',
            'duplicate_code' => 'Le code ":code" existe déjà.',
            'create_failed' => 'Échec de la création du site.',
            'import_failed' => 'L\'import a échoué. Veuillez réessayer.',
            'result' => ':imported site(s) importé(s), :skipped ignoré(s).',
            'success_title' => 'Import terminé',
            'no_imports' => 'Aucun site importé',
            'imported' => 'Importés',
            'skipped' => 'Ignorés',
            'errors' => 'Erreurs',
            'row' => 'Ligne',
            'more_errors' => 'erreurs supplémentaires',
            'import_more' => 'Importer d\'autres sites',
            'view_sites' => 'Voir les sites',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Employee Engagement (T180-T182)
    |--------------------------------------------------------------------------
    */

    'engage' => [
        'title' => 'Engagement des employés',
        'description' => 'Sensibilisez vos équipes et mesurez votre empreinte carbone personnelle.',
        'your_points' => 'Vos points',

        'tabs' => [
            'quiz' => 'Quiz climat',
            'calculator' => 'Calculateur',
            'challenges' => 'Défis',
            'leaderboard' => 'Classement',
        ],

        'quiz' => [
            'question' => 'Question',
            'completed' => 'Quiz terminé !',
            'excellent' => 'Excellent ! Vous êtes un expert du climat.',
            'good' => 'Bon résultat ! Continuez à apprendre.',
            'keep_learning' => 'Continuez à vous informer sur le climat.',
            'retry' => 'Recommencer le quiz',
        ],

        'calculator' => [
            'title' => 'Calculateur d\'empreinte personnelle',
            'commute_distance' => 'Distance domicile-travail (km)',
            'commute_mode' => 'Mode de transport',
            'diet' => 'Régime alimentaire',
            'flights_short' => 'Vols court-courrier / an',
            'flights_long' => 'Vols long-courrier / an',
            'heating_type' => 'Type de chauffage',
            'calculate' => 'Calculer mon empreinte',
            'your_footprint' => 'Votre empreinte carbone',
            'tonnes_year' => 't CO2e/an',
            'breakdown' => 'Répartition',
            'recalculate' => 'Recalculer',

            'modes' => [
                'car_petrol' => 'Voiture essence',
                'car_diesel' => 'Voiture diesel',
                'car_electric' => 'Voiture électrique',
                'public_transport' => 'Transports en commun',
                'bike' => 'Vélo',
                'walk' => 'Marche',
            ],

            'diets' => [
                'vegan' => 'Végan',
                'vegetarian' => 'Végétarien',
                'mixed' => 'Mixte',
                'meat_heavy' => 'Riche en viande',
            ],

            'heating' => [
                'gas' => 'Gaz naturel',
                'oil' => 'Fioul',
                'electric' => 'Électrique',
                'heat_pump' => 'Pompe à chaleur',
            ],
        ],

        'challenges' => [
            'title' => 'Défis éco-responsables',
            'no_car_week' => 'Semaine sans voiture',
            'meatless_monday' => 'Lundi sans viande',
            'energy_saver' => 'Économiseur d\'énergie',
            'bike_to_work' => 'Vélo au travail',
            'join' => 'Rejoindre',
            'leave' => 'Quitter',
            'mark_complete' => 'Marquer comme terminé',
            'co2_saved' => 'CO2 économisé',
        ],

        'leaderboard' => [
            'title' => 'Classement',
            'participate' => 'Participer au classement',
            'your_rank' => 'Votre rang',
            'rank' => 'Rang',
            'name' => 'Nom',
            'points' => 'Points',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Badge Promotion (T169-T172)
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Compliance Monitor (T177-T179)
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | 5 Pillars Navigation (T166)
    |--------------------------------------------------------------------------
    */

    'pillars' => [
        'measure' => 'Mesurer',
        'plan' => 'Planifier',
        'engage' => 'Engager',
        'report' => 'Rapporter',
        'promote' => 'Promouvoir',

        // Sub-items
        'sites' => 'Sites',
        'transition' => 'Plan de transition',
        'trajectory' => 'Trajectoire SBTi',
        'assessments' => 'Bilans annuels',
        'suppliers' => 'Fournisseurs',
        'employees' => 'Équipes',
        'reports' => 'Rapports',
        'compliance' => 'Conformité',
        'badges' => 'Badges',
        'showcase' => 'Vitrine',
    ],

    'settings' => [
        'title' => 'Paramètres',
        'organization' => 'Paramètres de l\'organisation',
        'my_company' => 'Mon entreprise',
        'team' => 'Équipe',
        'users' => 'Utilisateurs',
        'profile' => 'Profil',
        'sites' => 'Sites',
        'billing' => 'Abonnement',
        'navigation_mode' => 'Mode de navigation',
        'nav_standard' => 'Navigation standard',
        'nav_standard_desc' => 'Menu classique par fonctionnalité',
        'nav_pillars' => 'Navigation 5 Piliers',
        'nav_pillars_desc' => 'Organisée par piliers stratégiques (style TrackZero)',
    ],

    'profile' => [
        'title' => 'Profil',
        'info' => 'Informations du profil',
        'name' => 'Nom',
        'email' => 'Email',
        'save' => 'Sauvegarder',
        'password' => 'Mot de passe',
        'current_password' => 'Mot de passe actuel',
        'new_password' => 'Nouveau mot de passe',
        'confirm_password' => 'Confirmer le mot de passe',
        'change_password' => 'Changer le mot de passe',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users & Roles
    |--------------------------------------------------------------------------
    */

    'users' => [
        'invite' => 'Inviter un utilisateur',
        'member' => 'Membre',
        'role' => 'Rôle',
        'status' => 'Statut',
        'last_login' => 'Dernière connexion',
        'you' => '(vous)',
        'never' => 'Jamais',
        'edit_role' => 'Modifier le rôle',
        'remove' => 'Retirer',
        'confirm_remove' => 'Êtes-vous sûr de vouloir retirer cet utilisateur ?',
    ],

    'roles' => [
        'owner' => 'Propriétaire',
        'admin' => 'Administrateur',
        'manager' => 'Gestionnaire',
        'member' => 'Membre',
        'viewer' => 'Lecteur',
    ],

    'compliance' => [
        'title' => 'Tableau de bord Conformité',
        'subtitle' => 'Suivez votre conformité CSRD et vos certifications ISO',
        'reporting_year' => 'Année de reporting',
        'add_task' => 'Ajouter une tâche',

        // Tabs
        'tabs' => [
            'overview' => 'Vue d\'ensemble',
            'tasks' => 'Tâches',
        ],

        // Stats
        'progress' => 'Progression',
        'disclosures' => 'divulgations',
        'certifications' => 'Certifications',
        'certified' => 'Certifié',
        'in_progress' => 'En cours',
        'expiring_soon' => 'expirant bientôt',
        'overdue_tasks' => 'Tâches en retard',
        'no_overdue' => 'Aucune tâche en retard',
        'upcoming' => 'À venir',
        'no_upcoming' => 'Aucune tâche à venir',
        'by_category' => 'par catégorie',
        'requirements' => 'exigences',

        // CSRD Categories
        'categories' => [
            'environment' => 'Environnement',
            'social' => 'Social',
            'governance' => 'Gouvernance',
        ],

        // ISO Categories
        'iso_categories' => [
            'environmental' => 'Environnemental',
            'energy' => 'Énergie',
            'quality' => 'Qualité',
            'carbon' => 'Carbone',
        ],

        // Table Headers
        'code' => 'Code',
        'disclosure' => 'Divulgation',
        'category' => 'Catégorie',
        'status' => 'Statut',
        'mandatory' => 'Obligatoire',
        'no_frameworks' => 'Aucun cadre CSRD trouvé',
        'no_standards' => 'Aucune norme ISO trouvée',
        'expires' => 'Expire',
        'certifier' => 'Certificateur',

        // Status Labels
        'status' => [
            'not_started' => 'Non commencé',
            'in_progress' => 'En cours',
            'compliant' => 'Conforme',
            'non_compliant' => 'Non conforme',
        ],

        'cert_status' => [
            'not_certified' => 'Non certifié',
            'in_progress' => 'En cours',
            'certified' => 'Certifié',
            'expired' => 'Expiré',
        ],

        // Task Status
        'task_status' => [
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'overdue' => 'En retard',
        ],

        // Priority
        'priority' => [
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Haute',
            'critical' => 'Critique',
        ],

        // Tasks
        'task' => 'Tâche',
        'type' => 'Type',
        'due_date' => 'Date d\'échéance',
        'no_tasks' => 'Aucune tâche de conformité',
        'mark_complete' => 'Marquer comme terminé',
        'confirm_delete_task' => 'Êtes-vous sûr de vouloir supprimer cette tâche ?',

        // Task Modal
        'new_task' => 'Nouvelle tâche',
        'edit_task' => 'Modifier la tâche',
        'internal' => 'Interne',
        'title' => 'Titre',
        'description' => 'Description',
        'task_title_placeholder' => 'Titre de la tâche...',
        'task_description_placeholder' => 'Description détaillée...',

        // Messages
        'status_updated' => 'Statut mis à jour avec succès',
        'task_created' => 'Tâche créée avec succès',
        'task_updated' => 'Tâche mise à jour avec succès',
        'task_completed' => 'Tâche terminée avec succès',
        'task_deleted' => 'Tâche supprimée avec succès',
    ],

    'promote' => [
        'title' => 'Vitrine des Badges',
        'subtitle' => 'Partagez vos réussites carbone',
        'no_badges' => 'Pas encore de badges obtenus',
        'no_badges_description' => 'Continuez vos efforts de réduction carbone pour débloquer des badges.',
        'start_measuring' => 'Commencer à mesurer',

        // Badge details
        'your_badges' => 'Vos Badges',
        'badge_gallery' => 'Galerie de Badges',
        'total_points' => 'Points Totaux',
        'selected_badge' => 'Badge Sélectionné',
        'earned_on' => 'Obtenu le',
        'points' => 'points',

        // Actions
        'share' => 'Partager',
        'embed' => 'Intégrer',
        'download' => 'Télécharger',

        // Share modal
        'share_title' => 'Partager ce badge',
        'share_description' => 'Partagez votre réussite sur les réseaux sociaux ou copiez le lien.',
        'copy_link' => 'Copier le lien',
        'link_copied' => 'Lien copié !',
        'share_on_linkedin' => 'Partager sur LinkedIn',
        'share_on_twitter' => 'Partager sur X (Twitter)',
        'linkedin_summary' => 'Nous sommes fiers d\'avoir obtenu le badge :badge pour nos efforts en matière de gestion carbone. Vérifié par Carbex.',
        'twitter_text' => 'Nous avons obtenu le badge :badge pour notre engagement carbone ! #CarbonNeutral #Sustainability',

        // Embed modal
        'embed_title' => 'Intégrer ce badge',
        'embed_description' => 'Copiez le code HTML pour intégrer ce badge sur votre site web.',
        'embed_size' => 'Taille',
        'embed_size_small' => 'Petit (200x250)',
        'embed_size_medium' => 'Moyen (300x375)',
        'embed_size_large' => 'Grand (400x500)',
        'copy_code' => 'Copier le code',
        'code_copied' => 'Code copié !',
        'preview' => 'Aperçu',

        // Download modal
        'download_title' => 'Télécharger les assets',
        'download_description' => 'Téléchargez les assets pour vos communications.',
        'format' => 'Format',
        'download_badge' => 'Télécharger le badge',
        'download_signature' => 'Signature email',
        'download_social_kit' => 'Kit réseaux sociaux',
        'social_kit_preparing' => 'Préparation du kit en cours...',

        // Public badge page
        'awarded_to' => 'Décerné à',
        'verified' => 'Vérifié',
        'verified_by_carbex' => 'Vérifié par Carbex',
        'verify' => 'Vérifier',
        'learn_more' => 'En savoir plus',
        'cta_text' => 'Vous aussi, mesurez et réduisez votre empreinte carbone.',
        'start_free' => 'Commencer gratuitement',
        'all_rights_reserved' => 'Tous droits réservés.',

        // Email signature & SEO
        'seo_description' => ':organization a obtenu le badge :badge pour ses efforts de gestion carbone. Vérifié par Carbex.',
        'carbon_badge' => 'Badge Carbone',
        'our_organization' => 'Notre Organisation',
        'view_badge' => 'Voir le badge',
        'powered_by' => 'Propulsé par',
    ],

    /*
    |--------------------------------------------------------------------------
    | Suppliers Management
    |--------------------------------------------------------------------------
    */

    'suppliers' => [
        'title' => 'Gestion des fournisseurs',
        'description' => 'Gérez vos fournisseurs et collectez leurs données d\'émissions carbone.',
        'import_csv' => 'Importer CSV',
        'add_supplier' => 'Ajouter un fournisseur',
        'edit_supplier' => 'Modifier le fournisseur',
        'search_placeholder' => 'Rechercher un fournisseur...',
        'all_statuses' => 'Tous les statuts',
        'all_quality' => 'Toutes qualités',
        'empty' => 'Aucun fournisseur trouvé',
        'add_first' => 'Ajouter votre premier fournisseur',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce fournisseur ?',

        // Table headers
        'name' => 'Nom',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'sector' => 'Secteur',
        'annual_spend' => 'Dépenses annuelles',
        'status' => 'Statut',
        'data_quality' => 'Qualité des données',
        'country' => 'Pays',
        'contact_name' => 'Nom du contact',
        'contact_email' => 'Email du contact',
        'notes' => 'Notes',

        // Stats
        'stats' => [
            'total' => 'Total fournisseurs',
            'active' => 'Actifs',
            'with_data' => 'Avec données',
            'pending' => 'En attente',
            'total_spend' => 'Dépenses totales',
        ],

        // Status labels
        'statuses' => [
            'pending' => 'En attente',
            'invited' => 'Invité',
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ],

        // Data quality labels
        'quality' => [
            'none' => 'Aucune donnée',
            'estimated' => 'Estimée',
            'supplier_specific' => 'Spécifique fournisseur',
            'verified' => 'Vérifiée',
        ],

        // Invitation
        'invite' => 'Inviter',
        'send_invitation' => 'Envoyer une invitation',
        'due_date' => 'Date limite',
        'message' => 'Message personnalisé',
        'send' => 'Envoyer',

        // CSV Import
        'csv_file' => 'Fichier CSV',
        'csv_format' => 'Format attendu (séparateur point-virgule) :',
        'download_template' => 'Télécharger le modèle',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification
    |--------------------------------------------------------------------------
    */

    'gamification' => [
        'title' => 'Badges & Récompenses',
        'subtitle' => 'Gagnez des badges en progressant dans votre démarche carbone',
        'total_points' => 'Points totaux',
        'badges_earned' => 'Badges obtenus',
        'next_level' => 'Prochain niveau',
        'points' => 'points',
        'check_badges' => 'Voir les badges',
        'leaderboard' => 'Classement',
        'your_badges' => 'Vos badges',
        'no_badges_available' => 'Aucun badge disponible pour le moment',
        'how_it_works' => 'Comment ça marche',
        'badge_categories' => 'Catégories de badges',

        // Levels
        'level' => [
            'starter' => 'Débutant',
            'bronze' => 'Bronze',
            'silver' => 'Argent',
            'gold' => 'Or',
            'platinum' => 'Platine',
        ],

        // Categories
        'category' => [
            'all' => 'Tous',
            'assessment' => 'Bilan',
            'assessment_desc' => 'Badges liés à la réalisation de votre bilan carbone',
            'reduction' => 'Réduction',
            'reduction_desc' => 'Badges liés à vos efforts de réduction des émissions',
            'engagement' => 'Engagement',
            'engagement_desc' => 'Badges liés à votre engagement dans la plateforme',
            'expert' => 'Expert',
            'expert_desc' => 'Badges pour les utilisateurs avancés',
        ],

        // Steps
        'step1_title' => 'Complétez votre bilan',
        'step1_desc' => 'Remplissez vos données d\'émissions pour débloquer vos premiers badges',
        'step2_title' => 'Réduisez vos émissions',
        'step2_desc' => 'Mettez en place des actions de réduction pour gagner des points',
        'step3_title' => 'Partagez vos succès',
        'step3_desc' => 'Affichez vos badges sur votre site et réseaux sociaux',
        'badge_og_title' => ':organization a obtenu le badge :badge',
        'badge_earned_by' => 'Badge obtenu par',
        'earned_on' => 'Obtenu le',
        'discover_carbex' => 'Découvrir Carbex',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sectors
    |--------------------------------------------------------------------------
    */

    'sectors' => [
        'technology' => 'Technologie',
        'manufacturing' => 'Industrie',
        'services' => 'Services',
        'retail' => 'Commerce',
        'healthcare' => 'Santé',
        'finance' => 'Finance',
        'construction' => 'Construction',
        'transport' => 'Transport',
        'hospitality' => 'Hôtellerie-Restauration',
        'other' => 'Autre',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookies Consent (GDPR/RGPD)
    |--------------------------------------------------------------------------
    */

    'cookies' => [
        'title' => 'Paramètres des cookies',
        'description' => 'Nous utilisons des cookies pour vous offrir la meilleure expérience. Certains cookies sont essentiels au fonctionnement du site, d\'autres nous aident à l\'améliorer.',
        'legal_notice' => 'Conformément au RGPD Art. 7, nous avons besoin de votre consentement pour les cookies non essentiels.',

        'accept_all' => 'Tout accepter',
        'essential_only' => 'Essentiels uniquement',
        'customize' => 'Personnaliser',
        'save_preferences' => 'Enregistrer les préférences',

        'essential_title' => 'Cookies essentiels',
        'essential_desc' => 'Ces cookies sont indispensables au fonctionnement du site. Ils permettent les fonctions de base comme la navigation et l\'accès aux zones sécurisées.',
        'always_active' => 'Toujours actif',

        'functional_title' => 'Cookies fonctionnels',
        'functional_desc' => 'Ces cookies permettent des fonctionnalités avancées comme les préférences de langue, le fuseau horaire et l\'affichage personnalisé.',

        'analytics_title' => 'Cookies analytiques',
        'analytics_desc' => 'Ces cookies nous aident à comprendre comment les visiteurs interagissent avec le site pour l\'améliorer.',

        'marketing_title' => 'Cookies marketing',
        'marketing_desc' => 'Ces cookies sont utilisés pour rendre la publicité plus pertinente pour vous.',

        'privacy_policy' => 'Politique de confidentialité',
        'legal_notice_link' => 'Mentions légales',
    ],

    /*
    |--------------------------------------------------------------------------
    | GDPR Data Rights
    |--------------------------------------------------------------------------
    */

    'gdpr' => [
        'title' => 'Paramètres de confidentialité',
        'subtitle' => 'Gérez vos données personnelles conformément au RGPD',

        // Consent Management (Art. 7 RGPD)
        'consent_title' => 'Gestion des consentements',
        'consent_description' => 'Gérez vos consentements pour différents types de traitement de données',
        'consent_marketing' => 'Communications marketing',
        'consent_analytics' => 'Analyse d\'utilisation',
        'consent_ai' => 'Fonctionnalités IA',

        // Data Export (Art. 20 RGPD)
        'export_data' => 'Exporter mes données',
        'export_description' => 'Téléchargez toutes vos données personnelles dans un format portable (Art. 20 RGPD)',
        'export_button' => 'Télécharger mes données',
        'export_processing' => 'Préparation de l\'export...',
        'export_ready' => 'Votre export de données est prêt à être téléchargé.',

        // Data Deletion (Art. 17 RGPD)
        'delete_account' => 'Supprimer mon compte',
        'delete_description' => 'Supprimez votre compte et toutes les données associées (Art. 17 RGPD - Droit à l\'effacement)',
        'delete_warning' => 'Cette action est irréversible. Toutes vos données seront définitivement supprimées.',
        'delete_button' => 'Supprimer définitivement mon compte',
        'delete_confirm_title' => 'Vraiment supprimer le compte ?',
        'delete_confirm_message' => 'Veuillez saisir "SUPPRIMER" pour confirmer :',
        'delete_confirm_word' => 'SUPPRIMER',

        // Data Access (Art. 15 RGPD)
        'access_title' => 'Droit d\'accès',
        'access_description' => 'Vous avez le droit d\'accéder à vos données personnelles stockées (Art. 15 RGPD)',

        // Data Rectification (Art. 16 RGPD)
        'rectification_title' => 'Rectification',
        'rectification_description' => 'Vous pouvez rectifier vos données inexactes à tout moment (Art. 16 RGPD)',

        // Data Portability (Art. 20 RGPD)
        'portability_title' => 'Portabilité des données',
        'portability_description' => 'Recevez vos données dans un format lisible par machine',

        // Right to Object (Art. 21 RGPD)
        'object_title' => 'Droit d\'opposition',
        'object_description' => 'Vous pouvez vous opposer au traitement de vos données (Art. 21 RGPD)',

        // Legal References
        'legal_basis' => 'Base légale',
        'legal_basis_consent' => 'Consentement (Art. 6 §1 a RGPD)',
        'legal_basis_contract' => 'Exécution du contrat (Art. 6 §1 b RGPD)',
        'legal_basis_legal' => 'Obligation légale (Art. 6 §1 c RGPD)',
        'legal_basis_legitimate' => 'Intérêt légitime (Art. 6 §1 f RGPD)',

        // Contact
        'dpo_contact' => 'Délégué à la protection des données',
        'dpo_email' => 'dpo@carbex.de',
    ],

    /*
    |--------------------------------------------------------------------------
    | Marketing - Pricing Page
    |--------------------------------------------------------------------------
    */

    'marketing' => [
        'for_who' => [
            'title' => 'Pour qui ?',
            'description' => 'Carbex s\'adapte à toutes les tailles d\'entreprise : PME, ETI et grandes entreprises. Découvrez comment notre plateforme répond à vos besoins.',
            'hero_title' => 'Une solution pour chaque entreprise',
            'hero_subtitle' => 'De la PME au grand groupe, Carbex s\'adapte à vos besoins et à votre maturité carbone.',
            'trust_us' => 'Ils nous font confiance',

            'why_choose' => [
                'title' => 'Pourquoi choisir Carbex ?',
                'subtitle' => '3 bonnes raisons de nous faire confiance',
                'measure_title' => 'Mesurez votre impact',
                'measure_desc' => 'Réalisez facilement votre premier bilan carbone complet (Scope 1, 2, 3), sans expert et sans engagement. Notre assistant IA vous guide à chaque étape.',
                'drive_title' => 'Pilotez votre transition',
                'drive_desc' => 'Suivez vos émissions dans le temps, fixez des objectifs de réduction alignés SBTi et construisez un plan d\'action concret avec des recommandations personnalisées.',
                'comply_title' => 'Répondez aux obligations',
                'comply_desc' => 'Générez automatiquement vos rapports RSE, BEGES, CSRD ou ESG, et démontrez votre conformité aux réglementations françaises et européennes.',
            ],

            'audiences' => [
                'title' => 'Adapté à votre structure',
                'subtitle' => 'Que vous soyez PME, ETI ou grande entreprise',
                'sme' => [
                    'title' => 'PME',
                    'size' => '10 à 250 salariés',
                    'desc' => 'Mesurez votre empreinte carbone pour optimiser vos coûts, répondre à la réglementation et améliorer votre image auprès de vos clients.',
                    'feature1' => 'Premier bilan en moins d\'une journée',
                    'feature2' => 'Pas besoin d\'expert carbone',
                    'feature3' => 'Tarifs adaptés',
                    'cta' => 'Démarrer l\'essai gratuit',
                ],
                'midsize' => [
                    'title' => 'ETI',
                    'size' => '250 à 5000 salariés',
                    'desc' => 'Suivez l\'impact global de votre organisation, réduisez les émissions sur plusieurs sites et répondez aux demandes de vos clients grands comptes.',
                    'feature1' => 'Gestion multi-sites',
                    'feature2' => 'Conformité BEGES obligatoire',
                    'feature3' => 'Reporting automatisé',
                    'cta' => 'Choisir Premium',
                ],
                'enterprise' => [
                    'title' => 'Grandes Entreprises',
                    'size' => 'Plus de 5000 salariés',
                    'desc' => 'Gérez votre empreinte carbone mondiale, respectez les normes internationales et optimisez vos stratégies de réduction avec un accompagnement dédié.',
                    'feature1' => 'CSRD et GRI ready',
                    'feature2' => 'API et intégrations',
                    'feature3' => 'Support dédié',
                    'cta' => 'Nous contacter',
                ],
            ],

            'testimonial' => [
                'title' => 'Ce qu\'en disent nos clients',
                'quote' => 'Le support expert nous a été précieux pour affiner nos interprétations. La possibilité d\'importer automatiquement nos FEC et de gérer plusieurs sites a fait toute la différence. C\'est un outil robuste et professionnel.',
                'author' => 'Aicha Benhamou',
                'role' => 'Directrice Développement Durable — Terres & Saveurs',
            ],

            'cta' => [
                'title' => 'Prêt à mesurer votre empreinte ?',
                'subtitle' => 'Commencez gratuitement et découvrez comment Carbex peut vous aider.',
                'primary' => 'Essai gratuit 15 jours',
                'secondary' => 'Voir les tarifs',
            ],
        ],
        'contact' => [
            'title' => 'Contact',
            'description' => 'Contactez l\'équipe Carbex pour toute question sur notre plateforme de bilan carbone.',
            'hero_title' => 'Parlons de votre projet',
            'hero_subtitle' => 'Notre équipe est là pour répondre à vos questions et vous accompagner.',
            'contact_us' => 'Nous contacter',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'address' => 'Adresse',
            'hours' => 'Horaires',
            'hours_weekdays' => 'Lundi - Vendredi : 9h00 - 18h00',
            'hours_premium' => 'Support clients Premium : 24/7',
            'send_message' => 'Envoyer un message',
            'form' => [
                'name' => 'Nom complet',
                'email' => 'Email professionnel',
                'company' => 'Entreprise',
                'subject' => 'Sujet',
                'select_subject' => 'Sélectionnez un sujet',
                'message' => 'Message',
                'send' => 'Envoyer le message',
            ],
            'subjects' => [
                'demo' => 'Demande de démonstration',
                'pricing' => 'Question sur les tarifs',
                'enterprise' => 'Offre Enterprise',
                'partnership' => 'Partenariat',
                'support' => 'Support technique',
                'other' => 'Autre',
            ],
        ],
        'pricing' => [
            'title' => 'Tarifs',
            'description' => 'Découvrez nos offres de bilan carbone pour PME. Essai gratuit 15 jours, puis à partir de 40 EUR/mois.',
            'hero_title' => 'Tarifs simples et transparents',
            'hero_subtitle' => 'Commencez gratuitement. Évoluez selon vos besoins. Sans engagement.',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'free_trial' => 'Essai gratuit',
            'trial_duration' => '15 jours d\'essai complet',
            'includes' => 'Inclus :',
            'most_popular' => 'Le plus populaire',
            'advanced' => 'Avancé',
            'per_month' => 'par mois HT',
            'per_year' => 'par an HT',
            'premium_savings' => 'Soit 33 EUR/mois - Économisez 80 EUR/an',
            'advanced_savings' => 'Soit 100 EUR/mois - Économisez 240 EUR/an',
            'no_commitment' => 'Sans engagement, annulez à tout moment',
            'all_from_trial' => 'Tout de l\'Essai, plus :',
            'all_from_premium' => 'Tout de Premium, plus :',
            'start_trial' => 'Commencer l\'essai',
            'choose_premium' => 'Choisir Premium',
            'choose_advanced' => 'Choisir Avancé',
            'enterprise_title' => 'Besoin d\'une solution sur mesure ?',
            'enterprise_subtitle' => 'Pour les grandes entreprises, groupes et consultants.',
            'contact_team' => 'Contacter notre équipe',
            'comparison_title' => 'Comparatif des fonctionnalités',
            'trial' => 'Essai',
            'unlimited' => 'Illimité',
            'support_email' => 'Email',
            'support_priority' => 'Prioritaire',
            'support_dedicated' => 'Dédié',
            'faq_title' => 'Questions fréquentes',
            'cta_title' => 'Prêt à mesurer votre impact ?',
            'cta_subtitle' => 'Rejoignez les PME qui prennent le contrôle de leur empreinte carbone.',
            'start_free' => 'Commencer gratuitement',
            'trial_no_card' => '15 jours d\'essai gratuit. Sans carte bancaire.',

            'features' => [
                'one_user' => '1 utilisateur',
                'one_site' => '1 site',
                'full_access' => 'Accès complet à la plateforme',
                'one_report' => '1 rapport PDF',
                'email_support' => 'Support par email',
                'five_users' => 'Jusqu\'à 5 utilisateurs',
                'three_sites' => 'Jusqu\'à 3 sites',
                'bank_import' => 'Import bancaire automatique',
                'unlimited_reports' => 'Rapports illimités (Word, Excel, PDF)',
                'ademe_ghg' => 'Déclarations ADEME et GHG Protocol',
                'priority_support' => 'Support prioritaire',
                'unlimited_users' => 'Utilisateurs illimités',
                'unlimited_sites' => 'Sites illimités',
                'full_api' => 'Accès API complet',
                'scope3_suppliers' => 'Module fournisseurs Scope 3',
                'dedicated_support' => 'Support dédié',
                'custom_training' => 'Formation personnalisée',
            ],

            'table' => [
                'users' => 'Utilisateurs',
                'sites' => 'Sites',
                'reports' => 'Rapports',
                'bank_import' => 'Import bancaire',
                'ademe_ghg' => 'Export ADEME/GHG',
                'api_access' => 'Accès API',
                'suppliers_module' => 'Module fournisseurs',
                'support' => 'Support',
            ],

            'faq' => [
                'change_plan_q' => 'Puis-je changer de plan à tout moment ?',
                'change_plan_a' => 'Oui, vous pouvez passer à un plan supérieur à tout moment. La différence sera calculée au prorata. Pour passer à un plan inférieur, le changement prendra effet à la fin de votre période de facturation.',
                'trial_duration_q' => 'Quelle est la durée de l\'essai gratuit ?',
                'trial_duration_a' => 'L\'essai gratuit dure 15 jours avec un accès complet à toutes les fonctionnalités. Aucune carte bancaire n\'est requise pour commencer.',
                'prices_vat_q' => 'Les prix sont-ils HT ou TTC ?',
                'prices_vat_a' => 'Tous les prix affichés sont HT (hors taxes). La TVA applicable (20% en France) sera ajoutée lors du paiement.',
                'monthly_payment_q' => 'Comment fonctionne le paiement mensuel ?',
                'monthly_payment_a' => 'Le paiement mensuel est prélevé automatiquement chaque mois. Vous pouvez annuler à tout moment, sans frais ni pénalité. L\'abonnement reste actif jusqu\'à la fin du mois payé.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blog
    |--------------------------------------------------------------------------
    */

    'blog' => [
        'back_to_blog' => 'Retour au blog',
        'min_read' => 'min de lecture',
        'share_article' => 'Partager cet article',
        'share_twitter' => 'Partager sur Twitter',
        'share_linkedin' => 'Partager sur LinkedIn',
        'copy_link' => 'Copier le lien',
        'related_articles' => 'Articles similaires',
        'cta_title' => 'Prêt à mesurer votre empreinte carbone ?',
        'cta_subtitle' => 'Commencez votre bilan carbone en quelques minutes avec Carbex.',
        'cta_button' => 'Démarrer l\'essai gratuit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal Pages
    |--------------------------------------------------------------------------
    */

    'legal' => [
        // Common
        'label' => 'Legal',
        'last_updated' => 'Dernière mise à jour : Décembre 2024',

        // Legal Notice (Mentions légales)
        'mentions' => [
            'title' => 'Mentions Légales',
            'meta_description' => 'Mentions légales et politique de confidentialité de Carbex, plateforme de bilan carbone pour entreprises.',

            'section1_title' => '1. Éditeur du site',
            'site_edited_by' => 'Le site carbex.fr est édité par :',
            'company_name' => 'Carbex SAS',
            'company_type' => 'Société par Actions Simplifiée au capital de 10 000 €',
            'capital' => 'Capital : 10 000 EUR',
            'address' => 'Siège social : 123 Avenue de la République, 75011 Paris, France',
            'register' => 'RCS Paris : XXX XXX XXX',
            'tax_id' => 'SIRET : XXX XXX XXX XXXXX',
            'vat_id' => 'TVA intracommunautaire : FR XX XXX XXX XXX',
            'director' => 'Directeur de la publication : [Nom du dirigeant]',
            'email' => 'Email',

            'section2_title' => '2. Hébergement',
            'hosted_by' => 'Le site est hébergé par :',
            'hosting_name' => 'Scaleway SAS',
            'hosting_address' => '8 rue de la Ville l\'Évêque, 75008 Paris, France',
            'website' => 'Site web',

            'section3_title' => '3. Politique de confidentialité',
            'section3_1_title' => '3.1 Responsable du traitement',
            'section3_1_text' => 'Carbex SAS est responsable du traitement des données personnelles collectées sur ce site, conformément au Règlement Général sur la Protection des Données (RGPD).',
            'section3_2_title' => '3.2 Données collectées',
            'section3_2_text' => 'Nous collectons les données suivantes :',
            'data_id' => 'Données d\'identification : nom, prénom, email professionnel',
            'data_company' => 'Données de l\'entreprise : raison sociale, SIRET, secteur d\'activité',
            'data_connection' => 'Données de connexion : adresse IP, logs de connexion',
            'data_business' => 'Données métier : consommations énergétiques, données d\'émissions',
            'section3_3_title' => '3.3 Finalités du traitement',
            'section3_3_text' => 'Vos données sont utilisées pour :',
            'purpose_service' => 'Fournir le service de bilan carbone',
            'purpose_account' => 'Gérer votre compte et votre abonnement',
            'purpose_communication' => 'Vous envoyer des communications liées au service',
            'purpose_improve' => 'Améliorer nos services (statistiques anonymisées)',
            'purpose_legal' => 'Respecter nos obligations légales',
            'section3_4_title' => '3.4 Base légale',
            'section3_4_text' => 'Le traitement de vos données repose sur :',
            'legal_contract' => 'L\'exécution du contrat (fourniture du service)',
            'legal_legitimate' => 'Notre intérêt légitime (amélioration du service)',
            'legal_consent' => 'Votre consentement (newsletter, cookies non essentiels)',
            'legal_obligation' => 'Nos obligations légales (conservation des factures)',
            'section3_5_title' => '3.5 Durée de conservation',
            'account_data' => 'Données de compte : 3 ans après la fin de l\'abonnement',
            'billing_data' => 'Données de facturation : 10 ans (obligation légale)',
            'connection_logs' => 'Logs de connexion : 1 an',
            'business_data' => 'Données métier : supprimées sur demande ou 3 ans après inactivité',
            'section3_6_title' => '3.6 Destinataires des données',
            'section3_6_text' => 'Vos données peuvent être partagées avec :',
            'recipient_stripe' => 'Stripe : traitement des paiements',
            'recipient_hosting' => 'Scaleway : hébergement',
            'recipient_ai' => 'Anthropic (Claude AI) : assistant IA (données anonymisées)',
            'recipient_email' => 'Brevo : envoi d\'emails transactionnels',
            'no_transfer' => 'Aucune donnée n\'est transférée hors de l\'Union Européenne sans garanties adéquates.',
            'section3_7_title' => '3.7 Vos droits',
            'section3_7_text' => 'Conformément au RGPD, vous disposez des droits suivants :',
            'right_access' => 'Droit d\'accès : obtenir une copie de vos données',
            'right_rectification' => 'Droit de rectification : corriger vos données inexactes',
            'right_erasure' => 'Droit à l\'effacement : demander la suppression de vos données',
            'right_portability' => 'Droit à la portabilité : recevoir vos données dans un format structuré',
            'right_objection' => 'Droit d\'opposition : vous opposer au traitement',
            'right_restriction' => 'Droit à la limitation : limiter le traitement de vos données',
            'contact_dpo' => 'Pour exercer ces droits, contactez notre DPO :',
            'supervisory_authority' => 'En cas de litige, vous pouvez saisir la CNIL :',

            'section4_title' => '4. Cookies',
            'section4_1_title' => '4.1 Cookies essentiels',
            'section4_1_text' => 'Ces cookies sont nécessaires au fonctionnement du site :',
            'cookie_session' => 'Session : maintien de votre connexion',
            'cookie_csrf' => 'CSRF : sécurité contre les attaques',
            'cookie_preferences' => 'Préférences : langue, thème',
            'section4_2_title' => '4.2 Cookies analytiques',
            'section4_2_text' => 'Nous utilisons Plausible Analytics, une solution respectueuse de la vie privée qui ne collecte pas de données personnelles et ne nécessite pas de consentement.',
            'section4_3_title' => '4.3 Gestion des cookies',
            'section4_3_text' => 'Vous pouvez gérer vos préférences de cookies via le bandeau de consentement ou les paramètres de votre navigateur.',

            'section5_title' => '5. Propriété intellectuelle',
            'section5_text1' => 'L\'ensemble du contenu du site (textes, images, logos, code source) est protégé par le droit d\'auteur et appartient à Carbex SAS. Toute reproduction sans autorisation est interdite.',
            'section5_text2' => 'Les marques "Carbex" et "Empreinte Carbone" sont déposées. Les logos des partenaires et clients sont utilisés avec leur autorisation.',

            'section6_title' => '6. Limitation de responsabilité',
            'section6_text' => 'Carbex s\'efforce de fournir des informations exactes et à jour. Cependant, nous ne garantissons pas l\'absence d\'erreurs. Les calculs d\'émissions sont fournis à titre indicatif et ne constituent pas un conseil professionnel.',

            'section7_title' => '7. Liens externes',
            'section7_text' => 'Le site peut contenir des liens vers des sites tiers. Carbex n\'est pas responsable du contenu de ces sites.',

            'section8_title' => '8. Droit applicable',
            'section8_text' => 'Les présentes mentions légales sont soumises au droit français. Tout litige sera soumis aux tribunaux compétents de Paris.',

            'section9_title' => '9. Contact',
            'section9_text' => 'Pour toute question concernant ces mentions légales ou la protection de vos données :',
            'contact_legal' => 'Email',
            'contact_dpo_label' => 'DPO',
            'contact_mail' => 'Courrier : Carbex SAS - Service Juridique, 123 Avenue de la République, 75011 Paris',
        ],

        // Terms of Use (CGU)
        'cgu' => [
            'title' => 'Conditions Générales d\'Utilisation',
            'meta_description' => 'Conditions Générales d\'Utilisation de la plateforme Carbex, outil SaaS de bilan carbone.',

            'article1_title' => 'Article 1 - Définitions',
            'def_platform' => 'Plateforme : L\'application web Carbex accessible à l\'adresse app.carbex.fr',
            'def_user' => 'Utilisateur : Toute personne accédant à la Plateforme',
            'def_account' => 'Compte : Espace personnel de l\'Utilisateur sur la Plateforme',
            'def_organization' => 'Organisation : Entité juridique (entreprise) pour laquelle le bilan carbone est réalisé',
            'def_assessment' => 'Bilan : Évaluation des émissions de gaz à effet de serre sur une période donnée',

            'article2_title' => 'Article 2 - Acceptation des CGU',
            'article2_text' => 'L\'utilisation de la Plateforme implique l\'acceptation pleine et entière des présentes Conditions Générales d\'Utilisation. Si vous n\'acceptez pas ces conditions, veuillez ne pas utiliser la Plateforme.',

            'article3_title' => 'Article 3 - Accès à la Plateforme',
            'article3_1_title' => '3.1 Inscription',
            'article3_1_text' => 'L\'accès aux fonctionnalités de la Plateforme nécessite la création d\'un compte. L\'Utilisateur s\'engage à fournir des informations exactes et à les maintenir à jour.',
            'article3_2_title' => '3.2 Sécurité du compte',
            'article3_2_text' => 'L\'Utilisateur est responsable de la confidentialité de ses identifiants de connexion. Toute activité réalisée depuis son compte est présumée être de son fait.',
            'article3_3_title' => '3.3 Rôles et permissions',
            'article3_3_text' => 'La Plateforme propose différents niveaux d\'accès :',
            'role_owner' => 'Propriétaire : Accès complet, gestion des utilisateurs et facturation',
            'role_admin' => 'Administrateur : Accès complet aux données, pas d\'accès facturation',
            'role_member' => 'Membre : Saisie et consultation des données',
            'role_reader' => 'Lecteur : Consultation uniquement',

            'article4_title' => 'Article 4 - Utilisation de la Plateforme',
            'article4_1_title' => '4.1 Usage autorisé',
            'article4_1_text' => 'La Plateforme est destinée à :',
            'use_ghg' => 'Réaliser des bilans carbone conformes aux standards GHG Protocol',
            'use_track' => 'Suivre et analyser les émissions de gaz à effet de serre',
            'use_plan' => 'Définir et piloter des plans de réduction',
            'use_report' => 'Générer des rapports réglementaires (BEGES, CSRD)',
            'article4_2_title' => '4.2 Usages interdits',
            'article4_2_text' => 'Il est strictement interdit de :',
            'forbidden_illegal' => 'Utiliser la Plateforme à des fins illégales ou frauduleuses',
            'forbidden_security' => 'Tenter de contourner les mesures de sécurité',
            'forbidden_scraping' => 'Extraire massivement des données (scraping)',
            'forbidden_share' => 'Partager son compte avec des tiers non autorisés',
            'forbidden_false' => 'Utiliser la Plateforme pour générer des rapports mensongers',
            'forbidden_resell' => 'Revendre ou sous-licencier l\'accès à la Plateforme',

            'article5_title' => 'Article 5 - Données et contenu',
            'article5_1_title' => '5.1 Données de l\'Utilisateur',
            'article5_1_text' => 'L\'Utilisateur reste propriétaire des données qu\'il saisit sur la Plateforme. Carbex ne peut utiliser ces données que pour fournir le service et, de manière anonymisée, pour améliorer ses algorithmes.',
            'article5_2_title' => '5.2 Facteurs d\'émission',
            'article5_2_text' => 'Les facteurs d\'émission proviennent de sources officielles (Base Carbone ADEME, IPCC, etc.). Carbex ne garantit pas leur exactitude et invite l\'Utilisateur à les vérifier pour les usages réglementaires.',
            'article5_3_title' => '5.3 Sauvegarde',
            'article5_3_text' => 'Carbex effectue des sauvegardes régulières des données. Cependant, l\'Utilisateur est encouragé à exporter régulièrement ses données.',

            'article6_title' => 'Article 6 - Assistant IA',
            'article6_text' => 'La Plateforme intègre un assistant basé sur l\'intelligence artificielle. Les suggestions et recommandations fournies par l\'IA sont indicatives et ne constituent pas un conseil professionnel. L\'Utilisateur reste seul responsable des décisions prises sur la base de ces suggestions.',

            'article7_title' => 'Article 7 - Disponibilité',
            'article7_text' => 'Carbex s\'efforce d\'assurer une disponibilité continue de la Plateforme. Cependant, des interruptions peuvent survenir pour maintenance ou en cas de force majeure. Carbex informera les Utilisateurs dans la mesure du possible.',

            'article8_title' => 'Article 8 - Propriété intellectuelle',
            'article8_text' => 'Tous les éléments de la Plateforme (code, design, textes, algorithmes) sont protégés par le droit d\'auteur et appartiennent à Carbex SAS. Toute reproduction non autorisée est interdite.',

            'article9_title' => 'Article 9 - Limitation de responsabilité',
            'article9_text' => 'Carbex fournit la Plateforme "en l\'état". En aucun cas Carbex ne pourra être tenu responsable des dommages indirects, pertes de données ou manque à gagner liés à l\'utilisation de la Plateforme.',

            'article10_title' => 'Article 10 - Suspension et résiliation',
            'article10_text' => 'Carbex se réserve le droit de suspendre ou de résilier l\'accès d\'un Utilisateur en cas de violation des présentes CGU, après notification préalable sauf en cas d\'urgence.',

            'article11_title' => 'Article 11 - Modification des CGU',
            'article11_text' => 'Carbex peut modifier les présentes CGU à tout moment. Les Utilisateurs seront informés des modifications substantielles. La poursuite de l\'utilisation après notification vaut acceptation.',

            'article12_title' => 'Article 12 - Contact',
            'article12_text' => 'Pour toute question concernant ces CGU, contactez-nous à :',
        ],

        // Terms of Sale (CGV)
        'cgv' => [
            'title' => 'Conditions Générales de Vente',
            'meta_description' => 'Conditions Générales de Vente de la plateforme Carbex, outil SaaS de bilan carbone pour entreprises.',

            'article1_title' => 'Article 1 - Objet',
            'article1_text1' => 'Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre la société Carbex SAS (ci-après "Carbex") et tout client professionnel (ci-après "le Client") souhaitant souscrire aux services de la plateforme Carbex.',
            'article1_text2' => 'Carbex propose une solution SaaS (Software as a Service) de bilan carbone et de gestion des émissions de gaz à effet de serre, conforme aux standards GHG Protocol, ISO 14064 et ADEME.',

            'article2_title' => 'Article 2 - Services proposés',
            'article2_text' => 'Carbex propose les services suivants :',
            'service_assessment' => 'Réalisation de bilans carbone (Scopes 1, 2 et 3)',
            'service_factors' => 'Accès à la Base Carbone ADEME et aux facteurs d\'émission',
            'service_dashboard' => 'Tableaux de bord et analyses des émissions',
            'service_plan' => 'Plans de transition et suivi des actions de réduction',
            'service_report' => 'Génération de rapports conformes (BEGES, CSRD, GHG Protocol)',
            'service_ai' => 'Assistant IA pour l\'aide à la saisie et les recommandations',

            'article3_title' => 'Article 3 - Tarifs et modalités de paiement',
            'article3_1_title' => '3.1 Grille tarifaire',
            'article3_1_text' => 'Les tarifs en vigueur sont les suivants (HT) :',
            'price_trial' => 'Essai Gratuit : 0 € - 15 jours d\'accès complet',
            'price_premium' => 'Premium : 400 €/an ou 40 €/mois',
            'price_advanced' => 'Avancé : 1 200 €/an ou 120 €/mois',
            'price_enterprise' => 'Enterprise : Sur devis',
            'article3_2_title' => '3.2 Modalités de paiement',
            'article3_2_text' => 'Le paiement s\'effectue par carte bancaire via notre prestataire de paiement sécurisé Stripe. Les factures sont émises mensuellement ou annuellement selon le mode de facturation choisi.',
            'article3_2_vat' => 'La TVA applicable est celle en vigueur au jour de la facturation (20% pour la France métropolitaine).',

            'article4_title' => 'Article 4 - Durée et résiliation',
            'article4_1_title' => '4.1 Durée',
            'article4_1_text' => 'L\'abonnement est souscrit pour une durée déterminée (mensuelle ou annuelle) renouvelable par tacite reconduction.',
            'article4_2_title' => '4.2 Résiliation',
            'article4_2_text' => 'Le Client peut résilier son abonnement à tout moment depuis son espace client. La résiliation prend effet à la fin de la période de facturation en cours. Aucun remboursement au prorata ne sera effectué.',

            'article5_title' => 'Article 5 - Obligations de Carbex',
            'article5_text' => 'Carbex s\'engage à :',
            'obligation_access' => 'Fournir un accès continu à la plateforme (objectif de disponibilité 99,5%)',
            'obligation_security' => 'Maintenir la sécurité et la confidentialité des données',
            'obligation_support' => 'Assurer un support technique selon le plan souscrit',
            'obligation_update' => 'Mettre à jour régulièrement les facteurs d\'émission',

            'article6_title' => 'Article 6 - Obligations du Client',
            'article6_text' => 'Le Client s\'engage à :',
            'client_accurate' => 'Fournir des informations exactes et à jour',
            'client_credentials' => 'Ne pas partager ses identifiants de connexion',
            'client_terms' => 'Respecter les présentes CGV et les CGU',
            'client_payment' => 'Payer les sommes dues dans les délais impartis',

            'article7_title' => 'Article 7 - Propriété intellectuelle',
            'article7_text' => 'La plateforme Carbex, son code source, ses algorithmes, sa charte graphique et ses contenus sont la propriété exclusive de Carbex SAS. Le Client bénéficie uniquement d\'un droit d\'usage non exclusif et non cessible pendant la durée de son abonnement.',

            'article8_title' => 'Article 8 - Responsabilité',
            'article8_text1' => 'Les calculs d\'émissions fournis par Carbex sont basés sur les données saisies par le Client et les facteurs d\'émission officiels. Carbex ne saurait être tenu responsable de l\'exactitude des résultats en cas de données erronées fournies par le Client.',
            'article8_text2' => 'La responsabilité de Carbex est limitée au montant des sommes effectivement perçues au titre de l\'abonnement sur les 12 derniers mois.',

            'article9_title' => 'Article 9 - Protection des données',
            'article9_text' => 'Carbex s\'engage à respecter la réglementation en vigueur relative à la protection des données personnelles (RGPD). Pour plus d\'informations, consultez notre',
            'privacy_policy' => 'Politique de confidentialité',

            'article10_title' => 'Article 10 - Droit applicable et litiges',
            'article10_text' => 'Les présentes CGV sont soumises au droit français. En cas de litige, les parties s\'engagent à rechercher une solution amiable. À défaut, les tribunaux de Paris seront seuls compétents.',

            'article11_title' => 'Article 11 - Modification des CGV',
            'article11_text' => 'Carbex se réserve le droit de modifier les présentes CGV. Les modifications seront notifiées aux Clients par email au moins 30 jours avant leur entrée en vigueur.',
        ],

        // Commitments (Engagements)
        'engagements' => [
            'title' => 'Nos Engagements',
            'meta_description' => 'Découvrez les engagements de Carbex en matière de transparence, sécurité des données et responsabilité environnementale.',
            'label' => 'Nos valeurs',
            'hero_subtitle' => 'Chez Carbex, nous croyons que la transition écologique passe par la transparence, l\'accessibilité et l\'action concrète.',

            // Cards
            'security_title' => 'Sécurité des données',
            'security_text' => 'Vos données sont hébergées en France sur des serveurs certifiés. Nous appliquons les meilleurs standards de sécurité : chiffrement AES-256, authentification forte, audits réguliers.',
            'security_hosting' => 'Hébergement en France (Scaleway)',
            'security_gdpr' => 'Conformité RGPD',
            'security_backup' => 'Sauvegardes quotidiennes',

            'transparency_title' => 'Transparence totale',
            'transparency_text' => 'Nos calculs sont basés sur les facteurs d\'émission officiels de l\'ADEME. Chaque résultat est traçable et explicable.',
            'transparency_factors' => 'Facteurs ADEME officiels',
            'transparency_ghg' => 'Méthodologie GHG Protocol',
            'transparency_trace' => 'Traçabilité complète',

            'neutrality_title' => 'Neutralité carbone',
            'neutrality_text' => 'Nous pratiquons ce que nous préconisons. Carbex mesure et compense ses propres émissions chaque année.',
            'neutrality_annual' => 'Bilan carbone annuel publié',
            'neutrality_hosting' => 'Hébergement bas carbone',
            'neutrality_offset' => 'Compensation certifiée',

            'accessibility_title' => 'Accessibilité pour tous',
            'accessibility_text' => 'Le bilan carbone ne doit pas être réservé aux grandes entreprises. Nous proposons des tarifs adaptés aux PME.',
            'accessibility_trial' => 'Essai gratuit 15 jours',
            'accessibility_pricing' => 'Tarifs PME dès 400 €/an',
            'accessibility_support' => 'Support inclus',

            // Mission
            'mission_title' => 'Notre mission',
            'mission_text' => 'Démocratiser le bilan carbone pour que chaque entreprise, quelle que soit sa taille, puisse mesurer, comprendre et réduire son impact environnemental.',

            // Standards
            'standards_title' => 'Standards et méthodologies',
            'standard_uba' => 'Base Carbone officielle',
            'standard_ghg' => 'Protocol Scopes 1, 2, 3',
            'standard_iso' => '14064-1 compatible',
            'standard_csrd' => 'Rapports conformes',

            // CTA
            'cta_title' => 'Rejoignez les entreprises engagées',
            'cta_subtitle' => 'Commencez votre bilan carbone aujourd\'hui et contribuez à la transition écologique.',
            'cta_button' => 'Démarrer l\'essai gratuit',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transition Plan
    |--------------------------------------------------------------------------
    */

    'transition_plan' => [
        'title' => 'Plan de transition',
        'actions' => 'Actions',
        'edit_trajectory' => 'Modifier la trajectoire',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation
    |--------------------------------------------------------------------------
    */

    'sidebar' => [
        'dashboard' => 'Tableau de bord',
        'analysis' => 'Analyse',
        'documents' => 'Documents',
        'ai_analysis' => 'Analyse IA',
        'suppliers' => 'Fournisseurs',
        'transition_plan' => 'Plan de transition',
        'reports' => 'Rapports & exports',
        'badges' => 'Badges',
        'new' => 'NEW',

        // Scope names
        'scope1_name' => 'Scope 1 - Émissions directes',
        'scope2_name' => 'Scope 2 - Émissions indirectes liées à l\'énergie',
        'scope3_name' => 'Scope 3 - Autres émissions indirectes',

        // Scope 1 categories
        'cat_1_1' => 'Sources fixes de combustion',
        'cat_1_2' => 'Sources mobiles de combustion',
        'cat_1_4' => 'Émissions fugitives',
        'cat_1_5' => 'Biomasse (sols et forêts)',

        // Scope 2 categories
        'cat_2_1' => 'Consommation d\'électricité',

        // Scope 3 categories
        'cat_3_1' => 'Transport de marchandise amont',
        'cat_3_2' => 'Transport de marchandise aval',
        'cat_3_3' => 'Déplacements domicile-travail',
        'cat_3_5' => 'Déplacements professionnels',
        'cat_4_1' => 'Achats de biens',
        'cat_4_2' => 'Immobilisations de biens',
        'cat_4_3' => 'Gestion des déchets',
        'cat_4_4' => 'Actifs en leasing amont',
        'cat_4_5' => 'Achats de services',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Admin
    |--------------------------------------------------------------------------
    */

    'filament' => [
        'save_configuration' => 'Sauvegarder la configuration',
        'api_keys_configuration' => 'Configuration des clés API',
        'api_keys_description' => 'Les clés API sont stockées de manière sécurisée dans Docker Secrets.',
        'how_to_configure' => 'Comment configurer les clés API :',
        'step_create_secrets' => 'Créez les fichiers secrets dans docker/secrets/',
        'step_add_keys' => 'Ajoutez les clés API dans les fichiers correspondants',
        'step_auto_mount' => 'Les secrets sont automatiquement montés dans le container',
        'secrets_files' => 'Fichiers de secrets :',
        'anthropic_key' => 'Clé API Anthropic (Claude)',
        'openai_key' => 'Clé API OpenAI',
        'google_key' => 'Clé API Google AI',
        'deepseek_key' => 'Clé API DeepSeek',
        'example_command' => 'Exemple de commande :',
    ],

    /*
    |--------------------------------------------------------------------------
    | Supplier Portal
    |--------------------------------------------------------------------------
    */

    'supplier_portal' => [
        'title' => 'Portail Fournisseur',
        'supplier' => 'Fournisseur',
        'data_collection' => 'Collecte de données carbone :year',
        'data_collection_desc' => 'Veuillez renseigner vos émissions de gaz à effet de serre pour l\'année :year. Ces données nous permettront de calculer notre empreinte carbone Scope 3.',
        'deadline' => 'Date limite :',

        // Scope 1
        'scope1_title' => 'Émissions Scope 1 - Émissions directes',
        'scope1_desc' => 'Émissions provenant de sources détenues ou contrôlées par votre organisation (combustibles, véhicules, procédés).',
        'scope1_total' => 'Total Scope 1 (tCO2e)',
        'detail_by_category' => 'Détailler par catégorie',
        'stationary_combustion' => 'Combustion stationnaire (tCO2e)',
        'mobile_combustion' => 'Combustion mobile (tCO2e)',
        'fugitive_emissions' => 'Émissions fugitives (tCO2e)',
        'process_emissions' => 'Émissions de procédés (tCO2e)',

        // Scope 2
        'scope2_title' => 'Émissions Scope 2 - Énergie indirecte',
        'scope2_desc' => 'Émissions liées à l\'électricité, la chaleur ou la vapeur achetée.',
        'scope2_location' => 'Scope 2 Location-based (tCO2e)',
        'scope2_location_hint' => 'Basé sur le mix électrique du réseau',
        'scope2_market' => 'Scope 2 Market-based (tCO2e)',
        'scope2_market_hint' => 'Basé sur vos contrats d\'énergie',

        // Company info
        'company_info' => 'Informations entreprise',
        'company_info_desc' => 'Ces informations permettent de calculer votre intensité carbone.',
        'annual_revenue' => 'Chiffre d\'affaires annuel',
        'currency' => 'Devise',
        'employees_count' => 'Nombre d\'employés',

        // Verification
        'verification' => 'Vérification (optionnel)',
        'verification_standard' => 'Norme de vérification',
        'not_verified' => 'Non vérifié',
        'other' => 'Autre',
        'verifier' => 'Vérificateur',
        'verification_date' => 'Date de vérification',

        // Notes & Submit
        'notes' => 'Notes ou commentaires',
        'notes_placeholder' => 'Informations supplémentaires, méthodologie utilisée, hypothèses...',
        'confidentiality_notice' => 'Vos données seront traitées de manière confidentielle.',
        'submit' => 'Soumettre mes données',
        'submitting' => 'Envoi en cours...',

        // Success/Error
        'success_title' => 'Données envoyées avec succès !',
        'success_message' => 'Merci pour votre contribution. Vos données ont été transmises à :organization.',
        'error_occurred' => 'Une erreur est survenue.',
        'error_retry' => 'Une erreur est survenue. Veuillez réessayer.',

        // Status pages
        'not_found_title' => 'Invitation non trouvée',
        'not_found_message' => 'Ce lien d\'invitation n\'est pas valide ou a été supprimé.',
        'not_found_contact' => 'Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter l\'organisation qui vous a invité.',

        'expired_title' => 'Invitation expirée',
        'expired_message' => 'Cette invitation a expiré le :date.',
        'expired_contact' => 'Pour obtenir un nouveau lien, veuillez contacter :organization.',
        'contact_label' => 'Contact :',

        'completed_title' => 'Données déjà soumises',
        'completed_message' => 'Vous avez soumis vos données le :date.',
        'summary' => 'Récapitulatif',
        'year' => 'Année',
        'revenue' => 'Chiffre d\'affaires',
        'quality_score' => 'Score qualité',
        'modify_contact' => 'Pour modifier vos données, veuillez contacter :organization.',
    ],

];
