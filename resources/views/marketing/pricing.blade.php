@extends('layouts.marketing')

@section('title', 'Tarifs - Carbex')
@section('description', 'Decouvrez nos offres de bilan carbone pour PME. Essai gratuit 15 jours, puis a partir de 40EUR/mois.')

@section('content')
<div x-data="{ billingPeriod: 'annual' }">
    <!-- Hero -->
    <section class="pt-32 pb-16" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl lg:text-5xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                Tarifs simples et transparents
            </h1>
            <p class="text-lg mb-10" style="color: var(--text-secondary);">
                Commencez gratuitement. Evoluez selon vos besoins. Sans engagement.
            </p>

            <!-- Billing Toggle -->
            <div class="inline-flex items-center p-1.5 rounded-xl" style="background-color: #f1f5f9;">
                <button
                    @click="billingPeriod = 'monthly'"
                    :class="billingPeriod === 'monthly' ? 'bg-white shadow-sm' : ''"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-all"
                    :style="billingPeriod === 'monthly' ? 'color: var(--text-primary);' : 'color: var(--text-secondary);'"
                >
                    Mensuel
                </button>
                <button
                    @click="billingPeriod = 'annual'"
                    :class="billingPeriod === 'annual' ? 'bg-white shadow-sm' : ''"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-all flex items-center gap-2"
                    :style="billingPeriod === 'annual' ? 'color: var(--text-primary);' : 'color: var(--text-secondary);'"
                >
                    Annuel
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: var(--accent-light); color: var(--accent);">-17%</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16" style="background-color: var(--bg-primary);">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Free Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border flex flex-col" style="border-color: var(--border);">
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">Essai gratuit</p>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);">0</span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-6" style="color: var(--text-muted);">15 jours d'essai complet</p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">Inclus :</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                1 utilisateur
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                1 site
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Acces complet a la plateforme
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                1 rapport PDF
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Support par email
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="block w-full py-3.5 text-center text-sm font-medium rounded-xl border hover:bg-gray-50 transition-colors" style="color: var(--text-primary); border-color: var(--border);">
                        Commencer l'essai
                    </a>
                </div>

                <!-- Premium Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border-2 relative flex flex-col" style="border-color: var(--accent);">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1.5 text-xs font-semibold text-white rounded-full" style="background-color: var(--accent);">Le plus populaire</span>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">Premium</p>
                        <div class="flex items-baseline gap-1 mb-1">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);" x-text="billingPeriod === 'annual' ? '400' : '40'"></span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-2" style="color: var(--text-muted);" x-text="billingPeriod === 'annual' ? 'par an HT' : 'par mois HT'"></p>
                        <p class="text-xs mb-6" style="color: var(--accent);" x-show="billingPeriod === 'annual'">
                            Soit 33EUR/mois - Economisez 80EUR/an
                        </p>
                        <p class="text-xs mb-6" style="color: var(--text-muted);" x-show="billingPeriod === 'monthly'">
                            Sans engagement, annulez a tout moment
                        </p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">Tout de l'Essai, plus :</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Jusqu'a 5 utilisateurs
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Jusqu'a 3 sites
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Import bancaire automatique
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Rapports illimites (Word, Excel, PDF)
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Declarations ADEME et GHG Protocol
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Support prioritaire
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}?plan=premium" class="btn-primary block w-full py-3.5 text-center text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                        Choisir Premium
                    </a>
                </div>

                <!-- Advanced Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border flex flex-col" style="border-color: var(--border);">
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">Avance</p>
                        <div class="flex items-baseline gap-1 mb-1">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);" x-text="billingPeriod === 'annual' ? '1200' : '120'"></span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-2" style="color: var(--text-muted);" x-text="billingPeriod === 'annual' ? 'par an HT' : 'par mois HT'"></p>
                        <p class="text-xs mb-6" style="color: var(--accent);" x-show="billingPeriod === 'annual'">
                            Soit 100EUR/mois - Economisez 240EUR/an
                        </p>
                        <p class="text-xs mb-6" style="color: var(--text-muted);" x-show="billingPeriod === 'monthly'">
                            Sans engagement, annulez a tout moment
                        </p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">Tout de Premium, plus :</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Utilisateurs illimites
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Sites illimites
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Acces API complet
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Module fournisseurs Scope 3
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Support dedie
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Formation personnalisee
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}?plan=advanced" class="block w-full py-3.5 text-center text-sm font-medium rounded-xl border hover:bg-gray-50 transition-colors" style="color: var(--text-primary); border-color: var(--border);">
                        Choisir Avance
                    </a>
                </div>
            </div>

            <!-- Enterprise CTA -->
            <div class="mt-12 p-8 rounded-2xl text-center" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);">
                <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">Besoin d'une solution sur mesure ?</h3>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">Pour les grandes entreprises, groupes et consultants.</p>
                <a href="/contact" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-xl border-2" style="color: var(--accent); border-color: var(--accent);">
                    Contacter notre equipe
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Comparison -->
    <section class="py-20" style="background-color: var(--bg-card);">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="text-2xl font-semibold text-center mb-12" style="color: var(--text-primary);">Comparatif des fonctionnalites</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left py-4 font-medium" style="color: var(--text-primary);"></th>
                            <th class="text-center py-4 font-medium" style="color: var(--text-primary);">Essai</th>
                            <th class="text-center py-4 font-medium" style="color: var(--accent);">Premium</th>
                            <th class="text-center py-4 font-medium" style="color: var(--text-primary);">Avance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Utilisateurs</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">5</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Illimite</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Sites</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">3</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Illimite</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Rapports</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Illimite</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Illimite</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Import bancaire</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Export ADEME/GHG</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Acces API</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">Module fournisseurs</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr>
                            <td class="py-4" style="color: var(--text-secondary);">Support</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Email</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Prioritaire</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">Dedie</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20" style="background-color: var(--bg-primary);">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-2xl font-semibold text-center mb-12" style="color: var(--text-primary);">Questions frequentes</h2>

            <div class="space-y-4">
                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">Puis-je changer de plan a tout moment ?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        Oui, vous pouvez passer a un plan superieur a tout moment. La difference sera calculee au prorata. Pour passer a un plan inferieur, le changement prendra effet a la fin de votre periode de facturation.
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">Quelle est la duree de l'essai gratuit ?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        L'essai gratuit dure 15 jours avec un acces complet a toutes les fonctionnalites. Aucune carte bancaire n'est requise pour commencer.
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">Les prix sont-ils HT ou TTC ?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        Tous les prix affiches sont HT (hors taxes). La TVA applicable (20% en France) sera ajoutee lors du paiement.
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">Comment fonctionne le paiement mensuel ?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        Le paiement mensuel est preleve automatiquement chaque mois. Vous pouvez annuler a tout moment, sans frais ni penalite. L'abonnement reste actif jusqu'a la fin du mois paye.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20" style="background-color: var(--bg-card);">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-semibold mb-5" style="color: var(--text-primary);">
                Pret a mesurer votre impact ?
            </h2>
            <p class="text-lg mb-10" style="color: var(--text-secondary);">
                Rejoignez les PME qui prennent le controle de leur empreinte carbone.
            </p>
            <a href="{{ route('register') }}" class="btn-primary inline-flex items-center px-6 py-3.5 text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                Commencer gratuitement
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <p class="text-sm mt-4" style="color: var(--text-muted);">15 jours d'essai gratuit. Sans carte bancaire.</p>
        </div>
    </section>
</div>
@endsection
