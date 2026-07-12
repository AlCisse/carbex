# ADR-002: Emission Calculation Engine

## Status

Accepted

## Date

2024-02-01

## Context

LinsCarbon needs to calculate greenhouse gas emissions from various data sources:

- Bank transactions (spend-based)
- Energy consumption data (activity-based)
- Travel data (distance-based)
- Supplier-provided data

The calculation engine must:

- Support multiple calculation methodologies
- Handle different emission factor databases (ADEME, DEFRA, EPA, IEA)
- Provide audit trail for calculations
- Allow for recalculation when factors are updated

### Options Considered

1. **Inline calculation in controllers**
   - Simple, direct approach
   - Pros: Easy to implement initially
   - Cons: No reusability, hard to test, no audit trail

2. **Service class approach**
   - Dedicated service classes for calculations
   - Pros: Testable, reusable, clean separation
   - Cons: More initial setup

3. **Event-driven calculation**
   - Calculations triggered by events, processed async
   - Pros: Decoupled, scalable
   - Cons: Eventual consistency, more complex

4. **External calculation service**
   - Separate microservice for calculations
   - Pros: Independent scaling, language flexibility
   - Cons: Network overhead, operational complexity

## Decision

We chose a **hybrid of Options 2 and 3**: Service classes for calculation logic with event-driven triggering for async processing.

### Architecture

```
┌─────────────────┐     ┌─────────────────┐
│  Transaction    │────▶│ TransactionSynced│
│  Categorized    │     │     Event        │
└─────────────────┘     └────────┬─────────┘
                                 │
                                 ▼
                        ┌─────────────────┐
                        │CalculateEmissions│
                        │    Listener      │
                        └────────┬─────────┘
                                 │
                                 ▼
                        ┌─────────────────┐
                        │EmissionCalculator│
                        │    Service       │
                        └────────┬─────────┘
                                 │
                    ┌────────────┼────────────┐
                    ▼            ▼            ▼
             ┌───────────┐ ┌───────────┐ ┌───────────┐
             │  ADEME    │ │  DEFRA    │ │   IEA     │
             │  Factors  │ │  Factors  │ │  Factors  │
             └───────────┘ └───────────┘ └───────────┘
```

### Key Components

1. **EmissionCalculator Service**
   ```php
   class EmissionCalculator
   {
       public function calculateFromTransaction(Transaction $tx): Emission
       public function calculate(array $activityData): Emission
       public function recalculate(Emission $emission): Emission
   }
   ```

2. **EmissionFactorService**
   ```php
   class EmissionFactorService
   {
       public function findFactor(string $category, string $country, Carbon $date): EmissionFactor
       public function getSpendFactor(string $mcc, string $currency): float
   }
   ```

3. **Calculation Strategies**
   - `SpendBasedCalculation`: Uses spend × factor
   - `ActivityBasedCalculation`: Uses quantity × factor
   - `DistanceBasedCalculation`: Uses distance × mode factor

## Consequences

### Positive

- Clear separation of concerns
- Fully testable calculation logic
- Audit trail through Emission records
- Easy to add new calculation methods
- Async processing prevents blocking
- Recalculation capability built-in

### Negative

- Eventual consistency (emissions may lag behind transactions)
- Need to manage emission factor versioning
- Complexity in handling factor updates

### Implementation Details

```php
// Example calculation flow
class EmissionCalculator
{
    public function calculateFromTransaction(Transaction $transaction): Emission
    {
        $strategy = $this->selectStrategy($transaction);
        $factor = $this->factorService->findFactor(
            $transaction->category,
            $transaction->country ?? 'FR',
            $transaction->date
        );

        $co2_kg = $strategy->calculate($transaction, $factor);

        return Emission::create([
            'organization_id' => $transaction->organization_id,
            'transaction_id' => $transaction->id,
            'scope' => $factor->scope,
            'category' => $transaction->category,
            'co2_kg' => $co2_kg,
            'calculation_method' => $strategy->name(),
            'emission_factor_id' => $factor->id,
            'date' => $transaction->date,
        ]);
    }
}
```

## Related

- [ADR-001](001-multi-tenant-architecture.md) - Tenant scoping for emissions
- Emission Factor update strategy (future ADR)
