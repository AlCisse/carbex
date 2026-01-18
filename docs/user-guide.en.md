# Carbex User Guide

> Carbon Footprint Platform for European SMEs

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Dashboard](#dashboard)
3. [Sites and Scope](#sites-and-scope)
4. [Transactions and Data](#transactions-and-data)
5. [Bank Connections](#bank-connections)
6. [AI Assistant](#ai-assistant)
7. [Reports and Exports](#reports-and-exports)
8. [Suppliers](#suppliers)
9. [Account Settings](#account-settings)
10. [FAQ](#faq)

---

## Quick Start

### Account Creation

1. Go to [carbex.app](https://carbex.app)
2. Click **Create Account**
3. Enter your information:
   - Professional email
   - Password (min. 8 characters)
   - Organization name
4. Accept the terms of use
5. Validate your email via the link received

### First Configuration

After login, the setup wizard guides you:

1. **Organization Info**: Name, VAT/Tax ID, industry sector
2. **Reference Year**: Choose your first assessment year
3. **First Site**: Add your main site (headquarters, factory, office)
4. **Bank Connection** (optional): Connect accounts for automatic import

---

## Dashboard

The dashboard presents a summary view of your carbon footprint.

### Key Indicators

| Indicator | Description |
|-----------|-------------|
| **Total Emissions** | Total CO₂e for selected period |
| **Scope 1** | Direct emissions (vehicles, heating) |
| **Scope 2** | Indirect energy emissions (electricity) |
| **Scope 3** | Other indirect emissions (purchases, travel) |

### Available Charts

- **Monthly Evolution**: Track your emissions month by month
- **Scope Distribution**: Pie chart of 3 scopes
- **Top Categories**: Your main emission sources
- **Annual Comparison**: Evolution vs. previous year

### Filters

- By period (month, quarter, year)
- By site
- By scope (1, 2, 3)
- By emission category

---

## Sites and Scope

### Add a Site

1. Menu **Sites** → **Add Site**
2. Fill in:
   - Site name
   - Full address
   - Type (headquarters, office, factory, warehouse, store)
   - Area (m²)
   - Number of employees
3. Click **Save**

### Supported Site Types

| Type | Description | Typical Data |
|------|-------------|--------------|
| Headquarters | Main office | Electricity, heating, travel |
| Office | Administrative site | Electricity, IT |
| Factory | Production site | Energy, process, raw materials |
| Warehouse | Storage/logistics | Electricity, handling |
| Store | Point of sale | Electricity, air conditioning |

### Organizational Scope

Carbex supports two approaches:

- **Operational Control**: 100% of emissions from sites you control
- **Equity Share**: Emissions proportional to your ownership stake

---

## Transactions and Data

### Manual Import

1. Menu **Transactions** → **Import**
2. Download the Excel template
3. Fill in your data:
   - Date
   - Description
   - Amount
   - Category
   - Related site
4. Upload the completed file

### Emission Categories

#### Scope 1 - Direct Emissions

| Category | Examples |
|----------|----------|
| Stationary combustion | Gas heating, fuel oil |
| Mobile combustion | Company vehicles |
| Fugitive emissions | Air conditioning (leaks) |
| Industrial processes | Chemical reactions |

#### Scope 2 - Indirect Energy

| Category | Examples |
|----------|----------|
| Electricity | Power consumption |
| Heat/Steam | District heating |
| Cooling | District cooling |

#### Scope 3 - Other Indirect

| Category | Examples |
|----------|----------|
| Purchased goods | Raw materials, supplies |
| Purchased services | Consulting, IT, cleaning |
| Business travel | Flights, trains, hotels |
| Employee commuting | Employee cars |
| Upstream transportation | Supplier deliveries |
| Downstream transportation | Customer deliveries |
| Waste | Treatment, recycling |
| Capital goods | Equipment, buildings |

### Manual Entry

To add a transaction:

1. Menu **Transactions** → **New Transaction**
2. Fill in the fields:
   - Transaction date
   - Description
   - Amount (€)
   - Emission category
   - Related site
   - Supplier (optional)
3. Click **Save**

Emission calculation is automatic based on emission factors.

---

## Bank Connections

### Supported Banks

#### France (via Bridge)
- Crédit Agricole, BNP Paribas, Société Générale
- Crédit Mutuel, CIC, Banque Populaire
- Caisse d'Épargne, LCL, Boursorama
- And 350+ other institutions

#### Germany (via FinAPI)
- Deutsche Bank, Commerzbank
- Sparkasse, Volksbank
- N26, DKB, ING
- And 3000+ other institutions

### Connect a Bank

1. Menu **Connections** → **Add Bank**
2. Search for your bank
3. Authenticate (secure redirect)
4. Select accounts to sync
5. Confirm the connection

### Synchronization

- **Automatic**: Every 6 hours
- **Manual**: "Sync" button anytime
- **History**: Import of last 90 days on connection

### Automatic Categorization

Carbex automatically categorizes your transactions:

- **Built-in AI**: Analyzes description and merchant
- **MCC Codes**: Classification by merchant code
- **Learning**: Improves with your corrections

To correct a category:
1. Click on the transaction
2. Change the category
3. Check "Apply to all similar transactions" (optional)

---

## AI Assistant

### Features

The Carbex AI assistant helps you:

- **Analyze** your carbon data
- **Answer** your compliance questions
- **Suggest** reduction actions
- **Explain** regulations (CSRD, GHG Protocol)
- **Extract** data from your documents

### Usage

1. Click the assistant icon (bubble at bottom right)
2. Ask your question in natural language
3. The assistant responds with contextual data

### Example Questions

```
"What are my main emission sources?"
"How can I reduce my Scope 3?"
"Explain CSRD requirements"
"Analyze my emissions from last quarter"
"Which suppliers have the most impact?"
```

### Document Extraction

1. Menu **Documents** → **Import**
2. Drag and drop your files (PDF, images)
3. AI automatically extracts:
   - Energy bills (kWh, m³)
   - Fuel receipts (liters)
   - Supplier invoices (amounts, descriptions)

### Quotas by Subscription

| Plan | AI Requests | Model |
|------|-------------|-------|
| Free | 100/month | Gemini Flash Lite |
| Starter | 500/month | GPT-4o Mini |
| Professional | 2500/month | Claude Sonnet 4 |
| Enterprise | Unlimited | Claude Sonnet 4 |

---

## Reports and Exports

### Report Types

#### Carbon Footprint Report (PDF)

Complete report including:
- Emissions summary by scope
- Distribution charts
- Detail by category
- Time evolution
- Recommendations

#### GHG Protocol Report

International standard format:
- Scope 1, 2, 3 breakdown
- Emission factors used
- Calculation methodology
- Uncertainty analysis

#### CSRD Report (Europe)

ESRS standards for European companies:
- ESRS E1 - Climate Change
- Performance indicators
- Reduction targets
- Due diligence

#### Excel Export

Raw data for analysis:
- Detailed transactions
- Emissions by category
- Emission factors used
- GHG Protocol compatible format

### Generate a Report

1. Menu **Reports** → **New Report**
2. Select the type
3. Choose the period
4. Select sites
5. Click **Generate**
6. Download (PDF, Word, or Excel)

### Scheduling

Schedule automatic reports:
- Frequency: monthly, quarterly, annual
- Recipients: your team's emails
- Format: PDF or Excel

---

## Suppliers

### Supplier Portal

Invite your suppliers to share their carbon data:

1. Menu **Suppliers** → **Invite**
2. Enter supplier email and name
3. Supplier receives limited access
4. They enter their emission data
5. You receive data automatically

### Collected Data

- Emissions per unit sold
- Specific emission factors
- Environmental certifications
- Reduction targets

### Benefits

- **Accuracy**: Real data vs. estimates
- **Engagement**: Involve your value chain
- **Compliance**: CSRD requirement for Scope 3

---

## Account Settings

### User Profile

- Name, surname, email
- Language (EN/FR/DE)
- Timezone
- Notifications

### Organization

- Legal information
- Logo
- Reference year
- Industry sector

### Team

Invite collaborators:

| Role | Rights |
|------|--------|
| Administrator | Full access |
| Editor | Entry and modification |
| Viewer | View only |

### Subscription

Manage your subscription:
- Current plan and usage
- Invoice history
- Plan change
- Payment methods

### AI Assistant

Configure your AI preferences:
- Preferred model
- Response language
- Detail level

---

## FAQ

### General

**Q: Is Carbex GDPR compliant?**
> Yes, your data is hosted in Europe (OVH France) and encrypted. You can export or delete your data at any time.

**Q: Can I try for free?**
> Yes, the Free plan allows testing with 100 AI requests/month and all basic features.

### Data

**Q: Where do emission factors come from?**
> We use official databases: ADEME (France), UBA (Germany), GHG Protocol (international).

**Q: How are emissions calculated?**
> Emissions (kgCO₂e) = Activity Data × Emission Factor. For example: 100 kWh × 0.052 = 5.2 kgCO₂e

**Q: Can I use my own factors?**
> Yes, Professional and Enterprise plans allow customizing factors.

### Banking

**Q: Is my banking data secure?**
> Yes, we use PSD2-certified aggregators (Bridge, FinAPI). We never have access to your credentials.

**Q: Can I disconnect my bank?**
> Yes, menu Connections → click on the bank → Disconnect.

### Reports

**Q: Are reports legally valid?**
> Our reports follow regulatory formats (GHG Protocol, CSRD). We recommend third-party verification for legal obligations.

**Q: Can I customize reports?**
> Yes, add your logo and customize sections in Settings → Reports.

---

## Support

### Contact

- **Email**: support@carbex.app
- **Chat**: Icon at bottom right (business hours)
- **Documentation**: docs.carbex.app

### Resources

- [Help Center](https://help.carbex.app)
- [Video Tutorials](https://carbex.app/tutorials)
- [Blog](https://carbex.app/blog)
- [Webinars](https://carbex.app/webinars)

---

*Last updated: January 2026*
*Version: 1.0*
