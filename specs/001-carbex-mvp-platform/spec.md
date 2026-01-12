# Feature Specification: Carbex MVP - Plateforme SaaS Bilan Carbone Automatique

**Feature Branch**: `001-carbex-mvp-platform`
**Created**: 2025-12-28
**Updated**: 2025-01-12
**Status**: Implemented
**Priority Market**: Germany (DE) - P0

---

## Executive Summary

Carbex is a SaaS platform that automates carbon footprint calculation for European SMEs (10-250 employees) subject to CSRD 2025, BEGES, LkSG (German Supply Chain Act), and EU sustainability reporting requirements. The platform's core innovation is "Zero-Input Carbon" - automatic data collection via Open Banking (PSD2), energy provider APIs, supplier integrations, and AI-powered multi-provider categorization, reducing manual data entry from 80% to under 20%.

**Target Markets**: Germany (P0 - Primary), France (P1), EU expansion (AT, CH, BE, NL, ES, IT)
**Core Value Proposition**: First carbon footprint in 2 hours (vs. 2-4 weeks traditional)

### Key Differentiators (Implemented)
- **Multi-Provider AI**: Claude, GPT-4, Gemini, DeepSeek for transaction categorization
- **Open Banking PSD2**: Bridge (FR) + FinAPI (DE) for automatic transaction import
- **Energy API Integration**: Enedis + GRDF (FR) for real consumption data
- **Supplier Carbon Portal**: Scope 3 upstream data collection from supply chain
- **SSO/SAML Enterprise**: SAML 2.0 authentication for enterprise customers
- **Full API & Webhooks**: REST API with real-time webhook notifications

---

## Implementation Status

| Component | Status | Completion |
|-----------|--------|------------|
| Authentication & Multi-tenant | DONE | 100% |
| Organization & Site Management | DONE | 100% |
| Open Banking (Bridge + FinAPI) | DONE | 100% |
| Energy Connections (Enedis/GRDF) | DONE | 100% |
| AI Multi-Provider Categorization | DONE | 100% |
| Manual Data Entry & Import | DONE | 100% |
| Emission Calculation Engine | DONE | 100% |
| Dashboard & KPIs | DONE | 100% |
| Reporting (PDF/Excel) | DONE | 100% |
| Subscription & Billing (Stripe) | DONE | 100% |
| Supplier Portal | DONE | 100% |
| SSO/SAML Authentication | DONE | 100% |
| Public API & Webhooks | DONE | 100% |
| Admin Panel (Filament) | DONE | 100% |
| i18n (DE/FR/EN) | DONE | 100% |

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Organization Onboarding & Setup (Priority: P0)

A company administrator creates an account, sets up their organization with basic information (company name, country, sector, number of employees), and invites team members with appropriate roles.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** a new user visits the platform, **When** they complete 2-step registration (account + organization), **Then** they can create their organization with required fields (name, country, sector, employee count)
2. **Given** an organization exists, **When** the admin creates a site (physical location), **Then** the site appears in their organization structure with country and address
3. **Given** an admin has organization access, **When** they invite a user with a specific role (Admin/Contributor/Reader), **Then** the invitee receives an email and can join with assigned permissions
4. **Given** a Contributor role user, **When** they access the platform, **Then** they can only view/edit data for their assigned sites (not billing or settings)

---

### User Story 2 - Zero-Input Bank Connection & Auto-Sync (Priority: P0)

A user connects their company bank account via Open Banking (Bridge for France, FinAPI for Germany) to automatically import and categorize transactions for carbon calculation.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization admin in Germany, **When** they initiate bank connection via wizard, **Then** they are redirected to FinAPI OAuth flow and can authorize their business account
2. **Given** an organization admin in France, **When** they initiate bank connection, **Then** they are redirected to Bridge OAuth flow
3. **Given** a bank account is connected, **When** the sync job runs, **Then** new transactions since last sync are imported and normalized (date, amount, merchant, MCC code, description)
4. **Given** a transaction with MCC code 4511 (Airlines), **When** it is processed, **Then** it is automatically categorized as Scope 3 Cat. 6 (Business Travel) with calculated CO2e
5. **Given** a transaction with ambiguous merchant, **When** AI categorization runs, **Then** it assigns the most likely category with a confidence score using selected AI provider

