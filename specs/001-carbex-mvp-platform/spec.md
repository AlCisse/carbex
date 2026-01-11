# Feature Specification: Carbex MVP - Plateforme SaaS Bilan Carbone Automatique

**Feature Branch**: `001-carbex-mvp-platform`
**Created**: 2025-12-28
**Status**: Draft
**Input**: Plateforme SaaS de Bilan Carbone Automatique pour PME Europeennes avec Zero-Input Carbon via Open Banking, IA categorisation, et reporting multi-pays (FR/DE)

---

## Executive Summary

Carbex is a SaaS platform that automates carbon footprint calculation for European SMEs (10-250 employees) subject to CSRD, BEGES, and German sustainability reporting requirements. The platform's core innovation is "Zero-Input Carbon" - automatic data collection via Open Banking (PSD2), accounting software integrations, and AI-powered transaction categorization, reducing manual data entry from 80% to under 20%.

**Target Markets**: France (BEGES compliance) and Germany (CSRD compliance)
**Core Value Proposition**: First carbon footprint in 2 hours (vs. 2-4 weeks traditional)

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Organization Onboarding & Setup (Priority: P1)

A company administrator creates an account, sets up their organization with basic information (company name, country, sector, number of employees), and invites team members with appropriate roles.

**Why this priority**: Without organization setup, no other features can function. This is the entry point for all users and establishes the multi-tenant foundation.

**Independent Test**: Can be fully tested by creating an organization, configuring sites, and inviting users. Delivers immediate value as the user can start organizing their carbon data structure.

**Acceptance Scenarios**:

1. **Given** a new user visits the platform, **When** they complete registration with email verification, **Then** they can create their organization with required fields (name, country, sector, employee count)
2. **Given** an organization exists, **When** the admin creates a site (physical location), **Then** the site appears in their organization structure with country and address
3. **Given** an admin has organization access, **When** they invite a user with a specific role (Contributor/Reader), **Then** the invitee receives an email and can join with assigned permissions
4. **Given** a Contributor role user, **When** they access the platform, **Then** they can only view/edit data for their assigned sites (not billing or settings)

---

### User Story 2 - Zero-Input Bank Connection & Auto-Sync (Priority: P1)

A user connects their company bank account via Open Banking (Bridge for France, Finapi for Germany) to automatically import and categorize transactions for carbon calculation.

**Why this priority**: This is the core differentiating feature ("Zero-Input Carbon"). It delivers the main value proposition of automatic data collection.

**Independent Test**: Can be tested by connecting a bank account, syncing transactions, and viewing auto-categorized emission data in the dashboard.

**Acceptance Scenarios**:

1. **Given** an organization admin in France, **When** they initiate bank connection, **Then** they are redirected to Bridge OAuth flow and can authorize their business account
2. **Given** a bank account is connected, **When** the hourly sync job runs, **Then** new transactions since last sync are imported and normalized (date, amount, merchant, MCC code, description)
3. **Given** a transaction with MCC code 4511 (Airlines), **When** it is processed, **Then** it is automatically categorized as Scope 3 Cat. 6 (Business Travel) with calculated CO2e
4. **Given** a transaction with ambiguous merchant (e.g., "TOTAL"), **When** AI categorization runs, **Then** it assigns the most likely category with a confidence score
5. **Given** categorization confidence is below 0.9, **When** the transaction is saved, **Then** it is flagged for user validation with suggested category

---

### User Story 3 - Manual Data Entry & Import (Priority: P2)

A user manually enters emission data or imports bulk data from CSV/Excel/FEC files for sources that cannot be automatically connected.

**Why this priority**: Fallback for non-automated data sources. Essential for complete carbon footprint but secondary to automatic collection.

**Independent Test**: Can be tested by importing a CSV file with emission data and seeing it reflected in the dashboard.

**Acceptance Scenarios**:

1. **Given** a Contributor user, **When** they access the manual entry form, **Then** they can enter emission data with guided fields (activity type, quantity, unit, date, site)
2. **Given** a user uploads a valid CSV file with predefined columns, **When** import is processed, **Then** data is validated, mapped to emission categories, and added to the organization's records
3. **Given** a French organization uploads a FEC file (Fichier des Ecritures Comptables), **When** parsed successfully, **Then** accounting entries are categorized by expense type and converted to emissions
4. **Given** a user uploads a PDF invoice for energy, **When** OCR processing completes, **Then** extracted data (kWh, provider, period) is presented for confirmation before saving

---

### User Story 4 - Real-Time Dashboard & KPIs (Priority: P2)

A user views their organization's carbon footprint via an interactive dashboard showing total emissions, breakdown by scope (1/2/3), trends over time, and top emission sources.

**Why this priority**: Visual feedback is essential for user engagement and understanding their carbon impact. Supports decision-making.

