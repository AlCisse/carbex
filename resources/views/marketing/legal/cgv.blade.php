@extends('layouts.marketing')

@section('title', 'Conditions Generales de Vente - Carbex')
@section('description', 'Conditions Generales de Vente de la plateforme Carbex, outil SaaS de bilan carbone pour entreprises.')

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">Legal</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                Conditions Generales de Vente
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">Derniere mise a jour : Decembre 2024</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">Article 1 - Objet</h2>
            <p>
                Les presentes Conditions Generales de Vente (CGV) regissent les relations contractuelles entre la societe Carbex SAS (ci-apres "Carbex") et tout client professionnel (ci-apres "le Client") souhaitant souscrire aux services de la plateforme Carbex.
            </p>
            <p>
                Carbex propose une solution SaaS (Software as a Service) de bilan carbone et de gestion des emissions de gaz a effet de serre, conforme aux standards GHG Protocol, ISO 14064 et ADEME.
            </p>

            <h2 style="color: var(--text-primary);">Article 2 - Services proposes</h2>
            <p>Carbex propose les services suivants :</p>
            <ul>
                <li>Realisation de bilans carbone (Scopes 1, 2 et 3)</li>
                <li>Acces a la Base Carbone ADEME et aux facteurs d'emission</li>
                <li>Tableaux de bord et analyses des emissions</li>
                <li>Plans de transition et suivi des actions de reduction</li>
                <li>Generation de rapports conformes (BEGES, CSRD, GHG Protocol)</li>
                <li>Assistant IA pour l'aide a la saisie et les recommandations</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 3 - Tarifs et modalites de paiement</h2>
            <h3 style="color: var(--text-primary);">3.1 Grille tarifaire</h3>
            <p>Les tarifs en vigueur sont les suivants (HT) :</p>
            <ul>
                <li><strong>Essai Gratuit</strong> : 0 EUR - 15 jours d'acces complet</li>
                <li><strong>Premium</strong> : 400 EUR/an ou 40 EUR/mois</li>
                <li><strong>Avance</strong> : 1 200 EUR/an ou 120 EUR/mois</li>
                <li><strong>Enterprise</strong> : Sur devis</li>
            </ul>

            <h3 style="color: var(--text-primary);">3.2 Modalites de paiement</h3>
            <p>
                Le paiement s'effectue par carte bancaire via notre prestataire de paiement securise Stripe. Les factures sont emises mensuellement ou annuellement selon le mode de facturation choisi.
            </p>
            <p>
                La TVA applicable est celle en vigueur au jour de la facturation (20% pour la France metropolitaine).
            </p>

            <h2 style="color: var(--text-primary);">Article 4 - Duree et resiliation</h2>
            <h3 style="color: var(--text-primary);">4.1 Duree</h3>
            <p>
                L'abonnement est souscrit pour une duree determinee (mensuelle ou annuelle) renouvelable par tacite reconduction.
            </p>

            <h3 style="color: var(--text-primary);">4.2 Resiliation</h3>
            <p>
                Le Client peut resilier son abonnement a tout moment depuis son espace client. La resiliation prend effet a la fin de la periode de facturation en cours. Aucun remboursement au prorata ne sera effectue.
            </p>

            <h2 style="color: var(--text-primary);">Article 5 - Obligations de Carbex</h2>
            <p>Carbex s'engage a :</p>
            <ul>
                <li>Fournir un acces continu a la plateforme (objectif de disponibilite 99,5%)</li>
                <li>Maintenir la securite et la confidentialite des donnees</li>
                <li>Assurer un support technique selon le plan souscrit</li>
                <li>Mettre a jour regulierement les facteurs d'emission</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 6 - Obligations du Client</h2>
            <p>Le Client s'engage a :</p>
            <ul>
                <li>Fournir des informations exactes et a jour</li>
                <li>Ne pas partager ses identifiants de connexion</li>
                <li>Respecter les presentes CGV et les CGU</li>
                <li>Payer les sommes dues dans les delais impartis</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 7 - Propriete intellectuelle</h2>
            <p>
                La plateforme Carbex, son code source, ses algorithmes, sa charte graphique et ses contenus sont la propriete exclusive de Carbex SAS. Le Client beneficie uniquement d'un droit d'usage non exclusif et non cessible pendant la duree de son abonnement.
            </p>

            <h2 style="color: var(--text-primary);">Article 8 - Responsabilite</h2>
            <p>
                Les calculs d'emissions fournis par Carbex sont bases sur les donnees saisies par le Client et les facteurs d'emission officiels. Carbex ne saurait etre tenu responsable de l'exactitude des resultats en cas de donnees erronees fournies par le Client.
            </p>
            <p>
                La responsabilite de Carbex est limitee au montant des sommes effectivement percues au titre de l'abonnement sur les 12 derniers mois.
            </p>

            <h2 style="color: var(--text-primary);">Article 9 - Protection des donnees</h2>
            <p>
                Carbex s'engage a respecter la reglementation en vigueur relative a la protection des donnees personnelles (RGPD). Pour plus d'informations, consultez notre <a href="{{ route('mentions-legales') }}" style="color: var(--accent);">Politique de confidentialite</a>.
            </p>

            <h2 style="color: var(--text-primary);">Article 10 - Droit applicable et litiges</h2>
            <p>
                Les presentes CGV sont soumises au droit francais. En cas de litige, les parties s'engagent a rechercher une solution amiable. A defaut, les tribunaux de Paris seront seuls competents.
            </p>

            <h2 style="color: var(--text-primary);">Article 11 - Modification des CGV</h2>
            <p>
                Carbex se reserve le droit de modifier les presentes CGV. Les modifications seront notifiees aux Clients par email au moins 30 jours avant leur entree en vigueur.
            </p>

        </div>
    </div>
</section>
@endsection