---

### User Story 3 - Energy Provider Connection & ISO 50001 (Priority: P1)

A user connects their energy accounts (Enedis for electricity, GRDF for gas in France) to automatically import consumption data for Scope 2 calculations and ISO 50001 energy management.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization in France, **When** they initiate Enedis connection, **Then** they can authorize access via Enedis DataConnect API
2. **Given** an Enedis connection is active, **When** sync runs, **Then** electricity consumption (kWh) is imported with meter readings
3. **Given** a GRDF connection is active, **When** sync runs, **Then** gas consumption (m3/kWh) is imported and converted to Scope 2 emissions
4. **Given** consumption data exists, **When** viewing dashboard, **Then** Scope 2 emissions show real consumption vs estimated
5. **Given** ISO 50001 compliance required, **When** admin sets energy baseline, **Then** system tracks EnPIs (Energy Performance Indicators) against baseline
6. **Given** energy targets defined, **When** viewing energy dashboard, **Then** user sees actual vs target with variance analysis
7. **Given** energy audit scheduled, **When** generating ISO 50001 report, **Then** system exports energy data in audit-ready format

---

### User Story 4 - Manual Data Entry & Import (Priority: P1)

A user manually enters emission data or imports bulk data from CSV/Excel/FEC files for sources that cannot be automatically connected.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** a Contributor user, **When** they access the manual entry form, **Then** they can enter emission data with guided fields (activity type, quantity, unit, date, site)
2. **Given** a user uploads a valid CSV file with predefined columns, **When** import is processed, **Then** data is validated, mapped to emission categories, and added to the organization's records
3. **Given** a French organization uploads a FEC file (Fichier des Ecritures Comptables), **When** parsed successfully, **Then** accounting entries are categorized by expense type and converted to emissions
4. **Given** a user uploads a PDF invoice, **When** OCR processing completes, **Then** extracted data is presented for confirmation before saving

---

### User Story 5 - Real-Time Dashboard & KPIs (Priority: P1)

A user views their organization's carbon footprint via an interactive dashboard showing total emissions, breakdown by scope (1/2/3), trends over time, and top emission sources.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization with emission data, **When** a user opens the dashboard, **Then** they see total tCO2e, breakdown by Scope 1/2/3, and monthly trend chart
2. **Given** multiple sites exist, **When** viewing the dashboard, **Then** user can filter by site to see site-specific emissions
3. **Given** new transactions are synced, **When** dashboard refreshes, **Then** KPIs update to reflect new data
4. **Given** year-over-year data exists, **When** viewing comparison view, **Then** user sees N vs N-1 evolution with percentage change
5. **Given** Carbon equivalents feature, **When** viewing dashboard, **Then** user sees CO2 equivalents (flights Paris-NY, car km, hotel nights)

---

### User Story 6 - Emission Calculation Engine (Priority: P0)

The system calculates carbon emissions using country-specific emission factors (ADEME for France, UBA for Germany) across all three GHG Protocol scopes and 15 categories.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** electricity consumption data for a French site, **When** calculated, **Then** emissions use ADEME factor with location-based or market-based method
2. **Given** a fuel purchase transaction categorized as Scope 1, **When** processed, **Then** emissions are calculated using appropriate fuel emission factor for the country
3. **Given** a German organization, **When** Scope 2 electricity is calculated, **Then** UBA emission factor is applied
4. **Given** a Scope 3 Category 1 (Purchased Goods), **When** calculated, **Then** appropriate spend-based or activity-based factors are used
5. **Given** all 15 GHG Protocol categories, **When** data is entered, **Then** emissions are calculated for each category

---

### User Story 7 - Transaction Validation & Override (Priority: P2)

A user reviews transactions flagged with low confidence, validates or corrects categories, and creates rules for recurring merchants.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** flagged transactions exist, **When** user opens validation queue, **Then** they see transactions sorted by confidence with suggested categories
2. **Given** a user validates a category for a merchant, **When** saved with merchant rule, **Then** future transactions from this merchant auto-categorize correctly
3. **Given** a user overrides a category, **When** saved, **Then** emission calculation updates immediately to reflect new category

---

### User Story 8 - PDF & Excel Report Generation (Priority: P1)

