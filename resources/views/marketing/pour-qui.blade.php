@extends('layouts.marketing')

@section('title', 'Pour qui ? - Carbex')
@section('description', 'Carbex s\'adapte a toutes les tailles d\'entreprise : PME, ETI et grandes entreprises. Decouvrez comment notre plateforme repond a vos besoins.')

@section('content')
<!-- Hero -->
<section class="pt-32 pb-16" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <p class="text-sm font-medium mb-4" style="color: var(--accent);">Pour qui ?</p>
        <h1 class="text-4xl lg:text-5xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
            Une solution pour chaque entreprise
        </h1>
        <p class="text-lg" style="color: var(--text-secondary);">
            De la PME au grand groupe, Carbex s'adapte a vos besoins et a votre maturite carbone.
        </p>
    </div>
</section>

<!-- Pourquoi nous choisir (T103) -->
<section class="py-20" style="background-color: var(--bg-primary);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">Pourquoi choisir Carbex ?</h2>
            <p class="text-lg" style="color: var(--text-secondary);">3 bonnes raisons de nous faire confiance</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Mesurez votre impact -->
            <div class="bg-white rounded-2xl p-8 border hover-lift" style="border-color: var(--border);">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-7 h-7" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">Mesurez votre impact</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-secondary);">
                    Realisez facilement votre premier bilan carbone complet (Scope 1, 2, 3), sans expert et sans engagement. Notre assistant IA vous guide a chaque etape.
                </p>
            </div>

            <!-- Pilotez votre transition -->
            <div class="bg-white rounded-2xl p-8 border hover-lift" style="border-color: var(--border);">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-6" style="background-color: #dbeafe;">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">Pilotez votre transition</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-secondary);">
                    Suivez vos emissions dans le temps, fixez des objectifs de reduction alignes SBTi et construisez un plan d'action concret avec des recommandations personnalisees.
                </p>
            </div>

            <!-- Repondez aux obligations -->
            <div class="bg-white rounded-2xl p-8 border hover-lift" style="border-color: var(--border);">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-6" style="background-color: #fef3c7;">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">Repondez aux obligations</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-secondary);">
                    Generez automatiquement vos rapports RSE, BEGES, CSRD ou ESG, et demontrez votre conformite aux reglementations francaises et europeennes.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Target Audiences (T105) -->
<section class="py-20" style="background-color: var(--bg-card);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">Adapte a votre structure</h2>
            <p class="text-lg" style="color: var(--text-secondary);">Que vous soyez PME, ETI ou grande entreprise</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- PME -->
            <div class="bg-white rounded-2xl overflow-hidden border hover-lift" style="border-color: var(--border);">
                <div class="p-6" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-4 bg-white shadow-sm">
                        <svg class="w-8 h-8" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold" style="color: var(--text-primary);">PME</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">10 a 250 salaries</p>
                </div>
                <div class="p-6">
                    <p class="text-sm mb-6" style="color: var(--text-secondary);">
                        Mesurez votre empreinte carbone pour optimiser vos couts, repondre a la reglementation et ameliorer votre image aupres de vos clients.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Premier bilan en moins d'une journee
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Pas besoin d'expert carbone
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Tarifs adaptes
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-primary mt-6 block w-full py-3 text-center text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                        Demarrer l'essai gratuit
                    </a>
                </div>
            </div>

            <!-- ETI -->
            <div class="bg-white rounded-2xl overflow-hidden border hover-lift" style="border-color: var(--border);">
                <div class="p-6" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-4 bg-white shadow-sm">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold" style="color: var(--text-primary);">ETI</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">250 a 5000 salaries</p>
                </div>
                <div class="p-6">
                    <p class="text-sm mb-6" style="color: var(--text-secondary);">
                        Suivez l'impact global de votre organisation, reduisez les emissions sur plusieurs sites et repondez aux demandes de vos clients grands comptes.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Gestion multi-sites
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Conformite BEGES obligatoire
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Reporting automatise
                        </li>
                    </ul>
                    <a href="{{ route('register') }}?plan=premium" class="btn-primary mt-6 block w-full py-3 text-center text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                        Choisir Premium
                    </a>
                </div>
            </div>

            <!-- Grandes Entreprises -->
            <div class="bg-white rounded-2xl overflow-hidden border hover-lift" style="border-color: var(--border);">
                <div class="p-6" style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-4 bg-white shadow-sm">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold" style="color: var(--text-primary);">Grandes Entreprises</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Plus de 5000 salaries</p>
                </div>
                <div class="p-6">
                    <p class="text-sm mb-6" style="color: var(--text-secondary);">
                        Gerez votre empreinte carbone mondiale, respectez les normes internationales et optimisez vos strategies de reduction avec un accompagnement dedie.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            CSRD et GRI ready
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            API et integrations
                        </li>
                        <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Support dedie
                        </li>
                    </ul>
                    <a href="/contact" class="mt-6 block w-full py-3 text-center text-sm font-medium rounded-xl border" style="color: var(--text-primary); border-color: var(--border);">
                        Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Clients de reference (T104) -->
