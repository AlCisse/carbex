# ADR-006: uSearch pour la recherche semantique

**Date**: 2026-01-13
**Statut**: Accepte
**Decideurs**: Equipe LinsCarbon

## Contexte

La plateforme LinsCarbon necessite une recherche efficace des facteurs d'emission parmi plus de 15,000 entrees provenant de sources multiples (ADEME, UBA, GHG Protocol). Les utilisateurs formulent souvent des requetes en langage naturel qui ne correspondent pas exactement aux noms des facteurs dans la base de donnees.

Exemples de problemes avec la recherche textuelle :
- "transport avion" ne trouve pas "Aviation - vol long courrier"
- "chauffage bureau" ne trouve pas "Tertiaire - Combustion gaz naturel"
- Recherches multilingues (FR/EN/DE) difficiles a gerer

## Decision

Nous adoptons **uSearch** comme moteur de recherche vectorielle, deploye en tant que microservice Python (FastAPI), pour implementer la recherche semantique des facteurs d'emission.

### Composants implementes

1. **Microservice uSearch** (`services/usearch-api/`)
   - FastAPI avec uSearch HNSW
   - Multi-provider embeddings (local, OpenAI, Voyage)
   - API REST avec authentification

2. **Services PHP**
   - `USearchClient` : Client HTTP
   - `SemanticSearchService` : API haut niveau
   - `EmbeddingService` : Gestion des embeddings avec cache
   - `FactorRAGService` : RAG specialise facteurs

3. **Integration UI**
   - Toggle recherche semantique dans FactorSelector
   - Mode hybride (semantic + text) par defaut

## Options considerees

### Option 1: Meilisearch uniquement (rejetee)
- **Pour**: Deja integre, simple
- **Contre**: Pas de recherche semantique, correspondance exacte seulement

### Option 2: Elasticsearch avec plugin vectoriel (rejetee)
- **Pour**: Mature, features avancees
- **Contre**: Lourd, complexe, cout d'hebergement eleve

### Option 3: Pinecone / Weaviate (rejetee)
- **Pour**: Services manages, scalables
- **Contre**: Dependance externe, cout, latence reseau

### Option 4: uSearch en microservice (acceptee)
- **Pour**:
  - Leger et performant (C++ core)
  - Self-hosted (pas de dependance externe)
  - Flexible (multi-provider embeddings)
  - Fallback automatique vers Meilisearch
- **Contre**:
  - Service supplementaire a maintenir
  - Necessite synchronisation des index

## Consequences

### Positives
- Recherche naturelle en FR/EN/DE
- Meilleure UX pour la selection des facteurs
- Possibilite d'etendre aux transactions et documents
- Pas de dependance cloud externe

### Negatives
- Complexite accrue de l'infrastructure
- Necessite Redis pour le cache des embeddings
- Synchronisation des index a gerer

### Risques mitigees
- Fallback automatique vers Meilisearch si uSearch indisponible
- Cache Redis pour reduire les appels API embeddings
- Embeddings locaux gratuits (sentence-transformers)

## Implementation

```
┌─────────────────────────────────────────────────────┐
│                    LINSCARBON APP                       │
│  ┌─────────────┐    ┌──────────────────────────┐   │
│  │ Livewire UI │───▶│ SemanticSearchService    │   │
│  └─────────────┘    │ + FactorRAGService       │   │
│                     └────────────┬─────────────┘   │
│                                  │                  │
│                     ┌────────────▼─────────────┐   │
│                     │      USearchClient       │   │
│                     └────────────┬─────────────┘   │
└──────────────────────────────────┼─────────────────┘
                                   │ HTTP
┌──────────────────────────────────▼─────────────────┐
│               USEARCH-API (Python)                 │
│  ┌─────────────┐    ┌─────────────────────────┐   │
│  │   FastAPI   │───▶│ uSearch HNSW Index      │   │
│  └─────────────┘    └─────────────────────────┘   │
│         │                                          │
│  ┌──────▼──────┐                                  │
│  │ Embeddings  │ (local/OpenAI/Voyage)            │
│  └─────────────┘                                  │
└────────────────────────────────────────────────────┘
```

## Metriques de succes

- Taux de succes recherche : > 90% des requetes trouvent un facteur pertinent
- Temps de reponse : < 200ms pour une recherche
- Satisfaction utilisateur : Reduction des tickets support lies a la recherche

## References

- [uSearch GitHub](https://github.com/unum-cloud/usearch)
- [Documentation interne](../usearch-semantic-search.md)
- [GHG Protocol](https://ghgprotocol.org/)