A user generates PDF or Excel reports of their organization's carbon footprint for internal use, regulatory compliance, or sharing with stakeholders.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization with complete emission data, **When** user requests PDF export, **Then** report generates with executive summary, methodology, scope breakdown, and top sources
2. **Given** report generation completes, **When** user downloads PDF, **Then** document includes organization branding, period covered, and data source attribution
3. **Given** Excel export requested, **When** generated, **Then** detailed data is available with all emission records and calculations
4. **Given** CSRD compliance report requested, **When** generated, **Then** report follows ESRS 2 format requirements

---

### User Story 9 - Subscription & Payment (Priority: P2)

An organization admin subscribes to a paid plan via Stripe, manages billing information, and upgrades/downgrades their subscription.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** a user on trial, **When** they choose a plan (Starter/Pro/Business/Enterprise), **Then** they are redirected to Stripe Checkout and subscription activates on success
2. **Given** an active subscription, **When** user views billing section, **Then** they see current plan, next billing date, and invoice history
3. **Given** a Pro plan subscriber, **When** they access Zero-Input features, **Then** they can connect bank accounts as per plan limits
4. **Given** subscription expires, **When** trial ends without payment, **Then** account enters read-only mode with data preserved

---

### User Story 10 - Supplier Carbon Portal (Priority: P1)

An organization invites suppliers to submit their carbon footprint data for Scope 3 upstream calculations (Categories 1, 2, 4).

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization admin, **When** they add a supplier, **Then** they can invite them via email to submit emission data
2. **Given** a supplier receives invitation, **When** they access the portal, **Then** they can submit their product-level emission factors
3. **Given** supplier data is submitted, **When** verified, **Then** it replaces estimated spend-based factors with actual supplier data
4. **Given** LkSG compliance required, **When** viewing supplier dashboard, **Then** supply chain carbon data is aggregated

---

### User Story 11 - SSO/SAML Enterprise Authentication (Priority: P2)

Enterprise organizations configure SSO/SAML authentication for their employees, enabling single sign-on from their identity provider.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an Enterprise plan organization, **When** admin configures SAML settings, **Then** they can enter IdP metadata (Azure AD, Okta, etc.)
2. **Given** SAML is configured, **When** a user accesses Carbex, **Then** they are redirected to their IdP for authentication
3. **Given** successful IdP authentication, **When** user returns to Carbex, **Then** they are automatically logged in with mapped roles
4. **Given** IdP provides group claims, **When** mapped, **Then** users are assigned correct organization roles

---

### User Story 12 - Transition Plan & Reduction Targets (Priority: P1)

An organization creates carbon reduction targets aligned with SBTi methodology and tracks progress via a transition plan.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** baseline emissions calculated, **When** admin creates reduction target, **Then** they can set target year, reduction percentage, and scope
2. **Given** reduction targets exist, **When** viewing transition plan, **Then** user sees target trajectory vs actual emissions
3. **Given** SBTi alignment requested, **When** targets are validated, **Then** system indicates if targets meet 1.5C or 2C pathway
4. **Given** actions are defined, **When** tracking progress, **Then** estimated vs actual reductions are shown

---

### User Story 13 - Public API & Webhooks (Priority: P2)

External systems integrate with Carbex via REST API and receive real-time notifications via webhooks.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** an organization with API access, **When** admin creates API key, **Then** they receive key with configurable permissions
2. **Given** valid API key, **When** calling /api/v1/emissions, **Then** emission data is returned in JSON format
3. **Given** webhook configured for emission.created, **When** new emission is recorded, **Then** webhook fires to configured URL
4. **Given** API rate limits, **When** exceeded, **Then** appropriate 429 response with retry-after header

---

### User Story 14 - AI Assistant (Priority: P1)

Users interact with an AI assistant that helps with data entry, answers carbon accounting questions, and provides recommendations.

**Status**: IMPLEMENTED

**Acceptance Scenarios**:

1. **Given** AI assistant enabled, **When** user asks a question, **Then** response uses organization context and emission data
2. **Given** document uploaded, **When** AI processes it, **Then** emission data is extracted and suggested for import
3. **Given** high emissions in category, **When** requesting recommendations, **Then** AI provides actionable reduction suggestions
4. **Given** multi-provider support, **When** configured, **Then** admin can select preferred AI provider (Claude/GPT-4/Gemini/DeepSeek)

