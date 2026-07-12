# ADR-0001: Utilisation de Livewire pour le Frontend

## Statut

Accepté

## Contexte

LinsCarbon nécessite une interface utilisateur réactive avec:
- Des tableaux de bord avec mise à jour en temps réel
- Des formulaires multi-étapes complexes (onboarding, saisie d'émissions)
- Des graphiques interactifs
- Une navigation fluide sans rechargement complet

Options considérées:
1. **Vue.js/React SPA** - Application monopage séparée
2. **Inertia.js** - Hybride Laravel + Vue/React
3. **Livewire 3** - Composants PHP réactifs
4. **Blade traditionnel** - Templates avec JavaScript minimal

## Décision

Nous choisissons **Livewire 3** combiné avec Alpine.js pour les interactions légères.

## Justification

### Avantages

- **Productivité**: Pas de duplication de logique entre backend et frontend
- **Expertise PHP**: L'équipe maîtrise PHP/Laravel, courbe d'apprentissage minimale
- **Écosystème Laravel**: Intégration native avec validation, auth, policies
- **SEO**: Pages rendues côté serveur par défaut
- **Maintenance**: Un seul codebase, pas d'API à maintenir pour le frontend

### Inconvénients Acceptés

- **Latence réseau**: Chaque interaction fait un appel serveur (mitigé par le wire:loading)
- **Limite offline**: L'application ne fonctionne pas hors ligne (acceptable pour ce SaaS B2B)
- **Complexité graphiques**: Les charts nécessitent Alpine.js/Chart.js (acceptable)

### Performance

- Utilisation de `wire:poll` avec prudence
- Lazy loading des composants lourds
- Cache côté serveur pour les données dashboard

## Conséquences

- Les développeurs doivent maîtriser Livewire 3 et ses hooks de cycle de vie
- Les composants interactifs complexes (graphiques) utilisent Alpine.js
- Les tests incluent des tests Dusk pour les interactions Livewire

## Références

- [Livewire Documentation](https://livewire.laravel.com)
- [Alpine.js Documentation](https://alpinejs.dev)
