# Carbex Administrator Guide

> Technical documentation for Carbex platform administrators

---

## Table of Contents

1. [Admin Panel Access](#admin-panel-access)
2. [Admin Dashboard](#admin-dashboard)
3. [Organization Management](#organization-management)
4. [User Management](#user-management)
5. [Site Management](#site-management)
6. [AI Configuration](#ai-configuration)
7. [Emission Factors](#emission-factors)
8. [Subscriptions and Billing](#subscriptions-and-billing)
9. [Content (Blog)](#content-blog)
10. [Monitoring and Logs](#monitoring-and-logs)
11. [Maintenance](#maintenance)

---

## Admin Panel Access

### Access URL

```
Production : https://carbex.app/admin
Staging    : https://staging.carbex.app/admin
Local      : http://localhost:8000/admin
```

### Authentication

1. Go to `/admin/login`
2. Enter your admin credentials
3. Complete 2FA verification if enabled

### Administrator Roles

| Role | Rights |
|------|--------|
| Super Admin | Full access, system configuration |
| Admin | Organization, user, content management |
| Support | Read-only, user assistance |

---

## Admin Dashboard

### Global Metrics

| Metric | Description |
|--------|-------------|
| **Active Organizations** | Organizations with recent activity |
| **Total Users** | Total registered users |
| **Calculated Emissions** | Total CO₂e processed on platform |
| **AI Requests** | Number of AI requests this month |

### Charts

- **Registrations**: New organizations per week
- **AI Usage**: Requests by provider (Claude, GPT, Gemini)
- **MRR Revenue**: Monthly Recurring Revenue by plan
- **Emissions**: Total volume processed per month

### Alerts

- Organizations with exhausted AI quota
- Expired bank connections
- Synchronization errors
- Users inactive for 30+ days

---

## Organization Management

### Organization List

**Menu**: Administration → Organizations

| Column | Description |
|--------|-------------|
| Name | Organization name |
| Plan | Current subscription |
| Users | Number of members |
| Emissions | Total calculated CO₂e |
| Status | Active / Suspended / Trial |
| Created | Registration date |

### Available Actions

- **View**: Full organization details
- **Edit**: Modify information and plan
- **Impersonate**: Log in as user
- **Suspend**: Temporarily block access
- **Delete**: Permanent deletion (soft delete)

### Create an Organization

1. Click **New Organization**
2. Fill in:
   - Organization name
   - Owner email
   - Subscription plan
   - Country (FR/DE/UK)
   - Industry sector
3. Click **Create**

The owner will receive an invitation email.

### Organization Details

#### Information Tab

- Legal data (VAT, Tax ID)
- Address
- Industry sector
- Creation date
- Carbon reference year

#### Users Tab

List of members with roles:
- Owner
- Administrator
- Editor
- Viewer

#### Sites Tab

List of organization sites with:
- Name and type
- Address
- Area and headcount
- Associated emissions

#### Subscription Tab

- Current plan
- Start/end date
- Payment history
- Quota usage

#### Activity Tab

Audit log:
- Logins
- Modifications
- Data exports
- AI requests

---

## User Management

### User List

**Menu**: Administration → Users

| Column | Description |
|--------|-------------|
| Name | Full name |
| Email | Email address |
| Organization | Linked organization |
| Role | Role in organization |
| Last Login | Date/time |
| Status | Active / Inactive / Banned |

### Filters

- By organization
- By role
- By status
- By registration date

### User Actions

- **Reset Password**: Sends reset email
- **Verify Email**: Force verification
- **Impersonate**: Log in as the user
- **Ban**: Permanently block access
- **Delete**: Delete account

### Impersonation

To debug a user issue:

1. Click the "Impersonate" icon
2. You are logged in as the user
3. Orange banner indicates impersonation
4. Click "Return to Admin" to exit

> **Warning**: All actions are logged with impersonation mention.

---

## Site Management

### Site List

**Menu**: Administration → Sites

| Column | Description |
|--------|-------------|
| Name | Site name |
| Organization | Owner organization |
| Type | Headquarters, office, factory, etc. |
| Country | Location |
| Emissions | Total CO₂e |

### Site Types

| Type | Code | Description |
|------|------|-------------|
| Headquarters | `headquarters` | Main office |
| Office | `office` | Administrative site |
| Factory | `factory` | Production site |
| Warehouse | `warehouse` | Storage/logistics |
| Store | `store` | Point of sale |
| Data center | `datacenter` | IT infrastructure |
| Other | `other` | Uncategorized |

### Site Validation

Review pending sites:
- Valid address
- Area/headcount consistency
- No duplicates

---

## AI Configuration

### Access

**Menu**: Settings → AI Configuration

### Default Provider

Select the main AI provider:
- **Anthropic (Claude)** - Recommended
- **OpenAI (GPT)**
- **Google (Gemini)**
- **DeepSeek**

### Provider Configuration

#### Anthropic (Claude)

| Parameter | Description |
|-----------|-------------|
| Enable | Toggle on/off |
| Model | Claude Sonnet 4, Claude 3.5, etc. |
| Status | Key configured or not |

#### OpenAI (GPT)

| Parameter | Description |
|-----------|-------------|
| Enable | Toggle on/off |
| Model | GPT-4o, GPT-4o Mini, o1, etc. |
| Status | Key configured or not |

#### Google (Gemini)

| Parameter | Description |
|-----------|-------------|
| Enable | Toggle on/off |
| Model | Gemini 2.0 Flash, Gemini 1.5 Pro |
| Status | Key configured or not |

#### DeepSeek

| Parameter | Description |
|-----------|-------------|
| Enable | Toggle on/off |
| Model | DeepSeek Chat, DeepSeek Coder |
| Status | Key configured or not |

### Advanced Settings

| Parameter | Default Value | Description |
|-----------|---------------|-------------|
| Max Tokens | 4096 | Token limit per response |
| Temperature | 0.7 | 0.0 = deterministic, 1.0 = creative |

### Models by Subscription

Configure the AI model assigned to each plan:

| Plan | Tokens/month | Requests/month | Default Model |
|------|--------------|----------------|---------------|
| Free | 50K | 100 | Gemini 2.0 Flash Lite |
| Starter | 200K | 500 | GPT-4o Mini |
| Professional | 1M | 2500 | Claude Sonnet 4 |
| Enterprise | Unlimited | Unlimited | Claude Sonnet 4 |

To modify:
1. Select the model in the plan dropdown
2. Changes are saved automatically
3. Confirmation notification appears

### API Keys

#### Add a Key

1. Enter the key in the provider field
2. Click **Save**
3. Provider is automatically enabled

#### Test a Connection

1. Click **Test** next to the provider
2. A test call is made
3. Success/failure notification

#### Remove a Key

1. Click **Delete**
2. Confirm deletion
3. Provider is disabled

### Key Security

> API keys are encrypted with AES-256 before database storage. They are never exposed in plain text in the interface.

---

## Emission Factors

### Access

**Menu**: Carbon Data → Emission Factors

### Available Sources

| Source | Country | Categories |
|--------|---------|------------|
| ADEME | France | Energy, transport, purchases |
| UBA | Germany | Energy, industry |
| GHG Protocol | International | All scopes |
| DEFRA | UK | Energy, transport |

### Factor Structure

| Field | Description |
|-------|-------------|
| Name | Factor label |
| Category | Scope and subcategory |
| Value | kgCO₂e per unit |
| Unit | kWh, km, €, kg, etc. |
| Source | Origin database |
| Year | Reference year |
| Uncertainty | % uncertainty |

### Import Factors

1. Menu **Emission Factors** → **Import**
2. Select source (ADEME, UBA, etc.)
3. Choose year
4. Click **Import**

### Update Factors

ADEME factors are updated annually:

```bash
php artisan db:seed --class=AdemeFactorSeeder
```

### Custom Factors

For Enterprise clients:
1. Create a new factor
2. Enter specific values
3. Associate with the relevant organization

---

## Subscriptions and Billing

### Access

**Menu**: Finance → Subscriptions

### Available Plans

| Plan | Price/month | Features |
|------|-------------|----------|
| Free | €0 | Limited, 1 user |
| Starter | €49 | 3 users, 2 sites |
| Professional | €149 | 10 users, unlimited sites |
| Enterprise | Custom | All unlimited, dedicated support |

### Subscription Management

#### View a Subscription

- Current plan
- Start date
- Next billing
- Payment methods

#### Change a Plan

1. Select the organization
2. Click **Change Plan**
3. Select new plan
4. Confirm (proration calculated automatically)

#### Cancel a Subscription

1. Click **Cancel**
2. Select reason
3. Access remains active until period end

### Stripe Billing

Payments are managed via Stripe:

- Credit cards (Visa, Mastercard)
- SEPA (direct debit)
- Automatic invoices

### Coupons and Discounts

1. Menu **Coupons** → **New**
2. Coupon code
3. Type (% or fixed amount)
4. Validity period
5. Usage limit

---

## Content (Blog)

### Access

**Menu**: Content → Blog Articles

### Create an Article

1. Click **New Article**
2. Fill in:
   - Title
   - Slug (URL)
   - Content (Markdown editor)
   - Cover image
   - Category
   - Tags
   - Meta description (SEO)
3. Status: Draft or Published
4. Publication date (scheduling possible)

### Categories

- News
- Regulations (CSRD, GHG Protocol)
- Practical Guides
- Case Studies
- Webinars

### SEO

Each article includes:
- Meta title
- Meta description
- Open Graph image
- Canonical URL

---

## Monitoring and Logs

### Audit Log

**Menu**: Monitoring → Audit Log

All actions are tracked:
- User
- Action (create, update, delete)
- Resource concerned
- Before/after data
- IP and User-Agent
- Timestamp

### Audit Filters

- By user
- By action type
- By resource
- By period

### System Errors

**Menu**: Monitoring → Errors

Sentry integration for:
- PHP exceptions
- JavaScript errors
- API timeouts
- Queue errors

### Metrics

| Metric | Description |
|--------|-------------|
| Response time | Average request latency |
| Error rate | % of failed requests |
| Queue jobs | Pending/processed jobs |
| Cache hit rate | Redis cache efficiency |

---

## Maintenance

### Useful Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild cache
php artisan optimize

# Reindex search
php artisan scout:flush "App\Models\Transaction"
php artisan scout:import "App\Models\Transaction"

# Process pending jobs
php artisan queue:work

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Scheduled Tasks

| Task | Frequency | Description |
|------|-----------|-------------|
| `bank:sync` | Every 6h | Bank synchronization |
| `reports:generate` | Daily | Scheduled reports |
| `subscriptions:check` | Daily | Expiration check |
| `cleanup:temp` | Weekly | Temp files cleanup |
| `backup:run` | Daily | Database backup |

### Backups

Backups are automatic:
- **Database**: Daily, 30-day retention
- **Files**: Weekly, 4-week retention
- **Storage**: OVH Object Storage (S3 compatible)

Restore:
```bash
php artisan backup:list
php artisan backup:restore --backup=backup-2026-01-18.zip
```

### Maintenance Mode

Enable:
```bash
php artisan down --secret="admin-secret-token"
```

Admin access during maintenance:
```
https://carbex.app/admin-secret-token
```

Disable:
```bash
php artisan up
```

---

## Technical Contacts

### Team

| Role | Contact |
|------|---------|
| Lead Dev | dev@carbex.app |
| DevOps | ops@carbex.app |
| N2 Support | support@carbex.app |

### Escalation

1. **N1**: Customer support (chat, email)
2. **N2**: Technical support (bugs, config)
3. **N3**: Development (critical incidents)

### Technical Documentation

- [Developer Guide](./DEVELOPER_GUIDE.md)
- [API Reference](./api/README.md)
- [Architecture Decisions](./adr/README.md)
- [Deployment Runbook](./deployment-runbook.md)

---

*Last updated: January 2026*
*Version: 1.0*
