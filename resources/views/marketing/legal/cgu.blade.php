@extends('layouts.marketing')

@section('title', 'Conditions Generales d\'Utilisation - Carbex')
@section('description', 'Conditions Generales d\'Utilisation de la plateforme Carbex, outil SaaS de bilan carbone.')

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">Legal</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                Conditions Generales d'Utilisation
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">Derniere mise a jour : Decembre 2024</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">Article 1 - Definitions</h2>
            <ul>
                <li><strong>Plateforme</strong> : L'application web Carbex accessible a l'adresse app.carbex.fr</li>
                <li><strong>Utilisateur</strong> : Toute personne accedant a la Plateforme</li>
                <li><strong>Compte</strong> : Espace personnel de l'Utilisateur sur la Plateforme</li>
                <li><strong>Organisation</strong> : Entite juridique (entreprise) pour laquelle le bilan carbone est realise</li>
                <li><strong>Bilan</strong> : Evaluation des emissions de gaz a effet de serre sur une periode donnee</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 2 - Acceptation des CGU</h2>
            <p>
                L'utilisation de la Plateforme implique l'acceptation pleine et entiere des presentes Conditions Generales d'Utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser la Plateforme.
            </p>

            <h2 style="color: var(--text-primary);">Article 3 - Acces a la Plateforme</h2>
            <h3 style="color: var(--text-primary);">3.1 Inscription</h3>
            <p>
                L'acces aux fonctionnalites de la Plateforme necessite la creation d'un compte. L'Utilisateur s'engage a fournir des informations exactes et a les maintenir a jour.
            </p>

            <h3 style="color: var(--text-primary);">3.2 Securite du compte</h3>
            <p>
                L'Utilisateur est responsable de la confidentialite de ses identifiants de connexion. Toute activite realisee depuis son compte est presumee etre de son fait.
            </p>

            <h3 style="color: var(--text-primary);">3.3 Roles et permissions</h3>
            <p>La Plateforme propose differents niveaux d'acces :</p>
            <ul>
                <li><strong>Proprietaire</strong> : Acces complet, gestion des utilisateurs et facturation</li>
                <li><strong>Administrateur</strong> : Acces complet aux donnees, pas d'acces facturation</li>
                <li><strong>Membre</strong> : Saisie et consultation des donnees</li>
                <li><strong>Lecteur</strong> : Consultation uniquement</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 4 - Utilisation de la Plateforme</h2>
            <h3 style="color: var(--text-primary);">4.1 Usage autorise</h3>
            <p>La Plateforme est destinee a :</p>
            <ul>
                <li>Realiser des bilans carbone conformes aux standards GHG Protocol</li>
                <li>Suivre et analyser les emissions de gaz a effet de serre</li>
                <li>Definir et piloter des plans de reduction</li>
                <li>Generer des rapports reglementaires (BEGES, CSRD)</li>
            </ul>

            <h3 style="color: var(--text-primary);">4.2 Usages interdits</h3>
            <p>Il est strictement interdit de :</p>
            <ul>
                <li>Utiliser la Plateforme a des fins illegales ou frauduleuses</li>
                <li>Tenter de contourner les mesures de securite</li>
                <li>Extraire massivement des donnees (scraping)</li>
                <li>Partager son compte avec des tiers non autorises</li>
                <li>Utiliser la Plateforme pour generer des rapports mensongers</li>
                <li>Revendre ou sous-licencier l'acces a la Plateforme</li>
            </ul>

            <h2 style="color: var(--text-primary);">Article 5 - Donnees et contenu</h2>
            <h3 style="color: var(--text-primary);">5.1 Donnees de l'Utilisateur</h3>
            <p>
                L'Utilisateur reste proprietaire des donnees qu'il saisit sur la Plateforme. Carbex ne peut utiliser ces donnees que pour fournir le service et, de maniere anonymisee, pour ameliorer ses algorithmes.
            </p>

            <h3 style="color: var(--text-primary);">5.2 Facteurs d'emission</h3>
            <p>
                Les facteurs d'emission proviennent de sources officielles (Base Carbone ADEME, IPCC, etc.). Carbex ne garantit pas leur exactitude et invite l'Utilisateur a les verifier pour les usages reglementaires.
            </p>

            <h3 style="color: var(--text-primary);">5.3 Sauvegarde</h3>
            <p>
                Carbex effectue des sauvegardes regulieres des donnees. Cependant, l'Utilisateur est encourage a exporter regulierement ses donnees.
            </p>

            <h2 style="color: var(--text-primary);">Article 6 - Assistant IA</h2>
            <p>
                La Plateforme integre un assistant base sur l'intelligence artificielle. Les suggestions et recommandations fournies par l'IA sont indicatives et ne constituent pas un conseil professionnel. L'Utilisateur reste seul responsable des decisions prises sur la base de ces suggestions.
            </p>

            <h2 style="color: var(--text-primary);">Article 7 - Disponibilite</h2>
            <p>
                Carbex s'efforce d'assurer une disponibilite continue de la Plateforme. Cependant, des interruptions peuvent survenir pour maintenance ou en cas de force majeure. Carbex informera les Utilisateurs dans la mesure du possible.
            </p>

            <h2 style="color: var(--text-primary);">Article 8 - Propriete intellectuelle</h2>
            <p>
                Tous les elements de la Plateforme (code, design, textes, algorithmes) sont proteges par le droit d'auteur et appartiennent a Carbex SAS. Toute reproduction non autorisee est interdite.
            </p>

            <h2 style="color: var(--text-primary);">Article 9 - Limitation de responsabilite</h2>
            <p>
                Carbex fournit la Plateforme "en l'etat". En aucun cas Carbex ne pourra etre tenu responsable des dommages indirects, pertes de donnees ou manque a gagner lies a l'utilisation de la Plateforme.
            </p>

            <h2 style="color: var(--text-primary);">Article 10 - Suspension et resiliation</h2>
            <p>
                Carbex se reserve le droit de suspendre ou de resilier l'acces d'un Utilisateur en cas de violation des presentes CGU, apres notification prealable sauf en cas d'urgence.
            </p>

            <h2 style="color: var(--text-primary);">Article 11 - Modification des CGU</h2>
            <p>
                Carbex peut modifier les presentes CGU a tout moment. Les Utilisateurs seront informes des modifications substantielles. La poursuite de l'utilisation apres notification vaut acceptation.
            </p>

            <h2 style="color: var(--text-primary);">Article 12 - Contact</h2>
            <p>
                Pour toute question concernant ces CGU, contactez-nous a : <a href="mailto:legal@carbex.fr" style="color: var(--accent);">legal@carbex.fr</a>
            </p>

        </div>
    </div>
</section>
@endsection