---

### Edge Cases

- What happens when a bank connection expires or is revoked? System notifies admin and marks data as stale.
- How does system handle duplicate transactions from bank and accounting import? Deduplication based on date, amount, and merchant.
- What happens when a user uploads malformed CSV? System validates format, reports specific errors, and rejects invalid rows.
- How does system handle transactions in foreign currencies? Convert to organization's base currency using exchange rate at transaction date.
- What happens when emission factors are updated mid-year? Historical calculations remain unchanged; new calculations use updated factors with audit trail.
- How does system handle multi-site organizations with different countries? Each site inherits country-specific emission factors.
- What happens when supplier doesn't respond to invitation? System sends reminders and uses estimated factors until actual data received.
- How does SSO handle user provisioning? Just-in-time provisioning on first login with IdP attributes.

---

## Requirements *(mandatory)*

### Functional Requirements

#### Authentication & Authorization
- **FR-001**: System MUST allow user registration with email and password ✅
- **FR-002**: System MUST verify email addresses before account activation ✅
- **FR-003**: System MUST support password reset via email link ✅
- **FR-004**: System MUST enforce role-based access control (Super Admin, Admin Org, Contributor, Reader) ✅
- **FR-005**: System MUST restrict site-level access based on user role assignments ✅
- **FR-006**: System MUST support SAML 2.0 SSO for Enterprise plans ✅

#### Organization Management
- **FR-007**: System MUST allow creation of organizations with name, country, sector, and employee count ✅
- **FR-008**: System MUST support multiple sites per organization with location-specific settings ✅
- **FR-009**: System MUST allow admin to invite users via email with role assignment ✅
- **FR-010**: System MUST apply country-specific configurations (emission factors, regulatory templates) automatically ✅

#### Zero-Input Data Collection
- **FR-011**: System MUST integrate with Open Banking providers (Bridge for FR, FinAPI for DE) ✅
- **FR-012**: System MUST store OAuth tokens securely and encrypted ✅
- **FR-013**: System MUST sync bank transactions for connected accounts ✅
- **FR-014**: System MUST normalize transactions to unified format (date, amount, merchant, MCC, description) ✅
- **FR-015**: System MUST categorize transactions using MCC code mapping ✅
- **FR-016**: System MUST use AI categorization for ambiguous transactions (multi-provider) ✅
- **FR-017**: System MUST assign confidence scores to all categorizations ✅
- **FR-018**: System MUST flag transactions with low confidence for manual validation ✅

#### Energy Connections
- **FR-019**: System MUST integrate with Enedis DataConnect API for electricity data ✅
- **FR-020**: System MUST integrate with GRDF API for gas consumption data ✅
- **FR-021**: System MUST store energy consumption with meter readings and periods ✅

#### ISO 50001 Energy Management
- **FR-022**: System MUST support energy baseline definition per site/organization ✅
- **FR-023**: System MUST calculate EnPIs (Energy Performance Indicators) ✅
- **FR-024**: System MUST track energy targets with variance analysis ✅
- **FR-025**: System MUST support energy action plans with progress tracking ✅
- **FR-026**: System MUST generate ISO 50001 audit-ready reports ✅

#### Manual Data Entry
- **FR-027**: System MUST provide guided forms for manual emission data entry ✅
- **FR-028**: System MUST support CSV/Excel file import with predefined templates ✅
- **FR-029**: System MUST support FEC file import for French accounting data ✅
- **FR-030**: System MUST process documents via AI/OCR for data extraction ✅
- **FR-031**: System MUST validate imported data and report errors before saving ✅

#### Emission Calculation
- **FR-032**: System MUST calculate Scope 1 emissions (direct combustion, fleet vehicles) ✅
- **FR-033**: System MUST calculate Scope 2 emissions (electricity, heat) with location and market-based methods ✅
- **FR-034**: System MUST calculate all 15 Scope 3 categories per GHG Protocol ✅
- **FR-035**: System MUST use country-specific emission factors (ADEME for FR, UBA for DE) ✅
- **FR-036**: System MUST support EU country emission factors (AT, CH, BE, NL, ES, IT) ✅
- **FR-037**: System MUST maintain audit trail of emission factors used per calculation ✅

