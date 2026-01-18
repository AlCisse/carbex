# ADR-001: Multi-Tenant Architecture

## Status

Accepted

## Date

2024-01-15

## Context

LinsCarbon is a SaaS platform serving multiple organizations. We need to decide on a multi-tenancy strategy that balances:

- Data isolation and security
- Operational complexity
- Cost efficiency
- Performance
- Scalability

### Options Considered

1. **Single database, shared schema with tenant column**
   - All tenants share tables, filtered by `organization_id`
   - Pros: Simple, cost-effective, easy maintenance
   - Cons: Risk of data leakage, complex queries

2. **Single database, separate schemas per tenant**
   - Each tenant has its own PostgreSQL schema
   - Pros: Better isolation, easier per-tenant backup
   - Cons: Migration complexity, schema drift risk

3. **Separate database per tenant**
   - Each tenant has its own database
   - Pros: Full isolation, easy compliance
   - Cons: High operational overhead, expensive

4. **Hybrid approach**
   - Small tenants share, large tenants get dedicated resources
   - Pros: Flexible, cost-optimized
   - Cons: Complex implementation

## Decision

We chose **Option 1: Single database with tenant column isolation**.

### Implementation Details

1. **Tenant Identification**
   - All tenant-scoped tables include `organization_id` column
   - UUIDs used for all IDs to prevent enumeration attacks

2. **Query Scoping**
   - `BelongsToOrganization` trait automatically scopes all queries
   - Global scope applied at model boot time
   - Manual scoping disabled for admin operations only

3. **Security Measures**
   - Row-level policies enforced at application layer
   - Foreign key constraints ensure referential integrity
   - Audit logging for sensitive operations

```php
// Example: BelongsToOrganization trait
trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if ($organizationId = static::getCurrentOrganizationId()) {
                $builder->where('organization_id', $organizationId);
            }
        });

        static::creating(function (Model $model) {
            if (!$model->organization_id) {
                $model->organization_id = static::getCurrentOrganizationId();
            }
        });
    }
}
```

## Consequences

### Positive

- Simple to implement and maintain
- Cost-effective (single database instance)
- Easy to query across all tenants for admin purposes
- Simplified backup and restore procedures
- Straightforward migration management

### Negative

- Requires discipline to always include tenant scoping
- Potential for data leakage bugs if global scope bypassed incorrectly
- All tenants affected by database performance issues
- Harder to comply with data residency requirements for specific tenants

### Mitigations

- Comprehensive test suite verifying tenant isolation
- Code review checklist for tenant-scoped queries
- Regular security audits
- Consider database-level row security for critical tables

## Related

- [ADR-002](002-emission-calculation-engine.md) - Emission calculations scoped per tenant
