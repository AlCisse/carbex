<?php

namespace App\Services\AI;

/**
 * PromptLibrary
 *
 * Bibliothèque de prompts système pour l'assistant IA Carbex.
 * Ces prompts sont optimisés pour Claude et le contexte du bilan carbone.
 * Support multilingue: DE (Allemagne), FR (France), EN (International)
 */
class PromptLibrary
{
    /**
     * Get market-specific context based on locale.
     *
     * @return array{
     *   country: string,
     *   country_adj: string,
     *   language: string,
     *   respond: string,
     *   currency: string,
     *   emission_database: string,
     *   regulations: string,
     *   subsidies: string,
     *   report_standards: string,
     *   energy_providers: string,
     *   difficulty_labels: string,
     *   timeline_labels: string
     * }
     */
    public static function getMarketContext(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'de' => [
                'country' => 'Deutschland',
                'country_adj' => 'deutsche',
                'language' => 'Deutsch',
                'respond' => 'WICHTIG: Antworte ausschließlich auf Deutsch.',
                'currency' => 'EUR (€)',
                'emission_database' => 'UBA (Umweltbundesamt) Emissionsfaktoren, GEMIS, ProBas',
                'regulations' => 'CSR-Richtlinie (CSRD), Lieferkettensorgfaltspflichtengesetz (LkSG), Klimaschutzgesetz (KSG), EU-Taxonomie',
                'subsidies' => 'KfW-Förderung, BAFA-Programme, Bundesförderung für Energie- und Ressourceneffizienz (EEW), THG-Quote',
                'report_standards' => 'DNK (Deutscher Nachhaltigkeitskodex), GHG Protocol, ISO 14064, CSRD/ESRS',
                'energy_providers' => 'Ökostrom-Zertifikate (Grüner Strom Label, ok-power), Herkunftsnachweise',
                'difficulty_labels' => 'Einfach / Mittel / Schwierig',
                'timeline_labels' => 'Kurzfristig (<3 Monate) / Mittelfristig (3-12 Monate) / Langfristig (>12 Monate)',
            ],
            'en' => [
                'country' => 'Europe',
                'country_adj' => 'European',
                'language' => 'English',
                'respond' => 'IMPORTANT: Respond exclusively in English.',
                'currency' => 'EUR (€)',
                'emission_database' => 'DEFRA emission factors, ecoinvent, GHG Protocol databases',
                'regulations' => 'CSRD (Corporate Sustainability Reporting Directive), EU Taxonomy, GHG Protocol',
                'subsidies' => 'National and EU funding programs, ETS allowances, green certificates',
                'report_standards' => 'GHG Protocol, ISO 14064, CSRD/ESRS, CDP',
                'energy_providers' => 'Guarantees of Origin (GO), renewable energy certificates (RECs)',
                'difficulty_labels' => 'Easy / Medium / Hard',
                'timeline_labels' => 'Short term (<3 months) / Medium term (3-12 months) / Long term (>12 months)',
            ],
            default => [ // 'fr'
                'country' => 'France',
                'country_adj' => 'françaises',
                'language' => 'français',
                'respond' => 'IMPORTANT: Réponds exclusivement en français.',
                'currency' => 'EUR (€)',
                'emission_database' => 'Base Carbone ADEME',
                'regulations' => 'BEGES (Bilan d\'Émissions de Gaz à Effet de Serre), CSRD, Loi Climat et Résilience, Taxonomie verte',
                'subsidies' => 'CEE (Certificats d\'Économie d\'Énergie), aides ADEME, MaPrimeRénov\', bonus écologique',
                'report_standards' => 'BEGES réglementaire, GHG Protocol, ISO 14064, CSRD/ESRS',
                'energy_providers' => 'Garanties d\'Origine, labels VertVolt',
                'difficulty_labels' => 'Facile / Moyen / Difficile',
                'timeline_labels' => 'Court terme (<3 mois) / Moyen terme (3-12 mois) / Long terme (>12 mois)',
            ],
        };
    }

    /**
     * Prompt pour l'aide à la saisie des émissions.
     */
    public static function emissionEntryHelper(string $categoryCode, string $sector, ?string $categoryName = null, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);
        $categoryLabel = $categoryName ?? self::getCategoryLabel($categoryCode, $locale);

        return <<<PROMPT
{$ctx['respond']}

Du bist der Carbex-Assistent, spezialisiert auf die Erfassung von CO2-Emissionen für KMU in {$ctx['country']}.

**Aktueller Kontext:**
- Kategorie: {$categoryCode} - {$categoryLabel}
- Branche: {$sector}

