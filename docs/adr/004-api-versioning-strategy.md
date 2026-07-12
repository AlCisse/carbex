# ADR-004: API Versioning Strategy

## Status

Accepted

## Date

2024-03-01

## Context

LinsCarbon exposes a REST API for:
- Web application (SPA via Inertia.js)
- Mobile applications (future)
- External integrations (API keys)
- Webhooks

We need a versioning strategy that:
- Allows non-breaking changes without version bump
- Provides clear deprecation path
- Maintains backward compatibility
- Is easy for clients to adopt

### Options Considered

1. **URL Path Versioning**
   - `/api/v1/emissions`, `/api/v2/emissions`
   - Pros: Explicit, cacheable, easy to understand
   - Cons: URL pollution, harder to sunset

2. **Header Versioning**
   - `Accept: application/vnd.linscarbon.v1+json`
   - Pros: Clean URLs, flexible
   - Cons: Not visible in browser, harder debugging

3. **Query Parameter Versioning**
   - `/api/emissions?version=1`
   - Pros: Easy to test
   - Cons: Pollutes query string, caching issues

4. **No Versioning (Additive Only)**
   - Never remove fields, only add
   - Pros: Simplest for clients
   - Cons: API bloat over time

## Decision

We chose **URL Path Versioning** (`/api/v1/...`) as our primary strategy.

### Implementation

```
routes/
├── api.php              # Loads version routes
├── api/
│   ├── v1.php           # Version 1 routes
│   └── v2.php           # Future version 2 routes (when needed)
```

```php
// routes/api.php
Route::prefix('v1')->group(base_path('routes/api/v1.php'));
Route::prefix('v2')->group(base_path('routes/api/v2.php'));
```

### Versioning Rules

1. **Major Version (v1 → v2)**
   - Breaking changes to existing endpoints
   - Removal of fields or endpoints
   - Changed authentication mechanism

2. **Non-Breaking Changes (No Version Bump)**
   - Adding new endpoints
   - Adding new optional fields
   - Adding new optional parameters
   - Bug fixes that don't change contracts

### Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message",
  "meta": {
    "api_version": "v1",
    "deprecation_notice": null
  }
}
```

### Deprecation Strategy

1. **Announce**: Add `deprecation_notice` to responses
2. **Sunset Header**: `Sunset: Sat, 31 Dec 2025 23:59:59 GMT`
3. **Documentation**: Update docs with migration guide
4. **Email**: Notify API key holders
5. **Remove**: After 6-month deprecation period

```php
// Example deprecation response
{
  "success": true,
  "data": { ... },
  "meta": {
    "api_version": "v1",
    "deprecation_notice": {
      "message": "This endpoint is deprecated. Use /v2/emissions instead.",
      "sunset_date": "2025-06-01",
      "migration_guide": "https://docs.linscarbon.app/api/migration/v1-to-v2"
    }
  }
}
```

## Consequences

### Positive

- Clear, explicit versioning visible in URLs
- Easy to route traffic and monitor by version
- Clients can migrate at their own pace
- Good cache-key separation

### Negative

- URL "pollution" with version prefix
- Need to maintain multiple versions in parallel
- Risk of forgetting to deprecate old versions

### Implementation Guidelines

1. **Controllers**: Version-specific controllers when needed
   ```
   app/Http/Controllers/Api/V1/EmissionController.php
   app/Http/Controllers/Api/V2/EmissionController.php
   ```

2. **Resources**: Version-specific transformers
   ```
   app/Http/Resources/V1/EmissionResource.php
   app/Http/Resources/V2/EmissionResource.php
   ```

3. **Shared Logic**: Services remain version-agnostic
   ```
   app/Services/Emissions/EmissionService.php  # Shared
   ```

## Related

- API documentation standards
- Client SDK versioning strategy
