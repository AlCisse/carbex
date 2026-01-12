<?php

/**
 * Carbex - German Translations
 */

return [

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    'app_name' => 'Carbex',
    'tagline' => 'Automatische CO2-Bilanz für KMU',
    'welcome' => 'Willkommen bei Carbex',
    'dashboard' => 'Dashboard',
    'settings' => 'Einstellungen',
    'profile' => 'Profil',
    'logout' => 'Abmelden',
    'login' => 'Anmelden',
    'register' => 'Registrieren',
    'save' => 'Speichern',
    'cancel' => 'Abbrechen',
    'delete' => 'Löschen',
    'edit' => 'Bearbeiten',
    'create' => 'Erstellen',
    'back' => 'Zurück',
    'next' => 'Weiter',
    'previous' => 'Zurück',
    'search' => 'Suchen',
    'filter' => 'Filtern',
    'reset_filters' => 'Filter zurücksetzen',
    'export' => 'Exportieren',
    'import' => 'Importieren',
    'download' => 'Herunterladen',
    'upload' => 'Hochladen',

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [
        'loading' => 'Laden...',
        'saving' => 'Speichern...',
        'processing' => 'Verarbeitung...',
        'view_details' => 'Details anzeigen',
        'view_all' => 'Alle anzeigen',
        'select' => 'Auswählen',
        'inactive' => 'Inaktiv',
        'active' => 'Aktiv',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'delete' => 'Löschen',
        'edit' => 'Bearbeiten',
        'create' => 'Erstellen',
        'back' => 'Zurück',
        'next' => 'Weiter',
        'reset' => 'Zurücksetzen',
        'date' => 'Datum',
        'description' => 'Beschreibung',
        'quantity' => 'Menge',
        'unit' => 'Einheit',
        'amount_optional' => 'Betrag (optional)',
        'calculating' => 'Berechnung...',
        'actions' => 'Aktionen',
        'close' => 'Schließen',
        'confirm' => 'Bestätigen',
        'yes' => 'Ja',
        'no' => 'Nein',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home Page
    |--------------------------------------------------------------------------
    */

    'home' => [
        'title' => 'Carbex - CO2-Bilanz-Plattform für KMU',
        'meta_description' => 'Steuern Sie Ihren CO2-Fußabdruck und treffen Sie wirkungsvolle Entscheidungen. Die KI-Plattform, die CO2-Verpflichtungen in strategische Entscheidungen für KMU verwandelt.',
        'badge' => 'Carbon Intelligence for SMEs',
        'csrd_badge' => 'CSRD 2025 konform',

        // Navigation
        'nav' => [
            'features' => 'Funktionen',
            'pricing' => 'Preise',
            'resources' => 'Ressourcen',
            'login' => 'Anmelden',
            'start' => 'Loslegen',
        ],

        // Hero section
        'hero' => [
            'title_line1' => 'Steuern Sie Ihren',
            'title_line2' => 'CO2-Fußabdruck.',
            'subtitle' => 'Die KI-Plattform, die CO2-Verpflichtungen in strategische Entscheidungen für KMU verwandelt.',
            'cta_primary' => 'Kostenlos starten',
            'cta_secondary' => 'So funktioniert es',
            'no_commitment' => 'Keine Verpflichtung · 10 Min · Sichere Daten',
            'badges' => 'ADEME-Datenbank · GHG Protocol · CSRD Ready',
        ],

        // Dashboard preview
        'preview' => [
            'total_footprint' => 'Gesamtfußabdruck',
            'vs_previous_year' => 'vs. Vorjahr',
            'monthly_evolution' => 'Monatliche Entwicklung',
        ],

        // Features section
        'features' => [
            'title' => 'So funktioniert es',
            'subtitle' => '3 einfache Schritte zur Verwaltung Ihres CO2-Fußabdrucks',

            'step1' => [
                'title' => 'Automatisch messen',
                'description' => 'Importieren Sie Ihre PDF-Rechnungen, Buchhaltungsexporte oder Excel-Dateien. Unsere KI extrahiert und berechnet Ihre Emissionen nach GHG Protocol Standards.',
                'item1' => 'PDF, Excel, ERP Import',
                'item2' => '20.000+ ADEME-Faktoren',
                'item3' => 'Automatisch Scope 1, 2, 3',
            ],

            'step2' => [
                'title' => 'Mit KI verstehen',
                'description' => 'Stellen Sie Fragen in natürlicher Sprache. Unsere KI analysiert Ihre Daten und identifiziert Reduktionshebel.',
                'item1' => 'Datenanalyse',
                'item2' => 'Reduktionshebel',
                'item3' => 'CSRD/BEGES-Konformität',
                'ai_question' => 'Was sind meine Hauptemissionsquellen?',
                'ai_answer_title' => 'Ihre Top 3 Quellen:',
                'ai_answer1' => 'Eingekaufte Waren (42%)',
                'ai_answer2' => 'Reisen (28%)',
                'ai_answer3' => 'Strom (18%)',
            ],

            'step3' => [
                'title' => 'Effektiv reduzieren',
                'description' => 'Erhalten Sie personalisierte Empfehlungen mit CO₂-Auswirkung und ROI-Schätzungen.',
                'item1' => 'Maßnahmen nach Wirkung',
                'item2' => 'ROI und Einsparungen',
                'item3' => 'CSRD-Berichte',
                'action1_title' => 'Elektroflotte',
                'action1_impact' => '-180 tCO₂e',
                'action1_details' => 'ROI: 24 Monate · Einsparungen: 12k€/Jahr',
                'action2_title' => 'Grüne Energie',
                'action2_impact' => '-120 tCO₂e',
                'action2_details' => 'ROI: 6 Monate · Einsparungen: 3k€/Jahr',
            ],

            'upload' => [
                'file1_name' => 'stromrechnung-2024.pdf',
                'file1_category' => 'Scope 2 · Strom',
                'file1_status' => 'Verarbeitet',
                'file2_name' => 'buchhaltung-export.xlsx',
                'file2_category' => 'Scope 3 · Einkäufe',
                'file2_status' => 'In Bearbeitung',
            ],
        ],

        // Stats section
        'stats' => [
            'title' => 'Warum jetzt handeln',
            'subtitle' => 'CO2-Berichterstattung wird zum Wettbewerbsvorteil',
            'stat1_value' => '90%',
            'stat1_label' => 'der KMU-Emissionen stammen aus Scope 3',
            'stat2_value' => '67%',
            'stat2_label' => 'der Einkäufer bevorzugen verantwortungsvolle Unternehmen',
            'stat3_value' => '85%',
            'stat3_label' => 'der KMU sparen nach ihrer Bilanz Geld',
        ],

        // Pricing section
        'pricing' => [
            'title' => 'Einfache Preise',
            'subtitle' => 'Kostenlos starten',

            'free' => [
                'name' => 'Kostenlos',
                'price' => '0€',
                'period' => 'für immer',
                'feature1' => '5 Importe',
                'feature2' => '1 Bericht',
                'feature3' => 'Ohne KI',
                'cta' => 'Loslegen',
            ],

            'premium_monthly' => [
                'name' => 'Premium',
                'price' => '39€',
                'period' => 'pro Monat',
                'feature1' => 'KI (monatliches Kontingent)',
                'feature2' => 'Unbegrenzte Importe',
                'feature3' => '5 Benutzer',
                'cta' => 'Auswählen',
            ],

            'premium_annual' => [
                'name' => 'Premium',
                'price' => '400€',
                'period' => 'pro Jahr',
                'discount' => '-15%',
                'feature1' => 'Unbegrenzte KI',
                'feature2' => 'Unbegrenzte Importe',
                'feature3' => '5 Benutzer',
                'cta' => 'Auswählen',
            ],

            'enterprise' => [
                'name' => 'Unternehmen',
                'price' => '840€',
                'period' => 'pro Jahr',
                'old_price' => '1200€',
                'discount' => '-30%',
                'feature1' => 'Alles aus Premium +',
                'feature2' => 'Unbegrenzte Benutzer',
                'feature3' => 'API + Support',
                'cta' => 'Kontaktieren',
            ],
        ],

        // CTA section
        'cta' => [
            'title' => 'Bereit loszulegen?',
            'subtitle' => 'Starten Sie Ihre erste CO2-Bilanz in 10 Minuten.',
            'button' => 'Kostenlos starten',
            'note' => 'Keine Verpflichtung · Keine Kreditkarte',
        ],

        // Footer
        'footer' => [
            'tagline' => 'CO2-Bilanz für KMU',
            'product' => 'Produkt',
            'resources' => 'Ressourcen',
            'documentation' => 'Dokumentation',
            'csrd_guide' => 'CSRD-Leitfaden',
            'legal' => 'Rechtliches',
            'privacy' => 'Datenschutz',
            'terms' => 'AGB',
            'compliance' => 'ADEME · GHG Protocol · DSGVO',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Entry / Manual Entry
    |--------------------------------------------------------------------------
    */

    'data_entry' => [
        'title' => 'Manuelle Eingabe',
        'subtitle' => 'Erfassen Sie Aktivitäten, die nicht durch Banktransaktionen erfasst werden.',
        'success' => 'Aktivität erfolgreich erfasst!',
        'activity_type' => 'Aktivitätstyp',
        'energy' => 'Energie',
        'travel' => 'Reisen',
        'purchases' => 'Einkäufe',
        'waste' => 'Abfall',
        'freight' => 'Fracht',
        'site' => 'Standort',
        'select_site' => 'Standort auswählen...',
        'emission_category' => 'Emissionskategorie',
        'select_category' => 'Kategorie auswählen...',
        'description_placeholder' => 'Z.B.: Stromverbrauch Q1 2025, Geschäftsreise Berlin-München...',
        'origin' => 'Abfahrtsort',
        'destination' => 'Zielort',
        'origin_placeholder' => 'Z.B.: Berlin, BER',
        'destination_placeholder' => 'Z.B.: München, MUC',
        'travel_class' => 'Reiseklasse',
        'standard' => 'Standard',
        'economy' => 'Economy',
        'business' => 'Business',
        'first_class' => 'Erste Klasse',
        'passengers' => 'Anzahl Passagiere',
        'fuel_type' => 'Kraftstoff-/Energietyp',
        'not_specified' => 'Nicht angegeben',
        'grid_electricity' => 'Netzstrom',
        'renewable_electricity' => 'Ökostrom',
        'natural_gas' => 'Erdgas',
        'diesel' => 'Diesel',
        'petrol' => 'Benzin',
        'lpg' => 'Autogas',
        'heating_oil' => 'Heizöl',
        'calculate' => 'Emissionen berechnen',
        'calculation_result' => 'Ergebnis der Emissionsberechnung',
        'co2e_kg' => 'CO₂e (kg)',
        'co2e_tonnes' => 'CO₂e (Tonnen)',
        'scope' => 'Scope',
        'methodology' => 'Methodik',
        'emission_factor' => 'Emissionsfaktor:',
        'save_activity' => 'Aktivität speichern',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'login_title' => 'In Ihr Konto einloggen',
        'login_subtitle' => 'Verwalten Sie Ihren CO2-Fußabdruck',
        'login_button' => 'Anmelden',
        'email' => 'E-Mail-Adresse',
        'password' => 'Passwort',
        'remember_me' => 'Angemeldet bleiben',
        'forgot_password' => 'Passwort vergessen?',
        'no_account' => 'Noch kein Konto?',
        'register_link' => 'Konto erstellen',
        'register_title' => 'Erstellen Sie Ihr Konto',
        'register_subtitle' => 'Starten Sie Ihre CO2-Bilanz',
        'register_button' => 'Registrieren',
        'name' => 'Vollständiger Name',
        'confirm_password' => 'Passwort bestätigen',
        'already_have_account' => 'Bereits ein Konto?',
        'login_link' => 'Anmelden',
        'reset_password' => 'Passwort zurücksetzen',
        'send_reset_link' => 'Link senden',
        'reset_link_sent' => 'Ein Link zum Zurücksetzen wurde gesendet.',
        'step_account' => 'Konto',
        'step_organization' => 'Organisation',
        'organization_name' => 'Organisationsname',
        'country' => 'Land',
        'sector' => 'Branche',
        'select_sector' => 'Branche auswählen',
        'organization_size' => 'Unternehmensgröße',
        'select_size' => 'Größe auswählen',
        'employees' => 'Mitarbeiter',
        'accept_terms_html' => 'Ich akzeptiere die <a href="/terms" class="text-green-600 hover:underline">Nutzungsbedingungen</a>',
        'accept_privacy_html' => 'Ich akzeptiere die <a href="/privacy" class="text-green-600 hover:underline">Datenschutzrichtlinie</a>',
        'create_account' => 'Konto erstellen',
        'password_requirements' => 'Mindestens 8 Zeichen',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'dashboard' => 'Dashboard',
        'emissions' => 'Emissionen',
        'transactions' => 'Transaktionen',
        'banking' => 'Banken',
        'reports' => 'Berichte',
        'settings' => 'Einstellungen',
        'help' => 'Hilfe',
        'notifications' => 'Benachrichtigungen',
        'bookmarks' => 'Lesezeichen',
        'my_profile' => 'Mein Profil',
        'logout' => 'Abmelden',
    ],

    'navigation' => [
        'dashboard' => 'Dashboard',
        'emissions' => 'Emissionen',
        'transactions' => 'Transaktionen',
        'banking' => 'Banken',
        'reports' => 'Berichte',
        'settings' => 'Einstellungen',
        'profile' => 'Profil',
        'logout' => 'Abmelden',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title' => 'Dashboard',
        'add_emission' => 'Emission hinzufügen',
        'overview_for' => 'CO2-Fußabdruck Übersicht für :organization',
        'refresh_data' => 'Daten aktualisieren',
        'total_emissions' => 'Gesamtemissionen',
        'this_month' => 'Diesen Monat',
        'this_year' => 'Dieses Jahr',
        'vs_last_month' => 'vs. letzter Monat',
        'vs_last_year' => 'vs. letztes Jahr',
        'scope_breakdown' => 'Aufschlüsselung nach Scope',
        'category_breakdown' => 'Aufschlüsselung nach Kategorie',
        'trend' => 'Trend',
        'top_emitters' => 'Größte Emittenten',
        'recent_transactions' => 'Aktuelle Transaktionen',
        'pending_validation' => 'Ausstehende Validierung',
        'no_transactions' => 'Keine Transaktionen',
        'connect_bank_prompt' => 'Verbinden Sie Ihre Bankkonten, um Emissionen zu verfolgen.',
        'progress_title' => 'Bilanzfortschritt',
        'completed' => 'abgeschlossen',
        'of' => 'von',
        'categories' => 'Kategorien',
        'legend_completed' => 'Abgeschlossen',
        'legend_todo' => 'Zu erledigen',
        'legend_na' => 'Nicht zutreffend',
        'equivalents_title' => 'CO2-Äquivalente',
        'equivalents_subtitle' => 'Ihre Emissionen entsprechen...',
        'no_emissions' => 'Keine Emissionen für diesen Zeitraum erfasst.',
        'direct_emissions' => 'Direkte Emissionen',
        'indirect_energy' => 'Indirekte Energie',
        'value_chain' => 'Wertschöpfungskette',
        'transaction_coverage' => 'Transaktionsabdeckung',
        'categorized_of_total' => ':categorized von :total kategorisiert',
        'pending_count' => ':count ausstehend',
        'emissions_by_scope' => 'Emissionen nach Scope',
        'total' => 'Gesamt',
        'records' => 'Einträge',
        'no_data' => 'Keine Daten',
        'scope1_desc' => 'Direkte Emissionen aus eigenen Quellen',
        'scope2_desc' => 'Indirekte Emissionen aus eingekaufter Energie',
        'scope3_desc' => 'Alle anderen indirekten Emissionen',
        'emission_trends' => 'Emissionstrends',
        'no_trend_data' => 'Keine Trenddaten verfügbar',
        'trend_data_hint' => 'Daten erscheinen hier nach Verarbeitung der Transaktionen.',
        'top_categories' => 'Top-Emissionskategorien',
        'treemap' => 'Treemap',
        'bar_chart' => 'Balkendiagramm',
        'no_category_data' => 'Keine Kategoriedaten',
        'category_data_hint' => 'Kategorien erscheinen nach Kategorisierung der Transaktionen.',
        'transactions' => 'Transaktionen',
        'emission_intensity' => 'Emissionsintensität',
        'intensity_help' => 'Intensitätsmetriken helfen beim Vergleich der Emissionen',
        'per_employee' => 'Pro Mitarbeiter',
        'per_revenue' => 'Pro Umsatz',
        'per_area' => 'Pro Fläche',
        'emissions_by_site' => 'Emissionen nach Standort',
        'no_site_data' => 'Keine Standortdaten',
        'filter_by_site' => 'Nach Standort filtern',
        'all_sites' => 'Alle Standorte',
        'custom_range' => 'Benutzerdefinierter Zeitraum',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'quarters' => 'Quartale',
        'apply' => 'Anwenden',
        'add_sites_prompt' => 'Fügen Sie Standorte hinzu, um Vergleiche zu sehen.',
        'emissions' => 'Emissionen',
        'per_1000_eur' => 'Pro 1.000 € Ausgaben',
        'emission_intensity_per_1000' => 'CO2-Intensität pro 1.000 €',
        'total_spend' => 'Gesamtausgaben',
        'employees' => 'Mitarbeiter',
        'industry_benchmarks' => 'Branchenbenchmarks (Durchschnitt)',
        'sme_services' => 'KMU (Dienstleistungen)',
        'sme_manufacturing' => 'KMU (Produktion)',
        'sme_retail' => 'KMU (Handel)',
        'sme_it' => 'KMU (IT)',
        'benchmarks_source' => 'Quelle: ADEME/UBA Durchschnittswerte für europäische KMU',
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
        'paris_ny' => 'Paris-New York Hin- und Rückflüge',
        'round_trips' => 'Reisen',
        'earth_tours' => 'Erdumrundungen mit dem Auto',
        'tours' => 'Umrundungen',
        'hotel_nights' => 'Hotelübernachtungen',
        'nights' => 'Nächte',
        'car_km' => 'Kilometer mit dem Auto',
        'french_person' => 'Jährlicher Fußabdruck eines Deutschen',
        'years' => 'Jahre',
        'trees_needed' => 'Bäume zur Kompensation benötigt',
        'trees' => 'Bäume',
        'streaming' => 'Stunden Video-Streaming',
        'hours' => 'Stunden',
    ],

    /*
    |--------------------------------------------------------------------------
    | Evaluation Progress
    |--------------------------------------------------------------------------
    */

    'evaluation' => [
        'title' => 'Bewertungsschritte',
        'completed' => 'abgeschlossen',
        'setup' => 'Einrichtung',
        'setup_organization' => 'Organisationseinstellungen',
        'setup_organization_desc' => 'Name, Branche, Land',
        'setup_sites' => 'Standorte hinzufügen',
        'setup_sites_desc' => 'Büros, Lager, Fabriken...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Training Section
    |--------------------------------------------------------------------------
    */

    'training' => [
        'title' => 'Lernen',
        'subtitle' => 'Videos und Ressourcen zur CO2-Bilanzierung',
        'video1_title' => 'Was ist eine CO2-Bilanz?',
        'video1_desc' => 'Grundlagen der Treibhausgasemissionen verstehen',
        'video2_title' => 'Konto einrichten',
        'video2_desc' => 'Schnellstart-Anleitung mit Carbex',
        'video3_title' => 'Reduktionsziele definieren',
        'video3_desc' => 'SBTi-Strategien und Best Practices',
        'coming_soon' => 'Demnächst verfügbar',
        'need_help' => 'Brauchen Sie Hilfe?',
        'contact_support' => 'Support kontaktieren',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    'scopes' => [
        'scope1_name' => 'Direkte Emissionen',
        'scope2_name' => 'Indirekte energiebezogene Emissionen',
        'scope3_name' => 'Sonstige indirekte Emissionen',
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
        'title' => 'Emissionen',
        'total' => 'Gesamtemissionen',
        'scope_1' => 'Scope 1 - Direkte Emissionen',
        'scope_2' => 'Scope 2 - Energie',
        'scope_3' => 'Scope 3 - Indirekte Emissionen',
        'unit' => 'tCO2e',
        'unit_kg' => 'kgCO2e',
        'factor' => 'Emissionsfaktor',
        'source' => 'Quelle',
        'confidence' => 'Vertrauen',
        'validated' => 'Validiert',
        'pending' => 'Ausstehend',
        'help_category' => 'Wie füllt man diese Kategorie aus?',
        'mark_completed' => 'Als abgeschlossen markieren',
        'category_completed' => 'Kategorie als abgeschlossen markiert.',
        'sources_title' => 'Emissionsquellen',
        'sources_subtitle' => 'Emissionsquellen für diese Kategorie hinzufügen',
        'add_source' => 'Quelle hinzufügen',
        'edit_source' => 'Quelle bearbeiten',
        'new_source' => 'Neue Emissionsquelle',
        'no_sources' => 'Keine Emissionsquellen',
        'no_sources_hint' => 'Beginnen Sie mit dem Hinzufügen einer Emissionsquelle',
        'source_name' => 'Quellenname',
        'source_name_placeholder' => 'Z.B.: Strom Büro Berlin',
        'select_factor' => 'Emissionsfaktor auswählen',
        'quantity' => 'Menge',
        'calculated_emissions' => 'Berechnete Emissionen',
        'confirm_delete' => 'Möchten Sie diese Quelle wirklich löschen?',
        'validation' => [
            'name_required' => 'Quellenname ist erforderlich.',
            'quantity_required' => 'Menge ist erforderlich.',
            'factor_required' => 'Bitte wählen Sie einen Emissionsfaktor.',
        ],
        'categories' => [
            'fuel_gasoline' => 'Benzin',
            'fuel_diesel' => 'Diesel',
            'natural_gas' => 'Erdgas',
            'electricity' => 'Strom',
            'business_travel_air' => 'Flugreisen',
            'business_travel_rail' => 'Bahnreisen',
            'business_travel_hotel' => 'Hotels',
            'purchased_goods' => 'Einkäufe',
            'cloud_services' => 'Cloud-Dienste',
            'restaurant_meals' => 'Gastronomie',
        ],
        'factors' => [
            'title' => 'Emissionsfaktor auswählen',
            'subtitle' => 'Über :count Emissionsfaktoren erkunden',
            'search_placeholder' => 'Emissionsfaktor suchen...',
            'reset' => 'Zurücksetzen',
            'no_results' => 'Keine Faktoren gefunden',
            'no_results_hint' => 'Passen Sie Ihre Suchkriterien an.',
            'showing' => 'Anzeige',
            'to' => 'bis',
            'of' => 'von',
            'results' => 'Ergebnisse',
            'kg_co2e_per' => 'kgCO2e/',
            'create_custom' => 'Eigenen Faktor erstellen',
            'tabs' => [
                'all' => 'Alle',
                'ademe' => 'Base Carbone® ADEME',
                'uba' => 'UBA (Deutschland)',
                'ghg' => 'GHG Protocol',
                'custom' => 'Primärdaten',
            ],
            'filters' => [
                'all_countries' => 'Alle Länder',
                'all_units' => 'Alle Einheiten',
            ],
            'countries' => [
                'fr' => 'Frankreich',
                'de' => 'Deutschland',
                'eu' => 'Europäische Union',
                'gb' => 'Vereinigtes Königreich',
                'us' => 'Vereinigte Staaten',
            ],
            'units' => [
                'liter' => 'Liter',
                'tonne' => 'Tonne',
            ],
            'validation' => [
                'name_required' => 'Faktorname ist erforderlich.',
                'unit_required' => 'Einheit ist erforderlich.',
                'value_required' => 'Faktorwert ist erforderlich.',
                'value_numeric' => 'Wert muss eine Zahl sein.',
            ],
            'custom' => [
                'title' => 'Eigenen Faktor erstellen',
                'subtitle' => 'Definieren Sie Ihren eigenen Emissionsfaktor.',
                'name' => 'Faktorname',
                'name_placeholder' => 'Z.B.: Solarstrom vor Ort',
                'description' => 'Beschreibung (optional)',
                'description_placeholder' => 'Beschreiben Sie Quelle und Methodik...',
                'unit' => 'Einheit',
                'value' => 'Wert (kgCO2e)',
                'info' => 'Dieser Faktor wird Ihrer Organisation zugeordnet.',
                'create' => 'Faktor erstellen',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Banking
    |--------------------------------------------------------------------------
    */

    'banking' => [
        'title' => 'Bankverbindungen',
        'connect' => 'Bank verbinden',
        'disconnect' => 'Trennen',
        'refresh' => 'Aktualisieren',
        'last_sync' => 'Letzte Synchronisierung',
        'status' => [
            'connected' => 'Verbunden',
            'disconnected' => 'Getrennt',
            'error' => 'Fehler',
            'syncing' => 'Synchronisiere...',
        ],
        'accounts' => 'Konten',
        'transactions' => 'Transaktionen',
        'import_csv' => 'CSV-Datei importieren',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transaktionen',
        'total_transactions' => 'Gesamttransaktionen',
        'pending_categorization' => 'Kategorisierung ausstehend',
        'needs_review' => 'Prüfung erforderlich',
        'validated' => 'Validiert',
        'search_placeholder' => 'Transaktionen suchen...',
        'all_categories' => 'Alle Kategorien',
        'all_scopes' => 'Alle Scopes',
        'selected' => 'ausgewählt',
        'validate_all' => 'Alle validieren',
        'categorize_as' => 'Kategorisieren als...',
        'date' => 'Datum',
        'description' => 'Beschreibung',
        'category' => 'Kategorie',
        'amount' => 'Betrag',
        'emissions' => 'Emissionen',
        'actions' => 'Aktionen',
        'select' => 'Auswählen...',
        'add_category' => '+ Kategorie hinzufügen',
        'low_confidence' => 'Geringe Konfidenz',
        'validate' => 'Validieren',
        'create_rule' => 'Regel für diesen Händler erstellen',
        'exclude' => 'Ausschließen',
        'exclude_confirm' => 'Diese Transaktion von den Emissionen ausschließen?',
        'include' => 'Einschließen',
        'no_transactions' => 'Keine Transaktionen gefunden.',
        'status' => 'Status',
        'recategorize' => 'Neu kategorisieren',
        'bulk_validate' => 'Auswahl validieren',
        'filters' => [
            'all' => 'Alle',
            'pending' => 'Ausstehend',
            'validated' => 'Validiert',
            'excluded' => 'Ausgeschlossen',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'title' => 'Berichte & Exporte',
        'subtitle' => 'Erstellen und laden Sie Ihre CO2-Bilanzen herunter',
        'generate' => 'Erstellen',
        'generate_report' => 'Bericht erstellen',
        'generate_confirm' => 'Bericht für Jahr :year erstellen.',
        'download' => 'Herunterladen',
        'download_pdf' => 'PDF herunterladen',
        'download_excel' => 'Excel herunterladen',
        'select_year' => 'Jahr auswählen',
        'type' => 'Berichtstyp',
        'format' => 'Format',
        'period' => 'Zeitraum',
        'from' => 'Von',
        'to' => 'Bis',
        'carbon_footprint' => 'Vollständige CO2-Bilanz',
        'carbon_footprint_desc' => 'Vollständiger Word-Bericht mit Methodik und Ergebnissen.',
        'ademe' => 'ADEME-Erklärung',
        'ademe_desc' => 'Excel-Format kompatibel mit bilans-ges.ademe.fr.',
        'ghg' => 'GHG Protocol Bericht',
        'ghg_desc' => 'Excel-Export im WBCSD/WRI Format.',
        'types' => [
            'monthly' => 'Monatsbericht',
            'quarterly' => 'Quartalsbericht',
            'annual' => 'Jahresbericht',
            'beges' => 'Vereinfachte CO2-Bilanz',
            'custom' => 'Benutzerdefinierter Bericht',
        ],
        'history' => 'Berichtsverlauf',
        'no_reports' => 'Keine Berichte erstellt',
        'no_reports_desc' => 'Wählen Sie einen Berichtstyp aus.',
        'downloads' => 'Downloads',
        'confirm_delete' => 'Diesen Bericht löschen?',
        'status_completed' => 'Fertig',
        'status_processing' => 'In Bearbeitung',
        'status_pending' => 'Ausstehend',
        'status_failed' => 'Fehlgeschlagen',
        'include_details' => 'Details einbeziehen',
        'generating' => 'Wird erstellt...',
        'generation_started' => 'Berichterstellung gestartet.',
        'generation_failed' => 'Fehler bei der Berichterstellung.',
        'pending_generation' => 'Erstellung ausstehend',
        'ready' => 'Bericht fertig',
        'ghg_inventory' => 'THG-Inventar',
        'scope_breakdown' => 'Aufschlüsselung nach Scope',
        'category_analysis' => 'Kategorieanalyse',
        'period_comparison' => 'Periodenvergleich',
        'pdf' => [
            'carbon_footprint_report' => 'CO2-Bilanz',
            'executive_summary' => 'Zusammenfassung',
            'emissions_by_scope' => 'Emissionen nach Scope',
            'top_categories' => 'Top-Emissionskategorien',
            'emissions_by_site' => 'Emissionen nach Standort',
            'scope' => 'Scope',
            'description' => 'Beschreibung',
            'emissions' => 'Emissionen',
            'category' => 'Kategorie',
            'records' => 'Einträge',
            'site' => 'Standort',
            'location' => 'Ort',
            'methodology' => 'Methodik',
            'standard' => 'Standard',
            'emission_factors' => 'Emissionsfaktoren',
            'note' => 'Hinweis',
            'report_generated_on' => 'Bericht erstellt am',
            'compared_to_previous' => 'Im Vergleich zum Vorjahr',
            'reduction' => 'Reduktion',
            'increase' => 'Zunahme',
            'stable' => 'Stabil',
            'direct_emissions' => 'Direkte Emissionen',
            'indirect_energy_emissions' => 'Indirekte energiebezogene Emissionen',
            'value_chain_emissions' => 'Emissionen der Wertschöpfungskette',
            'calculation_standards' => 'Berechnungsstandards',
            'primary_standard' => 'Hauptstandard',
            'consolidation_approach' => 'Konsolidierungsansatz',
            'operational_control' => 'Betriebliche Kontrolle',
            'base_year' => 'Basisjahr',
            'reporting_period' => 'Berichtszeitraum',
            'emission_factor_sources' => 'Emissionsfaktor-Quellen',
            'source' => 'Quelle',
            'version' => 'Version',
            'applied_to' => 'Angewendet auf',
            'all_french_operations' => 'Alle französischen Betriebe',
            'other_countries' => 'Andere Länder',
            'scope_definitions' => 'Scope-Definitionen',
            'scope1_emissions' => 'Direkte Emissionen',
            'energy_indirect_emissions' => 'Indirekte energiebezogene Emissionen',
            'scope3_emissions' => 'Emissionen der Wertschöpfungskette',
            'process_emissions' => 'Prozessemissionen',
            'purchased_electricity' => 'Eingekaufter Strom',
            'data_quality_assessment' => 'Datenqualitätsbewertung',
            'data_type' => 'Datentyp',
            'quality_score' => 'Qualitätsbewertung',
            'energy_consumption' => 'Energieverbrauch',
            'business_travel' => 'Geschäftsreisen',
            'bank_transactions' => 'Banktransaktionen',
            'purchased_goods' => 'Eingekaufte Waren',
            'estimated_uncertainty' => 'Geschätzte Gesamtunsicherheit',
            'estimated_impact' => 'Geschätzte Auswirkung',
            'verification_statement' => 'Verifizierungserklärung',
            'verified_by' => 'Dieses THG-Inventar wurde verifiziert von',
            'verification_date' => 'Verifizierungsdatum',
            'detailed_emissions_by_scope' => 'Detaillierte Emissionen nach Scope',
            'calculation_method' => 'Berechnungsmethode',
            'electricity_mix' => 'Strommix',
            'grid_emission_factor' => 'Netz-Emissionsfaktor',
            'currently_tracking' => 'Derzeit erfasst',
            'categories_not_relevant' => 'Nicht relevante Kategorien',
            'summary_by_scope' => 'Zusammenfassung nach Scope',
            'share_of_total' => 'Anteil am Gesamten',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */

    'organization' => [
        'title' => 'Organisation',
        'name' => 'Firmenname',
        'legal_name' => 'Rechtlicher Name',
        'siret' => 'Handelsregisternummer',
        'registration_number' => 'Registrierungsnummer',
        'vat_number' => 'USt-IdNr.',
        'address' => 'Adresse',
        'address_line_2' => 'Adresszusatz',
        'city' => 'Stadt',
        'postal_code' => 'Postleitzahl',
        'country' => 'Land',
        'phone' => 'Telefon',
        'website' => 'Webseite',
        'sector' => 'Branche',
        'size' => 'Unternehmensgröße',
        'employees' => 'Mitarbeiterzahl',
        'fiscal_year' => 'Geschäftsjahr',
        'fiscal_year_start' => 'Geschäftsjahresbeginn',
        'default_currency' => 'Standardwährung',
        'timezone' => 'Zeitzone',
        'vat_rate' => 'MwSt-Satz',
        'sites' => 'Standorte',
        'add_site' => 'Standort hinzufügen',
        'general_info' => 'Allgemeine Informationen',
        'general_info_desc' => 'Grundlegende Informationen über Ihre Organisation',
        'legal_info' => 'Rechtliche Informationen',
        'legal_info_desc' => 'Offizielle Identifikationsnummern',
        'contact_info' => 'Kontaktinformationen',
        'fiscal_settings' => 'Steuerliche Einstellungen',
        'fiscal_settings_desc' => 'Geschäftsjahr und Referenzwährung',
        'country_config' => 'Länderkonfiguration',
        'country_config_desc' => 'Einstellungen basierend auf Ihrem Unternehmensland',
        'currencies' => [
            'eur' => 'EUR - Euro',
            'chf' => 'CHF - Schweizer Franken',
            'gbp' => 'GBP - Britisches Pfund',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription
    |--------------------------------------------------------------------------
    */

    'subscription' => [
        'title' => 'Abonnement',
        'current_plan' => 'Aktueller Plan',
        'upgrade' => 'Upgrade',
        'downgrade' => 'Downgrade',
        'cancel' => 'Abonnement kündigen',
        'trial' => 'Testphase',
        'trial_ends' => 'Testphase endet',
        'plans' => [
            'starter' => 'Starter',
            'business' => 'Business',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
        ],
        'features' => [
            'bank_connections' => 'Bankverbindungen',
            'transactions_month' => 'Transaktionen/Monat',
            'users' => 'Benutzer',
            'reports' => 'Berichte',
            'api_access' => 'API-Zugang',
            'support' => 'Support',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */

    'billing' => [
        'title' => 'Preise & Abonnement',
        'subtitle' => 'Wählen Sie den passenden Plan',
        'current_plan' => 'Aktueller Plan',
        'monthly' => 'Monatlich',
        'annual' => 'Jährlich',
        'per_month' => 'Monat zzgl. MwSt',
        'per_year' => 'Jahr zzgl. MwSt',
        'year' => 'Jahr',
        'currency' => '€',
        'users' => 'Benutzer',
        'popular' => 'Beliebt',
        'save' => 'Sparen Sie',
        'start_trial' => 'Kostenlos testen',
        'choose' => 'Diesen Plan wählen',
        'trial' => 'Testversion',
        'trial_ends' => 'Testphase endet',
        'trial_started' => 'Ihre 15-tägige kostenlose Testphase hat begonnen!',
        'free_trial' => 'Kostenlose Testversion',
        'days_remaining' => 'Tage verbleibend',
        'plan_premium' => 'Premium-Plan',
        'plan_advanced' => 'Erweiterter Plan',
        'checkout_title' => 'Abonnement abschließen',
        'billing_period' => 'Abrechnungszeitraum',
        'promo_code' => 'Promo-Code',
        'promo_placeholder' => 'Code eingeben',
        'apply' => 'Anwenden',
        'invalid_promo_code' => 'Ungültiger Promo-Code',
        'discount' => 'Rabatt',
        'annual_savings' => 'Jährliche Ersparnis',
        'total' => 'Gesamt',
        'checkout_button' => 'Zur Zahlung',
        'processing' => 'Verarbeitung...',
        'cancel' => 'Abbrechen',
        'secure_payment' => 'Sichere Zahlung über Stripe',
        'checkout_error' => 'Fehler beim Erstellen der Zahlung.',
        'plans' => [
            'free' => ['name' => 'Kostenlos', 'description' => 'Zum Kennenlernen'],
            'premium' => ['name' => 'Premium', 'description' => 'Für KMU'],
            'advanced' => ['name' => 'Erweitert', 'description' => 'Für große Unternehmen'],
        ],
        'manage' => 'Abonnement verwalten',
        'billing_portal' => 'Abrechnungsportal',
        'update_payment' => 'Zahlungsmethode aktualisieren',
        'cancel_subscription' => 'Abonnement kündigen',
        'resume_subscription' => 'Abonnement fortsetzen',
        'subscription_cancelled' => 'Ihr Abonnement wurde gekündigt.',
        'subscription_resumed' => 'Ihr Abonnement wurde reaktiviert.',
        'next_billing_date' => 'Nächste Abrechnung',
        'payment_method' => 'Zahlungsmethode',
        'invoices' => 'Rechnungen',
        'no_invoices' => 'Keine Rechnungen',
        'download_invoice' => 'Herunterladen',
        'subscription_billing' => 'Abonnement & Abrechnung',
        'manage_desc' => 'Verwalten Sie Ihr Abonnement und Ihre Zahlungsmethoden.',
        'subscription_activated' => 'Ihr Abonnement wurde aktiviert!',
        'checkout_canceled' => 'Zahlung wurde abgebrochen.',
        'on_trial' => 'Sie sind in der Testphase',
        'days_remaining' => 'Tage verbleibend',
        'trial_ends_on' => 'Testphase endet am :date',
        'upgrade_now' => 'Jetzt upgraden',
        'subscription_ends_on' => 'Ihr Abonnement endet am :date',
        'lose_premium' => 'Sie verlieren den Zugang zu Premium-Funktionen.',
        'no_subscription' => 'Kein aktives Abonnement',
        'start_trial_days' => ':days-tägige kostenlose Testphase starten',
        'usage' => 'Nutzung',
        'bank_connections' => 'Bankverbindungen',
        'sites' => 'Standorte',
        'monthly_reports' => 'Monatliche Berichte',
        'unlimited' => 'Unbegrenzt',
        'available_plans' => 'Verfügbare Pläne',
        'yearly' => 'Jährlich',
        'quick_actions' => 'Schnellaktionen',
        'update_payment_method' => 'Zahlungsmethode aktualisieren',
        'update_billing_address' => 'Rechnungsadresse aktualisieren',
        'recent_invoices' => 'Aktuelle Rechnungen',
        'no_invoices_yet' => 'Noch keine Rechnungen',
        'paid' => 'Bezahlt',
        'failed' => 'Fehlgeschlagen',
        'need_help' => 'Brauchen Sie Hilfe?',
        'help_choose_plan' => 'Unser Team hilft Ihnen gerne.',
        'contact_support' => 'Support kontaktieren',
        'cancel_subscription_title' => 'Abonnement kündigen?',
        'cancel_subscription_desc' => 'Abonnement bleibt bis :date aktiv.',
        'why_canceling' => 'Warum kündigen Sie? (optional)',
        'feedback_placeholder' => 'Ihr Feedback hilft uns...',
        'keep_subscription' => 'Abonnement behalten',
        'upgrade_to' => 'Upgrade auf :plan',
        'redirect_to_payment' => 'Sie werden zur sicheren Zahlung weitergeleitet.',
        'plan' => 'Plan',
        'continue_to_payment' => 'Weiter zur Zahlung',
        'unlimited_bank_connections' => 'Unbegrenzte Bankverbindungen',
        'unlimited_users' => 'Unbegrenzte Benutzer',
        'api_access' => 'API-Zugang',
        'upgrade' => 'Upgrade',
        'downgrade' => 'Downgrade',
        'manage_billing' => 'Abrechnung verwalten',
        'next_billing' => 'Nächste Abrechnung: :date',
        'month' => 'Monat',
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors & Messages
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'saved' => 'Änderungen gespeichert.',
        'created' => 'Element erfolgreich erstellt.',
        'updated' => 'Element erfolgreich aktualisiert.',
        'deleted' => 'Element gelöscht.',
        'error' => 'Ein Fehler ist aufgetreten.',
        'not_found' => 'Element nicht gefunden.',
        'unauthorized' => 'Aktion nicht autorisiert.',
        'validation_required' => 'Bitte validieren Sie die ausstehenden Transaktionen.',
        'sync_started' => 'Synchronisierung gestartet.',
        'sync_completed' => 'Synchronisierung abgeschlossen.',
        'report_generating' => 'Ihr Bericht wird erstellt.',
        'report_ready' => 'Ihr Bericht ist fertig.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assessments (Bilanzen)
    |--------------------------------------------------------------------------
    */

    'assessments' => [
        'title' => 'Meine CO2-Bilanzen',
        'subtitle' => 'Verwalten Sie Ihre jährlichen CO2-Bilanzen',
        'new' => 'Neue Bilanz starten',
        'edit' => 'Bilanz bearbeiten',
        'year' => 'Jahr',
        'year_label' => 'Bilanz :year',
        'revenue' => 'Umsatz',
        'employees' => 'Mitarbeiter',
        'status' => 'Status',
        'progress' => 'Fortschritt',
        'status_draft' => 'Entwurf',
        'status_active' => 'Aktiv',
        'status_completed' => 'Abgeschlossen',
        'activate' => 'Bilanz aktivieren',
        'activated' => 'Bilanz erfolgreich aktiviert.',
        'empty' => 'Noch keine Bilanzen',
        'empty_short' => 'Keine Bilanzen',
        'none' => 'Keine Bilanzen',
        'create_first' => 'Erste Bilanz erstellen',
        'confirm_delete' => 'Diese Bilanz löschen?',
        'manage' => 'Bilanzen verwalten',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions (Übergangsplan)
    |--------------------------------------------------------------------------
    */

    'actions' => [
        'title' => 'Übergangsplan',
        'subtitle' => 'Verwalten Sie Ihre CO2-Reduktionsmaßnahmen',
        'new' => 'Neue Maßnahme',
        'edit' => 'Maßnahme bearbeiten',
        'filter_all' => 'Alle',
        'status' => ['todo' => 'Zu erledigen', 'in_progress' => 'In Bearbeitung', 'completed' => 'Abgeschlossen'],
        'difficulty' => ['easy' => 'Einfach', 'medium' => 'Mittel', 'hard' => 'Schwer'],
        'form' => [
            'title' => 'Titel',
            'title_placeholder' => 'Z.B.: Flotte durch Elektrofahrzeuge ersetzen',
            'description' => 'Beschreibung',
            'description_placeholder' => 'Beschreiben Sie die Maßnahme...',
            'due_date' => 'Fälligkeitsdatum',
            'category' => 'Emissionskategorie',
            'category_none' => '-- Keine Kategorie --',
            'status' => 'Status',
            'estimated_cost' => 'Geschätzte Kosten',
            'co2_reduction' => 'Geschätzte CO2-Reduktion',
            'difficulty' => 'Schwierigkeitsgrad',
        ],
        'priority' => 'Priorität',
        'assigned_to' => 'Zugewiesen an',
        'empty' => 'Noch keine Maßnahmen',
        'empty_description' => 'Erstellen Sie eine Reduktionsmaßnahme',
        'create_first' => 'Erste Maßnahme erstellen',
        'confirm_delete' => 'Diese Maßnahme löschen?',
        'status_updated' => 'Maßnahmenstatus aktualisiert.',
        'overdue' => 'Überfällig',
        'start' => 'Starten',
        'complete' => 'Abschließen',
        'reopen' => 'Wiedereröffnen',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reduction Targets (Trajektorie)
    |--------------------------------------------------------------------------
    */

    'targets' => [
        'title' => 'Reduktionspfad',
        'subtitle' => 'Definieren Sie Ihre SBTi-konformen Reduktionsziele',
        'trajectory' => 'Trajektorie bearbeiten',
        'new' => 'Neues Ziel',
        'edit' => 'Ziel bearbeiten',
        'baseline_year' => 'Basisjahr',
        'target_year' => 'Zieljahr',
        'scope1_reduction' => 'Scope 1 Reduktion',
        'scope2_reduction' => 'Scope 2 Reduktion',
        'scope3_reduction' => 'Scope 3 Reduktion',
        'sbti_title' => 'Science Based Targets Initiative (SBTi)',
        'sbti_description' => 'Zur Einhaltung des Pariser Abkommens (1,5°C) müssen Unternehmen ihre Emissionen reduzieren um:',
        'sbti_scope12_rate' => '4,2% pro Jahr',
        'sbti_scope12_label' => 'für Scope 1 und 2 Emissionen',
        'sbti_scope3_rate' => '2,5% pro Jahr',
        'sbti_scope3_label' => 'für Scope 3 Emissionen',
        'sbti_aligned' => 'SBTi-konform',
        'sbti_not_aligned' => 'Nicht SBTi-konform',
        'sbti_info' => 'SBTi empfiehlt 4,2% jährliche Reduktion für Scope 1&2, 2,5% für Scope 3.',
        'apply_sbti' => 'SBTi-Ziele anwenden',
        'horizon' => ':years Jahre Horizont',
        'per_year' => 'Jahr',
        'notes' => 'Notizen',
        'notes_placeholder' => 'Zusätzliche Notizen...',
        'empty' => 'Keine Ziele definiert',
        'empty_description' => 'Definieren Sie Ihren Reduktionspfad',
        'create_first' => 'Meinen Pfad definieren',
        'confirm_delete' => 'Dieses Ziel löschen?',
        'annual_rate' => 'Jährliche Rate',
        'compliant' => 'Konform',
        'not_compliant' => 'Nicht konform',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trajectory Chart
    |--------------------------------------------------------------------------
    */

    'trajectory' => [
        'chart_title' => 'Emissionspfad',
        'chart_subtitle' => 'Tatsächliche Emissionen vs. Reduktionsziele',
        'select_target' => 'Ziel auswählen',
        'actual_emissions' => 'Tatsächliche Emissionen',
        'target_trajectory' => 'Zielpfad',
        'target' => 'Ziel',
        'axis_years' => 'Jahre',
        'axis_emissions' => 'Emissionen (tCO₂e)',
        'baseline' => 'Basis',
        'reduction' => 'Zielreduktion',
        'status' => 'Status',
        'on_track' => 'Auf Kurs',
        'off_track' => 'Abweichung',
        'no_data' => 'Keine Daten',
        'today' => 'Heute',
        'years_left' => ':years Jahre verbleibend',
        'empty' => 'Kein Pfad definiert',
        'empty_description' => 'Definieren Sie zuerst Ihre Reduktionsziele.',
        'create_target' => 'Meine Ziele definieren',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Chat
    |--------------------------------------------------------------------------
    */

    'support' => [
        'title' => 'Carbex Support',
        'online' => 'Online',
        'offline' => 'Offline',
        'clear_chat' => 'Gespräch löschen',
        'start_conversation' => 'Gespräch starten...',
        'quick_help' => 'Schnelle Hilfe benötigt?',
        'message_placeholder' => 'Nachricht eingeben...',
        'welcome_message' => 'Hallo! Wie kann ich Ihnen heute helfen?',
        'response_greeting' => 'Hallo! Wie kann ich helfen?',
        'response_thanks' => 'Gern geschehen!',
        'response_pricing' => 'Wir bieten 3 Pläne: Kostenlos (15-Tage-Test), Premium (400€/Jahr) und Erweitert (1.200€/Jahr).',
        'response_import' => 'Sie können Daten über Bankverbindung, CSV-Datei oder manuelle Eingabe importieren.',
        'response_report' => 'Gehen Sie zu Berichte & Exporte, um Berichte zu erstellen.',
        'response_emissions' => 'Die Emissionserfassung erfolgt nach Kategorien (Scope 1, 2 und 3).',
        'response_support' => 'Ich verbinde Sie mit unserem Team.',
        'default_response' => 'Für persönliche Unterstützung kontaktieren Sie unser Support-Team.',
        'contact_form_title' => 'Support kontaktieren',
        'your_name' => 'Ihr Name',
        'name_placeholder' => 'Max Mustermann',
        'your_email' => 'Ihre E-Mail',
        'email_placeholder' => 'max@firma.de',
        'submit_request' => 'Anfrage senden',
        'contact_submitted' => 'Danke :name! Wir antworten an :email innerhalb von 24 Stunden.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Import Wizard
    |--------------------------------------------------------------------------
    */

    'import' => [
        'upload' => 'Hochladen',
        'map_columns' => 'Spalten zuordnen',
        'validate' => 'Validieren',
        'import' => 'Importieren',
        'upload_data_file' => 'Datendatei hochladen',
        'import_desc' => 'Importieren Sie Transaktionen oder Aktivitäten aus CSV-, Excel- oder FEC-Dateien',
        'import_type' => 'Importtyp',
        'bank_transactions' => 'Banktransaktionen',
        'csv_export' => 'CSV-Export von Ihrer Bank',
        'activities' => 'Aktivitäten',
        'activities_desc' => 'Energie, Reisen, Einkäufe...',
        'fec_france' => 'FEC (Frankreich)',
        'fec_desc' => 'Französischer Buchhaltungsexport',
        'target_site' => 'Zielstandort',
        'select_site' => 'Standort auswählen...',
        'data_file' => 'Datendatei',
        'drag_drop' => 'Datei hierher ziehen oder',
        'browse' => 'durchsuchen',
        'file_types' => 'CSV, Excel (.xlsx, .xls) bis 10MB',
        'uploading' => 'Hochladen...',
        'analyze_file' => 'Datei analysieren',
        'analyzing' => 'Analyse läuft...',
        'map_columns_title' => 'Spalten zuordnen',
        'map_columns_desc' => 'Ordnen Sie Ihre Dateispalten den erforderlichen Feldern zu',
        'detected' => 'Erkannt:',
        'rows' => 'Zeilen',
        'columns' => 'Spalten',
        'select_column' => '-- Spalte auswählen --',
        'sample_data' => 'Datenvorschau',
        'validate_mapping' => 'Zuordnung validieren',
        'validating' => 'Validierung...',
        'validation_results' => 'Validierungsergebnisse',
        'review_before_import' => 'Vor dem Import prüfen',
        'total_rows' => 'Gesamtzeilen',
        'valid' => 'Gültig',
        'invalid' => 'Ungültig',
        'warnings' => 'Warnungen',
        'validation_errors' => 'Validierungsfehler',
        'and_more' => '... und :count weitere',
        'import_rows' => ':count Zeilen importieren',
        'starting' => 'Wird gestartet...',
        'import_started' => 'Import gestartet!',
        'processing_background' => 'Wird im Hintergrund verarbeitet.',
        'import_another' => 'Weitere Datei importieren',
        'go_to_dashboard' => 'Zum Dashboard',
    ],

    /*
    |--------------------------------------------------------------------------
    | Banking Connection Wizard
    |--------------------------------------------------------------------------
    */

    'banking_wizard' => [
        'country' => 'Land',
        'bank' => 'Bank',
        'authorize' => 'Autorisieren',
        'connect' => 'Verbinden',
        'done' => 'Fertig',
        'select_country' => 'Land auswählen',
        'supported_countries' => 'Wir unterstützen Open Banking in Deutschland und Frankreich',
        'via' => 'über',
        'select_bank' => 'Bank auswählen',
        'supported_banks' => 'Wählen Sie aus unterstützten Banken',
        'search_banks' => 'Banken suchen...',
        'no_banks_found' => 'Keine Banken gefunden.',
        'authorize_connection' => 'Verbindung autorisieren',
        'redirect_to_bank' => 'Sie werden zu Ihrer Bank weitergeleitet',
        'secure_connection' => 'Sichere Verbindung (PSD2)',
        'psd2_info' => 'Wir nutzen Open Banking (PSD2) für sichere Verbindungen. Wir sehen niemals Ihre Zugangsdaten.',
        'what_we_access' => 'Worauf wir zugreifen:',
        'account_balances' => 'Kontostände',
        'transaction_history' => 'Transaktionshistorie (letzte 90 Tage)',
        'transaction_details' => 'Transaktionsbeschreibungen',
        'what_we_dont_access' => 'Worauf wir NICHT zugreifen:',
        'login_credentials' => 'Ihre Zugangsdaten',
        'ability_transfers' => 'Möglichkeit für Überweisungen',
        'investment_details' => 'Persönliche Investitionsdetails',
        'continue_to_bank' => 'Weiter zur Bank',
        'redirecting' => 'Weiterleitung...',
        'redirecting_to_bank' => 'Weiterleitung zu Ihrer Bank...',
        'complete_authorization' => 'Bitte autorisieren Sie im neuen Fenster.',
        'click_if_not_redirected' => 'Hier klicken falls keine Weiterleitung',
        'bank_connected' => 'Bank verbunden!',
        'sync_in_progress' => 'Transaktionen werden synchronisiert.',
        'connect_another' => 'Weitere Bank verbinden',
        'connected_banks' => 'Verbundene Banken',
        'accounts' => 'Konten',
        'synced' => 'Synchronisiert',
        'sync_now' => 'Jetzt synchronisieren',
        'disconnect' => 'Trennen',
        'disconnect_confirm' => 'Diese Bank trennen?',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Reports - Methodology
    |--------------------------------------------------------------------------
    */

    'methodology' => [
        'title' => 'Methodik & Datenqualität',
        'calculation_standards' => 'Berechnungsstandards',
        'primary_standard' => 'Hauptstandard',
        'consolidation_approach' => 'Konsolidierungsansatz',
        'operational_control' => 'Betriebliche Kontrolle',
        'base_year' => 'Basisjahr',
        'reporting_period' => 'Berichtszeitraum',
        'emission_factor_sources' => 'Emissionsfaktor-Quellen',
        'source' => 'Quelle',
        'version' => 'Version',
        'applied_to' => 'Angewendet auf',
        'all_french_operations' => 'Alle französischen Betriebe',
        'uk_operations' => 'UK-Betriebe',
        'other_countries' => 'Andere Länder',
        'scope_definitions' => 'Scope-Definitionen',
        'direct_emissions' => 'Direkte Emissionen',
        'scope1_desc' => 'Emissionen aus eigenen oder kontrollierten Quellen.',
        'company_vehicles' => 'Firmenfahrzeuge (Flotte)',
        'onsite_fuel' => 'Brennstoffverbrennung vor Ort',
        'fugitive_emissions' => 'Flüchtige Emissionen (Kältemittel)',
        'process_emissions' => 'Prozessemissionen',
        'energy_indirect' => 'Indirekte energiebezogene Emissionen',
        'scope2_desc' => 'Emissionen aus eingekauftem Strom, Dampf, Heizung.',
        'purchased_electricity' => 'Eingekaufter Strom',
        'district_heating' => 'Fernwärme/-kälte',
        'steam' => 'Dampf',
        'location_based_note' => 'Standortbasierte Methode standardmäßig angewendet.',
        'value_chain' => 'Emissionen der Wertschöpfungskette',
        'scope3_desc' => 'Alle anderen indirekten Emissionen.',
        'cat1_purchased' => 'Kat. 1: Eingekaufte Waren und Dienstleistungen',
        'cat5_waste' => 'Kat. 5: Erzeugte Abfälle',
        'cat6_travel' => 'Kat. 6: Geschäftsreisen',
        'cat7_commuting' => 'Kat. 7: Pendeln der Mitarbeiter',
        'cat8_leased' => 'Kat. 8: Vorgelagerte geleaste Vermögenswerte',
        'data_quality_assessment' => 'Datenqualitätsbewertung',
        'data_type' => 'Datentyp',
        'quality_score' => 'Qualitätsbewertung',
        'coverage' => 'Abdeckung',
        'energy_consumption' => 'Energieverbrauch',
        'invoices_meters' => 'Rechnungen / Smart Meter',
        'business_travel' => 'Geschäftsreisen',
        'bank_transactions' => 'Banktransaktionen',
        'purchased_goods' => 'Eingekaufte Waren',
        'spend_based' => 'Ausgabenbasierte Schätzung',
        'uncertainty_limitations' => 'Unsicherheit & Einschränkungen',
        'uncertainty_factors' => 'Faktoren, die zur Unsicherheit beitragen:',
        'uncertainty_1' => 'Emissionsfaktoren basieren auf Durchschnittswerten.',
        'uncertainty_2' => 'Ausgabenbasierte Berechnungen verwenden Umrechnungsfaktoren.',
        'uncertainty_3' => 'Einige Scope-3-Kategorien verwenden Schätzungen.',
        'estimated_uncertainty' => 'Geschätzte Gesamtunsicherheit',
        'exclusions' => 'Ausschlüsse',
        'exclusions_desc' => 'Ausgeschlossene Emissionsquellen:',
        'estimated_impact' => 'Geschätzte Auswirkung',
        'verification_statement' => 'Verifizierungserklärung',
        'verified_by' => 'Dieses THG-Inventar wurde verifiziert von',
        'to_standard' => 'gemäß Standard',
        'verification_date' => 'Verifizierungsdatum',
        'ghg_prepared' => 'Gemäß GHG Protocol Corporate Standard erstellt.',
        'verification_recommended' => 'Unabhängige Drittverifizierung wird empfohlen.',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Reports - Scope Breakdown
    |--------------------------------------------------------------------------
    */

    'scope_breakdown' => [
        'title' => 'Detaillierte Emissionen nach Scope',
        'direct_emissions' => 'Direkte Emissionen',
        'scope1_desc' => 'Emissionen aus eigenen oder kontrollierten Quellen.',
        'category' => 'Kategorie',
        'emissions_tco2e' => 'Emissionen (t CO₂e)',
        'share' => 'Anteil',
        'trend' => 'Trend',
        'no_scope1_data' => 'Keine Scope-1-Emissionen erfasst.',
        'energy_indirect' => 'Indirekte energiebezogene Emissionen',
        'scope2_desc' => 'Emissionen aus eingekauftem Strom, Dampf, Heizung.',
        'calculation_method' => 'Berechnungsmethode',
        'location_based' => 'Standortbasiert',
        'market_based' => 'Marktbasiert',
        'consumption' => 'Verbrauch',
        'no_scope2_data' => 'Keine Scope-2-Emissionen erfasst.',
        'electricity_mix' => 'Strommix',
        'grid_emission_factor' => 'Netz-Emissionsfaktor',
        'value_chain' => 'Emissionen der Wertschöpfungskette',
        'scope3_desc' => 'Alle anderen indirekten Emissionen.',
        'ghg_category' => 'GHG Protocol Kategorie',
        'no_scope3_data' => 'Keine Scope-3-Emissionen erfasst.',
        'scope3_coverage' => 'Scope 3 Abdeckung',
        'currently_tracking' => 'Derzeit erfasst',
        'of_15_categories' => 'von 15 Scope-3-Kategorien',
        'not_relevant' => 'Als nicht relevant eingestufte Kategorien',
        'summary_by_scope' => 'Zusammenfassung nach Scope',
        'scope' => 'Scope',
        'share_of_total' => 'Anteil am Gesamten',
        'vs_previous' => 'vs. Vorperiode',
        'total' => 'Gesamt',
    ],

    /*
    |--------------------------------------------------------------------------
    | Documents (AI Extraction)
    |--------------------------------------------------------------------------
    */

    'documents' => [
        'title' => 'Dokumente',
        'subtitle' => 'Laden Sie Rechnungen hoch und KI extrahiert Emissionsdaten',
        'upload' => 'Dokument hochladen',
        'new_upload' => 'Neues Dokument',
        'list' => 'Hochgeladene Dokumente',
        'drop_files' => 'Dateien hier ablegen',
        'or_click' => 'oder klicken zum Auswählen',
        'uploading' => 'Wird hochgeladen',
        'processing' => 'KI-Verarbeitung',
        'process' => 'Dokument verarbeiten',
        'type' => 'Dokumenttyp',
        'file_required' => 'Bitte wählen Sie eine Datei',
        'file_too_large' => 'Datei zu groß (max 10 MB)',
        'invalid_type' => 'Nicht unterstützter Dateityp',
        'upload_success' => 'Dokument hochgeladen. Verarbeitung...',
        'upload_error' => 'Fehler beim Hochladen',
        'no_documents' => 'Keine Dokumente',
        'no_documents_hint' => 'Laden Sie Rechnungen hoch, um Emissionsdaten zu extrahieren',
        'validated' => 'Validiert',
        'emission_linked' => 'Emission erstellt',
        'confidence' => 'Konfidenz',
        'view' => 'Details anzeigen',
        'validate' => 'Validieren',
        'create_emission' => 'Emission erstellen',
        'reprocess' => 'Erneut verarbeiten',
        'cannot_reprocess' => 'Kann nicht erneut verarbeitet werden',
        'reprocessing' => 'Wird erneut verarbeitet...',
        'deleted' => 'Dokument gelöscht',
        'confirm_delete' => 'Dieses Dokument löschen?',
        'extracted_data' => 'Extrahierte Daten',
        'supplier' => 'Lieferant',
        'date' => 'Datum',
        'amount' => 'Betrag',
        'category' => 'Kategorie',
        'line_items' => 'Rechnungspositionen',
        'description' => 'Beschreibung',
        'quantity' => 'Menge',
        'unit' => 'Einheit',
        'validate_data' => 'Extrahierte Daten validieren',
        'validation_instructions' => 'Prüfen und bei Bedarf korrigieren',
        'confidence_level' => 'KI-Konfidenzniveau',
        'confirm_validation' => 'Validierung bestätigen',
        'validation_success' => 'Dokument validiert',
        'fields' => [
            'supplier_name' => 'Lieferant',
            'date' => 'Datum',
            'total_amount' => 'Gesamtbetrag',
            'invoice_number' => 'Rechnungsnummer',
            'document_type' => 'Dokumenttyp',
            'suggested_category' => 'Vorgeschlagene Kategorie',
        ],

        // Document types
        'types' => [
            'invoice' => 'Rechnung',
            'energy_bill' => 'Energierechnung',
            'fuel_receipt' => 'Tankbeleg',
            'transport_invoice' => 'Transportrechnung',
            'purchase_order' => 'Bestellung',
            'bank_statement' => 'Kontoauszug',
            'expense_report' => 'Spesenabrechnung',
            'other' => 'Sonstiges',
        ],

        // Processing statuses
        'statuses' => [
            'pending' => 'Ausstehend',
            'processing' => 'Wird verarbeitet',
            'completed' => 'Abgeschlossen',
            'failed' => 'Fehlgeschlagen',
            'needs_review' => 'Überprüfung erforderlich',
        ],

        // File formats
        'file_formats' => 'PDF, Bilder, Excel, CSV (max 10 MB)',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Assistant
    |--------------------------------------------------------------------------
    */

    'ai' => [
        'ai_help' => 'KI-Hilfe',
        'close' => 'Schließen',
        'ask_question' => 'Stellen Sie Ihre Frage...',
        'not_logged_in' => 'Nicht angemeldet',
        'not_available' => 'KI nicht verfügbar',

        // Quick prompts
        'quick_prompts' => [
            'emission_sources' => 'Welche Emissionsquellen soll ich einbeziehen?',
            'consumption_data' => 'Wie finde ich meine Verbrauchsdaten?',
            'which_unit' => 'Welche Einheit soll ich verwenden?',
            'emission_factors' => 'Emissionsfaktoren erklären',
        ],

        // Analysis Page
        'analysis_title' => 'KI-Analyse',
        'analysis_description' => 'Erhalten Sie personalisierte Empfehlungen basierend auf der Analyse Ihrer CO2-Emissionen.',
        'recommendations_title' => 'KI-Empfehlungen',
        'powered_by' => 'Powered by :provider',
        'refresh' => 'Aktualisieren',
        'not_configured' => 'KI nicht konfiguriert',
        'configure_to_use' => 'Konfigurieren Sie einen API-Schlüssel in den Einstellungen, um KI-Empfehlungen zu aktivieren.',
        'configure_ai' => 'KI konfigurieren',
        'ready_to_analyze' => 'Bereit zur Analyse',
        'analyze_description' => 'Klicken Sie auf die Schaltfläche unten, um die Analyse Ihrer Emissionen zu starten und personalisierte Empfehlungen zu erhalten.',
        'start_analysis' => 'Analyse starten',
        'analyzing' => 'Analyse läuft...',
        'analyzing_emissions' => 'Ihre Emissionen werden analysiert...',
        'recommendations_count' => 'Empfehlungen',
        'potential_reduction' => 'Potenzielle Reduktion',
        'key_insights' => 'Wichtige Erkenntnisse',
        'recommended_actions' => 'Empfohlene Maßnahmen',
        'add_selected' => ':count Maßnahme(n) hinzufügen',
        'add_action' => 'Zum Plan hinzufügen',
        'no_recommendations' => 'Keine Empfehlungen verfügbar',
        'top_emission_categories' => 'Top-Emissionskategorien',
        'how_it_works' => 'So funktioniert es',
        'how_it_works_description' => 'Unsere KI analysiert Ihre Emissionsdaten, identifiziert Reduktionsmöglichkeiten und schlägt konkrete Maßnahmen für Ihre Branche vor.',
        'data_sources' => 'Datenquellen',
        'data_source_emissions' => '• Ihre Emissionsdaten (Scope 1, 2, 3)',
        'data_source_sector' => '• Branchen-Benchmarks',
        'data_source_benchmarks' => '• Best Practices am Markt',
        'privacy' => 'Datenschutz',
        'privacy_description' => 'Ihre Daten bleiben vertraulich und werden nur zur Generierung Ihrer personalisierten Empfehlungen verwendet.',

        'helper' => [
            'current_category' => 'Aktuelle Kategorie',
            'not_configured' => 'KI nicht konfiguriert',
            'configure_api_key' => 'Konfigurieren Sie einen API-Schlüssel in den Admin-Einstellungen.',
            'configure_ai' => 'KI konfigurieren',
            'suggested_sources' => 'Vorgeschlagene Quellen für diese Kategorie',
            'suggested_category' => 'Vorgeschlagene Kategorie',
            'use_category' => 'Diese Kategorie verwenden',
            'suggested_factor' => 'Vorgeschlagener Emissionsfaktor',
            'use_factor' => 'Diesen Faktor verwenden',
            'quick_actions' => 'Schnellaktionen',
            'how_to_fill' => 'Wie ausfüllen?',
            'suggest_factor' => 'Faktor vorschlagen',
            'suggest_category' => 'Kategorie vorschlagen',
            'ask_about_category' => 'Frage zu dieser Kategorie',
            'frequent_questions' => 'Häufige Fragen',
        ],
        'chat' => [
            'assistant_name' => 'Carbex Assistent',
            'subtitle' => 'KI - CO2-Bilanz',
            'new_conversation' => 'Neues Gespräch',
            'welcome' => 'Hallo! Ich bin Ihr Assistent.',
            'welcome_description' => 'Ich kann bei Ihrer CO2-Bilanz helfen, Emissionsfaktoren erklären und Reduktionsmaßnahmen vorschlagen.',
            'powered_by' => 'Powered by Claude AI',
            'unlimited' => 'Unbegrenzt',
            'remaining' => 'verbleibend',
            'remaining_plural' => 'verbleibend',
            'ai_not_available' => 'KI ist in Ihrem Plan nicht verfügbar',
            'upgrade_premium' => 'Auf Premium upgraden',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sites - Multi-sites Management (T174-T175)
    |--------------------------------------------------------------------------
    */

    'sites' => [
        'manage_sites' => 'Standorte verwalten',
        'add_site' => 'Standort hinzufügen',
        'add' => 'Standort hinzufügen',
        'employees' => 'Mitarbeiter',
        'no_sites' => 'Keine Standorte konfiguriert',
        'no_sites_desc' => 'Fügen Sie Ihre verschiedenen Standorte hinzu, um deren CO2-Emissionen einzeln zu verfolgen.',
        'add_first' => 'Ersten Standort hinzufügen',

        // Site fields
        'name' => 'Name',
        'code' => 'Code',
        'code_hint' => 'Optionaler eindeutiger Code (z.B. BER-01)',
        'type' => 'Typ',
        'address' => 'Adresse',
        'city' => 'Stadt',
        'postal_code' => 'Postleitzahl',
        'country' => 'Land',
        'floor_area' => 'Fläche (m²)',
        'energy_rating' => 'Energieeffizienzklasse',
        'construction_year' => 'Baujahr',
        'heating_type' => 'Heizungsart',
        'employee_count' => 'Anzahl Mitarbeiter',
        'electricity_provider' => 'Stromanbieter',
        'renewable_energy' => 'Erneuerbare Energie',
        'renewable_percentage' => 'Anteil erneuerbarer Energie',
        'is_primary' => 'Hauptstandort',
        'primary' => 'Primär',
        'set_as_primary' => 'Als Hauptstandort festlegen',
        'edit' => 'Standort bearbeiten',
        'delete_title' => 'Standort löschen',
        'delete_confirm' => 'Möchten Sie diesen Standort wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',

        // Site types
        'types' => [
            'office' => 'Büro',
            'warehouse' => 'Lager',
            'factory' => 'Fabrik',
            'store' => 'Geschäft',
            'datacenter' => 'Rechenzentrum',
            'other' => 'Sonstiges',
        ],

        'comparison' => [
            'title' => 'Standortvergleich',
            'subtitle' => 'Analysieren und vergleichen Sie die CO2-Emissionen Ihrer verschiedenen Standorte',
            'total_sites' => 'Aktive Standorte',
            'total_emissions' => 'Gesamtemissionen',
            'top_emitter' => 'Größter Emittent',
            'average_per_site' => 'Durchschnitt pro Standort',
            'year' => 'Jahr',
            'scope' => 'Scope',
            'all_scopes' => 'Alle Scopes',
            'metric' => 'Metrik',
            'metric_total' => 'Gesamtemissionen',
            'metric_per_m2' => 'Pro m² Fläche',
            'metric_per_employee' => 'Pro Mitarbeiter',
            'sort_by' => 'Sortieren nach',
            'sort_emissions_desc' => 'Emissionen (absteigend)',
            'sort_emissions_asc' => 'Emissionen (aufsteigend)',
            'sort_name_asc' => 'Name (A-Z)',
            'sort_name_desc' => 'Name (Z-A)',
            'sort_intensity_desc' => 'Intensität (absteigend)',
            'sort_intensity_asc' => 'Intensität (aufsteigend)',
            'chart_title' => 'Emissionen nach Standort',
            'emissions_unit' => 't CO₂e',
            'no_sites' => 'Keine Standorte konfiguriert',
            'no_sites_description' => 'Fügen Sie Ihre Standorte hinzu, um deren Emissionen zu vergleichen',
            'table_title' => 'Standortdetails',
            'site' => 'Standort',
            'total' => 'Gesamt',
            'intensity' => 'Intensität',
            'share' => 'Anteil',
            'recommendations' => 'Empfehlungen',
        ],

        'recommendations' => [
            'high_emitter' => 'Dieser Standort emittiert :percent% mehr als der Durchschnitt. Priorität für Reduktionsmaßnahmen.',
            'scope1_heavy' => 'Hohe direkte Emissionen. Erwägen Sie Alternativen zu fossilen Brennstoffen.',
            'scope2_heavy' => 'Hoher Stromverbrauch. Erwägen Sie Ökostromverträge oder Energieeffizienz.',
            'high_intensity' => 'Hohe CO2-Intensität pro m². Energieaudit empfohlen.',
            'missing_area' => 'Geben Sie die Fläche ein, um die CO2-Intensität zu berechnen.',
            'missing_employees' => 'Geben Sie die Mitarbeiterzahl für die Pro-Kopf-Analyse ein.',
            'good_performance' => 'Ausgezeichnete Leistung! Dieser Standort liegt deutlich unter dem Durchschnitt.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Employee Engagement (T180-T182)
    |--------------------------------------------------------------------------
    */

    'engage' => [
        'title' => 'Mitarbeiter-Engagement',
        'description' => 'Motivieren Sie Ihre Teams und messen Sie Ihren persönlichen CO2-Fußabdruck.',
        'your_points' => 'Ihre Punkte',

        'tabs' => [
            'quiz' => 'Klima-Quiz',
            'calculator' => 'Rechner',
            'challenges' => 'Challenges',
            'leaderboard' => 'Rangliste',
        ],

        'quiz' => [
            'question' => 'Frage',
            'completed' => 'Quiz abgeschlossen!',
            'excellent' => 'Ausgezeichnet! Sie sind ein Klimaexperte.',
            'good' => 'Gutes Ergebnis! Lernen Sie weiter.',
            'keep_learning' => 'Lernen Sie weiter über das Klima.',
            'retry' => 'Quiz wiederholen',
        ],

        'calculator' => [
            'title' => 'Persönlicher Fußabdruck-Rechner',
            'commute_distance' => 'Pendelstrecke (km)',
            'commute_mode' => 'Transportmittel',
            'diet' => 'Ernährung',
            'flights_short' => 'Kurzstreckenflüge / Jahr',
            'flights_long' => 'Langstreckenflüge / Jahr',
            'heating_type' => 'Heizungsart',
            'calculate' => 'Meinen Fußabdruck berechnen',
            'your_footprint' => 'Ihr CO2-Fußabdruck',
            'tonnes_year' => 't CO2e/Jahr',
            'breakdown' => 'Aufschlüsselung',
            'recalculate' => 'Neu berechnen',

            'modes' => [
                'car_petrol' => 'Benzin-Auto',
                'car_diesel' => 'Diesel-Auto',
                'car_electric' => 'Elektroauto',
                'public_transport' => 'Öffentliche Verkehrsmittel',
                'bike' => 'Fahrrad',
                'walk' => 'Zu Fuß',
            ],

            'diets' => [
                'vegan' => 'Vegan',
                'vegetarian' => 'Vegetarisch',
                'mixed' => 'Gemischt',
                'meat_heavy' => 'Fleischreich',
            ],

            'heating' => [
                'gas' => 'Erdgas',
                'oil' => 'Öl',
                'electric' => 'Elektrisch',
                'heat_pump' => 'Wärmepumpe',
            ],
        ],

        'challenges' => [
            'title' => 'Umwelt-Challenges',
            'no_car_week' => 'Autofreie Woche',
            'meatless_monday' => 'Fleischloser Montag',
            'energy_saver' => 'Energiesparer',
            'bike_to_work' => 'Mit dem Rad zur Arbeit',
            'join' => 'Beitreten',
            'leave' => 'Verlassen',
            'mark_complete' => 'Als abgeschlossen markieren',
            'co2_saved' => 'CO2 gespart',
        ],

        'leaderboard' => [
            'title' => 'Rangliste',
            'participate' => 'An der Rangliste teilnehmen',
            'your_rank' => 'Ihr Rang',
            'rank' => 'Rang',
            'name' => 'Name',
            'points' => 'Punkte',
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
        'measure' => 'Messen',
        'plan' => 'Planen',
        'engage' => 'Einbinden',
        'report' => 'Berichten',
        'promote' => 'Fördern',

        'sites' => 'Standorte',
        'transition' => 'Übergangsplan',
        'trajectory' => 'SBTi-Trajektorie',
        'assessments' => 'Jahresbilanzen',
        'suppliers' => 'Lieferanten',
        'employees' => 'Teams',
        'reports' => 'Berichte',
        'compliance' => 'Compliance',
        'badges' => 'Abzeichen',
        'showcase' => 'Vitrine',
    ],

    'settings' => [
        'team' => 'Team',
        'sites' => 'Standorte',
        'navigation_mode' => 'Navigationsmodus',
        'nav_standard' => 'Standardnavigation',
        'nav_standard_desc' => 'Klassisches Menü nach Funktionen',
        'nav_pillars' => '5-Säulen-Navigation',
        'nav_pillars_desc' => 'Organisiert nach strategischen Säulen (TrackZero-Stil)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users & Roles
    |--------------------------------------------------------------------------
    */

    'users' => [
        'invite' => 'Benutzer einladen',
        'member' => 'Mitglied',
        'role' => 'Rolle',
        'status' => 'Status',
        'last_login' => 'Letzte Anmeldung',
        'you' => '(Sie)',
        'never' => 'Nie',
        'edit_role' => 'Rolle bearbeiten',
        'remove' => 'Entfernen',
        'confirm_remove' => 'Sind Sie sicher, dass Sie diesen Benutzer entfernen möchten?',
    ],

    'roles' => [
        'owner' => 'Eigentümer',
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'member' => 'Mitglied',
        'viewer' => 'Betrachter',
    ],

    'compliance' => [
        'title' => 'Compliance-Dashboard',
        'subtitle' => 'Verfolgen Sie Ihre CSRD-Konformität und ISO-Zertifizierungen',
        'reporting_year' => 'Berichtsjahr',
        'add_task' => 'Aufgabe hinzufügen',

        'tabs' => [
            'overview' => 'Übersicht',
            'tasks' => 'Aufgaben',
        ],

        'progress' => 'Fortschritt',
        'disclosures' => 'Offenlegungen',
        'certifications' => 'Zertifizierungen',
        'certified' => 'Zertifiziert',
        'in_progress' => 'In Bearbeitung',
        'expiring_soon' => 'läuft bald ab',
        'overdue_tasks' => 'Überfällige Aufgaben',
        'no_overdue' => 'Keine überfälligen Aufgaben',
        'upcoming' => 'Anstehend',
        'no_upcoming' => 'Keine anstehenden Aufgaben',
        'by_category' => 'nach Kategorie',
        'requirements' => 'Anforderungen',

        'categories' => [
            'environment' => 'Umwelt',
            'social' => 'Soziales',
            'governance' => 'Unternehmensführung',
        ],

        'iso_categories' => [
            'environmental' => 'Umwelt',
            'energy' => 'Energie',
            'quality' => 'Qualität',
            'carbon' => 'CO2',
        ],

        'code' => 'Code',
        'disclosure' => 'Offenlegung',
        'category' => 'Kategorie',
        'status' => 'Status',
        'mandatory' => 'Pflicht',
        'no_frameworks' => 'Keine CSRD-Rahmenwerke gefunden',
        'no_standards' => 'Keine ISO-Standards gefunden',
        'expires' => 'Läuft ab',
        'certifier' => 'Zertifizierer',

        'status' => [
            'not_started' => 'Nicht begonnen',
            'in_progress' => 'In Bearbeitung',
            'compliant' => 'Konform',
            'non_compliant' => 'Nicht konform',
        ],

        'cert_status' => [
            'not_certified' => 'Nicht zertifiziert',
            'in_progress' => 'In Bearbeitung',
            'certified' => 'Zertifiziert',
            'expired' => 'Abgelaufen',
        ],

        'task_status' => [
            'pending' => 'Ausstehend',
            'in_progress' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
            'overdue' => 'Überfällig',
        ],

        'priority' => [
            'low' => 'Niedrig',
            'medium' => 'Mittel',
            'high' => 'Hoch',
            'critical' => 'Kritisch',
        ],

        'task' => 'Aufgabe',
        'type' => 'Typ',
        'due_date' => 'Fälligkeitsdatum',
        'no_tasks' => 'Keine Compliance-Aufgaben',
        'mark_complete' => 'Als erledigt markieren',
        'confirm_delete_task' => 'Sind Sie sicher, dass Sie diese Aufgabe löschen möchten?',

        'new_task' => 'Neue Aufgabe',
        'edit_task' => 'Aufgabe bearbeiten',
        'internal' => 'Intern',
        'title' => 'Titel',
        'description' => 'Beschreibung',
        'task_title_placeholder' => 'Aufgabentitel...',
        'task_description_placeholder' => 'Detaillierte Beschreibung...',

        'status_updated' => 'Status erfolgreich aktualisiert',
        'task_created' => 'Aufgabe erfolgreich erstellt',
        'task_updated' => 'Aufgabe erfolgreich aktualisiert',
        'task_completed' => 'Aufgabe erfolgreich abgeschlossen',
        'task_deleted' => 'Aufgabe erfolgreich gelöscht',
    ],

    'promote' => [
        'title' => 'Abzeichen-Vitrine',
        'subtitle' => 'Teilen Sie Ihre CO2-Erfolge',
        'no_badges' => 'Noch keine Abzeichen verdient',
        'no_badges_description' => 'Setzen Sie Ihre CO2-Reduktionsbemühungen fort, um Abzeichen freizuschalten.',
        'start_measuring' => 'Mit der Messung beginnen',

        // Badge details
        'your_badges' => 'Ihre Abzeichen',
        'badge_gallery' => 'Abzeichen-Galerie',
        'total_points' => 'Gesamtpunkte',
        'selected_badge' => 'Ausgewähltes Abzeichen',
        'earned_on' => 'Verdient am',
        'points' => 'Punkte',

        // Actions
        'share' => 'Teilen',
        'embed' => 'Einbetten',
        'download' => 'Herunterladen',

        // Share modal
        'share_title' => 'Dieses Abzeichen teilen',
        'share_description' => 'Teilen Sie Ihre Leistung in sozialen Medien oder kopieren Sie den Link.',
        'copy_link' => 'Link kopieren',
        'link_copied' => 'Link kopiert!',
        'share_on_linkedin' => 'Auf LinkedIn teilen',
        'share_on_twitter' => 'Auf X (Twitter) teilen',
        'linkedin_summary' => 'Wir sind stolz, das Abzeichen :badge für unsere CO2-Managementbemühungen erhalten zu haben. Verifiziert von Carbex.',
        'twitter_text' => 'Wir haben das Abzeichen :badge für unser CO2-Engagement erhalten! #CarbonNeutral #Nachhaltigkeit',

        // Embed modal
        'embed_title' => 'Dieses Abzeichen einbetten',
        'embed_description' => 'Kopieren Sie den HTML-Code, um dieses Abzeichen auf Ihrer Website einzubetten.',
        'embed_size' => 'Größe',
        'embed_size_small' => 'Klein (200x250)',
        'embed_size_medium' => 'Mittel (300x375)',
        'embed_size_large' => 'Groß (400x500)',
        'copy_code' => 'Code kopieren',
        'code_copied' => 'Code kopiert!',
        'preview' => 'Vorschau',

        // Download modal
        'download_title' => 'Assets herunterladen',
        'download_description' => 'Laden Sie Assets für Ihre Kommunikation herunter.',
        'format' => 'Format',
        'download_badge' => 'Abzeichen herunterladen',
        'download_signature' => 'E-Mail-Signatur',
        'download_social_kit' => 'Social-Media-Kit',
        'social_kit_preparing' => 'Kit wird vorbereitet...',

        // Public badge page
        'awarded_to' => 'Verliehen an',
        'verified' => 'Verifiziert',
        'verified_by_carbex' => 'Verifiziert von Carbex',
        'verify' => 'Überprüfen',
        'learn_more' => 'Mehr erfahren',
        'cta_text' => 'Auch Sie können Ihren CO2-Fußabdruck messen und reduzieren.',
        'start_free' => 'Kostenlos starten',
        'all_rights_reserved' => 'Alle Rechte vorbehalten.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Onboarding
    |--------------------------------------------------------------------------
    */
    'onboarding' => [
        'company_info' => 'Unternehmensinformationen',
        'company_info_desc' => 'Geben Sie die Informationen Ihres Unternehmens ein, um zu beginnen.',
        'company_name' => 'Unternehmensname',
        'siret' => 'Handelsregisternummer',
        'sector' => 'Branche',
        'select_sector' => 'Branche auswählen',
        'company_size' => 'Unternehmensgröße',
        'select_size' => 'Größe auswählen',
        'site_config' => 'Standortkonfiguration',
        'site_config_desc' => 'Fügen Sie Ihren ersten Standort hinzu, um mit der Verfolgung Ihrer Emissionen zu beginnen.',
        'site_name' => 'Standortname',
        'address' => 'Adresse',
        'city' => 'Stadt',
        'postal_code' => 'Postleitzahl',
        'bank_connection' => 'Bankverbindung',
        'bank_connection_desc' => 'Verbinden Sie Ihr Bankkonto, um die Verfolgung Ihrer CO2-Ausgaben zu automatisieren.',
        'bank_connection_info' => 'Die Bankverbindung ermöglicht es Ihnen, Ihre Transaktionen automatisch zu importieren und Ihren CO2-Fußabdruck zu berechnen.',
        'connect_bank' => 'Meine Bank verbinden',
        'skip_step' => 'Diesen Schritt überspringen',
        'congratulations' => 'Herzlichen Glückwunsch',
        'setup_complete' => 'Ihr Konto ist eingerichtet. Sie können nun Ihren CO2-Fußabdruck verfolgen.',
        'go_to_dashboard' => 'Zum Dashboard',
    ],

    'sectors' => [
        'technology' => 'Technologie',
        'manufacturing' => 'Industrie',
        'services' => 'Dienstleistungen',
        'retail' => 'Handel',
        'healthcare' => 'Gesundheitswesen',
        'finance' => 'Finanzwesen',
        'construction' => 'Bauwesen',
        'transport' => 'Transport',
        'hospitality' => 'Gastgewerbe',
        'energy' => 'Energie',
        'other' => 'Sonstiges',
    ],

    'sizes' => [
        'small' => 'Klein (1-50 Mitarbeiter)',
        'medium' => 'Mittel (51-250 Mitarbeiter)',
        'large' => 'Groß (251-1000 Mitarbeiter)',
        'enterprise' => 'Sehr groß (1000+ Mitarbeiter)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie Consent (GDPR/TTDSG/BDSG)
    |--------------------------------------------------------------------------
    */

    'cookies' => [
        'title' => 'Cookie-Einstellungen',
        'description' => 'Wir verwenden Cookies, um Ihnen das beste Nutzererlebnis zu bieten. Einige Cookies sind für den Betrieb der Website erforderlich, während andere uns helfen, die Website zu verbessern.',
        'legal_notice' => 'Gemäß DSGVO Art. 7, TTDSG § 25 und BDSG benötigen wir Ihre Einwilligung für nicht-essenzielle Cookies.',

        'accept_all' => 'Alle akzeptieren',
        'essential_only' => 'Nur Essenzielle',
        'customize' => 'Anpassen',
        'save_preferences' => 'Einstellungen speichern',

        'essential_title' => 'Essenzielle Cookies',
        'essential_desc' => 'Diese Cookies sind für den Betrieb der Website unbedingt erforderlich. Sie ermöglichen Grundfunktionen wie Seitennavigation und Zugriff auf sichere Bereiche.',
        'always_active' => 'Immer aktiv',

        'functional_title' => 'Funktionale Cookies',
        'functional_desc' => 'Diese Cookies ermöglichen erweiterte Funktionen wie Spracheinstellungen, Zeitzone und personalisierte Darstellung.',

        'analytics_title' => 'Analyse-Cookies',
        'analytics_desc' => 'Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren, um sie zu verbessern.',

        'marketing_title' => 'Marketing-Cookies',
        'marketing_desc' => 'Diese Cookies werden verwendet, um Werbung relevanter für Sie zu gestalten.',

        'privacy_policy' => 'Datenschutzerklärung',
        'legal_notice_link' => 'Impressum',
    ],

    /*
    |--------------------------------------------------------------------------
    | GDPR/DSGVO Data Rights (German BDSG Compliance)
    |--------------------------------------------------------------------------
    */

    'gdpr' => [
        'title' => 'Datenschutz-Einstellungen',
        'subtitle' => 'Verwalten Sie Ihre persönlichen Daten gemäß DSGVO und BDSG',

        // Data Export (Art. 20 DSGVO)
        'export_data' => 'Daten exportieren',
        'export_description' => 'Laden Sie alle Ihre persönlichen Daten in einem portablen Format herunter (Art. 20 DSGVO)',
        'export_button' => 'Meine Daten herunterladen',
        'export_processing' => 'Export wird vorbereitet...',
        'export_ready' => 'Ihr Datenexport ist bereit zum Download.',

        // Data Deletion (Art. 17 DSGVO, § 35 BDSG)
        'delete_account' => 'Konto löschen',
        'delete_description' => 'Löschen Sie Ihr Konto und alle zugehörigen Daten (Art. 17 DSGVO - Recht auf Löschung)',
        'delete_warning' => 'Diese Aktion kann nicht rückgängig gemacht werden. Alle Ihre Daten werden unwiderruflich gelöscht.',
        'delete_button' => 'Konto unwiderruflich löschen',
        'delete_confirm_title' => 'Konto wirklich löschen?',
        'delete_confirm_message' => 'Bitte geben Sie "LÖSCHEN" ein, um zu bestätigen:',
        'delete_confirm_word' => 'LÖSCHEN',

        // Consent Management (Art. 7 DSGVO)
        'consent_title' => 'Einwilligungen verwalten',
        'consent_description' => 'Verwalten Sie Ihre Einwilligungen für verschiedene Datenverarbeitungen',
        'consent_marketing' => 'Marketing-Kommunikation',
        'consent_analytics' => 'Nutzungsanalyse',
        'consent_ai' => 'KI-gestützte Funktionen',

        // Data Access (Art. 15 DSGVO)
        'access_title' => 'Auskunftsrecht',
        'access_description' => 'Sie haben das Recht auf Auskunft über Ihre gespeicherten Daten (Art. 15 DSGVO)',

        // Data Rectification (Art. 16 DSGVO)
        'rectification_title' => 'Berichtigung',
        'rectification_description' => 'Sie können unrichtige Daten jederzeit berichtigen (Art. 16 DSGVO)',

        // Data Portability (Art. 20 DSGVO)
        'portability_title' => 'Datenübertragbarkeit',
        'portability_description' => 'Ihre Daten in einem maschinenlesbaren Format erhalten',

        // Right to Object (Art. 21 DSGVO, § 37 BDSG)
        'object_title' => 'Widerspruchsrecht',
        'object_description' => 'Sie können der Verarbeitung Ihrer Daten widersprechen (Art. 21 DSGVO)',

        // Legal References
        'legal_basis' => 'Rechtsgrundlage',
        'legal_basis_consent' => 'Einwilligung (Art. 6 Abs. 1 lit. a DSGVO)',
        'legal_basis_contract' => 'Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO)',
        'legal_basis_legal' => 'Rechtliche Verpflichtung (Art. 6 Abs. 1 lit. c DSGVO)',
        'legal_basis_legitimate' => 'Berechtigtes Interesse (Art. 6 Abs. 1 lit. f DSGVO)',

        // Contact
        'dpo_contact' => 'Datenschutzbeauftragter',
        'dpo_email' => 'dpo@carbex.fr',
    ],

    /*
    |--------------------------------------------------------------------------
    | German Regulatory Compliance
    |--------------------------------------------------------------------------
    */

    'compliance' => [
        'title' => 'Regulatorische Konformität',

        // German Climate Regulations
        'klimaschutzgesetz' => 'Bundes-Klimaschutzgesetz (KSG)',
        'klimaschutzgesetz_desc' => 'Konform mit den deutschen Klimazielen nach dem Bundes-Klimaschutzgesetz',

        // EU Regulations
        'csrd' => 'CSRD 2025',
        'csrd_desc' => 'Corporate Sustainability Reporting Directive - EU Nachhaltigkeitsberichterstattung',

        'esrs' => 'ESRS Standards',
        'esrs_desc' => 'European Sustainability Reporting Standards',

        'eu_taxonomy' => 'EU-Taxonomie',
        'eu_taxonomy_desc' => 'EU-Klassifizierungssystem für nachhaltige Wirtschaftstätigkeiten',

        // German Data Protection
        'bdsg' => 'BDSG',
        'bdsg_desc' => 'Bundesdatenschutzgesetz - Deutsche Datenschutzgesetzgebung',

        'ttdsg' => 'TTDSG',
        'ttdsg_desc' => 'Telekommunikation-Telemedien-Datenschutz-Gesetz',

        // Banking Regulations
        'psd2' => 'PSD2',
        'psd2_desc' => 'Zahlungsdiensterichtlinie - Sichere Bankverbindungen',

        'bafin' => 'BaFin-konform',
        'bafin_desc' => 'Konform mit den Anforderungen der Bundesanstalt für Finanzdienstleistungsaufsicht',

        // Emission Standards
        'ghg_protocol' => 'GHG Protocol',
        'ghg_protocol_desc' => 'Greenhouse Gas Protocol - Internationaler Standard für Treibhausgasbilanzierung',

        'uba' => 'UBA-Faktoren',
        'uba_desc' => 'Emissionsfaktoren des Umweltbundesamtes für Deutschland',

        'din_en_iso' => 'DIN EN ISO 14064',
        'din_en_iso_desc' => 'Internationale Norm für Treibhausgasbilanzierung und -verifizierung',
    ],

    /*
    |--------------------------------------------------------------------------
    | Suppliers Management
    |--------------------------------------------------------------------------
    */

    'suppliers' => [
        'title' => 'Lieferantenverwaltung',
        'description' => 'Verwalten Sie Ihre Lieferanten und erfassen Sie deren CO2-Emissionsdaten.',
        'import_csv' => 'CSV importieren',
        'add_supplier' => 'Lieferant hinzufügen',
        'edit_supplier' => 'Lieferant bearbeiten',
        'search_placeholder' => 'Lieferanten suchen...',
        'all_statuses' => 'Alle Status',
        'all_quality' => 'Alle Qualitätsstufen',
        'empty' => 'Keine Lieferanten gefunden',
        'add_first' => 'Ersten Lieferanten hinzufügen',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diesen Lieferanten löschen möchten?',

        // Table headers
        'name' => 'Name',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'sector' => 'Branche',
        'annual_spend' => 'Jährliche Ausgaben',
        'status' => 'Status',
        'data_quality' => 'Datenqualität',
        'country' => 'Land',
        'contact_name' => 'Kontaktname',
        'contact_email' => 'Kontakt-E-Mail',
        'notes' => 'Notizen',

        // Stats
        'stats' => [
            'total' => 'Lieferanten gesamt',
            'active' => 'Aktiv',
            'with_data' => 'Mit Daten',
            'pending' => 'Ausstehend',
            'total_spend' => 'Gesamtausgaben',
        ],

        // Status labels
        'statuses' => [
            'pending' => 'Ausstehend',
            'invited' => 'Eingeladen',
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        // Data quality labels
        'quality' => [
            'none' => 'Keine Daten',
            'estimated' => 'Geschätzt',
            'supplier_specific' => 'Lieferantenspezifisch',
            'verified' => 'Verifiziert',
        ],

        // Invitation
        'invite' => 'Einladen',
        'send_invitation' => 'Einladung senden',
        'due_date' => 'Fälligkeitsdatum',
        'message' => 'Persönliche Nachricht',
        'send' => 'Senden',

        // CSV Import
        'csv_file' => 'CSV-Datei',
        'csv_format' => 'Erwartetes Format (Semikolon-Trennzeichen):',
        'download_template' => 'Vorlage herunterladen',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification (Abzeichen & Belohnungen)
    |--------------------------------------------------------------------------
    */

    'gamification' => [
        'title' => 'Abzeichen & Belohnungen',
        'subtitle' => 'Verdienen Sie Abzeichen, während Sie in Ihrer CO2-Reise vorankommen',
        'total_points' => 'Gesamtpunkte',
        'badges_earned' => 'Verdiente Abzeichen',
        'next_level' => 'Nächste Stufe',
        'points' => 'Punkte',
        'check_badges' => 'Abzeichen ansehen',
        'leaderboard' => 'Rangliste',
        'your_badges' => 'Ihre Abzeichen',
        'no_badges_available' => 'Derzeit keine Abzeichen verfügbar',
        'how_it_works' => 'So funktioniert es',
        'badge_categories' => 'Abzeichen-Kategorien',
        'level' => [
            'starter' => 'Anfänger',
            'bronze' => 'Bronze',
            'silver' => 'Silber',
            'gold' => 'Gold',
            'platinum' => 'Platin',
        ],
        'category' => [
            'all' => 'Alle',
            'assessment' => 'Bilanz',
            'assessment_desc' => 'Abzeichen für die Erstellung Ihrer CO2-Bilanz',
            'reduction' => 'Reduktion',
            'reduction_desc' => 'Abzeichen für Ihre Emissionsreduzierungsbemühungen',
            'engagement' => 'Engagement',
            'engagement_desc' => 'Abzeichen für Ihr Plattform-Engagement',
            'expert' => 'Experte',
            'expert_desc' => 'Abzeichen für fortgeschrittene Benutzer',
        ],
        'step1_title' => 'Bilanz erstellen',
        'step1_desc' => 'Geben Sie Ihre Emissionsdaten ein, um Ihre ersten Abzeichen freizuschalten',
        'step2_title' => 'Emissionen reduzieren',
        'step2_desc' => 'Führen Sie Reduktionsmaßnahmen durch, um Punkte zu sammeln',
        'step3_title' => 'Erfolge teilen',
        'step3_desc' => 'Zeigen Sie Ihre Abzeichen auf Ihrer Website und in sozialen Netzwerken',
    ],

    /*
    |--------------------------------------------------------------------------
    | Marketing - Pricing Page
    |--------------------------------------------------------------------------
    */

    'marketing' => [
        'for_who' => [
            'title' => 'Für wen?',
            'description' => 'Carbex passt sich an alle Unternehmensgrößen an: KMU, Mittelstand und Großunternehmen. Entdecken Sie, wie unsere Plattform Ihre Bedürfnisse erfüllt.',
            'hero_title' => 'Eine Lösung für jedes Unternehmen',
            'hero_subtitle' => 'Vom KMU bis zum Großkonzern – Carbex passt sich an Ihre Bedürfnisse und Ihre CO2-Reife an.',
            'trust_us' => 'Sie vertrauen uns',

            'why_choose' => [
                'title' => 'Warum Carbex wählen?',
                'subtitle' => '3 gute Gründe, uns zu vertrauen',
                'measure_title' => 'Messen Sie Ihre Auswirkungen',
                'measure_desc' => 'Erstellen Sie einfach Ihre erste vollständige CO2-Bilanz (Scope 1, 2, 3), ohne Experte und ohne Verpflichtung. Unser KI-Assistent begleitet Sie bei jedem Schritt.',
                'drive_title' => 'Steuern Sie Ihre Transformation',
                'drive_desc' => 'Verfolgen Sie Ihre Emissionen im Zeitverlauf, setzen Sie SBTi-konforme Reduktionsziele und erstellen Sie einen konkreten Aktionsplan mit personalisierten Empfehlungen.',
                'comply_title' => 'Erfüllen Sie Ihre Pflichten',
                'comply_desc' => 'Generieren Sie automatisch Ihre RSE-, BEGES-, CSRD- oder ESG-Berichte und weisen Sie Ihre Konformität mit deutschen und europäischen Vorschriften nach.',
            ],

            'audiences' => [
                'title' => 'Angepasst an Ihre Struktur',
                'subtitle' => 'Ob KMU, Mittelstand oder Großunternehmen',
                'sme' => [
                    'title' => 'KMU',
                    'size' => '10 bis 250 Mitarbeiter',
                    'desc' => 'Messen Sie Ihren CO2-Fußabdruck, um Kosten zu optimieren, Vorschriften einzuhalten und Ihr Image bei Kunden zu verbessern.',
                    'feature1' => 'Erste Bilanz in weniger als einem Tag',
                    'feature2' => 'Kein CO2-Experte erforderlich',
                    'feature3' => 'Angepasste Tarife',
                    'cta' => 'Kostenlose Testversion starten',
                ],
                'midsize' => [
                    'title' => 'Mittelstand',
                    'size' => '250 bis 5.000 Mitarbeiter',
                    'desc' => 'Verfolgen Sie die Gesamtauswirkungen Ihrer Organisation, reduzieren Sie Emissionen an mehreren Standorten und erfüllen Sie die Anforderungen Ihrer Großkunden.',
                    'feature1' => 'Multi-Standort-Verwaltung',
                    'feature2' => 'Pflicht-BEGES-Konformität',
                    'feature3' => 'Automatisiertes Reporting',
                    'cta' => 'Premium wählen',
                ],
                'enterprise' => [
                    'title' => 'Großunternehmen',
                    'size' => 'Mehr als 5.000 Mitarbeiter',
                    'desc' => 'Verwalten Sie Ihren weltweiten CO2-Fußabdruck, halten Sie internationale Standards ein und optimieren Sie Ihre Reduktionsstrategien mit dedizierter Betreuung.',
                    'feature1' => 'CSRD und GRI ready',
                    'feature2' => 'API und Integrationen',
                    'feature3' => 'Dedizierter Support',
                    'cta' => 'Kontaktieren Sie uns',
                ],
            ],

            'testimonial' => [
                'title' => 'Was unsere Kunden sagen',
                'quote' => 'Der Experten-Support war für uns wertvoll, um unsere Interpretationen zu verfeinern. Die Möglichkeit, unsere FEC automatisch zu importieren und mehrere Standorte zu verwalten, hat den Unterschied gemacht. Es ist ein robustes und professionelles Werkzeug.',
                'author' => 'Aicha Benhamou',
                'role' => 'Nachhaltigkeits-Direktorin — Terres & Saveurs',
            ],

            'cta' => [
                'title' => 'Bereit, Ihren Fußabdruck zu messen?',
                'subtitle' => 'Starten Sie kostenlos und entdecken Sie, wie Carbex Ihnen helfen kann.',
                'primary' => '15 Tage kostenlose Testversion',
                'secondary' => 'Preise anzeigen',
            ],
        ],
        'contact' => [
            'title' => 'Kontakt',
            'description' => 'Kontaktieren Sie das Carbex-Team bei Fragen zu unserer CO2-Bilanz-Plattform.',
            'hero_title' => 'Sprechen wir über Ihr Projekt',
            'hero_subtitle' => 'Unser Team ist hier, um Ihre Fragen zu beantworten und Sie zu begleiten.',
            'contact_us' => 'Kontaktieren Sie uns',
            'email' => 'E-Mail',
            'phone' => 'Telefon',
            'address' => 'Adresse',
            'hours' => 'Öffnungszeiten',
            'hours_weekdays' => 'Montag - Freitag: 9:00 - 18:00',
            'hours_premium' => 'Premium-Kundensupport: 24/7',
            'send_message' => 'Nachricht senden',
            'form' => [
                'name' => 'Vollständiger Name',
                'email' => 'Geschäftliche E-Mail',
                'company' => 'Unternehmen',
                'subject' => 'Betreff',
                'select_subject' => 'Betreff auswählen',
                'message' => 'Nachricht',
                'send' => 'Nachricht senden',
            ],
            'subjects' => [
                'demo' => 'Demo anfordern',
                'pricing' => 'Preisfrage',
                'enterprise' => 'Enterprise-Angebot',
                'partnership' => 'Partnerschaft',
                'support' => 'Technischer Support',
                'other' => 'Sonstiges',
            ],
        ],
        'pricing' => [
            'title' => 'Preise',
            'description' => 'Entdecken Sie unsere CO2-Bilanz-Angebote für KMU. 15 Tage kostenlose Testversion, dann ab 40 EUR/Monat.',
            'hero_title' => 'Einfache und transparente Preise',
            'hero_subtitle' => 'Starten Sie kostenlos. Erweitern Sie nach Bedarf. Ohne Bindung.',
            'monthly' => 'Monatlich',
            'annual' => 'Jährlich',
            'free_trial' => 'Kostenlose Testversion',
            'trial_duration' => '15 Tage voller Zugang',
            'includes' => 'Enthalten:',
            'most_popular' => 'Am beliebtesten',
            'advanced' => 'Erweitert',
            'per_month' => 'pro Monat zzgl. MwSt.',
            'per_year' => 'pro Jahr zzgl. MwSt.',
            'premium_savings' => '33 EUR/Monat - 80 EUR/Jahr sparen',
            'advanced_savings' => '100 EUR/Monat - 240 EUR/Jahr sparen',
            'no_commitment' => 'Ohne Bindung, jederzeit kündbar',
            'all_from_trial' => 'Alles aus der Testversion, plus:',
            'all_from_premium' => 'Alles aus Premium, plus:',
            'start_trial' => 'Testversion starten',
            'choose_premium' => 'Premium wählen',
            'choose_advanced' => 'Erweitert wählen',
            'enterprise_title' => 'Benötigen Sie eine maßgeschneiderte Lösung?',
            'enterprise_subtitle' => 'Für große Unternehmen, Konzerne und Berater.',
            'contact_team' => 'Unser Team kontaktieren',
            'comparison_title' => 'Funktionsvergleich',
            'trial' => 'Testversion',
            'unlimited' => 'Unbegrenzt',
            'support_email' => 'E-Mail',
            'support_priority' => 'Priorität',
            'support_dedicated' => 'Dediziert',
            'faq_title' => 'Häufig gestellte Fragen',
            'cta_title' => 'Bereit, Ihre Wirkung zu messen?',
            'cta_subtitle' => 'Schließen Sie sich den KMU an, die ihren CO2-Fußabdruck kontrollieren.',
            'start_free' => 'Kostenlos starten',
            'trial_no_card' => '15 Tage kostenlose Testversion. Keine Kreditkarte erforderlich.',

            'features' => [
                'one_user' => '1 Benutzer',
                'one_site' => '1 Standort',
                'full_access' => 'Voller Plattformzugang',
                'one_report' => '1 PDF-Bericht',
                'email_support' => 'E-Mail-Support',
                'five_users' => 'Bis zu 5 Benutzer',
                'three_sites' => 'Bis zu 3 Standorte',
                'bank_import' => 'Automatischer Bankimport',
                'unlimited_reports' => 'Unbegrenzte Berichte (Word, Excel, PDF)',
                'ademe_ghg' => 'ADEME und GHG Protocol Deklarationen',
                'priority_support' => 'Prioritäts-Support',
                'unlimited_users' => 'Unbegrenzte Benutzer',
                'unlimited_sites' => 'Unbegrenzte Standorte',
                'full_api' => 'Vollständiger API-Zugang',
                'scope3_suppliers' => 'Scope 3 Lieferantenmodul',
                'dedicated_support' => 'Dedizierter Support',
                'custom_training' => 'Individuelle Schulung',
            ],

            'table' => [
                'users' => 'Benutzer',
                'sites' => 'Standorte',
                'reports' => 'Berichte',
                'bank_import' => 'Bankimport',
                'ademe_ghg' => 'Export ADEME/GHG',
                'api_access' => 'API-Zugang',
                'suppliers_module' => 'Lieferantenmodul',
                'support' => 'Support',
            ],

            'faq' => [
                'change_plan_q' => 'Kann ich meinen Plan jederzeit ändern?',
                'change_plan_a' => 'Ja, Sie können jederzeit zu einem höheren Plan wechseln. Die Differenz wird anteilig berechnet. Um zu einem niedrigeren Plan zu wechseln, wird die Änderung am Ende Ihres Abrechnungszeitraums wirksam.',
                'trial_duration_q' => 'Wie lange dauert die kostenlose Testversion?',
                'trial_duration_a' => 'Die kostenlose Testversion dauert 15 Tage mit vollem Zugang zu allen Funktionen. Keine Kreditkarte erforderlich, um zu beginnen.',
                'prices_vat_q' => 'Sind die Preise zzgl. oder inkl. MwSt.?',
                'prices_vat_a' => 'Alle angezeigten Preise sind Nettopreise (zzgl. MwSt.). Die gültige MwSt. (19% in Deutschland) wird beim Bezahlen hinzugefügt.',
                'monthly_payment_q' => 'Wie funktioniert die monatliche Zahlung?',
                'monthly_payment_a' => 'Die monatliche Zahlung wird jeden Monat automatisch abgebucht. Sie können jederzeit ohne Gebühren oder Strafen kündigen. Das Abonnement bleibt bis zum Ende des bezahlten Monats aktiv.',
            ],
        ],
    ],

];
