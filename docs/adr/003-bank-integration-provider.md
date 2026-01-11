# ADR-003: Bank Integration Provider Selection

## Status

Accepted

## Date

2024-02-15

## Context

Carbex needs to connect to users' bank accounts to automatically fetch transactions for carbon footprint calculation. This requires integration with a banking data aggregation service.

### Requirements

- Support for European banks (focus on France initially)
- PSD2 compliance
- Reliable transaction categorization
- Reasonable pricing for SaaS model
- Good API documentation and support

### Providers Evaluated

| Provider | EU Coverage | Pricing | API Quality | PSD2 Status |
|----------|-------------|---------|-------------|-------------|
| Bridge | 500+ banks, strong FR | Per-connection | Excellent | Licensed |
| Plaid | Limited EU | Per-connection | Excellent | Partner mode |
| Tink | Good EU | Higher pricing | Good | Licensed |
| Nordigen | Good EU | Free tier | Good | Licensed |
| Salt Edge | Broad | Per-user | Good | Licensed |

## Decision

We chose **Bridge** as our primary banking data provider.

### Rationale

1. **French Market Focus**: Bridge has excellent coverage of French banks (our initial target market)

2. **PSD2 Compliance**: Bridge is a licensed Account Information Service Provider (AISP)

3. **MCC Enrichment**: Bridge provides enhanced transaction data including MCC codes, which directly supports our emission categorization

4. **Pricing Model**: Per-connection pricing aligns with our subscription model

5. **API Quality**: Well-documented REST API with webhooks for real-time updates

### Integration Architecture

```
┌─────────────────┐         ┌─────────────────┐
│   Carbex App    │         │    Bridge API   │
│                 │         │                 │
│  ┌───────────┐  │         │  ┌───────────┐  │
│  │  OAuth    │──┼─────────┼─▶│  Connect  │  │
│  │  Flow     │  │         │  │  Widget   │  │
│  └───────────┘  │         │  └───────────┘  │
│       │         │         │       │         │
│       ▼         │         │       ▼         │
│  ┌───────────┐  │◀────────┼──┌───────────┐  │
│  │  Webhook  │  │         │  │  Callback │  │
│  │  Handler  │  │         │  │           │  │
│  └───────────┘  │         │  └───────────┘  │
│       │         │         │                 │
│       ▼         │         │                 │
│  ┌───────────┐  │────────▶│  ┌───────────┐  │
│  │   Sync    │  │         │  │  /accounts │  │
│  │   Job     │  │         │  │  /trans.   │  │
│  └───────────┘  │         │  └───────────┘  │
└─────────────────┘         └─────────────────┘
```

### Key Implementation Points

1. **Connection Flow**
   ```php
   // Initiate connection
   $redirectUrl = $bridgeClient->connect([
       'user_uuid' => $organization->id,
       'callback_url' => route('banking.callback'),
   ]);

   // Handle callback
   public function handleCallback(Request $request)
   {
       $connection = BankConnection::create([
           'organization_id' => auth()->user()->organization_id,
           'provider' => 'bridge',
           'external_id' => $request->item_id,
           'status' => 'connected',
       ]);

       SyncBankAccounts::dispatch($connection);
   }
   ```

2. **Transaction Sync**
   ```php
   // Hourly sync job
   public function syncTransactions(BankConnection $connection)
   {
       $transactions = $this->bridgeClient->getTransactions(
           $connection->external_id,
           $connection->last_sync_at
       );

       foreach ($transactions as $tx) {
           Transaction::updateOrCreate(
               ['external_id' => $tx['id']],
               $this->mapTransaction($tx)
           );
       }

       $connection->update(['last_sync_at' => now()]);
   }
   ```

3. **Error Handling**
   - Connection expiry detection and user notification
   - Retry logic for temporary failures
   - Fallback to manual refresh if needed

## Consequences

### Positive

- Rapid time-to-market with reliable bank connectivity
- Rich transaction data including MCC codes
- Webhook support for real-time updates
- Good compliance posture (PSD2 licensed)

### Negative

- Vendor lock-in to Bridge
- Per-connection costs affect margins
- Limited to supported banks
- Need to handle consent renewal (90-day cycle)

### Mitigations

- Abstraction layer (`BankingProviderInterface`) allows future provider additions
- Multi-provider support designed into data model
- User notification workflow for consent renewal
- Fallback to manual upload for unsupported banks

## Related

- [ADR-002](002-emission-calculation-engine.md) - MCC codes used for categorization
- Bank account types mapping documentation
