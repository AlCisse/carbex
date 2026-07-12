<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('linscarbon.supplier_portal.title') }} - {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div x-data="supplierPortal()" class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('linscarbon.supplier_portal.title') }}</h1>
                    <p class="text-gray-600 mt-1">{{ $organization->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ __('linscarbon.supplier_portal.supplier') }}</p>
                    <p class="font-semibold text-gray-900">{{ $supplier->name }}</p>
                </div>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ __('linscarbon.supplier_portal.data_collection', ['year' => $invitation->year]) }}</h3>
                    <p class="mt-1 text-sm text-blue-700">
                        {{ __('linscarbon.supplier_portal.data_collection_desc', ['year' => $invitation->year]) }}
                    </p>
                    <p class="mt-2 text-sm text-blue-600">
                        <strong>{{ __('linscarbon.supplier_portal.deadline') }}</strong> {{ $invitation->expires_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Scope 1 -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 text-sm font-bold mr-2">1</span>
                    {{ __('linscarbon.supplier_portal.scope1_title') }}
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('linscarbon.supplier_portal.scope1_desc') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.scope1_total') }}</label>
                        <input type="number" step="0.01" x-model="form.scope1_total"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>

                <details class="mt-4">
                    <summary class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">{{ __('linscarbon.supplier_portal.detail_by_category') }}</summary>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 pl-4 border-l-2 border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.stationary_combustion') }}</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.stationary_combustion"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.mobile_combustion') }}</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.mobile_combustion"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.fugitive_emissions') }}</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.fugitive_emissions"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.process_emissions') }}</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.process_emissions"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                </details>
            </div>

            <!-- Scope 2 -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 text-sm font-bold mr-2">2</span>
                    {{ __('linscarbon.supplier_portal.scope2_title') }}
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('linscarbon.supplier_portal.scope2_desc') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.scope2_location') }}</label>
                        <input type="number" step="0.01" x-model="form.scope2_location"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">{{ __('linscarbon.supplier_portal.scope2_location_hint') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.scope2_market') }}</label>
                        <input type="number" step="0.01" x-model="form.scope2_market"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">{{ __('linscarbon.supplier_portal.scope2_market_hint') }}</p>
                    </div>
                </div>
            </div>

            <!-- Company Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-bold mr-2">3</span>
                    {{ __('linscarbon.supplier_portal.company_info') }}
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('linscarbon.supplier_portal.company_info_desc') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.annual_revenue') }}</label>
                        <input type="number" step="0.01" x-model="form.revenue"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.currency') }}</label>
                        <select x-model="form.revenue_currency"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="EUR">EUR</option>
                            <option value="CHF">CHF</option>
                            <option value="GBP">GBP</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.employees_count') }}</label>
                        <input type="number" x-model="form.employees"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Verification -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 text-sm font-bold mr-2">4</span>
                    {{ __('linscarbon.supplier_portal.verification') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.verification_standard') }}</label>
                        <select x-model="form.verification_standard"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">{{ __('linscarbon.supplier_portal.not_verified') }}</option>
                            <option value="ISO 14064-1">ISO 14064-1</option>
                            <option value="ISO 14064-3">ISO 14064-3</option>
                            <option value="GHG Protocol">GHG Protocol</option>
                            <option value="BEGES">BEGES</option>
                            <option value="Other">{{ __('linscarbon.supplier_portal.other') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.verifier') }}</label>
                        <input type="text" x-model="form.verifier_name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.verification_date') }}</label>
                        <input type="date" x-model="form.verification_date"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700">{{ __('linscarbon.supplier_portal.notes') }}</label>
                <textarea x-model="form.notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                          placeholder="{{ __('linscarbon.supplier_portal.notes_placeholder') }}"></textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    {{ __('linscarbon.supplier_portal.confidentiality_notice') }}
                </p>
                <button type="submit" :disabled="isSubmitting"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                    <span x-show="!isSubmitting">{{ __('linscarbon.supplier_portal.submit') }}</span>
                    <span x-show="isSubmitting">{{ __('linscarbon.supplier_portal.submitting') }}</span>
                </button>
            </div>
        </form>

        <!-- Success Modal -->
        <div x-show="showSuccess" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="relative bg-white rounded-lg max-w-md w-full p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('linscarbon.supplier_portal.success_title') }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ __('linscarbon.supplier_portal.success_message', ['organization' => $organization->name]) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function supplierPortal() {
            return {
                isSubmitting: false,
                showSuccess: false,
                form: {
                    scope1_total: {{ $existingData?->scope1_total ?? 'null' }},
                    scope1_breakdown: {
                        stationary_combustion: null,
                        mobile_combustion: null,
                        fugitive_emissions: null,
                        process_emissions: null,
                    },
                    scope2_location: {{ $existingData?->scope2_location ?? 'null' }},
                    scope2_market: {{ $existingData?->scope2_market ?? 'null' }},
                    revenue: {{ $existingData?->revenue ?? 'null' }},
                    revenue_currency: '{{ $existingData?->revenue_currency ?? 'EUR' }}',
                    employees: {{ $existingData?->employees ?? 'null' }},
                    verification_standard: '{{ $existingData?->verification_standard ?? '' }}',
                    verifier_name: '{{ $existingData?->verifier_name ?? '' }}',
                    verification_date: '{{ $existingData?->verification_date?->format('Y-m-d') ?? '' }}',
                    notes: '{{ $existingData?->notes ?? '' }}',
                },

                async submitForm() {
                    this.isSubmitting = true;

                    try {
                        const response = await fetch('{{ url("/supplier-portal/{$invitation->token}/submit") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify(this.form),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showSuccess = true;
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        } else {
                            alert(data.message || '{{ __('linscarbon.supplier_portal.error_occurred') }}');
                        }
                    } catch (error) {
                        alert('{{ __('linscarbon.supplier_portal.error_retry') }}');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
