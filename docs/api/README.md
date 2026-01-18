# LinsCarbon API Documentation

## Overview

The LinsCarbon API provides programmatic access to carbon footprint tracking and management features. The API follows RESTful conventions and uses JSON for request/response bodies.

**Base URL:** `https://api.linscarbon.app/api/v1`

**API Version:** v1

## Authentication

### Session Authentication (Web App)

For the web application, authentication is handled via Laravel Sanctum with session cookies.

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### API Key Authentication (External Integrations)

For external integrations, use API keys:

```bash
GET /api/v1/external/emissions
Authorization: Bearer cbx_your_api_key_here
# or
X-API-Key: cbx_your_api_key_here
```

API keys can be created in Settings > API Keys. Each key has:
- Configurable scopes (read:emissions, write:transactions, etc.)
- Rate limits (per minute and per day)
- IP restrictions (optional)
- Expiration date (optional)

## Rate Limiting

| Plan | Requests/minute | Requests/day |
|------|-----------------|--------------|
| Starter | 60 | 10,000 |
| Professional | 120 | 50,000 |
| Enterprise | 300 | 200,000 |

Rate limit headers are included in all responses:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Requests remaining
- `X-RateLimit-Reset`: Unix timestamp when limit resets

## Response Format

All responses follow this structure:

```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message"
}
```

Error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error"]
  }
}
```

## Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register new organization |
| POST | `/auth/login` | Login and get token |
| POST | `/auth/logout` | Logout (authenticated) |
| GET | `/auth/me` | Get current user |
| PUT | `/auth/me` | Update profile |
| POST | `/auth/change-password` | Change password |
| POST | `/auth/forgot-password` | Request password reset |
| POST | `/auth/reset-password` | Reset password with token |

### Organization

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/organization` | Get current organization |
| PUT | `/organization` | Update organization |
| GET | `/organization/stats` | Get organization statistics |

### Sites

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/sites` | List all sites |
| POST | `/sites` | Create a site |
| GET | `/sites/{id}` | Get site details |
| PUT | `/sites/{id}` | Update site |
| DELETE | `/sites/{id}` | Delete site |

### Banking

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/banking/connections` | List bank connections |
| POST | `/banking/connections/initiate` | Start bank connection flow |
| POST | `/banking/connections/callback` | Handle OAuth callback |
| DELETE | `/banking/connections/{id}` | Remove connection |
| POST | `/banking/connections/{id}/sync` | Trigger manual sync |
| GET | `/banking/accounts` | List bank accounts |
| PUT | `/banking/accounts/{id}` | Update account settings |
| POST | `/banking/accounts/{id}/toggle-sync` | Enable/disable sync |

### Transactions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/transactions` | List transactions (paginated) |
| GET | `/transactions/pending-review` | Get transactions needing review |
| GET | `/transactions/stats` | Get transaction statistics |
| GET | `/transactions/{id}` | Get transaction details |
| PUT | `/transactions/{id}/categorize` | Categorize transaction |
| PUT | `/transactions/{id}/validate` | Validate categorization |
| PUT | `/transactions/{id}/exclude` | Exclude from calculations |
| POST | `/transactions/bulk-categorize` | Bulk categorize transactions |
| POST | `/transactions/import` | Import transactions from file |

### Emissions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/emissions` | List all emissions |
| GET | `/emissions/summary` | Get emissions summary |
| GET | `/emissions/by-scope` | Breakdown by scope |
| GET | `/emissions/by-category` | Breakdown by category |
| GET | `/emissions/by-site` | Breakdown by site |
| GET | `/emissions/timeline` | Timeline data |
| GET | `/emissions/comparison` | Year-over-year comparison |

### Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard` | Full dashboard data |
| GET | `/dashboard/kpis` | Key performance indicators |
| GET | `/dashboard/scope-breakdown` | Scope 1/2/3 breakdown |
| GET | `/dashboard/trends` | Emission trends |
| GET | `/dashboard/categories` | Top categories |
| GET | `/dashboard/sites` | Site comparison |
| GET | `/dashboard/intensity` | Carbon intensity metrics |

### Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports` | List generated reports |
| POST | `/reports` | Generate new report |
| GET | `/reports/{id}` | Get report details |
| GET | `/reports/{id}/download` | Download report file |
| DELETE | `/reports/{id}` | Delete report |

### Subscription

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/subscription` | Get current subscription |
| GET | `/subscription/plans` | List available plans |
| POST | `/subscription/checkout` | Start checkout session |
| POST | `/subscription/portal` | Access billing portal |
| POST | `/subscription/change-plan` | Change plan |
| POST | `/subscription/cancel` | Cancel subscription |
| POST | `/subscription/resume` | Resume cancelled subscription |
| GET | `/subscription/invoices` | List invoices |

### API Keys

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api-keys` | List API keys |
| POST | `/api-keys` | Create API key |
| GET | `/api-keys/{id}` | Get key details |
| PUT | `/api-keys/{id}` | Update key |
| DELETE | `/api-keys/{id}` | Revoke key |
| POST | `/api-keys/{id}/regenerate` | Regenerate key |
| GET | `/api-keys/scopes` | List available scopes |

### Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/webhooks` | List webhooks |
| POST | `/webhooks` | Create webhook |
| GET | `/webhooks/{id}` | Get webhook details |
| PUT | `/webhooks/{id}` | Update webhook |
| DELETE | `/webhooks/{id}` | Delete webhook |
| POST | `/webhooks/{id}/test` | Send test event |
| POST | `/webhooks/{id}/regenerate-secret` | Regenerate secret |
| GET | `/webhooks/{id}/deliveries` | Get delivery history |
| POST | `/webhooks/{id}/deliveries/{delivery}/retry` | Retry failed delivery |
| GET | `/webhooks/events` | List available events |

### External API (API Key Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/external/emissions` | Get emissions data |
| GET | `/external/emissions/summary` | Get emissions summary |
| GET | `/external/emissions/by-scope` | Scope breakdown |
| GET | `/external/organization` | Get organization info |
| GET | `/external/sites` | List sites |
| GET | `/external/reports` | List reports |
| GET | `/external/reports/{id}` | Get report details |

## Webhook Events

Configure webhooks to receive real-time notifications:

| Event | Description |
|-------|-------------|
| `emission.calculated` | New emissions calculated |
| `transaction.synced` | New transactions synced |
| `transaction.categorized` | Transaction categorized |
| `report.generated` | Report generation complete |
| `report.failed` | Report generation failed |
| `subscription.created` | New subscription |
| `subscription.updated` | Subscription changed |
| `subscription.cancelled` | Subscription cancelled |
| `invoice.paid` | Invoice paid |
| `user.invited` | User invited |
| `user.joined` | User accepted invitation |
| `bank.connected` | Bank account connected |
| `bank.disconnected` | Bank account disconnected |
| `bank.sync_completed` | Bank sync completed |
| `bank.sync_failed` | Bank sync failed |

### Webhook Signature Verification

All webhooks are signed using HMAC-SHA256. Verify signatures:

```php
$signature = hash_hmac('sha256', $payload, $webhookSecret);
$isValid = hash_equals($signature, $receivedSignature);
```

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid or missing authentication |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource doesn't exist |
| 422 | Validation Error - Check errors object |
| 429 | Rate Limited - Too many requests |
| 500 | Server Error - Contact support |

## SDKs

Official SDKs coming soon:
- JavaScript/TypeScript
- Python
- PHP

## Support

- Documentation: https://docs.linscarbon.app
- API Status: https://status.linscarbon.app
- Email: api-support@linscarbon.app