**Independent Test**: Can be tested by viewing dashboard with sample data showing scope breakdown, trends, and top emission categories.

**Acceptance Scenarios**:

1. **Given** an organization with emission data, **When** a user opens the dashboard, **Then** they see total tCO2e, breakdown by Scope 1/2/3, and monthly trend chart
2. **Given** multiple sites exist, **When** viewing the dashboard, **Then** user can filter by site to see site-specific emissions
3. **Given** new transactions are synced, **When** dashboard refreshes, **Then** KPIs update to reflect new data within minutes
4. **Given** year-over-year data exists, **When** viewing comparison view, **Then** user sees N vs N-1 evolution with percentage change

---

### User Story 5 - Emission Calculation Engine (Priority: P1)

The system calculates carbon emissions using country-specific emission factors (ADEME for France, UBA for Germany) across all three GHG Protocol scopes.

**Why this priority**: Core calculation engine that transforms raw data into meaningful carbon metrics. Foundation for all reporting.

**Independent Test**: Can be tested by entering known activity data and verifying correct emission factor application and CO2e output.

**Acceptance Scenarios**:

1. **Given** electricity consumption data for a French site, **When** calculated, **Then** emissions use ADEME factor (0.052 kgCO2e/kWh) with location-based method
2. **Given** a fuel purchase transaction categorized as Scope 1, **When** processed, **Then** emissions are calculated using appropriate fuel emission factor for the country
3. **Given** a German organization, **When** Scope 2 electricity is calculated, **Then** UBA emission factor (0.362 kgCO2e/kWh) is applied
4. **Given** a business trip transaction (flight), **When** categorized, **Then** emissions are calculated under Scope 3 Category 6 using distance-based or spend-based factors

---

### User Story 6 - Transaction Validation & Override (Priority: P2)

A user reviews transactions flagged with low confidence, validates or corrects categories, and creates rules for recurring merchants.

**Why this priority**: Ensures data accuracy and enables user learning of the system. Improves AI model through feedback loop.

**Independent Test**: Can be tested by reviewing flagged transactions, correcting categories, and verifying rules apply to future transactions.

**Acceptance Scenarios**:

1. **Given** flagged transactions exist, **When** user opens validation queue, **Then** they see transactions sorted by confidence with suggested categories
2. **Given** a user validates a category for merchant "TOTAL", **When** saved with "remember" option, **Then** future transactions from this merchant auto-categorize correctly
3. **Given** a user overrides a category, **When** saved, **Then** emission calculation updates immediately to reflect new category

---

### User Story 7 - PDF Report Generation (Priority: P2)

A user generates a PDF summary report of their organization's carbon footprint for internal use or sharing with stakeholders.

**Why this priority**: Tangible deliverable users can share with management, investors, or auditors. Validates platform value.

**Independent Test**: Can be tested by generating a PDF report and verifying it contains accurate emission data, methodology, and visualizations.

**Acceptance Scenarios**:

1. **Given** an organization with complete emission data, **When** user requests PDF export, **Then** report generates with executive summary, methodology, scope breakdown, and top sources
2. **Given** report generation completes, **When** user downloads PDF, **Then** document includes organization branding, period covered, and data source attribution
3. **Given** a report is generated, **When** reviewed, **Then** it contains emission factors used and calculation methodology for audit trail

---

### User Story 8 - Subscription & Payment (Priority: P3)

An organization admin subscribes to a paid plan, manages billing information, and upgrades/downgrades their subscription.

**Why this priority**: Monetization is essential for business sustainability but not core to product experience. Can be delayed initially.

**Independent Test**: Can be tested by subscribing to a plan, making a payment, and verifying access to plan-specific features.

**Acceptance Scenarios**:

1. **Given** a user on trial, **When** they choose a plan (Starter/Pro/Business), **Then** they are redirected to secure payment flow and subscription activates on success
2. **Given** an active subscription, **When** user views billing section, **Then** they see current plan, next billing date, and invoice history
3. **Given** a Pro plan subscriber, **When** they access Zero-Input features, **Then** they can connect 1 bank account as per plan limits
4. **Given** subscription expires, **When** trial ends without payment, **Then** account enters read-only mode with data preserved

---

### User Story 9 - Commute Survey (Priority: P3)

An organization collects employee commute data via a simple survey to calculate Scope 3 Category 7 emissions (employee commuting).

**Why this priority**: Important for complete Scope 3 but can use aggregated estimates initially. Survey mechanism is complex.

**Independent Test**: Can be tested by sending survey to employees, collecting responses, and seeing commute emissions in dashboard.

**Acceptance Scenarios**:

