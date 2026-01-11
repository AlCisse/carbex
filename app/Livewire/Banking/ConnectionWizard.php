<?php

namespace App\Livewire\Banking;

use App\Jobs\SyncBankTransactions;
use App\Models\BankConnection;
use App\Services\Banking\BridgeService;
use App\Services\Banking\FinapiService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ConnectionWizard extends Component
{
    // Wizard state
    public int $step = 1;
    public string $country = '';
    public string $selectedBankId = '';
    public string $searchQuery = '';
    public bool $isLoading = false;
    public ?string $errorMessage = null;
    public ?string $redirectUrl = null;

    // Connection result
    public ?string $connectionId = null;
    public bool $connectionSuccess = false;

    protected $queryString = ['step'];

    public function mount(): void
    {
        // Default to organization's country
        $this->country = auth()->user()->organization->country ?? 'FR';
    }

    #[Computed]
    public function banks(): Collection
    {
        if (empty($this->country)) {
            return collect();
        }

        $provider = $this->getProviderForCountry($this->country);

        if (! $provider) {
            return collect();
        }

        $banks = $provider->getBanks($this->country);

        // Filter by search query
        if (! empty($this->searchQuery)) {
            $query = strtolower($this->searchQuery);
            $banks = $banks->filter(function ($bank) use ($query) {
                return str_contains(strtolower($bank['name']), $query);
            });
        }

        return $banks->sortBy('name');
    }

    #[Computed]
    public function connections(): Collection
    {
        return BankConnection::where('organization_id', auth()->user()->organization_id)
            ->with('accounts')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function hasActiveConnections(): bool
    {
        return $this->connections->where('status', 'active')->isNotEmpty();
    }

    public function selectCountry(string $country): void
    {
        $this->country = strtoupper($country);
        $this->selectedBankId = '';
        $this->searchQuery = '';
        $this->step = 2;
    }

    public function selectBank(string $bankId): void
    {
        $this->selectedBankId = $bankId;
        $this->step = 3;
    }

    public function initiateConnection(): void
    {
        if (empty($this->selectedBankId) || empty($this->country)) {
            $this->errorMessage = __('Please select a bank first.');

            return;
        }

        $this->isLoading = true;
        $this->errorMessage = null;

        try {
            $provider = $this->getProviderForCountry($this->country);

            if (! $provider) {
                throw new \RuntimeException(__('No banking provider available for this country.'));
            }

            $result = $provider->initiateConnection(
                auth()->user()->organization_id,
                $this->selectedBankId,
                route('banking.callback', ['provider' => $provider->getProvider()])
            );

            $this->redirectUrl = $result['redirect_url'];
            $this->step = 4;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function handleCallback(string $code, string $state): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        try {
            $provider = $this->getProviderForCountry($this->country);

            if (! $provider) {
                throw new \RuntimeException(__('Provider not found.'));
            }

            $connection = $provider->handleCallback(
                $code,
                $state,
                auth()->user()->organization_id
            );

            $this->connectionId = $connection->id;
            $this->connectionSuccess = true;
            $this->step = 5;

            // Queue sync
            SyncBankTransactions::dispatch($connection);

            $this->dispatch('connection-created', connectionId: $connection->id);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function syncConnection(string $connectionId): void
    {
        $connection = BankConnection::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($connectionId);

        SyncBankTransactions::dispatch($connection);

        $this->dispatch('sync-started', connectionId: $connectionId);
    }

    public function disconnectBank(string $connectionId): void
    {
        $connection = BankConnection::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($connectionId);

        $provider = $this->getProvider($connection->provider);

        if ($provider) {
            $provider->disconnect($connection);
        }

        $connection->delete();

        $this->dispatch('connection-removed', connectionId: $connectionId);
    }

    public function resetWizard(): void
    {
        $this->step = 1;
        $this->selectedBankId = '';
        $this->searchQuery = '';
        $this->errorMessage = null;
        $this->redirectUrl = null;
        $this->connectionSuccess = false;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    private function getProviderForCountry(string $country)
    {
        $bridgeService = app(BridgeService::class);
        $finapiService = app(FinapiService::class);

        if ($bridgeService->supportsCountry($country)) {
            return $bridgeService;
        }

        if ($finapiService->supportsCountry($country)) {
            return $finapiService;
        }

        return null;
    }

    private function getProvider(string $provider)
    {
        return match ($provider) {
            'bridge' => app(BridgeService::class),
            'finapi' => app(FinapiService::class),
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.banking.connection-wizard');
    }
}
