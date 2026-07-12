# ADR-0004: Source des Facteurs d'Émission

## Statut

Accepté

## Contexte

Les calculs d'émissions nécessitent des facteurs d'émission (FE) fiables:
- Conversion quantité → kg CO2e
- Conformité réglementaire française
- Mise à jour régulière des facteurs
- Traçabilité des sources

Sources considérées:
1. **Base Carbone® ADEME** - Référence française officielle
2. **DEFRA** - Facteurs UK (pour comparaison)
3. **GHG Protocol** - Facteurs génériques
4. **Facteurs personnalisés** - Permettre aux clients de modifier

## Décision

Nous utilisons la **Base Carbone® ADEME** comme source principale avec possibilité de facteurs personnalisés.

## Justification

### Avantages Base Carbone

- **Conformité**: Référence officielle pour le BEGES réglementaire
- **Couverture**: 4000+ facteurs couvrant tous les secteurs
- **Localisation**: Facteurs adaptés au mix énergétique français
- **Crédibilité**: Validation ADEME auprès des autorités

### Structure des Facteurs

| Champ | Description | Exemple |
|-------|-------------|---------|
| `source` | Origine du facteur | `ademe`, `custom` |
| `ademe_id` | ID Base Carbone | `10345` |
| `name` | Nom du facteur | `Diesel routier` |
| `unit` | Unité d'entrée | `L`, `kWh`, `EUR` |
| `co2e_factor` | kgCO2e par unité | `2.68` |
| `uncertainty` | Incertitude (%) | `5` |
| `valid_from` | Date de validité | `2024-01-01` |

## Modèle de Données

```php
Schema::create('emission_factors', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('category_id');
    $table->string('source')->default('ademe'); // ademe, custom
    $table->string('ademe_id')->nullable();
    $table->string('name');
    $table->string('name_en')->nullable();
    $table->string('unit');
    $table->decimal('co2e_factor', 12, 6);
    $table->decimal('uncertainty_percent', 5, 2)->nullable();
    $table->json('metadata')->nullable(); // données ADEME brutes
    $table->date('valid_from');
    $table->date('valid_until')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['category_id', 'unit', 'is_active']);
});
```

## Calcul d'Émission

```php
class EmissionCalculator
{
    public function calculate(
        float $quantity,
        string $unit,
        Category $category,
        ?Carbon $date = null
    ): float {
        $factor = EmissionFactor::query()
            ->where('category_id', $category->id)
            ->where('unit', $unit)
            ->where('is_active', true)
            ->where('valid_from', '<=', $date ?? now())
            ->where(fn ($q) => $q
                ->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $date ?? now())
            )
            ->first();

        if (!$factor) {
            throw new NoEmissionFactorException($category, $unit);
        }

        return $quantity * $factor->co2e_factor;
    }
}
```

## Mise à Jour des Facteurs

```bash
# Commande artisan pour import Base Carbone
php artisan linscarbon:import-emission-factors

# Source: API Base Carbone ou fichier CSV
# Fréquence: Mensuelle (cron)
```

## Conséquences

- Les facteurs ADEME sont importés régulièrement
- Chaque calcul référence le facteur utilisé (traçabilité)
- Les rapports incluent les sources des facteurs
- Les clients peuvent demander des facteurs personnalisés (admin)

## Références

- [Base Carbone ADEME](https://base-empreinte.ademe.fr)
- [Documentation API Base Carbone](https://data.ademe.fr)
