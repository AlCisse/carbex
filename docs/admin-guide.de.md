# Carbex Administratorhandbuch

> Technische Dokumentation für Carbex-Plattformadministratoren

---

## Inhaltsverzeichnis

1. [Admin-Panel-Zugang](#admin-panel-zugang)
2. [Admin-Dashboard](#admin-dashboard)
3. [Organisationsverwaltung](#organisationsverwaltung)
4. [Benutzerverwaltung](#benutzerverwaltung)
5. [Standortverwaltung](#standortverwaltung)
6. [KI-Konfiguration](#ki-konfiguration)
7. [Emissionsfaktoren](#emissionsfaktoren)
8. [Abonnements und Abrechnung](#abonnements-und-abrechnung)
9. [Inhalte (Blog)](#inhalte-blog)
10. [Monitoring und Logs](#monitoring-und-logs)
11. [Wartung](#wartung)

---

## Admin-Panel-Zugang

### Zugangs-URL

```
Produktion : https://carbex.app/admin
Staging    : https://staging.carbex.app/admin
Lokal      : http://localhost:8000/admin
```

### Authentifizierung

1. Gehen Sie zu `/admin/login`
2. Geben Sie Ihre Admin-Zugangsdaten ein
3. Schließen Sie die 2FA-Verifizierung ab, falls aktiviert

### Administratorrollen

| Rolle | Rechte |
|-------|--------|
| Super Admin | Vollzugriff, Systemkonfiguration |
| Admin | Organisations-, Benutzer-, Inhaltsverwaltung |
| Support | Nur Lesezugriff, Benutzerunterstützung |

---

## Admin-Dashboard

### Globale Metriken

| Metrik | Beschreibung |
|--------|--------------|
| **Aktive Organisationen** | Organisationen mit aktueller Aktivität |
| **Gesamtbenutzer** | Gesamtzahl registrierter Benutzer |
| **Berechnete Emissionen** | Gesamt-CO₂e auf der Plattform verarbeitet |
| **KI-Anfragen** | Anzahl der KI-Anfragen diesen Monat |

### Diagramme

- **Registrierungen**: Neue Organisationen pro Woche
- **KI-Nutzung**: Anfragen nach Anbieter (Claude, GPT, Gemini)
- **MRR-Umsatz**: Monatlich wiederkehrender Umsatz nach Plan
- **Emissionen**: Gesamtvolumen pro Monat verarbeitet

### Warnungen

- Organisationen mit erschöpftem KI-Kontingent
- Abgelaufene Bankverbindungen
- Synchronisierungsfehler
- Benutzer inaktiv seit 30+ Tagen

---

## Organisationsverwaltung

### Organisationsliste

**Menü**: Administration → Organisationen

| Spalte | Beschreibung |
|--------|--------------|
| Name | Organisationsname |
| Plan | Aktuelles Abonnement |
| Benutzer | Anzahl der Mitglieder |
| Emissionen | Gesamt berechnetes CO₂e |
| Status | Aktiv / Gesperrt / Testversion |
| Erstellt | Registrierungsdatum |

### Verfügbare Aktionen

- **Anzeigen**: Vollständige Organisationsdetails
- **Bearbeiten**: Informationen und Plan ändern
- **Identität annehmen**: Als Benutzer anmelden
- **Sperren**: Zugang vorübergehend blockieren
- **Löschen**: Endgültige Löschung (Soft Delete)

### Organisation erstellen

1. Klicken Sie auf **Neue Organisation**
2. Füllen Sie aus:
   - Organisationsname
   - E-Mail des Inhabers
   - Abonnementplan
   - Land (DE/FR/UK)
   - Branche
3. Klicken Sie auf **Erstellen**

Der Inhaber erhält eine Einladungs-E-Mail.

### Organisationsdetails

#### Registerkarte Informationen

- Rechtliche Daten (USt-IdNr., Steuernummer)
- Adresse
- Branche
- Erstellungsdatum
- CO₂-Referenzjahr

#### Registerkarte Benutzer

Liste der Mitglieder mit Rollen:
- Inhaber
- Administrator
- Bearbeiter
- Betrachter

#### Registerkarte Standorte

Liste der Organisationsstandorte mit:
- Name und Typ
- Adresse
- Fläche und Mitarbeiterzahl
- Zugeordnete Emissionen

#### Registerkarte Abonnement

- Aktueller Plan
- Start-/Enddatum
- Zahlungshistorie
- Kontingentnutzung

#### Registerkarte Aktivität

Audit-Log:
- Anmeldungen
- Änderungen
- Datenexporte
- KI-Anfragen

---

## Benutzerverwaltung

### Benutzerliste

**Menü**: Administration → Benutzer

| Spalte | Beschreibung |
|--------|--------------|
| Name | Vollständiger Name |
| E-Mail | E-Mail-Adresse |
| Organisation | Zugeordnete Organisation |
| Rolle | Rolle in der Organisation |
| Letzte Anmeldung | Datum/Uhrzeit |
| Status | Aktiv / Inaktiv / Gesperrt |

### Filter

- Nach Organisation
- Nach Rolle
- Nach Status
- Nach Registrierungsdatum

### Benutzeraktionen

- **Passwort zurücksetzen**: Sendet Reset-E-Mail
- **E-Mail verifizieren**: Verifizierung erzwingen
- **Identität annehmen**: Als Benutzer anmelden
- **Sperren**: Zugang dauerhaft blockieren
- **Löschen**: Konto löschen

### Identitätsannahme

Um ein Benutzerproblem zu debuggen:

1. Klicken Sie auf das "Identität annehmen"-Symbol
2. Sie sind als Benutzer angemeldet
3. Orangefarbenes Banner zeigt Identitätsannahme an
4. Klicken Sie auf "Zurück zu Admin" zum Beenden

> **Warnung**: Alle Aktionen werden mit Vermerk der Identitätsannahme protokolliert.

---

## Standortverwaltung

### Standortliste

**Menü**: Administration → Standorte

| Spalte | Beschreibung |
|--------|--------------|
| Name | Standortname |
| Organisation | Eigentümerorganisation |
| Typ | Zentrale, Büro, Fabrik usw. |
| Land | Standort |
| Emissionen | Gesamt-CO₂e |

### Standorttypen

| Typ | Code | Beschreibung |
|-----|------|--------------|
| Zentrale | `headquarters` | Hauptbüro |
| Büro | `office` | Verwaltungsstandort |
| Fabrik | `factory` | Produktionsstandort |
| Lager | `warehouse` | Lagerung/Logistik |
| Geschäft | `store` | Verkaufsstelle |
| Rechenzentrum | `datacenter` | IT-Infrastruktur |
| Sonstiges | `other` | Nicht kategorisiert |

### Standortvalidierung

Überprüfen Sie ausstehende Standorte:
- Gültige Adresse
- Flächen-/Mitarbeiterkonsistenz
- Keine Duplikate

---

## KI-Konfiguration

### Zugang

**Menü**: Einstellungen → KI-Konfiguration

### Standardanbieter

Wählen Sie den Haupt-KI-Anbieter:
- **Anthropic (Claude)** - Empfohlen
- **OpenAI (GPT)**
- **Google (Gemini)**
- **DeepSeek**

### Anbieterkonfiguration

#### Anthropic (Claude)

| Parameter | Beschreibung |
|-----------|--------------|
| Aktivieren | Ein-/Ausschalten |
| Modell | Claude Sonnet 4, Claude 3.5 usw. |
| Status | Schlüssel konfiguriert oder nicht |

#### OpenAI (GPT)

| Parameter | Beschreibung |
|-----------|--------------|
| Aktivieren | Ein-/Ausschalten |
| Modell | GPT-4o, GPT-4o Mini, o1 usw. |
| Status | Schlüssel konfiguriert oder nicht |

#### Google (Gemini)

| Parameter | Beschreibung |
|-----------|--------------|
| Aktivieren | Ein-/Ausschalten |
| Modell | Gemini 2.0 Flash, Gemini 1.5 Pro |
| Status | Schlüssel konfiguriert oder nicht |

#### DeepSeek

| Parameter | Beschreibung |
|-----------|--------------|
| Aktivieren | Ein-/Ausschalten |
| Modell | DeepSeek Chat, DeepSeek Coder |
| Status | Schlüssel konfiguriert oder nicht |

### Erweiterte Einstellungen

| Parameter | Standardwert | Beschreibung |
|-----------|--------------|--------------|
| Max Tokens | 4096 | Token-Limit pro Antwort |
| Temperatur | 0.7 | 0.0 = deterministisch, 1.0 = kreativ |

### Modelle nach Abonnement

Konfigurieren Sie das KI-Modell für jeden Plan:

| Plan | Tokens/Monat | Anfragen/Monat | Standardmodell |
|------|--------------|----------------|----------------|
| Kostenlos | 50K | 100 | Gemini 2.0 Flash Lite |
| Starter | 200K | 500 | GPT-4o Mini |
| Professional | 1M | 2500 | Claude Sonnet 4 |
| Enterprise | Unbegrenzt | Unbegrenzt | Claude Sonnet 4 |

Zum Ändern:
1. Wählen Sie das Modell im Plan-Dropdown
2. Änderungen werden automatisch gespeichert
3. Bestätigungsbenachrichtigung erscheint

### API-Schlüssel

#### Schlüssel hinzufügen

1. Geben Sie den Schlüssel im Anbieterfeld ein
2. Klicken Sie auf **Speichern**
3. Anbieter wird automatisch aktiviert

#### Verbindung testen

1. Klicken Sie auf **Testen** neben dem Anbieter
2. Ein Testaufruf wird durchgeführt
3. Erfolgs-/Fehlerbenachrichtigung

#### Schlüssel entfernen

1. Klicken Sie auf **Löschen**
2. Bestätigen Sie die Löschung
3. Anbieter wird deaktiviert

### Schlüsselsicherheit

> API-Schlüssel werden vor der Datenbankspeicherung mit AES-256 verschlüsselt. Sie werden niemals im Klartext in der Oberfläche angezeigt.

---

## Emissionsfaktoren

### Zugang

**Menü**: Carbon Data → Emissionsfaktoren

### Verfügbare Quellen

| Quelle | Land | Kategorien |
|--------|------|------------|
| ADEME | Frankreich | Energie, Transport, Einkäufe |
| UBA | Deutschland | Energie, Industrie |
| GHG Protocol | International | Alle Scopes |
| DEFRA | UK | Energie, Transport |

### Faktorstruktur

| Feld | Beschreibung |
|------|--------------|
| Name | Faktorbezeichnung |
| Kategorie | Scope und Unterkategorie |
| Wert | kgCO₂e pro Einheit |
| Einheit | kWh, km, €, kg usw. |
| Quelle | Ursprungsdatenbank |
| Jahr | Referenzjahr |
| Unsicherheit | % Unsicherheit |

### Faktoren importieren

1. Menü **Emissionsfaktoren** → **Importieren**
2. Quelle auswählen (ADEME, UBA usw.)
3. Jahr auswählen
4. Klicken Sie auf **Importieren**

### Faktoren aktualisieren

ADEME-Faktoren werden jährlich aktualisiert:

```bash
php artisan db:seed --class=AdemeFactorSeeder
```

### Benutzerdefinierte Faktoren

Für Enterprise-Kunden:
1. Erstellen Sie einen neuen Faktor
2. Geben Sie spezifische Werte ein
3. Ordnen Sie der betreffenden Organisation zu

---

## Abonnements und Abrechnung

### Zugang

**Menü**: Finanzen → Abonnements

### Verfügbare Pläne

| Plan | Preis/Monat | Funktionen |
|------|-------------|------------|
| Kostenlos | 0€ | Eingeschränkt, 1 Benutzer |
| Starter | 49€ | 3 Benutzer, 2 Standorte |
| Professional | 149€ | 10 Benutzer, unbegrenzte Standorte |
| Enterprise | Auf Anfrage | Alles unbegrenzt, dedizierter Support |

### Abonnementverwaltung

#### Abonnement anzeigen

- Aktueller Plan
- Startdatum
- Nächste Abrechnung
- Zahlungsmethoden

#### Plan ändern

1. Wählen Sie die Organisation
2. Klicken Sie auf **Plan ändern**
3. Wählen Sie neuen Plan
4. Bestätigen (Prorata automatisch berechnet)

#### Abonnement kündigen

1. Klicken Sie auf **Kündigen**
2. Wählen Sie den Grund
3. Zugang bleibt bis Periodenende aktiv

### Stripe-Abrechnung

Zahlungen werden über Stripe verwaltet:

- Kreditkarten (Visa, Mastercard)
- SEPA (Lastschrift)
- Automatische Rechnungen

### Gutscheine und Rabatte

1. Menü **Gutscheine** → **Neu**
2. Gutscheincode
3. Typ (% oder Festbetrag)
4. Gültigkeitsdauer
5. Nutzungslimit

---

## Inhalte (Blog)

### Zugang

**Menü**: Inhalte → Blog-Artikel

### Artikel erstellen

1. Klicken Sie auf **Neuer Artikel**
2. Füllen Sie aus:
   - Titel
   - Slug (URL)
   - Inhalt (Markdown-Editor)
   - Titelbild
   - Kategorie
   - Tags
   - Meta-Beschreibung (SEO)
3. Status: Entwurf oder Veröffentlicht
4. Veröffentlichungsdatum (Planung möglich)

### Kategorien

- Neuigkeiten
- Vorschriften (CSRD, GHG Protocol)
- Praktische Anleitungen
- Fallstudien
- Webinare

### SEO

Jeder Artikel enthält:
- Meta-Titel
- Meta-Beschreibung
- Open Graph-Bild
- Kanonische URL

---

## Monitoring und Logs

### Audit-Log

**Menü**: Monitoring → Audit-Log

Alle Aktionen werden verfolgt:
- Benutzer
- Aktion (erstellen, aktualisieren, löschen)
- Betroffene Ressource
- Vorher-/Nachher-Daten
- IP und User-Agent
- Zeitstempel

### Audit-Filter

- Nach Benutzer
- Nach Aktionstyp
- Nach Ressource
- Nach Zeitraum

### Systemfehler

**Menü**: Monitoring → Fehler

Sentry-Integration für:
- PHP-Exceptions
- JavaScript-Fehler
- API-Timeouts
- Queue-Fehler

### Metriken

| Metrik | Beschreibung |
|--------|--------------|
| Antwortzeit | Durchschnittliche Anfragelatenz |
| Fehlerrate | % fehlgeschlagener Anfragen |
| Queue-Jobs | Ausstehende/verarbeitete Jobs |
| Cache-Trefferquote | Redis-Cache-Effizienz |

---

## Wartung

### Nützliche Artisan-Befehle

```bash
# Alle Caches leeren
php artisan optimize:clear

# Cache neu aufbauen
php artisan optimize

# Suche neu indizieren
php artisan scout:flush "App\Models\Transaction"
php artisan scout:import "App\Models\Transaction"

# Ausstehende Jobs verarbeiten
php artisan queue:work

# Fehlgeschlagene Jobs anzeigen
php artisan queue:failed

# Fehlgeschlagene Jobs wiederholen
php artisan queue:retry all
```

### Geplante Aufgaben

| Aufgabe | Häufigkeit | Beschreibung |
|---------|------------|--------------|
| `bank:sync` | Alle 6h | Banksynchronisierung |
| `reports:generate` | Täglich | Geplante Berichte |
| `subscriptions:check` | Täglich | Ablaufprüfung |
| `cleanup:temp` | Wöchentlich | Temp-Dateien bereinigen |
| `backup:run` | Täglich | Datenbank-Backup |

### Backups

Backups sind automatisch:
- **Datenbank**: Täglich, 30 Tage Aufbewahrung
- **Dateien**: Wöchentlich, 4 Wochen Aufbewahrung
- **Speicher**: OVH Object Storage (S3-kompatibel)

Wiederherstellung:
```bash
php artisan backup:list
php artisan backup:restore --backup=backup-2026-01-18.zip
```

### Wartungsmodus

Aktivieren:
```bash
php artisan down --secret="admin-secret-token"
```

Admin-Zugang während Wartung:
```
https://carbex.app/admin-secret-token
```

Deaktivieren:
```bash
php artisan up
```

---

## Technische Kontakte

### Team

| Rolle | Kontakt |
|-------|---------|
| Lead Dev | dev@carbex.app |
| DevOps | ops@carbex.app |
| N2 Support | support@carbex.app |

### Eskalation

1. **N1**: Kundensupport (Chat, E-Mail)
2. **N2**: Technischer Support (Bugs, Konfiguration)
3. **N3**: Entwicklung (kritische Vorfälle)

### Technische Dokumentation

- [Entwicklerhandbuch](./DEVELOPER_GUIDE.md)
- [API-Referenz](./api/README.md)
- [Architekturentscheidungen](./adr/README.md)
- [Deployment-Runbook](./deployment-runbook.md)

---

*Zuletzt aktualisiert: Januar 2026*
*Version: 1.0*