#### Dashboard & Visualization
- **FR-038**: System MUST display total emissions in tCO2e on dashboard ✅
- **FR-039**: System MUST show scope breakdown (Scope 1/2/3) with percentages ✅
- **FR-040**: System MUST display monthly and yearly trend charts ✅
- **FR-041**: System MUST allow filtering by site, time period, and scope ✅
- **FR-042**: System MUST display carbon equivalents (flights, car km, etc.) ✅

#### Reporting
- **FR-043**: System MUST generate PDF summary reports with configurable sections ✅
- **FR-044**: System MUST generate Excel exports with detailed data ✅
- **FR-045**: System MUST include methodology and emission factors in reports ✅
- **FR-046**: Reports MUST include organization details, period, and scope breakdown ✅
- **FR-047**: System MUST support CSRD/ESRS 2 report format ✅

#### Subscription & Billing
- **FR-048**: System MUST offer tiered subscription plans (Starter, Pro, Business, Enterprise) ✅
- **FR-049**: System MUST process payments securely via Stripe ✅
- **FR-050**: System MUST enforce plan-specific feature limits (sites, users, connections) ✅
- **FR-051**: System MUST provide 14-day free trial for non-Enterprise plans ✅
- **FR-052**: System MUST maintain access to data in read-only mode when subscription lapses ✅

#### Supplier Management
- **FR-053**: System MUST allow adding suppliers with contact information ✅
- **FR-054**: System MUST support supplier invitation via email ✅
- **FR-055**: System MUST accept supplier emission data submissions ✅
- **FR-056**: System MUST integrate supplier data into Scope 3 calculations ✅

#### API & Integrations
- **FR-057**: System MUST provide REST API with JSON responses ✅
- **FR-058**: System MUST support API key authentication with scopes ✅
- **FR-059**: System MUST enforce API rate limiting ✅
- **FR-060**: System MUST support webhook notifications for key events ✅
- **FR-061**: System MUST provide webhook delivery tracking and retry ✅

#### Internationalization
- **FR-062**: System MUST support German (primary), French, and English languages ✅
- **FR-063**: System MUST display currency, dates, and numbers in locale-appropriate formats ✅
- **FR-064**: System MUST have NO hardcoded text in any component ✅

### Key Entities (Implemented)

