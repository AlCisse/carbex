# Architecture Decision Records (ADR)

This directory contains Architecture Decision Records for the LinsCarbon project.

## What are ADRs?

An Architecture Decision Record (ADR) is a document that captures an important architectural decision made along with its context and consequences.

## ADR Template

Use the following template for new ADRs:

```markdown
# ADR-XXX: Title

## Status

[Proposed | Accepted | Deprecated | Superseded by ADR-XXX]

## Context

What is the issue that we're seeing that is motivating this decision or change?

## Decision

What is the change that we're proposing and/or doing?

## Consequences

What becomes easier or more difficult to do because of this change?
```

## ADR Index

| ID | Title | Status | Date |
|----|-------|--------|------|
| [ADR-001](001-multi-tenant-architecture.md) | Multi-Tenant Architecture | Accepted | 2024-01-15 |
| [ADR-002](002-emission-calculation-engine.md) | Emission Calculation Engine | Accepted | 2024-02-01 |
| [ADR-003](003-bank-integration-provider.md) | Bank Integration Provider Selection | Accepted | 2024-02-15 |
| [ADR-004](004-api-versioning-strategy.md) | API Versioning Strategy | Accepted | 2024-03-01 |
| [ADR-005](005-report-generation-approach.md) | Report Generation Approach | Accepted | 2024-03-15 |
| [ADR-0001](0001-livewire-for-frontend.md) | Livewire pour le Frontend (FR) | Accepted | 2024-12-30 |
| [ADR-0002](0002-ghg-protocol-scopes.md) | GHG Protocol Scopes (FR) | Accepted | 2024-12-30 |
| [ADR-0003](0003-multi-tenancy-approach.md) | Approche Multi-Tenant (FR) | Accepted | 2024-12-30 |
| [ADR-0004](0004-emission-factors-source.md) | Facteurs d'Émission ADEME (FR) | Accepted | 2024-12-30 |
| [ADR-0005](0005-report-generation-architecture.md) | Génération de Rapports (FR) | Accepted | 2024-12-30 |

## Creating a New ADR

1. Create a new file: `XXX-title-with-dashes.md`
2. Use the template above
3. Submit a pull request for review
4. Update this README index once accepted