1. **Given** an admin initiates commute survey, **When** employees receive the link, **Then** they can submit commute mode, distance, and frequency without authentication
2. **Given** survey responses are collected, **When** processed, **Then** commute emissions are calculated using mode-specific emission factors
3. **Given** partial survey response rate, **When** calculating emissions, **Then** system extrapolates based on response rate and employee count

---

### Edge Cases

- What happens when a bank connection expires or is revoked? System notifies admin and marks data as stale.
- How does system handle duplicate transactions from bank and accounting import? Deduplication based on date, amount, and merchant.
- What happens when a user uploads malformed CSV? System validates format, reports specific errors, and rejects invalid rows.
- How does system handle transactions in foreign currencies? Convert to organization's base currency using exchange rate at transaction date.
- What happens when emission factors are updated mid-year? Historical calculations remain unchanged; new calculations use updated factors with audit trail.
- How does system handle multi-site organizations with different countries? Each site inherits country-specific emission factors.

---

## Requirements *(mandatory)*

### Functional Requirements

#### Authentication & Authorization
- **FR-001**: System MUST allow user registration with email and password
- **FR-002**: System MUST verify email addresses before account activation
- **FR-003**: System MUST support password reset via email link
- **FR-004**: System MUST enforce role-based access control (Super Admin, Admin Org, Contributor, Reader)
- **FR-005**: System MUST restrict site-level access based on user role assignments

#### Organization Management
- **FR-006**: System MUST allow creation of organizations with name, country, sector, and employee count
- **FR-007**: System MUST support multiple sites per organization with location-specific settings
- **FR-008**: System MUST allow admin to invite users via email with role assignment
- **FR-009**: System MUST apply country-specific configurations (emission factors, regulatory templates) automatically

#### Zero-Input Data Collection
- **FR-010**: System MUST integrate with Open Banking providers (Bridge for FR, Finapi for DE)
- **FR-011**: System MUST store OAuth tokens securely and encrypted
- **FR-012**: System MUST sync bank transactions hourly for connected accounts
- **FR-013**: System MUST normalize transactions to unified format (date, amount, merchant, MCC, description)
- **FR-014**: System MUST categorize transactions using MCC code mapping (90% of cases)
- **FR-015**: System MUST use AI categorization for ambiguous transactions
- **FR-016**: System MUST assign confidence scores to all categorizations
- **FR-017**: System MUST flag transactions with confidence below 0.9 for manual validation

#### Manual Data Entry
- **FR-018**: System MUST provide guided forms for manual emission data entry
- **FR-019**: System MUST support CSV/Excel file import with predefined templates
- **FR-020**: System MUST support FEC file import for French accounting data
- **FR-021**: System MUST process invoice images via OCR for energy data extraction
- **FR-022**: System MUST validate imported data and report errors before saving

#### Emission Calculation
- **FR-023**: System MUST calculate Scope 1 emissions (direct combustion, fleet vehicles)
- **FR-024**: System MUST calculate Scope 2 emissions (electricity, heat)
- **FR-025**: System MUST calculate Scope 3 emissions (Categories 1, 6, 7 for MVP)
- **FR-026**: System MUST use country-specific emission factors (ADEME for FR, UBA for DE)
- **FR-027**: System MUST support location-based method for Scope 2 electricity
- **FR-028**: System MUST maintain audit trail of emission factors used per calculation

#### Dashboard & Visualization
- **FR-029**: System MUST display total emissions in tCO2e on dashboard
- **FR-030**: System MUST show scope breakdown (Scope 1/2/3) with percentages
- **FR-031**: System MUST display monthly and yearly trend charts
- **FR-032**: System MUST allow filtering by site, time period, and scope
- **FR-033**: System MUST update dashboard data within 5 minutes of new data entry/sync

#### Reporting
- **FR-034**: System MUST generate PDF summary reports with configurable sections
- **FR-035**: System MUST include methodology and emission factors in reports
- **FR-036**: Reports MUST include organization details, period, and scope breakdown

#### Subscription & Billing
- **FR-037**: System MUST offer tiered subscription plans (Starter, Pro, Business, Enterprise)
- **FR-038**: System MUST process payments securely
- **FR-039**: System MUST enforce plan-specific feature limits (sites, users, Zero-Input connections)
- **FR-040**: System MUST provide 14-day free trial for non-Enterprise plans
- **FR-041**: System MUST maintain access to data in read-only mode when subscription lapses

#### Internationalization
- **FR-042**: System MUST support French, German, and English languages
- **FR-043**: System MUST display currency, dates, and numbers in locale-appropriate formats

### Key Entities