<section class="py-16" style="background-color: var(--bg-primary);">
    <div class="max-w-6xl mx-auto px-6">
        <p class="text-center text-sm font-medium mb-10" style="color: var(--text-muted);">Ils nous font confiance</p>
        <div class="flex flex-wrap items-center justify-center gap-12 opacity-60">
            <!-- Placeholder logos -->
            <div class="h-10 flex items-center justify-center px-6 rounded-lg" style="background-color: #f1f5f9;">
                <span class="text-lg font-semibold" style="color: var(--text-muted);">SUEZ</span>
            </div>
            <div class="h-10 flex items-center justify-center px-6 rounded-lg" style="background-color: #f1f5f9;">
                <span class="text-lg font-semibold" style="color: var(--text-muted);">VAUBAN</span>
            </div>
            <div class="h-10 flex items-center justify-center px-6 rounded-lg" style="background-color: #f1f5f9;">
                <span class="text-lg font-semibold" style="color: var(--text-muted);">NEODD</span>
            </div>
            <div class="h-10 flex items-center justify-center px-6 rounded-lg" style="background-color: #f1f5f9;">
                <span class="text-lg font-semibold" style="color: var(--text-muted);">ADEME</span>
            </div>
        </div>
    </div>
</section>

<!-- Temoignage (T106) -->
<section class="py-20" style="background-color: var(--bg-card);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-semibold mb-4" style="color: var(--text-primary);">Ce qu'en disent nos clients</h2>
        </div>

        <div class="bg-white rounded-2xl p-8 md:p-12 border relative" style="border-color: var(--border);">
            <svg class="absolute top-6 left-8 w-12 h-12 opacity-10" style="color: var(--accent);" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
            <div class="relative">
                <p class="text-lg md:text-xl leading-relaxed mb-8" style="color: var(--text-secondary);">
                    "Le support expert nous a ete precieux pour affiner nos interpretations. La possibilite d'importer automatiquement nos FEC et de gerer plusieurs sites a fait toute la difference. C'est un outil robuste et professionnel."
                </p>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-lg font-semibold text-white" style="background-color: var(--accent);">
                        AB
                    </div>
                    <div>
                        <p class="font-semibold" style="color: var(--text-primary);">Aicha Benhamou</p>
                        <p class="text-sm" style="color: var(--text-muted);">Directrice Developpement Durable — Terres & Saveurs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-semibold mb-5" style="color: var(--text-primary);">
            Pret a mesurer votre empreinte ?
        </h2>
        <p class="text-lg mb-10" style="color: var(--text-secondary);">
            Commencez gratuitement et decouvrez comment Carbex peut vous aider.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="btn-primary inline-flex items-center px-6 py-3.5 text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                Essai gratuit 15 jours
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center px-6 py-3.5 text-sm font-medium rounded-xl border-2" style="color: var(--accent); border-color: var(--accent);">
                Voir les tarifs
            </a>
        </div>
    </div>
</section>
@endsection
