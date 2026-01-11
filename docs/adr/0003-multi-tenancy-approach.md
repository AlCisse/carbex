# ADR-0003: Approche Multi-Tenant

## Statut

Accepté

## Contexte

Carbex est un SaaS multi-tenant où chaque organisation:
- Doit avoir ses données isolées des autres
- Peut avoir plusieurs utilisateurs et sites
- Doit pouvoir être facturée indépendamment
- Doit pouvoir exporter/supprimer ses données (RGPD)

Approches considérées:
1. **Base de données séparée par tenant** - Isolation maximale
2. **Schema séparé par tenant** - Isolation logique
3. **Colonne tenant_id** - Données partagées avec filtrage
4. **Hybride** - Données sensibles séparées, reste partagé

## Décision

Nous choisissons l'approche **colonne tenant_id** (organization_id) avec scopes automatiques.

## Justification

### Avantages

- **Simplicité**: Une seule base de données à maintenir
- **Coût**: Pas de multiplication des ressources DB
- **Migration**: Schéma unique, migrations simples
- **Requêtes cross-tenant**: Possibles pour analytics agrégées (futur)
- **Scalabilité**: Sharding horizontal possible plus tard

### Inconvénients Acceptés

- **Risque de fuite**: Atténué par les scopes automatiques et tests
- **Performance**: Index sur organization_id requis
- **Backup/Restore par tenant**: Plus complexe (mais faisable)

## Implémentation

### Trait BelongsToOrganization

```php
trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        // Scope automatique pour les requêtes
        static::addGlobalScope('organization', function ($query) {
            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id);
            }
        });

        // Attribution automatique à la création
        static::creating(function ($model) {
            if (auth()->check() && !$model->organization_id) {
                $model->organization_id = auth()->user()->organization_id;
            }
        });
    }
}
```

### Modèles Concernés

- `User`
- `Site`
- `Assessment`
- `EmissionRecord`
- `Transaction`
- `Report`
- `BankConnection`
- `ApiKey`
- `Webhook`

### Middleware de Vérification

```php
// Vérifie que l'utilisateur accède à sa propre organisation
class EnsureOrganizationAccess
{
    public function handle($request, Closure $next)
    {
        $model = $request->route()->parameter('model');

        if ($model && $model->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return $next($request);
    }
}
```

## Tests de Sécurité

```php
public function test_user_cannot_access_other_organization_data(): void
{
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user1 = User::factory()->for($org1)->create();
    $assessment = Assessment::factory()->for($org2)->create();

    $this->actingAs($user1)
        ->get("/assessments/{$assessment->id}")
        ->assertForbidden();
}
```

## Conséquences

- Tous les modèles tenant-aware utilisent le trait `BelongsToOrganization`
- Les migrations incluent `organization_id` avec index
- Les tests vérifient l'isolation des données
- Les exports RGPD filtrent par organization_id

## Références

- [Laravel Scopes](https://laravel.com/docs/eloquent#global-scopes)
- [Multi-tenancy Patterns](https://docs.microsoft.com/en-us/azure/architecture/patterns/multi-tenant)