- **Organization**: Company using the platform. Attributes: name, country, sector, employee count, subscription plan, default currency, settings
- **Site**: Physical location belonging to an organization. Attributes: name, address, country, organization reference
- **User**: Person with platform access. Attributes: email, name, role, organization reference, assigned sites, preferences
- **BankConnection**: OAuth link to external bank. Attributes: provider (bridge/finapi), status, last sync, accounts
- **BankAccount**: Individual bank account. Attributes: iban, name, balance, currency, connection reference
- **Transaction**: Financial record from bank or import. Attributes: date, amount, currency, merchant, MCC code, description, source, categorization, confidence score
- **EnergyConnection**: Link to energy provider. Attributes: provider (enedis/grdf), status, meter ID
- **EnergyConsumption**: Energy usage record. Attributes: period, consumption (kWh), meter reading, connection reference
- **EmissionRecord**: Calculated emission entry. Attributes: scope, category, amount (kgCO2e), activity data, emission factor used, site reference
- **EmissionFactor**: Reference data for calculations. Attributes: country, category, value, unit, source (ademe/uba), valid dates
- **Subscription**: Billing relationship via Stripe. Attributes: plan, status, stripe IDs, trial dates
- **Invoice**: Billing document. Attributes: amount, status, stripe ID, PDF URL
- **Report**: Generated document. Attributes: type, period, format (pdf/excel), file path
- **Supplier**: External company in supply chain. Attributes: name, country, contact, status
- **SupplierInvitation**: Invitation to submit data. Attributes: email, token, status, expiry
- **SupplierProduct**: Product with emission factor. Attributes: name, emission factor, unit
- **SupplierEmission**: Submitted emission data. Attributes: product, quantity, emissions
- **ApiKey**: API access credential. Attributes: name, key hash, scopes, rate limits
- **Webhook**: Event notification config. Attributes: URL, events, secret, status
- **WebhookDelivery**: Delivery attempt log. Attributes: payload, response, status code
- **SsoConfiguration**: SAML settings. Attributes: IdP URL, certificate, entity ID, attribute mapping
- **Assessment**: Carbon assessment. Attributes: period, status, completeness
- **Action**: Reduction action. Attributes: name, category, estimated reduction, status
- **ReductionTarget**: SBTi-aligned target. Attributes: base year, target year, reduction %, scope
- **AIConversation**: Chat history. Attributes: messages, context, user reference
- **AISetting**: AI configuration. Attributes: provider, model, quotas
- **UploadedDocument**: Document for processing. Attributes: file path, type, processing status
- **EnergyBaseline**: ISO 50001 energy baseline. Attributes: site, year, consumption (kWh), normalized factors
- **EnergyTarget**: ISO 50001 energy target. Attributes: baseline reference, target year, reduction %, EnPI type
- **Esrs2Disclosure**: CSRD disclosure. Attributes: year, requirement code, content, status, verified date
- **ClimateTransitionPlan**: CSRD transition plan. Attributes: base year, target year, targets JSON, actions JSON
- **DoubleMaterialityAssessment**: CSRD materiality. Attributes: year, impact matrix, financial matrix, stakeholder input

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can complete organization setup and first bank connection in under 30 minutes ✅
- **SC-002**: 70% of transaction data is collected automatically via Zero-Input (bank + energy integrations) ✅
- **SC-003**: First carbon footprint report can be generated within 2 hours of initial setup ✅
- **SC-004**: Manual data entry represents less than 20% of total emission data for connected organizations ✅
- **SC-005**: Auto-categorization achieves 90% accuracy for transactions with MCC codes ✅
- **SC-006**: AI categorization achieves 80% accuracy for ambiguous transactions ✅
- **SC-007**: Dashboard data updates within 5 minutes of new data availability ✅
- **SC-008**: Users can generate PDF reports within 30 seconds ✅
- **SC-009**: System supports 1,000 concurrent users without performance degradation
- **SC-010**: Platform achieves NPS score above 40 from pilot users
- **SC-011**: 10 paying customers within MVP phase (Starter/Pro/Business plans)
- **SC-012**: User task completion rate exceeds 85% for primary workflows ✅

---

## Technical Architecture

### Stack (Implemented)
- **Backend**: PHP 8.4+ / Laravel 12.x
- **Frontend**: Livewire 3 (full-page components) + Alpine.js + Tailwind CSS
- **Database**: MySQL 8.0 / PostgreSQL
- **Cache**: Redis
- **Queue**: Laravel Horizon (Redis-backed)
- **Search**: Laravel Scout (Meilisearch)
- **Admin**: Filament 3.x
- **Payments**: Laravel Cashier (Stripe)
- **PDF**: DomPDF / Browsershot
- **Testing**: PHPUnit + Pest + Playwright (E2E)

### External Services (Integrated)
- **Open Banking FR**: Bridge API
- **Open Banking DE**: FinAPI
- **Energy FR**: Enedis DataConnect, GRDF ADICT
- **AI Providers**: Claude (Anthropic), GPT-4 (OpenAI), Gemini (Google), DeepSeek
- **Payments**: Stripe
- **Email**: Postmark / AWS SES
- **Storage**: S3-compatible (Scaleway)

### API Structure (Implemented)
```
/api/v1/
├── /organizations
├── /sites
├── /emissions
├── /transactions
├── /reports
├── /bank-connections
├── /energy-connections
├── /suppliers
├── /webhooks
└── /assessments
```

---

## Scope Boundaries

### Implemented (MVP Complete)
- ✅ User authentication and organization management
- ✅ Multi-site support with multi-country settings
- ✅ Open Banking integration (FR: Bridge, DE: FinAPI)
- ✅ Energy provider integration (Enedis, GRDF)
- ✅ MCC-based transaction categorization
- ✅ AI-assisted categorization (multi-provider)
- ✅ Manual data entry forms
- ✅ CSV/Excel/FEC import
- ✅ Document AI processing
- ✅ All 15 Scope 3 categories calculation
- ✅ Multi-country emission factors (DE, FR, AT, CH, BE, NL, ES, IT)
- ✅ Real-time dashboard with KPIs
- ✅ PDF and Excel report generation
- ✅ Subscription management with 4 tiers
- ✅ Supplier portal for Scope 3 upstream
- ✅ SSO/SAML authentication
- ✅ Public API and webhooks
- ✅ Reduction targets and transition planning
- ✅ German, French, English language support
- ✅ Admin panel (Filament)

