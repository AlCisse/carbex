<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Portail Fournisseur - {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div x-data="supplierPortal()" class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Portail Fournisseur</h1>
                    <p class="text-gray-600 mt-1">{{ $organization->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Fournisseur</p>
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
                    <h3 class="text-sm font-medium text-blue-800">Collecte de donnees carbone {{ $invitation->year }}</h3>
                    <p class="mt-1 text-sm text-blue-700">
                        Veuillez renseigner vos emissions de gaz a effet de serre pour l'annee {{ $invitation->year }}.
                        Ces donnees nous permettront de calculer notre empreinte carbone Scope 3.
                    </p>
                    <p class="mt-2 text-sm text-blue-600">
                        <strong>Date limite :</strong> {{ $invitation->expires_at->format('d/m/Y') }}
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
                    Emissions Scope 1 - Emissions directes
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    Emissions provenant de sources detenues ou controlees par votre organisation (combustibles, vehicules, procedes).
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Scope 1 (tCO2e)</label>
                        <input type="number" step="0.01" x-model="form.scope1_total"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>

                <details class="mt-4">
                    <summary class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">Detailler par categorie</summary>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 pl-4 border-l-2 border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Combustion stationnaire (tCO2e)</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.stationary_combustion"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Combustion mobile (tCO2e)</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.mobile_combustion"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Emissions fugitives (tCO2e)</label>
                            <input type="number" step="0.01" x-model="form.scope1_breakdown.fugitive_emissions"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Emissions de procedes (tCO2e)</label>
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
                    Emissions Scope 2 - Energie indirecte
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    Emissions liees a l'electricite, la chaleur ou la vapeur achetee.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scope 2 Location-based (tCO2e)</label>
                        <input type="number" step="0.01" x-model="form.scope2_location"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">Base sur le mix electrique du reseau</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scope 2 Market-based (tCO2e)</label>
                        <input type="number" step="0.01" x-model="form.scope2_market"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">Base sur vos contrats d'energie</p>
                    </div>
                </div>
            </div>

            <!-- Company Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-bold mr-2">3</span>
                    Informations entreprise
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    Ces informations permettent de calculer votre intensite carbone.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Chiffre d'affaires annuel</label>
                        <input type="number" step="0.01" x-model="form.revenue"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Devise</label>
                        <select x-model="form.revenue_currency"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="EUR">EUR</option>
                            <option value="CHF">CHF</option>
                            <option value="GBP">GBP</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre d'employes</label>
                        <input type="number" x-model="form.employees"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Verification -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 text-sm font-bold mr-2">4</span>
                    Verification (optionnel)
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Norme de verification</label>
                        <select x-model="form.verification_standard"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Non verifie</option>
                            <option value="ISO 14064-1">ISO 14064-1</option>
                            <option value="ISO 14064-3">ISO 14064-3</option>
                            <option value="GHG Protocol">GHG Protocol</option>
                            <option value="BEGES">BEGES</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Verificateur</label>
                        <input type="text" x-model="form.verifier_name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de verification</label>
                        <input type="date" x-model="form.verification_date"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700">Notes ou commentaires</label>
                <textarea x-model="form.notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                          placeholder="Informations supplementaires, methodologie utilisee, hypotheses..."></textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Vos donnees seront traitees de maniere confidentielle.
                </p>
                <button type="submit" :disabled="isSubmitting"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                    <span x-show="!isSubmitting">Soumettre mes donnees</span>
                    <span x-show="isSubmitting">Envoi en cours...</span>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Donnees envoyees avec succes !</h3>
                    <p class="text-sm text-gray-500">
                        Merci pour votre contribution. Vos donnees ont ete transmises a {{ $organization->name }}.
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
                            alert(data.message || 'Une erreur est survenue.');
                        }
                    } catch (error) {
                        alert('Une erreur est survenue. Veuillez reessayer.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
