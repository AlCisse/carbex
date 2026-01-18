# Carbex Benutzerhandbuch

> CO₂-Bilanz-Plattform für europäische KMU

---

## Inhaltsverzeichnis

1. [Schnellstart](#schnellstart)
2. [Dashboard](#dashboard)
3. [Standorte und Geltungsbereich](#standorte-und-geltungsbereich)
4. [Transaktionen und Daten](#transaktionen-und-daten)
5. [Bankverbindungen](#bankverbindungen)
6. [KI-Assistent](#ki-assistent)
7. [Berichte und Exporte](#berichte-und-exporte)
8. [Lieferanten](#lieferanten)
9. [Kontoeinstellungen](#kontoeinstellungen)
10. [FAQ](#faq)

---

## Schnellstart

### Kontoerstellung

1. Besuchen Sie [carbex.app](https://carbex.app)
2. Klicken Sie auf **Konto erstellen**
3. Geben Sie Ihre Daten ein:
   - Geschäftliche E-Mail
   - Passwort (mind. 8 Zeichen)
   - Organisationsname
4. Akzeptieren Sie die Nutzungsbedingungen
5. Bestätigen Sie Ihre E-Mail über den erhaltenen Link

### Erstkonfiguration

Nach der Anmeldung führt Sie der Einrichtungsassistent:

1. **Organisationsdaten**: Name, USt-IdNr., Branche
2. **Referenzjahr**: Wählen Sie Ihr erstes Bilanzjahr
3. **Erster Standort**: Fügen Sie Ihren Hauptstandort hinzu (Zentrale, Fabrik, Büro)
4. **Bankverbindung** (optional): Verbinden Sie Konten für automatischen Import

---

## Dashboard

Das Dashboard zeigt eine Übersicht Ihres CO₂-Fußabdrucks.

### Wichtige Kennzahlen

| Kennzahl | Beschreibung |
|----------|--------------|
| **Gesamtemissionen** | Gesamt-CO₂e für ausgewählten Zeitraum |
| **Scope 1** | Direkte Emissionen (Fahrzeuge, Heizung) |
| **Scope 2** | Indirekte Energieemissionen (Strom) |
| **Scope 3** | Sonstige indirekte Emissionen (Einkäufe, Reisen) |

### Verfügbare Diagramme

- **Monatliche Entwicklung**: Verfolgen Sie Ihre Emissionen Monat für Monat
- **Scope-Verteilung**: Kreisdiagramm der 3 Scopes
- **Top-Kategorien**: Ihre wichtigsten Emissionsquellen
- **Jahresvergleich**: Entwicklung zum Vorjahr

### Filter

- Nach Zeitraum (Monat, Quartal, Jahr)
- Nach Standort
- Nach Scope (1, 2, 3)
- Nach Emissionskategorie

---

## Standorte und Geltungsbereich

### Standort hinzufügen

1. Menü **Standorte** → **Standort hinzufügen**
2. Füllen Sie aus:
   - Standortname
   - Vollständige Adresse
   - Typ (Zentrale, Büro, Fabrik, Lager, Geschäft)
   - Fläche (m²)
   - Mitarbeiteranzahl
3. Klicken Sie auf **Speichern**

### Unterstützte Standorttypen

| Typ | Beschreibung | Typische Daten |
|-----|--------------|----------------|
| Zentrale | Hauptbüro | Strom, Heizung, Reisen |
| Büro | Verwaltungsstandort | Strom, IT |
| Fabrik | Produktionsstandort | Energie, Prozesse, Rohstoffe |
| Lager | Lagerung/Logistik | Strom, Handling |
| Geschäft | Verkaufsstelle | Strom, Klimaanlage |

### Organisatorischer Geltungsbereich

Carbex unterstützt zwei Ansätze:

- **Operative Kontrolle**: 100% der Emissionen von kontrollierten Standorten
- **Kapitalanteil**: Emissionen proportional zu Ihrer Beteiligung

---

## Transaktionen und Daten

### Manueller Import

1. Menü **Transaktionen** → **Importieren**
2. Laden Sie die Excel-Vorlage herunter
3. Füllen Sie Ihre Daten aus:
   - Datum
   - Beschreibung
   - Betrag
   - Kategorie
   - Zugehöriger Standort
4. Laden Sie die ausgefüllte Datei hoch

### Emissionskategorien

#### Scope 1 - Direkte Emissionen

| Kategorie | Beispiele |
|-----------|-----------|
| Stationäre Verbrennung | Gasheizung, Heizöl |
| Mobile Verbrennung | Firmenfahrzeuge |
| Flüchtige Emissionen | Klimaanlage (Leckagen) |
| Industrieprozesse | Chemische Reaktionen |

#### Scope 2 - Indirekte Energie

| Kategorie | Beispiele |
|-----------|-----------|
| Strom | Stromverbrauch |
| Wärme/Dampf | Fernwärme |
| Kälte | Fernkälte |

#### Scope 3 - Sonstige Indirekte

| Kategorie | Beispiele |
|-----------|-----------|
| Eingekaufte Waren | Rohstoffe, Verbrauchsmaterial |
| Eingekaufte Dienstleistungen | Beratung, IT, Reinigung |
| Geschäftsreisen | Flüge, Züge, Hotels |
| Pendeln der Mitarbeiter | Mitarbeiterfahrzeuge |
| Vorgelagerter Transport | Lieferantenlieferungen |
| Nachgelagerter Transport | Kundenlieferungen |
| Abfall | Behandlung, Recycling |
| Kapitalgüter | Ausrüstung, Gebäude |

### Manuelle Eingabe

Um eine Transaktion hinzuzufügen:

1. Menü **Transaktionen** → **Neue Transaktion**
2. Füllen Sie die Felder aus:
   - Transaktionsdatum
   - Beschreibung
   - Betrag (€)
   - Emissionskategorie
   - Zugehöriger Standort
   - Lieferant (optional)
3. Klicken Sie auf **Speichern**

Die Emissionsberechnung erfolgt automatisch basierend auf Emissionsfaktoren.

---

## Bankverbindungen

### Unterstützte Banken

#### Frankreich (über Bridge)
- Crédit Agricole, BNP Paribas, Société Générale
- Crédit Mutuel, CIC, Banque Populaire
- Caisse d'Épargne, LCL, Boursorama
- Und 350+ weitere Institute

#### Deutschland (über FinAPI)
- Deutsche Bank, Commerzbank
- Sparkasse, Volksbank
- N26, DKB, ING
- Und 3000+ weitere Institute

### Bank verbinden

1. Menü **Verbindungen** → **Bank hinzufügen**
2. Suchen Sie Ihre Bank
3. Authentifizieren Sie sich (sichere Weiterleitung)
4. Wählen Sie zu synchronisierende Konten
5. Bestätigen Sie die Verbindung

### Synchronisierung

- **Automatisch**: Alle 6 Stunden
- **Manuell**: "Synchronisieren"-Button jederzeit
- **Historie**: Import der letzten 90 Tage bei Verbindung

### Automatische Kategorisierung

Carbex kategorisiert Ihre Transaktionen automatisch:

- **Integrierte KI**: Analysiert Beschreibung und Händler
- **MCC-Codes**: Klassifizierung nach Händlercode
- **Lernen**: Verbessert sich durch Ihre Korrekturen

Um eine Kategorie zu korrigieren:
1. Klicken Sie auf die Transaktion
2. Ändern Sie die Kategorie
3. Aktivieren Sie "Auf alle ähnlichen Transaktionen anwenden" (optional)

---

## KI-Assistent

### Funktionen

Der Carbex KI-Assistent hilft Ihnen:

- **Analysieren** Ihrer CO₂-Daten
- **Beantworten** Ihrer Compliance-Fragen
- **Vorschlagen** von Reduktionsmaßnahmen
- **Erklären** von Vorschriften (CSRD, GHG Protocol)
- **Extrahieren** von Daten aus Ihren Dokumenten

### Verwendung

1. Klicken Sie auf das Assistenten-Symbol (Sprechblase unten rechts)
2. Stellen Sie Ihre Frage in natürlicher Sprache
3. Der Assistent antwortet mit kontextbezogenen Daten

### Beispielfragen

```
"Was sind meine wichtigsten Emissionsquellen?"
"Wie kann ich meinen Scope 3 reduzieren?"
"Erkläre die CSRD-Anforderungen"
"Analysiere meine Emissionen vom letzten Quartal"
"Welche Lieferanten haben den größten Einfluss?"
```

### Dokumentenextraktion

1. Menü **Dokumente** → **Importieren**
2. Ziehen Sie Ihre Dateien per Drag & Drop (PDF, Bilder)
3. KI extrahiert automatisch:
   - Energierechnungen (kWh, m³)
   - Kraftstoffbelege (Liter)
   - Lieferantenrechnungen (Beträge, Beschreibungen)

### Kontingente nach Abonnement

| Plan | KI-Anfragen | Modell |
|------|-------------|--------|
| Kostenlos | 100/Monat | Gemini Flash Lite |
| Starter | 500/Monat | GPT-4o Mini |
| Professional | 2500/Monat | Claude Sonnet 4 |
| Enterprise | Unbegrenzt | Claude Sonnet 4 |

---

## Berichte und Exporte

### Berichtstypen

#### CO₂-Bilanz-Bericht (PDF)

Vollständiger Bericht mit:
- Emissionsübersicht nach Scope
- Verteilungsdiagramme
- Detail nach Kategorie
- Zeitliche Entwicklung
- Empfehlungen

#### GHG Protocol Bericht

Internationales Standardformat:
- Scope 1, 2, 3 Aufschlüsselung
- Verwendete Emissionsfaktoren
- Berechnungsmethodik
- Unsicherheitsanalyse

#### CSRD-Bericht (Europa)

ESRS-Standards für europäische Unternehmen:
- ESRS E1 - Klimawandel
- Leistungsindikatoren
- Reduktionsziele
- Sorgfaltspflicht

#### Excel-Export

Rohdaten für Analyse:
- Detaillierte Transaktionen
- Emissionen nach Kategorie
- Verwendete Emissionsfaktoren
- GHG Protocol kompatibles Format

### Bericht erstellen

1. Menü **Berichte** → **Neuer Bericht**
2. Wählen Sie den Typ
3. Wählen Sie den Zeitraum
4. Wählen Sie Standorte
5. Klicken Sie auf **Erstellen**
6. Herunterladen (PDF, Word oder Excel)

### Planung

Planen Sie automatische Berichte:
- Häufigkeit: monatlich, vierteljährlich, jährlich
- Empfänger: E-Mails Ihres Teams
- Format: PDF oder Excel

---

## Lieferanten

### Lieferantenportal

Laden Sie Ihre Lieferanten ein, ihre CO₂-Daten zu teilen:

1. Menü **Lieferanten** → **Einladen**
2. Geben Sie E-Mail und Namen des Lieferanten ein
3. Lieferant erhält eingeschränkten Zugang
4. Er gibt seine Emissionsdaten ein
5. Sie erhalten die Daten automatisch

### Erfasste Daten

- Emissionen pro verkaufter Einheit
- Spezifische Emissionsfaktoren
- Umweltzertifizierungen
- Reduktionsziele

### Vorteile

- **Genauigkeit**: Echte Daten statt Schätzungen
- **Engagement**: Beziehen Sie Ihre Wertschöpfungskette ein
- **Compliance**: CSRD-Anforderung für Scope 3

---

## Kontoeinstellungen

### Benutzerprofil

- Name, Vorname, E-Mail
- Sprache (DE/EN/FR)
- Zeitzone
- Benachrichtigungen

### Organisation

- Rechtliche Informationen
- Logo
- Referenzjahr
- Branche

### Team

Laden Sie Mitarbeiter ein:

| Rolle | Rechte |
|-------|--------|
| Administrator | Vollzugriff |
| Bearbeiter | Eingabe und Änderung |
| Betrachter | Nur Ansicht |

### Abonnement

Verwalten Sie Ihr Abonnement:
- Aktueller Plan und Nutzung
- Rechnungshistorie
- Planwechsel
- Zahlungsmethoden

### KI-Assistent

Konfigurieren Sie Ihre KI-Präferenzen:
- Bevorzugtes Modell
- Antwortsprache
- Detailgrad

---

## FAQ

### Allgemein

**F: Ist Carbex DSGVO-konform?**
> Ja, Ihre Daten werden in Europa (OVH Frankreich) gehostet und verschlüsselt. Sie können Ihre Daten jederzeit exportieren oder löschen.

**F: Kann ich kostenlos testen?**
> Ja, der kostenlose Plan ermöglicht Tests mit 100 KI-Anfragen/Monat und allen Grundfunktionen.

### Daten

**F: Woher kommen die Emissionsfaktoren?**
> Wir verwenden offizielle Datenbanken: ADEME (Frankreich), UBA (Deutschland), GHG Protocol (international).

**F: Wie werden Emissionen berechnet?**
> Emissionen (kgCO₂e) = Aktivitätsdaten × Emissionsfaktor. Beispiel: 100 kWh × 0,052 = 5,2 kgCO₂e

**F: Kann ich eigene Faktoren verwenden?**
> Ja, Professional- und Enterprise-Pläne erlauben die Anpassung von Faktoren.

### Banking

**F: Sind meine Bankdaten sicher?**
> Ja, wir verwenden PSD2-zertifizierte Aggregatoren (Bridge, FinAPI). Wir haben niemals Zugriff auf Ihre Zugangsdaten.

**F: Kann ich meine Bank trennen?**
> Ja, Menü Verbindungen → klicken Sie auf die Bank → Trennen.

### Berichte

**F: Sind Berichte rechtlich gültig?**
> Unsere Berichte folgen regulatorischen Formaten (GHG Protocol, CSRD). Wir empfehlen eine Prüfung durch Dritte für gesetzliche Verpflichtungen.

**F: Kann ich Berichte anpassen?**
> Ja, fügen Sie Ihr Logo hinzu und passen Sie Abschnitte in Einstellungen → Berichte an.

---

## Support

### Kontakt

- **E-Mail**: support@carbex.app
- **Chat**: Symbol unten rechts (Geschäftszeiten)
- **Dokumentation**: docs.carbex.app

### Ressourcen

- [Hilfezentrum](https://help.carbex.app)
- [Video-Tutorials](https://carbex.app/tutorials)
- [Blog](https://carbex.app/blog)
- [Webinare](https://carbex.app/webinars)

---

*Zuletzt aktualisiert: Januar 2026*
*Version: 1.0*
