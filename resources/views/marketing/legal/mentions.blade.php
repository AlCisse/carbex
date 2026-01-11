@extends('layouts.marketing')

@section('title', 'Mentions Legales - Carbex')
@section('description', 'Mentions legales et politique de confidentialite de Carbex, plateforme de bilan carbone pour entreprises.')

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">Legal</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                Mentions Legales
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">Derniere mise a jour : Decembre 2024</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">1. Editeur du site</h2>
            <p>
                Le site carbex.fr est edite par :
            </p>
            <div class="bg-gray-50 p-6 rounded-xl my-6">
                <p class="mb-2"><strong>Carbex SAS</strong></p>
                <p class="mb-2">Societe par Actions Simplifiee au capital de 10 000 EUR</p>
                <p class="mb-2">Siege social : 123 Avenue de la Republique, 75011 Paris, France</p>
                <p class="mb-2">RCS Paris : XXX XXX XXX</p>
                <p class="mb-2">SIRET : XXX XXX XXX XXXXX</p>
                <p class="mb-2">TVA intracommunautaire : FR XX XXX XXX XXX</p>
                <p class="mb-2">Directeur de la publication : [Nom du dirigeant]</p>
                <p>Email : <a href="mailto:contact@carbex.fr" style="color: var(--accent);">contact@carbex.fr</a></p>
            </div>

            <h2 style="color: var(--text-primary);">2. Hebergement</h2>
            <p>Le site est heberge par :</p>
            <div class="bg-gray-50 p-6 rounded-xl my-6">
                <p class="mb-2"><strong>Scaleway SAS</strong></p>
                <p class="mb-2">8 rue de la Ville l'Eveque, 75008 Paris, France</p>
                <p>Site web : <a href="https://www.scaleway.com" target="_blank" rel="noopener" style="color: var(--accent);">www.scaleway.com</a></p>
            </div>

            <h2 style="color: var(--text-primary);">3. Politique de confidentialite</h2>

            <h3 style="color: var(--text-primary);">3.1 Responsable du traitement</h3>
            <p>
                Carbex SAS est responsable du traitement des donnees personnelles collectees sur ce site, conformement au Reglement General sur la Protection des Donnees (RGPD).
            </p>

            <h3 style="color: var(--text-primary);">3.2 Donnees collectees</h3>
            <p>Nous collectons les donnees suivantes :</p>
            <ul>
                <li><strong>Donnees d'identification</strong> : nom, prenom, email professionnel</li>
                <li><strong>Donnees de l'entreprise</strong> : raison sociale, SIRET, secteur d'activite</li>
                <li><strong>Donnees de connexion</strong> : adresse IP, logs de connexion</li>
                <li><strong>Donnees metier</strong> : consommations energetiques, donnees d'emissions</li>
            </ul>

            <h3 style="color: var(--text-primary);">3.3 Finalites du traitement</h3>
            <p>Vos donnees sont utilisees pour :</p>
            <ul>
                <li>Fournir le service de bilan carbone</li>
                <li>Gerer votre compte et votre abonnement</li>
                <li>Vous envoyer des communications liees au service</li>
                <li>Ameliorer nos services (statistiques anonymisees)</li>
                <li>Respecter nos obligations legales</li>
            </ul>

            <h3 style="color: var(--text-primary);">3.4 Base legale</h3>
            <p>Le traitement de vos donnees repose sur :</p>
            <ul>
                <li>L'execution du contrat (fourniture du service)</li>
                <li>Notre interet legitime (amelioration du service)</li>
                <li>Votre consentement (newsletter, cookies non essentiels)</li>
                <li>Nos obligations legales (conservation des factures)</li>
            </ul>

            <h3 style="color: var(--text-primary);">3.5 Duree de conservation</h3>
            <ul>
                <li><strong>Donnees de compte</strong> : 3 ans apres la fin de l'abonnement</li>
                <li><strong>Donnees de facturation</strong> : 10 ans (obligation legale)</li>
                <li><strong>Logs de connexion</strong> : 1 an</li>
                <li><strong>Donnees metier</strong> : supprimees sur demande ou 3 ans apres inactivite</li>
            </ul>

            <h3 style="color: var(--text-primary);">3.6 Destinataires des donnees</h3>
            <p>Vos donnees peuvent etre partagees avec :</p>
            <ul>
                <li><strong>Stripe</strong> : traitement des paiements</li>
                <li><strong>Scaleway</strong> : hebergement</li>
                <li><strong>Anthropic (Claude AI)</strong> : assistant IA (donnees anonymisees)</li>
                <li><strong>Brevo</strong> : envoi d'emails transactionnels</li>
            </ul>
            <p>Aucune donnee n'est transferee hors de l'Union Europeenne sans garanties adequates.</p>

            <h3 style="color: var(--text-primary);">3.7 Vos droits</h3>
            <p>Conformement au RGPD, vous disposez des droits suivants :</p>
            <ul>
                <li><strong>Droit d'acces</strong> : obtenir une copie de vos donnees</li>
                <li><strong>Droit de rectification</strong> : corriger vos donnees inexactes</li>
                <li><strong>Droit a l'effacement</strong> : demander la suppression de vos donnees</li>
                <li><strong>Droit a la portabilite</strong> : recevoir vos donnees dans un format structure</li>
                <li><strong>Droit d'opposition</strong> : vous opposer au traitement</li>
                <li><strong>Droit a la limitation</strong> : limiter le traitement de vos donnees</li>
            </ul>
            <p>
                Pour exercer ces droits, contactez notre DPO : <a href="mailto:dpo@carbex.fr" style="color: var(--accent);">dpo@carbex.fr</a>
            </p>
            <p>
                En cas de litige, vous pouvez saisir la CNIL : <a href="https://www.cnil.fr" target="_blank" rel="noopener" style="color: var(--accent);">www.cnil.fr</a>
            </p>

            <h2 style="color: var(--text-primary);">4. Cookies</h2>

            <h3 style="color: var(--text-primary);">4.1 Cookies essentiels</h3>
            <p>Ces cookies sont necessaires au fonctionnement du site :</p>
            <ul>
                <li><strong>Session</strong> : maintien de votre connexion</li>
                <li><strong>CSRF</strong> : securite contre les attaques</li>
                <li><strong>Preferences</strong> : langue, theme</li>
            </ul>

            <h3 style="color: var(--text-primary);">4.2 Cookies analytiques</h3>
            <p>
                Nous utilisons Plausible Analytics, une solution respectueuse de la vie privee qui ne collecte pas de donnees personnelles et ne necessite pas de consentement.
            </p>

            <h3 style="color: var(--text-primary);">4.3 Gestion des cookies</h3>
            <p>
                Vous pouvez gerer vos preferences de cookies via le bandeau de consentement ou les parametres de votre navigateur.
            </p>

            <h2 style="color: var(--text-primary);">5. Propriete intellectuelle</h2>
            <p>
                L'ensemble du contenu du site (textes, images, logos, code source) est protege par le droit d'auteur et appartient a Carbex SAS. Toute reproduction sans autorisation est interdite.
            </p>
            <p>
                Les marques "Carbex" et "Empreinte Carbone" sont deposees. Les logos des partenaires et clients sont utilises avec leur autorisation.
            </p>

            <h2 style="color: var(--text-primary);">6. Limitation de responsabilite</h2>
            <p>
                Carbex s'efforce de fournir des informations exactes et a jour. Cependant, nous ne garantissons pas l'absence d'erreurs. Les calculs d'emissions sont fournis a titre indicatif et ne constituent pas un conseil professionnel.
            </p>

            <h2 style="color: var(--text-primary);">7. Liens externes</h2>
            <p>
                Le site peut contenir des liens vers des sites tiers. Carbex n'est pas responsable du contenu de ces sites.
            </p>

            <h2 style="color: var(--text-primary);">8. Droit applicable</h2>
            <p>
                Les presentes mentions legales sont soumises au droit francais. Tout litige sera soumis aux tribunaux competents de Paris.
            </p>

            <h2 style="color: var(--text-primary);">9. Contact</h2>
            <p>
                Pour toute question concernant ces mentions legales ou la protection de vos donnees :
            </p>
            <ul>
                <li>Email : <a href="mailto:legal@carbex.fr" style="color: var(--accent);">legal@carbex.fr</a></li>
                <li>DPO : <a href="mailto:dpo@carbex.fr" style="color: var(--accent);">dpo@carbex.fr</a></li>
                <li>Courrier : Carbex SAS - Service Juridique, 123 Avenue de la Republique, 75011 Paris</li>
            </ul>

        </div>
    </div>
</section>
@endsection
