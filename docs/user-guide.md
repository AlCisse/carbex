# Guide Utilisateur Carbex

> Plateforme de Bilan Carbone pour PME Européennes

---

## Table des matières

1. [Démarrage rapide](#démarrage-rapide)
2. [Tableau de bord](#tableau-de-bord)
3. [Sites et périmètre](#sites-et-périmètre)
4. [Transactions et données](#transactions-et-données)
5. [Connexions bancaires](#connexions-bancaires)
6. [Assistant IA](#assistant-ia)
7. [Rapports et exports](#rapports-et-exports)
8. [Fournisseurs](#fournisseurs)
9. [Paramètres du compte](#paramètres-du-compte)
10. [FAQ](#faq)

---

## Démarrage rapide

### Création de compte

1. Rendez-vous sur [carbex.app](https://carbex.app)
2. Cliquez sur **Créer un compte**
3. Renseignez vos informations :
   - Email professionnel
   - Mot de passe (min. 8 caractères)
   - Nom de votre organisation
4. Acceptez les conditions d'utilisation
5. Validez votre email via le lien reçu

### Première configuration

Après connexion, l'assistant de configuration vous guide :

1. **Informations organisation** : Nom, SIRET/TVA, secteur d'activité
2. **Année de référence** : Choisissez l'année de votre premier bilan
3. **Premier site** : Ajoutez votre site principal (siège, usine, bureau)
4. **Connexion bancaire** (optionnel) : Connectez vos comptes pour import automatique

---

## Tableau de bord

Le tableau de bord présente une vue synthétique de votre empreinte carbone.

### Indicateurs clés

| Indicateur | Description |
|------------|-------------|
| **Émissions totales** | Total CO₂e sur la période sélectionnée |
| **Scope 1** | Émissions directes (véhicules, chauffage) |
| **Scope 2** | Émissions indirectes énergétiques (électricité) |
| **Scope 3** | Autres émissions indirectes (achats, déplacements) |

### Graphiques disponibles

- **Évolution mensuelle** : Suivi de vos émissions mois par mois
- **Répartition par scope** : Camembert des 3 scopes
- **Top catégories** : Vos principales sources d'émissions
- **Comparaison annuelle** : Évolution vs année précédente

### Filtres

- Par période (mois, trimestre, année)
- Par site
- Par scope (1, 2, 3)
- Par catégorie d'émission

---

## Sites et périmètre

### Ajouter un site

1. Menu **Sites** → **Ajouter un site**
2. Renseignez :
   - Nom du site
   - Adresse complète
   - Type (siège, bureau, usine, entrepôt, magasin)
   - Surface (m²)
   - Nombre d'employés
3. Cliquez sur **Enregistrer**

### Types de sites supportés

| Type | Description | Données typiques |
|------|-------------|------------------|
| Siège social | Bureau principal | Électricité, chauffage, déplacements |
| Bureau | Site administratif | Électricité, informatique |
| Usine | Site de production | Énergie, process, matières premières |
| Entrepôt | Stockage/logistique | Électricité, manutention |
| Magasin | Point de vente | Électricité, climatisation |

### Périmètre organisationnel

Carbex supporte deux approches :

- **Contrôle opérationnel** : 100% des émissions des sites que vous contrôlez
- **Part du capital** : Émissions au prorata de votre participation

---

## Transactions et données

### Import manuel

1. Menu **Transactions** → **Importer**
2. Téléchargez le modèle Excel
3. Remplissez vos données :
   - Date
   - Description
   - Montant
   - Catégorie
   - Site concerné
4. Importez le fichier complété

### Catégories d'émissions

#### Scope 1 - Émissions directes

| Catégorie | Exemples |
|-----------|----------|
| Combustion fixe | Chauffage gaz, fioul |
| Combustion mobile | Véhicules de société |
| Émissions fugitives | Climatisation (fuites) |
| Procédés industriels | Réactions chimiques |

#### Scope 2 - Énergie indirecte

| Catégorie | Exemples |
|-----------|----------|
| Électricité | Consommation électrique |
| Chaleur/Vapeur | Réseau de chaleur urbain |
| Froid | Réseau de froid |

#### Scope 3 - Autres indirectes

| Catégorie | Exemples |
|-----------|----------|
| Achats de biens | Matières premières, fournitures |
| Achats de services | Conseil, informatique, nettoyage |
| Déplacements professionnels | Avion, train, hôtels |
| Trajets domicile-travail | Voitures employés |
| Transport amont | Livraison fournisseurs |
| Transport aval | Livraison clients |
| Déchets | Traitement, recyclage |
| Immobilisations | Équipements, bâtiments |

### Saisie manuelle

Pour ajouter une transaction :

1. Menu **Transactions** → **Nouvelle transaction**
2. Renseignez les champs :
   - Date de la transaction
   - Description
   - Montant (€)
   - Catégorie d'émission
   - Site concerné
   - Fournisseur (optionnel)
3. Cliquez sur **Enregistrer**

Le calcul des émissions est automatique selon les facteurs d'émission.

---

## Connexions bancaires

### Banques supportées

#### France (via Bridge)
- Crédit Agricole, BNP Paribas, Société Générale
- Crédit Mutuel, CIC, Banque Populaire
- Caisse d'Épargne, LCL, Boursorama
- Et 350+ autres établissements

#### Allemagne (via FinAPI)
- Deutsche Bank, Commerzbank
- Sparkasse, Volksbank
- N26, DKB, ING
- Et 3000+ autres établissements

### Connecter une banque

1. Menu **Connexions** → **Ajouter une banque**
2. Recherchez votre banque
3. Authentifiez-vous (redirection sécurisée)
4. Sélectionnez les comptes à synchroniser
5. Validez la connexion

### Synchronisation

- **Automatique** : Toutes les 6 heures
- **Manuelle** : Bouton "Synchroniser" à tout moment
- **Historique** : Import des 90 derniers jours à la connexion

### Catégorisation automatique

Carbex catégorise automatiquement vos transactions :

- **IA intégrée** : Analyse du libellé et du marchand
- **Codes MCC** : Classification par code marchand
- **Apprentissage** : S'améliore avec vos corrections

Pour corriger une catégorie :
1. Cliquez sur la transaction
2. Modifiez la catégorie
3. Cochez "Appliquer à toutes les transactions similaires" (optionnel)

---

## Assistant IA

### Fonctionnalités

L'assistant IA Carbex vous aide à :

- **Analyser** vos données carbone
- **Répondre** à vos questions sur la conformité
- **Suggérer** des actions de réduction
- **Expliquer** les réglementations (CSRD, BEGES)
- **Extraire** les données de vos documents

### Utilisation

1. Cliquez sur l'icône assistant (bulle en bas à droite)
2. Posez votre question en langage naturel
3. L'assistant répond avec des données contextuelles

### Exemples de questions

```
"Quelles sont mes principales sources d'émissions ?"
"Comment réduire mon Scope 3 ?"
"Explique-moi les exigences CSRD"
"Analyse mes émissions du dernier trimestre"
"Quels fournisseurs ont le plus d'impact ?"
```

### Extraction de documents

1. Menu **Documents** → **Importer**
2. Glissez-déposez vos fichiers (PDF, images)
3. L'IA extrait automatiquement :
   - Factures d'énergie (kWh, m³)
   - Relevés de carburant (litres)
   - Factures fournisseurs (montants, descriptions)

### Quotas par abonnement

| Plan | Requêtes IA | Modèle |
|------|-------------|--------|
| Gratuit | 100/mois | Gemini Flash Lite |
| Starter | 500/mois | GPT-4o Mini |
| Professional | 2500/mois | Claude Sonnet 4 |
| Enterprise | Illimité | Claude Sonnet 4 |

---

## Rapports et exports

### Types de rapports

#### Bilan Carbone (PDF)

Rapport complet incluant :
- Synthèse des émissions par scope
- Graphiques de répartition
- Détail par catégorie
- Évolution temporelle
- Recommandations

#### Rapport BEGES (France)

Format réglementaire français :
- Conforme décret n°2022-982
- Catégories ADEME
- Incertitudes calculées
- Plan d'actions requis

#### Rapport CSRD (Europe)

Normes ESRS pour entreprises européennes :
- ESRS E1 - Changement climatique
- Indicateurs de performance
- Objectifs de réduction
- Due diligence

#### Export Excel

Données brutes pour analyse :
- Transactions détaillées
- Émissions par catégorie
- Facteurs d'émission utilisés
- Format ADEME compatible

### Générer un rapport

1. Menu **Rapports** → **Nouveau rapport**
2. Sélectionnez le type
3. Choisissez la période
4. Sélectionnez les sites
5. Cliquez sur **Générer**
6. Téléchargez (PDF, Word ou Excel)

### Planification

Programmez des rapports automatiques :
- Fréquence : mensuel, trimestriel, annuel
- Destinataires : emails de votre équipe
- Format : PDF ou Excel

---

## Fournisseurs

### Portail fournisseurs

Invitez vos fournisseurs à partager leurs données carbone :

1. Menu **Fournisseurs** → **Inviter**
2. Renseignez email et nom du fournisseur
3. Le fournisseur reçoit un accès limité
4. Il renseigne ses données d'émissions
5. Vous recevez les données automatiquement

### Données collectées

- Émissions par unité vendue
- Facteurs d'émission spécifiques
- Certifications environnementales
- Objectifs de réduction

### Avantages

- **Précision** : Données réelles vs estimations
- **Engagement** : Impliquez votre chaîne de valeur
- **Conformité** : Exigence CSRD pour le Scope 3

---

## Paramètres du compte

### Profil utilisateur

- Nom, prénom, email
- Langue (FR/EN/DE)
- Fuseau horaire
- Notifications

### Organisation

- Informations légales
- Logo
- Année de référence
- Secteur d'activité

### Équipe

Invitez des collaborateurs :

| Rôle | Droits |
|------|--------|
| Administrateur | Accès complet |
| Éditeur | Saisie et modification |
| Lecteur | Consultation uniquement |

### Abonnement

Gérez votre abonnement :
- Plan actuel et utilisation
- Historique des factures
- Changement de plan
- Moyens de paiement

### Assistant IA

Configurez vos préférences IA :
- Modèle préféré
- Langue des réponses
- Niveau de détail

---

## FAQ

### Général

**Q : Carbex est-il conforme au RGPD ?**
> Oui, vos données sont hébergées en Europe (OVH France) et chiffrées. Vous pouvez exporter ou supprimer vos données à tout moment.

**Q : Puis-je essayer gratuitement ?**
> Oui, le plan Gratuit permet de tester avec 100 requêtes IA/mois et toutes les fonctionnalités de base.

### Données

**Q : D'où viennent les facteurs d'émission ?**
> Nous utilisons les bases officielles : ADEME (France), UBA (Allemagne), GHG Protocol (international).

**Q : Comment sont calculées les émissions ?**
> Émissions (kgCO₂e) = Donnée d'activité × Facteur d'émission. Par exemple : 100 kWh × 0.052 = 5.2 kgCO₂e

**Q : Puis-je utiliser mes propres facteurs ?**
> Oui, les plans Professional et Enterprise permettent de personnaliser les facteurs.

### Bancaire

**Q : Mes données bancaires sont-elles sécurisées ?**
> Oui, nous utilisons des agrégateurs certifiés DSP2 (Bridge, FinAPI). Nous n'avons jamais accès à vos identifiants.

**Q : Puis-je déconnecter ma banque ?**
> Oui, menu Connexions → cliquez sur la banque → Déconnecter.

### Rapports

**Q : Les rapports sont-ils opposables ?**
> Nos rapports respectent les formats réglementaires (BEGES, CSRD). Nous recommandons une vérification par un tiers pour les obligations légales.

**Q : Puis-je personnaliser les rapports ?**
> Oui, ajoutez votre logo et personnalisez les sections dans Paramètres → Rapports.

---

## Support

### Contact

- **Email** : support@carbex.app
- **Chat** : Icône en bas à droite (heures ouvrées)
- **Documentation** : docs.carbex.app

### Ressources

- [Centre d'aide](https://help.carbex.app)
- [Tutoriels vidéo](https://carbex.app/tutorials)
- [Blog](https://carbex.app/blog)
- [Webinaires](https://carbex.app/webinars)

---

*Dernière mise à jour : Janvier 2026*
*Version : 1.0*