### Future Phases
- Mobile applications (Phase 2)
- BEGES official regulatory format (Phase 2)
- German energy provider integrations (Phase 2)
- Sectoral benchmarks (Phase 2)
- Carbon offset marketplace (Phase 3)
- Blockchain verification (Phase 3)

---

## Dependencies

### External Services
- Bridge API (Open Banking FR)
- FinAPI (Open Banking DE)
- Enedis DataConnect API
- GRDF ADICT API
- Claude API (Anthropic)
- OpenAI API (GPT-4)
- Google AI (Gemini)
- DeepSeek API
- Stripe (payments)
- Postmark (email)

### Data Sources
- ADEME Base Empreinte (FR emission factors)
- UBA (DE emission factors)
- EU emission factors database
- Market-based electricity factors

### Infrastructure
- EU-based cloud hosting (Scaleway/OVH) for GDPR compliance
- Redis for caching and queues
- S3-compatible object storage

---

## Risks

| Risk | Likelihood | Impact | Mitigation | Status |
|------|------------|--------|------------|--------|
| Open Banking provider API changes | Medium | High | Abstract provider logic; monitor API changelogs | Mitigated |
| Low MCC code coverage | Medium | Medium | Multi-provider AI fallback; user feedback loop | Mitigated |
| GDPR/financial data compliance | Low | High | EU hosting; encrypt all sensitive data; audit trails | Mitigated |
| User adoption friction (bank OAuth) | Medium | Medium | Clear UX guidance; trust indicators; wizard flow | Mitigated |
| Emission factor accuracy disputes | Low | Medium | Document all sources; allow factor overrides | Mitigated |
| AI provider rate limits/costs | Medium | Medium | Multi-provider fallback; caching; quotas | Mitigated |
| Supplier portal low adoption | Medium | Medium | Reminders; incentives; estimated fallback | Monitoring |

---

## Compliance

### Regulatory Framework (Supported)

#### CSRD 2025 (Corporate Sustainability Reporting Directive)
- **Applicability**: Companies with 250+ employees OR €50M+ revenue OR €25M+ assets
- **Timeline**: Large companies from FY2024, SMEs from FY2026
- **Scope**: Double materiality assessment (impact + financial)
- **Implementation**: Full ESRS disclosure support

#### ESRS (European Sustainability Reporting Standards)
| Standard | Description | Status |
|----------|-------------|--------|
| ESRS 2 | General disclosures | Implemented |
| ESRS E1 | Climate change | Implemented |
| ESRS E2 | Pollution | Planned |
| ESRS E3 | Water & marine resources | Planned |
| ESRS E4 | Biodiversity | Planned |
| ESRS E5 | Resource use & circular economy | Planned |
| ESRS S1-S4 | Social standards | Planned |
| ESRS G1 | Governance | Implemented |

#### ISO Standards
- **ISO 14064-1:2018**: GHG quantification with uncertainty tracking
- **ISO 50001:2018**: Energy management system (EnMS)
  - Energy baselines and EnPIs (Energy Performance Indicators)
  - Plan-Do-Check-Act cycle
  - Energy targets and action plans
  - Monitoring & measurement
  - Internal audit support

#### National Regulations
- **BEGES** (France): Bilan des Emissions de Gaz a Effet de Serre
- **LkSG** (Germany): Lieferkettensorgfaltspflichtengesetz (Supply Chain Due Diligence)
- **CSDDD** (EU): Corporate Sustainability Due Diligence Directive

#### Carbon Standards
- **GHG Protocol**: Scopes 1, 2, 3 (all 15 categories)
- **SBTi**: Science Based Targets initiative alignment
- **EU Taxonomy**: Sustainable activities classification (Regulation 2020/852)

### Data Protection
- **GDPR/DSGVO**: Full compliance with EU data protection
- **Art. 37(5) DSGVO**: Data Protection Officer (dpo@carbex.de)
- **Data residency**: EU-only hosting (Scaleway Paris/Amsterdam)
- **Encryption**: AES-256 at rest, TLS 1.3 in transit
- **Data retention**: Configurable per organization
- **Right to erasure**: Automated data deletion workflows
