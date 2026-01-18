# Guide Administrateur Carbex

> Documentation technique pour les administrateurs de la plateforme Carbex

---

## Table des matières

1. [Accès au panel admin](#accès-au-panel-admin)
2. [Tableau de bord admin](#tableau-de-bord-admin)
3. [Gestion des organisations](#gestion-des-organisations)
4. [Gestion des utilisateurs](#gestion-des-utilisateurs)
5. [Gestion des sites](#gestion-des-sites)
6. [Configuration IA](#configuration-ia)
7. [Facteurs d'émission](#facteurs-démission)
8. [Abonnements et facturation](#abonnements-et-facturation)
9. [Contenu (Blog)](#contenu-blog)
10. [Monitoring et logs](#monitoring-et-logs)
11. [Maintenance](#maintenance)

---

## Accès au panel admin

### URL d'accès

```
Production : https://carbex.app/admin
Staging    : https://staging.carbex.app/admin
Local      : http://localhost:8000/admin
```

### Authentification

1. Accédez à `/admin/login`
2. Entrez vos identifiants administrateur
3. Validez l'authentification 2FA si activée

### Rôles administrateur

| Rôle | Droits |
|------|--------|
| Super Admin | Accès complet, configuration système |
| Admin | Gestion organisations, utilisateurs, contenu |
| Support | Lecture seule, assistance utilisateurs |

---

## Tableau de bord admin

### Métriques globales

| Métrique | Description |
|----------|-------------|
| **Organisations actives** | Nombre d'organisations avec activité récente |
| **Utilisateurs totaux** | Nombre total d'utilisateurs inscrits |
| **Émissions calculées** | Total CO₂e traité sur la plateforme |
| **Requêtes IA** | Nombre de requêtes IA du mois |

### Graphiques

- **Inscriptions** : Nouvelles organisations par semaine
- **Utilisation IA** : Requêtes par provider (Claude, GPT, Gemini)
- **Revenus MRR** : Monthly Recurring Revenue par plan
- **Émissions** : Volume total traité par mois

### Alertes

- Organisations avec quota IA épuisé
- Connexions bancaires expirées
- Erreurs de synchronisation
- Utilisateurs inactifs depuis 30+ jours

---

## Gestion des organisations

### Liste des organisations

**Menu** : Administration → Organizations

| Colonne | Description |
|---------|-------------|
| Nom | Nom de l'organisation |
| Plan | Abonnement actuel |
| Utilisateurs | Nombre de membres |
| Émissions | Total CO₂e calculé |
| Statut | Actif / Suspendu / Trial |
| Créé le | Date d'inscription |

### Actions disponibles

- **Voir** : Détail complet de l'organisation
- **Éditer** : Modifier informations et plan
- **Impersonner** : Se connecter en tant qu'utilisateur
- **Suspendre** : Bloquer temporairement l'accès
- **Supprimer** : Suppression définitive (soft delete)

### Créer une organisation

1. Cliquez sur **Nouvelle organisation**
2. Renseignez :
   - Nom de l'organisation
   - Email du propriétaire
   - Plan d'abonnement
   - Pays (FR/DE/UK)
   - Secteur d'activité
3. Cliquez sur **Créer**

L'utilisateur propriétaire recevra un email d'invitation.

### Détail organisation

#### Onglet Informations

- Données légales (SIRET, TVA)
- Adresse
- Secteur d'activité
- Date de création
- Année de référence carbone

#### Onglet Utilisateurs

Liste des membres avec rôles :
- Propriétaire
- Administrateur
- Éditeur
- Lecteur

#### Onglet Sites

Liste des sites de l'organisation avec :
- Nom et type
- Adresse
- Surface et effectif
- Émissions associées

#### Onglet Abonnement

- Plan actuel
- Date de début/fin
- Historique des paiements
- Utilisation des quotas

#### Onglet Activité

Journal d'audit :
- Connexions
- Modifications
- Exports de données
- Requêtes IA

---

## Gestion des utilisateurs

### Liste des utilisateurs

**Menu** : Administration → Users

| Colonne | Description |
|---------|-------------|
| Nom | Nom complet |
| Email | Adresse email |
| Organisation | Organisation rattachée |
| Rôle | Rôle dans l'organisation |
| Dernière connexion | Date/heure |
| Statut | Actif / Inactif / Banni |

### Filtres

- Par organisation
- Par rôle
- Par statut
- Par date d'inscription

### Actions utilisateur

- **Réinitialiser mot de passe** : Envoie un email de reset
- **Vérifier email** : Force la vérification
- **Impersonner** : Se connecter en tant que l'utilisateur
- **Bannir** : Bloquer définitivement l'accès
- **Supprimer** : Suppression du compte

### Impersonation

Pour débugger un problème utilisateur :

1. Cliquez sur l'icône "Impersonner"
2. Vous êtes connecté en tant que l'utilisateur
3. Bandeau orange indique l'impersonation
4. Cliquez sur "Revenir admin" pour sortir

> **Attention** : Toutes les actions sont loguées avec mention de l'impersonation.

---

## Gestion des sites

### Liste des sites

**Menu** : Administration → Sites

| Colonne | Description |
|---------|-------------|
| Nom | Nom du site |
| Organisation | Organisation propriétaire |
| Type | Siège, bureau, usine, etc. |
| Pays | Localisation |
| Émissions | Total CO₂e |

### Types de sites

| Type | Code | Description |
|------|------|-------------|
| Siège social | `headquarters` | Bureau principal |
| Bureau | `office` | Site administratif |
| Usine | `factory` | Site de production |
| Entrepôt | `warehouse` | Stockage/logistique |
| Magasin | `store` | Point de vente |
| Data center | `datacenter` | Infrastructure IT |
| Autre | `other` | Non catégorisé |

### Validation des sites

Vérifiez les sites en attente :
- Adresse valide
- Cohérence surface/effectif
- Absence de doublons

---

## Configuration IA

### Accès

**Menu** : Paramètres → Configuration IA

### Provider par défaut

Sélectionnez le provider IA principal :
- **Anthropic (Claude)** - Recommandé
- **OpenAI (GPT)**
- **Google (Gemini)**
- **DeepSeek**

### Configuration par provider

#### Anthropic (Claude)

| Paramètre | Description |
|-----------|-------------|
| Activer | Toggle on/off |
| Modèle | Claude Sonnet 4, Claude 3.5, etc. |
| Statut | Clé configurée ou non |

#### OpenAI (GPT)

| Paramètre | Description |
|-----------|-------------|
| Activer | Toggle on/off |
| Modèle | GPT-4o, GPT-4o Mini, o1, etc. |
| Statut | Clé configurée ou non |

#### Google (Gemini)

| Paramètre | Description |
|-----------|-------------|
| Activer | Toggle on/off |
| Modèle | Gemini 2.0 Flash, Gemini 1.5 Pro |
| Statut | Clé configurée ou non |

#### DeepSeek

| Paramètre | Description |
|-----------|-------------|
| Activer | Toggle on/off |
| Modèle | DeepSeek Chat, DeepSeek Coder |
| Statut | Clé configurée ou non |

### Paramètres avancés

| Paramètre | Valeur par défaut | Description |
|-----------|-------------------|-------------|
| Tokens maximum | 4096 | Limite de tokens par réponse |
| Température | 0.7 | 0.0 = déterministe, 1.0 = créatif |

### Modèles par abonnement

Configurez le modèle IA attribué à chaque plan :

| Plan | Tokens/mois | Requêtes/mois | Modèle par défaut |
|------|-------------|---------------|-------------------|
| Gratuit | 50K | 100 | Gemini 2.0 Flash Lite |
| Starter | 200K | 500 | GPT-4o Mini |
| Professional | 1M | 2500 | Claude Sonnet 4 |
| Enterprise | Illimité | Illimité | Claude Sonnet 4 |

Pour modifier :
1. Sélectionnez le modèle dans le dropdown du plan
2. La modification est sauvegardée automatiquement
3. Notification de confirmation

### Clés API

#### Ajouter une clé

1. Entrez la clé dans le champ du provider
2. Cliquez sur **Enregistrer**
3. Le provider est automatiquement activé

#### Tester une connexion

1. Cliquez sur **Tester** à côté du provider
2. Un appel de test est effectué
3. Notification succès/échec

#### Supprimer une clé

1. Cliquez sur **Supprimer**
2. Confirmez la suppression
3. Le provider est désactivé

### Sécurité des clés

> Les clés API sont chiffrées avec AES-256 avant stockage en base de données. Elles ne sont jamais exposées en clair dans l'interface.

---

## Facteurs d'émission

### Accès

**Menu** : Carbon Data → Emission Factors

### Sources disponibles

| Source | Pays | Catégories |
|--------|------|------------|
| ADEME | France | Énergie, transport, achats |
| UBA | Allemagne | Énergie, industrie |
| GHG Protocol | International | Tous scopes |
| DEFRA | UK | Énergie, transport |

### Structure d'un facteur

| Champ | Description |
|-------|-------------|
| Nom | Libellé du facteur |
| Catégorie | Scope et sous-catégorie |
| Valeur | kgCO₂e par unité |
| Unité | kWh, km, €, kg, etc. |
| Source | Base de données d'origine |
| Année | Année de référence |
| Incertitude | % d'incertitude |

### Importer des facteurs

1. Menu **Emission Factors** → **Importer**
2. Sélectionnez la source (ADEME, UBA, etc.)
3. Choisissez l'année
4. Cliquez sur **Importer**

### Mettre à jour les facteurs

Les facteurs ADEME sont mis à jour annuellement :

```bash
php artisan db:seed --class=AdemeFactorSeeder
```

### Facteurs personnalisés

Pour les clients Enterprise :
1. Créez un nouveau facteur
2. Renseignez les valeurs spécifiques
3. Associez à l'organisation concernée

---

## Abonnements et facturation

### Accès

**Menu** : Finance → Subscriptions

### Plans disponibles

| Plan | Prix/mois | Fonctionnalités |
|------|-----------|-----------------|
| Gratuit | 0€ | Limité, 1 utilisateur |
| Starter | 49€ | 3 utilisateurs, 2 sites |
| Professional | 149€ | 10 utilisateurs, sites illimités |
| Enterprise | Sur devis | Tout illimité, support dédié |

### Gestion des abonnements

#### Voir un abonnement

- Plan actuel
- Date de début
- Prochaine facturation
- Moyens de paiement

#### Modifier un plan

1. Sélectionnez l'organisation
2. Cliquez sur **Changer de plan**
3. Sélectionnez le nouveau plan
4. Validez (prorata calculé automatiquement)

#### Annuler un abonnement

1. Cliquez sur **Annuler**
2. Sélectionnez la raison
3. L'accès reste actif jusqu'à fin de période

### Facturation Stripe

Les paiements sont gérés via Stripe :

- Cartes bancaires (Visa, Mastercard)
- SEPA (prélèvement)
- Factures automatiques

### Coupon et remises

1. Menu **Coupons** → **Nouveau**
2. Code du coupon
3. Type (% ou montant fixe)
4. Durée de validité
5. Limite d'utilisation

---

## Contenu (Blog)

### Accès

**Menu** : Contenu → Articles de blog

### Créer un article

1. Cliquez sur **Nouvel article**
2. Renseignez :
   - Titre
   - Slug (URL)
   - Contenu (éditeur Markdown)
   - Image de couverture
   - Catégorie
   - Tags
   - Meta description (SEO)
3. Statut : Brouillon ou Publié
4. Date de publication (programmation possible)

### Catégories

- Actualités
- Réglementation (CSRD, BEGES)
- Guides pratiques
- Études de cas
- Webinaires

### SEO

Chaque article inclut :
- Meta title
- Meta description
- Open Graph image
- Canonical URL

---

## Monitoring et logs

### Journal d'audit

**Menu** : Monitoring → Audit Log

Toutes les actions sont tracées :
- Utilisateur
- Action (create, update, delete)
- Ressource concernée
- Données avant/après
- IP et User-Agent
- Timestamp

### Filtres audit

- Par utilisateur
- Par type d'action
- Par ressource
- Par période

### Erreurs système

**Menu** : Monitoring → Errors

Intégration Sentry pour :
- Exceptions PHP
- Erreurs JavaScript
- Timeouts API
- Erreurs de queue

### Métriques

| Métrique | Description |
|----------|-------------|
| Temps de réponse | Latence moyenne des requêtes |
| Taux d'erreur | % de requêtes en erreur |
| Queue jobs | Jobs en attente/traités |
| Cache hit rate | Efficacité du cache Redis |

---

## Maintenance

### Commandes Artisan utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Reconstruire le cache
php artisan optimize

# Reindexer la recherche
php artisan scout:flush "App\Models\Transaction"
php artisan scout:import "App\Models\Transaction"

# Traiter les jobs en attente
php artisan queue:work

# Voir les jobs échoués
php artisan queue:failed

# Relancer les jobs échoués
php artisan queue:retry all
```

### Tâches planifiées

| Tâche | Fréquence | Description |
|-------|-----------|-------------|
| `bank:sync` | Toutes les 6h | Synchronisation bancaire |
| `reports:generate` | Quotidien | Rapports programmés |
| `subscriptions:check` | Quotidien | Vérification expirations |
| `cleanup:temp` | Hebdo | Nettoyage fichiers temp |
| `backup:run` | Quotidien | Backup base de données |

### Backups

Les backups sont automatiques :
- **Base de données** : Quotidien, rétention 30 jours
- **Fichiers** : Hebdomadaire, rétention 4 semaines
- **Stockage** : OVH Object Storage (S3 compatible)

Restauration :
```bash
php artisan backup:list
php artisan backup:restore --backup=backup-2026-01-18.zip
```

### Mode maintenance

Activer :
```bash
php artisan down --secret="admin-secret-token"
```

Accès admin pendant maintenance :
```
https://carbex.app/admin-secret-token
```

Désactiver :
```bash
php artisan up
```

---

## Contacts techniques

### Équipe

| Rôle | Contact |
|------|---------|
| Lead Dev | dev@carbex.app |
| DevOps | ops@carbex.app |
| Support N2 | support@carbex.app |

### Escalade

1. **N1** : Support client (chat, email)
2. **N2** : Support technique (bugs, config)
3. **N3** : Développement (incidents critiques)

### Documentation technique

- [Guide développeur](./DEVELOPER_GUIDE.md)
- [API Reference](./api/README.md)
- [Architecture Decisions](./adr/README.md)
- [Runbook déploiement](./deployment-runbook.md)

---

*Dernière mise à jour : Janvier 2026*
*Version : 1.0*
