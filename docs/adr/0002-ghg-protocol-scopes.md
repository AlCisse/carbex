# ADR-0002: Structure des Émissions selon GHG Protocol

## Statut

Accepté

## Contexte

La comptabilité carbone nécessite une structure standardisée pour:
- Classifier les émissions par source
- Assurer la conformité réglementaire (BEGES, CSRD)
- Permettre les comparaisons inter-entreprises
- Faciliter le reporting normalisé

Standards considérés:
1. **GHG Protocol** - Standard international ISO 14064
2. **Bilan Carbone® ADEME** - Méthodologie française
3. **Structure propriétaire** - Catégorisation personnalisée

## Décision

Nous adoptons la structure **GHG Protocol** avec les 3 scopes et 15 catégories du Scope 3.

## Structure Implémentée

### Scopes

| Scope | Description | Exemples |
|-------|-------------|----------|
| 1 | Émissions directes | Combustion fixe, flotte véhicules |
| 2 | Énergie indirecte | Électricité, chaleur achetée |
| 3 | Autres indirectes | Achats, déplacements, fret |

### Catégories Scope 3 (Corporate Value Chain)

```
3.1  Achats de biens et services
3.2  Biens d'équipement
3.3  Combustibles et énergie (non inclus scopes 1&2)
3.4  Transport amont
3.5  Déchets générés
3.6  Déplacements professionnels
3.7  Déplacements domicile-travail
3.8  Actifs loués amont
3.9  Transport aval
3.10 Transformation des produits vendus
3.11 Utilisation des produits vendus
3.12 Fin de vie des produits vendus
3.13 Actifs loués aval
3.14 Franchises
3.15 Investissements
```

## Modèle de Données

```php
// Category
Schema::create('categories', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->tinyInteger('scope'); // 1, 2, 3
    $table->string('code');       // "1.1", "3.6"
    $table->string('name');
    $table->string('name_en');
    $table->text('description')->nullable();
});

// EmissionRecord
Schema::create('emission_records', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('assessment_id');
    $table->foreignUuid('category_id');
    $table->tinyInteger('scope');  // Dénormalisé pour performance
    $table->decimal('quantity', 15, 4);
    $table->string('unit');
    $table->decimal('co2e_kg', 15, 4);
});
```

## Conséquences

- Les rapports BEGES sont générés automatiquement par mapping
- Les catégories sont traduites FR/EN
- L'import de données externes (comptables) utilise un mapping vers les catégories GHG
- Les facteurs d'émission sont liés aux catégories

## Références

- [GHG Protocol Corporate Standard](https://ghgprotocol.org/corporate-standard)
- [GHG Protocol Scope 3 Standard](https://ghgprotocol.org/scope-3-standard)
- [Méthode BEGES réglementaire](https://www.ecologie.gouv.fr/bilan-ges)