- **Organization**: Company using the platform. Attributes: name, country, sector, employee count, subscription plan, created date
- **Site**: Physical location belonging to an organization. Attributes: name, address, country, organization reference
- **User**: Person with platform access. Attributes: email, name, role, organization reference, assigned sites
- **BankConnection**: OAuth link to external bank. Attributes: provider, status, last sync, organization reference
- **Transaction**: Financial record from bank or import. Attributes: date, amount, currency, merchant, MCC code, description, source, categorization, confidence score
- **EmissionRecord**: Calculated emission entry. Attributes: scope, category, amount (kgCO2e), activity data, emission factor used, calculation date, site reference
- **EmissionFactor**: Reference data for calculations. Attributes: country, category, value, unit, source, valid from/to dates
- **Subscription**: Billing relationship. Attributes: plan, status, start date, next billing date, payment method reference
- **Report**: Generated document. Attributes: type, period, generation date, file reference, organization reference

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can complete organization setup and first bank connection in under 30 minutes
- **SC-002**: 70% of transaction data is collected automatically via Zero-Input (bank + accounting integrations)
- **SC-003**: First carbon footprint report can be generated within 2 hours of initial setup (vs. 2-4 weeks industry standard)
- **SC-004**: Manual data entry represents less than 20% of total emission data for connected organizations
- **SC-005**: Auto-categorization achieves 90% accuracy for transactions with MCC codes
- **SC-006**: AI categorization achieves 80% accuracy for ambiguous transactions
- **SC-007**: Dashboard data updates within 5 minutes of new data availability
- **SC-008**: Users can generate PDF reports within 30 seconds
- **SC-009**: System supports 1,000 concurrent users without performance degradation
- **SC-010**: Platform achieves NPS score above 40 from pilot users
- **SC-011**: 10 paying customers within MVP phase (Starter/Pro/Business plans)
- **SC-012**: User task completion rate exceeds 85% for primary workflows (setup, connection, report generation)

---

## Assumptions

1. **Open Banking Availability**: Bridge (FR) and Finapi (DE) APIs are available and support business account connections with MCC codes
2. **Emission Factors**: ADEME and UBA emission factor databases are publicly accessible and can be integrated
3. **AI Categorization**: Claude API is available for transaction categorization with acceptable latency and cost
4. **OCR Accuracy**: AWS Textract provides sufficient accuracy for invoice data extraction (energy bills)
5. **User Technical Comfort**: Target users (DAF, RSE managers) are comfortable with OAuth bank connection flows
6. **Regulatory Scope**: MVP focuses on BEGES (FR) and CSRD basics; full regulatory compliance is Phase 2
7. **Currency**: All target market organizations operate in EUR; multi-currency is a future enhancement
8. **Scope 3 Completeness**: MVP covers Categories 1, 6, 7; full 15 categories is Phase 3
9. **Mobile**: MVP is web-only; mobile apps are Phase 2

---

## Scope Boundaries

### In Scope (MVP - Phase 1)
- User authentication and organization management
- Multi-site support within single country
- Open Banking integration (FR: Bridge, DE: Finapi)
- MCC-based transaction categorization
- AI-assisted categorization for ambiguous transactions
- Manual data entry forms
- CSV/Excel/FEC import
- OCR for energy invoices
- Scope 1, 2, and Scope 3 (Categories 1, 6, 7) calculation
- France and Germany emission factors
- Real-time dashboard with KPIs
- PDF report generation
- Subscription management with 4 tiers
- 14-day free trial
- French, German, English language support

### Out of Scope (Future Phases)
- Mobile applications (Phase 2)
- Energy provider direct integrations (Enedis, GRDF) (Phase 2)
- BEGES/CSRD regulatory report formats (Phase 2)
- Scope 3 Categories 2-5, 8-15 (Phase 2-3)
- Sectoral benchmarks (Phase 2)
- Reduction trajectory planning (Phase 2)
- Supplier portal for Scope 3 upstream (Phase 3)
- Additional EU countries (BE, NL, AT, CH, ES, IT) (Phase 3)
- SSO/SAML authentication (Phase 3)
- Public API and webhooks (Phase 3)
- SBTi-aligned target setting (Phase 3)

---

## Dependencies

- **External Services**: Bridge API (Open Banking FR), Finapi API (Open Banking DE), Claude API (AI categorization), AWS Textract (OCR), Stripe (payments)
- **Data Sources**: ADEME Base Empreinte (FR emission factors), UBA (DE emission factors)
- **Infrastructure**: EU-based cloud hosting (Scaleway) for GDPR compliance

---

## Risks

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Open Banking provider API changes | Medium | High | Abstract provider logic; monitor API changelogs |
| Low MCC code coverage | Medium | Medium | Robust AI fallback; user feedback loop |
| GDPR/financial data compliance | Low | High | EU hosting; encrypt all sensitive data; audit trails |
| User adoption friction (bank OAuth) | Medium | Medium | Clear UX guidance; trust indicators; support documentation |
| Emission factor accuracy disputes | Low | Medium | Document all sources; allow factor overrides with justification |