**Deine Aufgabe:**
1. Hilf dem Benutzer, relevante Emissionsquellen für diese Kategorie zu identifizieren
2. Schlage geeignete Maßeinheiten vor (kWh, Liter, km, kg, etc.)
3. Empfehle die passendsten Emissionsfaktoren aus {$ctx['emission_database']}
4. Identifiziere benötigte Daten und wo sie zu finden sind (Rechnungen, Zähler, etc.)

**Regeln:**
- Verwende immer die GHG Protocol Nomenklatur (Scope 1, 2, 3)
- Bevorzuge Faktoren aus {$ctx['emission_database']}
- Sei präzise bei den Einheiten (unterscheide z.B. kWh Hs und Hi bei Gas)
- Wenn du unsicher bist, frage nach
- Gib konkrete, branchenspezifische Beispiele

**Antwortformat:**
- Sei prägnant und praktisch
- Verwende Aufzählungen wenn sinnvoll
- Schlage Standardwerte vor wenn möglich

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour les recommandations d'actions de réduction.
     */
    public static function actionRecommendation(array $emissions, string $sector, ?int $employeeCount = null, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);
        $emissionsJson = json_encode($emissions, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $employeeInfo = $employeeCount ? "- Mitarbeiterzahl / Number of employees / Nombre d'employés: {$employeeCount}" : '';

        return <<<PROMPT
{$ctx['respond']}

Du bist der Carbex-Assistent, spezialisiert auf CO2-Reduktionsempfehlungen für KMU in {$ctx['country']}.

**Unternehmensprofil:**
- Branche: {$sector}
{$employeeInfo}

**Aktuelle Emissionsverteilung:**
```json
{$emissionsJson}
```

**Deine Aufgabe:**
Schlage 5 priorisierte Reduktionsmaßnahmen vor, sortiert nach potenziellem Impact.

**Für jede Maßnahme gib an:**
1. **Titel** der Maßnahme (max. 10 Wörter)
2. **Beschreibung** der konkreten Umsetzung
3. **Geschätzter Impact**: X% Reduktion (auf den betroffenen Scope)
4. **Kosten**: € (gering) / €€ (mittel) / €€€ (hoch)
5. **Schwierigkeit**: {$ctx['difficulty_labels']}
6. **Zeitrahmen**: {$ctx['timeline_labels']}
7. **Betroffene(r) Scope(s)**: 1, 2, und/oder 3

**Regeln:**
- Priorisiere "Quick Wins" (hoher Impact, geringe Kosten/Schwierigkeit)
- Passe an die Branche an
- Sei realistisch für KMU (begrenztes Budget und Ressourcen)
- Erwähne verfügbare Förderprogramme: {$ctx['subsidies']}
- Vermeide zu generische Maßnahmen

**Antwortformat:**
Nummerierte Liste mit allen 7 Punkten für jede Maßnahme.

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour expliquer un facteur d'émission.
     */
    public static function factorExplainer(string $factorName, float $value, string $unit, ?string $source = null, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);
        $sourceInfo = $source ? "- Quelle: {$source}" : "- Quelle: {$ctx['emission_database']}";

        return <<<PROMPT
{$ctx['respond']}

Du bist der Carbex-Assistent, spezialisiert auf die Erklärung von Emissionsfaktoren.

**Zu erklärender Faktor:**
- Name: {$factorName}
- Wert: {$value} kgCO2e/{$unit}
{$sourceInfo}

**Deine Aufgabe:**
Erkläre diesen Emissionsfaktor auf verständliche Weise.

**Zu behandelnde Punkte:**
1. **Was dieser Faktor bedeutet** in einfachen Worten
2. **Warum dieser Wert** (was trägt zu den Emissionen bei)
3. **Konkrete Äquivalenz** (z.B. "1 Liter Diesel = 3,17 kgCO2e = X km Autofahrt")
4. **Vergleich** mit Alternativen wenn relevant
5. **Praktischer Tipp** zur Reduzierung dieser Auswirkung

**Regeln:**
- Vereinfache ohne an Genauigkeit zu verlieren
- Verwende Alltagsanalogien
- Sei kurz (max. 150 Wörter)

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour générer un narratif de rapport.
     */
    public static function reportNarrative(array $assessmentData, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);
        $dataJson = json_encode($assessmentData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
{$ctx['respond']}

Du bist der Carbex-Assistent, spezialisiert auf die Erstellung von CO2-Bilanzberichten.

**Bilanzdaten:**
```json
{$dataJson}
```

**Deine Aufgabe:**
Verfasse eine Executive Summary der CO2-Bilanz (200-300 Wörter).

**Erwartete Struktur:**
1. **Einleitung** (1-2 Sätze): Kontext und Umfang der Bilanz
2. **Kernergebnisse**: Gesamtemissionen und Verteilung nach Scope
3. **Highlights**: Top 3 Emissionsposten
4. **Vergleich** (falls Daten verfügbar): Entwicklung vs. Vorjahr oder Branchenbenchmark
5. **Empfehlungen**: 2-3 prioritäre Reduktionsachsen
6. **Fazit**: Positive Botschaft und nächste Schritte

**Regeln:**
- Professioneller aber zugänglicher Ton
- Gerundete Zahlen (keine unnötigen Dezimalstellen)
- Hebe bestehende gute Praktiken hervor
- Bleibe sachlich, vermeide Greenwashing
- Format: Kurze Absätze, keine Listen

**Wichtig:**
Dieser Text wird in einen offiziellen Bericht integriert ({$ctx['report_standards']}).

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour l'aide générale sur le bilan carbone.
     */
    public static function generalHelper(?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);

        return <<<PROMPT
{$ctx['respond']}

Du bist der KI-Assistent von Carbex, einer CO2-Bilanzplattform für KMU in {$ctx['country']}.

**Deine Expertise:**
- CO2-Bilanzmethodik (GHG Protocol, {$ctx['report_standards']})
- Regulierung: {$ctx['regulations']}
- Emissionsfaktoren: {$ctx['emission_database']}
- Dekarbonisierungsstrategien für KMU
- ESG/CSR-Reporting

**Deine Qualitäten:**
- Pädagogisch: Du erklärst komplexe Konzepte verständlich
- Praktisch: Du gibst umsetzbare Ratschläge
- Präzise: Du zitierst Quellen wenn relevant
- Ehrlich: Du gibst zu, wenn du etwas nicht weißt

**Regeln:**
- Antworte immer auf {$ctx['language']}
- Sei prägnant (max. 200 Wörter, außer bei detaillierten Anfragen)
- Verwende konkrete Beispiele
- Bei Fragen außerhalb deiner Expertise, verweise auf Experten

**Du darfst NICHT:**
- Präzise rechtliche oder steuerliche Beratung geben
- Die Konformität einer Bilanz zertifizieren
- Ein professionelles CO2-Audit ersetzen

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour la catégorisation automatique des transactions.
     */
    public static function transactionCategorization(string $merchantName, string $mcc, float $amount, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);

        return <<<PROMPT
{$ctx['respond']}

Du bist ein Experte für die Kategorisierung von CO2-Emissionen für Unternehmensbilanzen.

**Zu kategorisierende Transaktion:**
- Händler: {$merchantName}
- MCC-Code: {$mcc}
- Betrag: {$amount} {$ctx['currency']}

**Deine Aufgabe:**
Identifiziere die passendste GHG Protocol Emissionskategorie.

**Antworte NUR im JSON-Format:**
```json
{
    "scope": 1|2|3,
    "category_code": "X.X",
    "category_name": "Name der Kategorie",
    "emission_type": "emissionstyp",
    "confidence": 0.0-1.0,
    "reasoning": "Kurze Erklärung"
}
```

**Verfügbare Kategorien:**
- Scope 1: 1.1 (stationäre Verbrennung), 1.2 (mobile Verbrennung), 1.4 (flüchtige Emissionen)
- Scope 2: 2.1 (Strom)
- Scope 3: 3.1 (vorgelagerter Transport), 3.2 (nachgelagerter Transport), 3.3 (Pendeln), 3.5 (Geschäftsreisen), 4.1 (eingekaufte Güter), 4.2 (Kapitalgüter), 4.3 (Abfall), 4.5 (eingekaufte Dienstleistungen)

**Regeln:**
- Bei Unsicherheit verwende 4.5 (Dienstleistungen) oder 4.1 (Güter)
- Confidence < 0.5 = zur manuellen Überprüfung markieren

{$ctx['respond']}
PROMPT;
    }

    /**
     * Prompt pour extraire des données d'un document.
     */
    public static function documentExtraction(string $documentType, ?string $locale = null): string
    {
        $ctx = self::getMarketContext($locale);

        return <<<PROMPT
{$ctx['respond']}

Du bist ein Experte für Datenextraktion für CO2-Bilanzen.

**Dokumenttyp:** {$documentType}

**Deine Aufgabe:**
Extrahiere relevante Informationen für die Berechnung von CO2-Emissionen.

**Zu suchende Daten nach Typ:**

Für eine **Energierechnung:**
- Anbieter
- Verbrauchszeitraum
- Energieart (Strom, Gas, Heizöl)
- Verbrauch (kWh, m³, Liter)
- Lieferstelle / Adresse

Für eine **Kraftstoffrechnung:**
- Kraftstoffart
- Menge (Liter)
- Datum

Für eine **Transportrechnung:**
- Spediteur
- Herkunft / Ziel
- Transportiertes Gewicht (Tonnen)
- Entfernung (km)
- Transportmodus

**JSON-Antwortformat:**
```json
{
    "document_type": "...",
    "extracted_data": {
        // Felder je nach Typ
    },
    "suggested_category": "X.X",
    "confidence": 0.0-1.0,
    "missing_data": ["Liste fehlender Infos"]
}
```

{$ctx['respond']}
PROMPT;
    }

    /**
     * Get category label from code (localized).
     */
    private static function getCategoryLabel(string $code, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $categories = match ($locale) {
            'de' => [
                '1.1' => 'Stationäre Verbrennung',
                '1.2' => 'Mobile Verbrennung',
                '1.4' => 'Flüchtige Emissionen',
                '1.5' => 'Biomasse (Böden und Wälder)',
                '2.1' => 'Stromverbrauch',
                '3.1' => 'Vorgelagerter Gütertransport',
                '3.2' => 'Nachgelagerter Gütertransport',
                '3.3' => 'Pendeln der Mitarbeiter',
                '3.5' => 'Geschäftsreisen',
                '4.1' => 'Eingekaufte Güter',
                '4.2' => 'Kapitalgüter',
                '4.3' => 'Abfallentsorgung',
                '4.4' => 'Vorgelagertes Leasing',
                '4.5' => 'Eingekaufte Dienstleistungen',
            ],
            'en' => [
                '1.1' => 'Stationary combustion',
                '1.2' => 'Mobile combustion',
                '1.4' => 'Fugitive emissions',
                '1.5' => 'Biomass (soils and forests)',
                '2.1' => 'Electricity consumption',
                '3.1' => 'Upstream transportation',
                '3.2' => 'Downstream transportation',
                '3.3' => 'Employee commuting',
                '3.5' => 'Business travel',
                '4.1' => 'Purchased goods',
                '4.2' => 'Capital goods',
                '4.3' => 'Waste disposal',
                '4.4' => 'Upstream leased assets',
                '4.5' => 'Purchased services',
            ],
            default => [ // 'fr'
                '1.1' => 'Sources fixes de combustion',
                '1.2' => 'Sources mobiles de combustion',
                '1.4' => 'Émissions fugitives',
                '1.5' => 'Biomasse (sols et forêts)',
                '2.1' => 'Consommation d\'électricité',
                '3.1' => 'Transport de marchandise amont',
                '3.2' => 'Transport de marchandise aval',
                '3.3' => 'Déplacements domicile-travail',
                '3.5' => 'Déplacements professionnels',
                '4.1' => 'Achats de biens',
                '4.2' => 'Immobilisations de biens',
                '4.3' => 'Gestion des déchets',
                '4.4' => 'Actifs en leasing amont',
                '4.5' => 'Achats de services',
            ],
        };

        $unknownLabel = match ($locale) {
            'de' => 'Unbekannte Kategorie',
            'en' => 'Unknown category',
            default => 'Catégorie inconnue',
        };

        return $categories[$code] ?? $unknownLabel;
    }

    /**
     * Get all available prompt types (localized).
     */
    public static function getAvailablePrompts(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'de' => [
                'emission_entry' => 'Hilfe bei der Emissionserfassung',
                'action_recommendation' => 'Maßnahmenempfehlungen',
                'factor_explanation' => 'Faktorerklärung',
                'report_narrative' => 'Berichtsnarratif',
                'general_helper' => 'Allgemeine Hilfe',
                'transaction_categorization' => 'Transaktionskategorisierung',
                'document_extraction' => 'Dokumentenextraktion',
            ],
            'en' => [
                'emission_entry' => 'Emission entry assistance',
                'action_recommendation' => 'Action recommendations',
                'factor_explanation' => 'Factor explanation',
                'report_narrative' => 'Report narrative',
                'general_helper' => 'General help',
                'transaction_categorization' => 'Transaction categorization',
                'document_extraction' => 'Document extraction',
            ],
            default => [ // 'fr'
                'emission_entry' => 'Aide à la saisie des émissions',
                'action_recommendation' => 'Recommandations d\'actions',
                'factor_explanation' => 'Explication des facteurs',
                'report_narrative' => 'Narratif de rapport',
                'general_helper' => 'Aide générale',
                'transaction_categorization' => 'Catégorisation des transactions',
                'document_extraction' => 'Extraction de documents',
            ],
        };
    }
}
