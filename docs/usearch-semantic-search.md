# uSearch - Recherche Semantique pour Carbex

> Documentation technique de l'integration uSearch pour la recherche vectorielle et semantique dans Carbex.

**Version**: 1.0
**Derniere mise a jour**: 2026-01-13

---

## Table des matieres

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Installation et configuration](#installation-et-configuration)
4. [Microservice Python (usearch-api)](#microservice-python-usearch-api)
5. [Services PHP](#services-php)
6. [Utilisation](#utilisation)
7. [API Endpoints](#api-endpoints)
8. [Commandes Artisan](#commandes-artisan)
9. [Integration UI (Livewire)](#integration-ui-livewire)
10. [Troubleshooting](#troubleshooting)

---

## Vue d'ensemble

### Qu'est-ce que uSearch ?

[uSearch](https://github.com/unum-cloud/usearch) est une bibliotheque de recherche vectorielle haute performance utilisee pour implementer la recherche semantique dans Carbex. Elle permet de trouver des elements similaires basee sur la signification plutot que sur des correspondances exactes de mots-cles.

### Cas d'usage dans Carbex

| Index | Description | Modeles |
|-------|-------------|---------|
| `emission_factors` | Recherche semantique des facteurs d'emission | EmissionFactor |
| `transactions` | Auto-categorisation des transactions bancaires | Transaction |
| `documents` | Extraction IA des documents uploades | UploadedDocument |
| `actions` | Recommandations d'actions de reduction | Action |

### Avantages

- **Recherche naturelle** : L'utilisateur peut chercher "transport en avion" et trouver "Aviation - vol long courrier"
- **Multilangue** : Fonctionne en FR/EN/DE grace aux embeddings multilingues
- **Hybride** : Combine recherche semantique + texte pour des resultats optimaux
- **Fallback** : Bascule automatiquement sur Meilisearch si uSearch indisponible

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                           CARBEX LARAVEL                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────────┐    ┌──────────────────┐    ┌───────────────┐  │
│  │ FactorSelector  │───▶│ SemanticSearch   │───▶│ USearchClient │  │
│  │ (Livewire)      │    │ Service          │    │ (HTTP)        │  │
│  └─────────────────┘    └──────────────────┘    └───────┬───────┘  │
│                                │                        │          │
│                                ▼                        │          │
│                         ┌──────────────────┐            │          │
│                         │ EmbeddingService │            │          │
│                         │ (Cache Redis)    │            │          │
│                         └──────────────────┘            │          │
│                                                         │          │
└─────────────────────────────────────────────────────────┼──────────┘
                                                          │
                                                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      USEARCH-API (Python FastAPI)                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────┐    ┌─────────────────┐    ┌────────────────────┐  │
│  │ /search     │    │ EmbeddingService│    │ SearchEngine       │  │
│  │ /index      │───▶│ (Multi-provider)│───▶│ (uSearch HNSW)     │  │
│  │ /embeddings │    └─────────────────┘    └────────────────────┘  │
│  └─────────────┘              │                       │            │
│                               ▼                       ▼            │
│                    ┌─────────────────┐    ┌────────────────────┐   │
│                    │ OpenAI / Local  │    │ /data/indexes/     │   │
│                    │ sentence-transf │    │ (Persistent)       │   │
│                    └─────────────────┘    └────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Flux de donnees

1. **Indexation** :
   ```
   EmissionFactor → EmbeddingService → USearchClient → usearch-api → Vector Index
   ```

2. **Recherche** :
   ```
   Query utilisateur → SemanticSearchService → USearchClient → usearch-api
                                ↓
   Resultats semantiques + Meilisearch text → Fusion hybride → Resultats
   ```

---

## Installation et configuration

### Prerequisites

- Docker et Docker Compose
- PHP 8.4+
- Redis (pour le cache des embeddings)
- Cle API OpenAI (optionnel, pour embeddings haute qualite)

### 1. Configuration Docker

Le service `usearch` est defini dans `docker-compose.yml` :

```yaml
services:
  usearch:
    build:
      context: ./services/usearch-api
      dockerfile: Dockerfile
    container_name: carbex-usearch
    ports:
      - "8001:8001"
    environment:
      - USEARCH_API_KEY=${USEARCH_API_KEY:-carbex-dev-key}
      - OPENAI_API_KEY=${OPENAI_API_KEY:-}
      - EMBEDDING_PROVIDER=${EMBEDDING_PROVIDER:-local}
      - VECTOR_DIMENSIONS=${VECTOR_DIMENSIONS:-384}
    volumes:
      - usearch_data:/data/indexes
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8001/health"]
      interval: 30s
      timeout: 10s
      retries: 3
    restart: unless-stopped

volumes:
  usearch_data:
```

### 2. Variables d'environnement (.env)

```bash
# uSearch Configuration
USEARCH_URL=http://localhost:8001
USEARCH_API_KEY=your-secure-api-key

# Embedding Provider: local | openai | voyage
EMBEDDING_PROVIDER=local
VECTOR_DIMENSIONS=384

# OpenAI (si EMBEDDING_PROVIDER=openai)
OPENAI_API_KEY=sk-...

# Voyage AI (si EMBEDDING_PROVIDER=voyage)
VOYAGE_API_KEY=pa-...
```

### 3. Configuration PHP

Fichier `config/usearch.php` :

```php
<?php

return [
    // URL du microservice uSearch
    'url' => env('USEARCH_URL', 'http://localhost:8001'),

    // Cle API pour authentification
    'api_key' => env('USEARCH_API_KEY'),

    // Index disponibles
    'indexes' => [
        'emission_factors' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos', // cosine similarity
        ],
        'transactions' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
        ],
        'documents' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
        ],
        'actions' => [
            'dimensions' => (int) env('VECTOR_DIMENSIONS', 384),
            'metric' => 'cos',
        ],
    ],

    // Configuration des embeddings
    'embeddings' => [
        'provider' => env('EMBEDDING_PROVIDER', 'local'),
        'cache_ttl' => 86400 * 7, // 7 jours
        'batch_size' => 100,
    ],

    // Configuration de la recherche
    'search' => [
        'top_k' => 20,
        'min_score' => 0.5,
        'hybrid_weight' => 0.7, // 70% semantic, 30% text
    ],
];
```

### 4. Demarrage des services

```bash
# Demarrer tous les services
docker compose up -d

# Verifier que uSearch fonctionne
curl http://localhost:8001/health

# Reponse attendue:
# {"status": "healthy", "indexes": {...}}
```

---

## Microservice Python (usearch-api)

### Structure

```
services/usearch-api/
├── app/
│   ├── main.py          # FastAPI application
│   ├── search.py        # Moteur de recherche uSearch
│   ├── embeddings.py    # Service d'embeddings multi-provider
│   └── models.py        # Modeles Pydantic
├── Dockerfile
├── requirements.txt
├── .env.example
└── tests/
    └── test_api.py
```

### Providers d'embeddings

| Provider | Modele | Dimensions | Qualite | Cout |
|----------|--------|------------|---------|------|
| `local` | all-MiniLM-L6-v2 | 384 | Bonne | Gratuit |
| `openai` | text-embedding-3-small | 1536 | Excellente | ~$0.02/1M tokens |
| `voyage` | voyage-large-2 | 1024 | Excellente | ~$0.10/1M tokens |

### Endpoints du microservice

| Methode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/health` | Verification sante du service |
| GET | `/stats` | Statistiques des index |
| POST | `/search` | Recherche semantique |
| POST | `/search/vector` | Recherche par vecteur |
| POST | `/similar/{index}/{id}` | Trouver similaires |
| POST | `/index` | Indexer un item |
| POST | `/index/batch` | Indexation batch |
| DELETE | `/index/{index}/{id}` | Supprimer un item |
| POST | `/indexes/{name}` | Creer un index |
| DELETE | `/indexes/{name}` | Supprimer un index |
| POST | `/indexes/{name}/optimize` | Optimiser un index |
| POST | `/embeddings` | Generer embedding |
| POST | `/embeddings/batch` | Embeddings batch |

### Exemple de requete

```bash
# Recherche semantique
curl -X POST http://localhost:8001/search \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your-api-key" \
  -d '{
    "query": "emissions transport avion",
    "index": "emission_factors",
    "top_k": 10,
    "min_score": 0.5
  }'
```

---

## Services PHP

### USearchClient

Client HTTP pour communiquer avec le microservice.

```php
use App\Services\Search\USearchClient;

$client = app(USearchClient::class);

// Verifier la disponibilite
if ($client->isAvailable()) {
    // Recherche semantique
    $results = $client->search('emission_factors', 'transport aerien', 10);

    // Indexer un facteur
    $client->index('emission_factors', $factorId, $content, $metadata);

    // Recherche par similarite
    $similar = $client->findSimilar('emission_factors', $factorId, 5);
}
```

### SemanticSearchService

API de haut niveau combinant recherche semantique et textuelle.

```php
use App\Services\Search\SemanticSearchService;

$searchService = app(SemanticSearchService::class);

// Recherche hybride (semantic + text)
$results = $searchService->hybridSearch(
    query: 'vehicule electrique',
    index: 'emission_factors',
    limit: 20,
    filters: ['scope' => 1]
);

// Recherche specialisee pour facteurs
$factors = $searchService->searchEmissionFactors(
    query: 'chauffage gaz naturel',
    filters: ['source' => 'ADEME'],
    limit: 10
);

// Trouver des elements similaires
$similar = $searchService->findSimilar(
    index: 'emission_factors',
    id: $factor->id,
    limit: 5
);
```

### EmbeddingService

Gestion des embeddings avec cache Redis.

```php
use App\Services\Search\EmbeddingService;

$embeddingService = app(EmbeddingService::class);

// Generer un embedding (avec cache)
$vector = $embeddingService->embed('Transport aerien long courrier');

// Embeddings batch
$vectors = $embeddingService->embedBatch([
    'Electricite reseau',
    'Gaz naturel chauffage',
    'Diesel vehicule',
]);

// Indexer un modele
$embeddingService->embedAndIndex($emissionFactor);

// Synchroniser tous les facteurs non indexes
$embeddingService->syncUnsyncedEmbeddings('emission_factors');
```

### FactorRAGService

Service RAG specialise pour les facteurs d'emission.

```php
use App\Services\AI\FactorRAGService;

$rag = app(FactorRAGService::class);

// Recherche hybride avec contexte
$results = $rag->hybridSearch('climatisation bureau', [
    'scope' => 2,
    'country' => 'FR',
], 20);

// Autocomplete semantique
$suggestions = $rag->autocomplete('trans', 5);

// Facteurs similaires
$similar = $rag->findSimilarFactors($factor, 5);

// Recommandations par secteur
$recommended = $rag->recommendFactorsForSector('manufacturing', 10);

// Statistiques
$stats = $rag->getIndexStats();
```

---

## Utilisation

### Indexation initiale des facteurs

```bash
# Via Artisan (recommande)
php artisan usearch:index-factors --full

# Avec filtre par source
php artisan usearch:index-factors --source=ADEME

# Execution synchrone (pour debug)
php artisan usearch:index-factors --sync

# Via API
curl -X POST http://localhost/api/v1/search/indexes/emission_factors/reindex \
  -H "Authorization: Bearer $TOKEN"
```

### Recherche via API

```bash
# Recherche semantique
curl -X POST http://localhost/api/v1/search/semantic \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "emissions voiture essence",
    "index": "emission_factors",
    "limit": 10
  }'

# Recherche hybride
curl -X POST http://localhost/api/v1/search/hybrid \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "chauffage batiment",
    "index": "emission_factors",
    "limit": 20,
    "filters": {"scope": 2}
  }'
```

### Utilisation dans le code

```php
// Dans un Controller
public function searchFactors(Request $request)
{
    $rag = app(FactorRAGService::class);

    $results = $rag->hybridSearch(
        $request->input('query'),
        $request->input('filters', []),
        $request->input('limit', 20)
    );

    return response()->json([
        'factors' => $results['factors'],
        'search_mode' => $results['mode'], // 'semantic' ou 'text'
        'total' => count($results['factors']),
    ]);
}
```

---

## API Endpoints

### Endpoints publics (API v1)

| Methode | Endpoint | Description | Rate Limit |
|---------|----------|-------------|------------|
| POST | `/api/v1/search/semantic` | Recherche semantique | 60/min |
| GET | `/api/v1/search/semantic/factors` | Recherche facteurs | 120/min |
| POST | `/api/v1/search/hybrid` | Recherche hybride | 60/min |
| GET | `/api/v1/search/similar/{index}/{id}` | Items similaires | 60/min |
| GET | `/api/v1/search/indexes` | Liste des index | 30/min |
| POST | `/api/v1/search/indexes/{index}/reindex` | Reindexation | 5/hour |
| GET | `/api/v1/search/health` | Sante du service | 60/min |

### Exemple de reponse

```json
{
  "data": [
    {
      "id": "uuid-factor-1",
      "name": "Voiture particuliere - essence",
      "category": "Transport routier",
      "scope": 1,
      "value": 0.193,
      "unit": "kgCO2e/km",
      "source": "ADEME",
      "score": 0.92
    },
    {
      "id": "uuid-factor-2",
      "name": "Vehicule utilitaire leger - essence",
      "category": "Transport routier",
      "scope": 1,
      "value": 0.245,
      "unit": "kgCO2e/km",
      "source": "ADEME",
      "score": 0.87
    }
  ],
  "meta": {
    "query": "voiture essence",
    "mode": "semantic",
    "total": 2,
    "search_time_ms": 45
  }
}
```

---

## Commandes Artisan

### usearch:index-factors

Indexe les facteurs d'emission dans uSearch.

```bash
# Indexation complete
php artisan usearch:index-factors --full

# Options disponibles
--full          # Reindexation complete (supprime et recree)
--source=ADEME  # Filtrer par source (ADEME, UBA, GHG, etc.)
--batch=100     # Taille des batches
--sync          # Execution synchrone (pas de job queue)
```

### usearch:health

Verifie l'etat du service uSearch.

```bash
php artisan usearch:health

# Sortie exemple:
# uSearch Service Status
# ----------------------
# URL: http://localhost:8001
# Status: Healthy
#
# Indexes:
# - emission_factors: 15,432 vectors (active)
# - transactions: 0 vectors (empty)
# - documents: 0 vectors (empty)
# - actions: 245 vectors (active)
```

---

## Integration UI (Livewire)

### FactorSelector Component

Le composant `FactorSelector` integre la recherche semantique avec un toggle.

```php
// app/Livewire/Emissions/FactorSelector.php

class FactorSelector extends Component
{
    public string $search = '';
    public bool $useSemanticSearch = true;
    public string $searchMode = 'text';

    public function getFactorsProperty(): LengthAwarePaginator
    {
        if ($this->useSemanticSearch && strlen($this->search) >= 3) {
            return $this->getSemanticSearchResults();
        }

        $this->searchMode = 'text';
        return $this->getTextSearchResults();
    }

    protected function getSemanticSearchResults(): LengthAwarePaginator
    {
        $factorRAG = app(FactorRAGService::class);

        $results = $factorRAG->hybridSearch(
            $this->search,
            $this->buildFilters(),
            100
        );

        $this->searchMode = $results['mode'] ?? 'semantic';

        // Pagination manuelle des resultats
        return new LengthAwarePaginator(
            collect($results['factors'])->forPage($this->page, $this->perPage),
            count($results['factors']),
            $this->perPage,
            $this->page
        );
    }
}
```

### Vue Blade

```blade
{{-- resources/views/livewire/emissions/factor-selector.blade.php --}}

<div class="mb-4">
    {{-- Toggle recherche semantique --}}
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox"
               wire:model.live="useSemanticSearch"
               class="sr-only peer">
        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer
                    peer-checked:bg-green-600 peer-checked:after:translate-x-full
                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                    after:bg-white after:rounded-full after:h-5 after:w-5
                    after:transition-all"></div>
        <span class="ms-3 text-sm font-medium text-gray-700">
            {{ __('carbex.emissions.semantic_search') }}
        </span>
    </label>

    {{-- Indicateur de mode --}}
    @if($search && strlen($search) >= 3)
        <span class="ml-4 text-xs px-2 py-1 rounded-full
                     {{ $searchMode === 'semantic' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
            {{ $searchMode === 'semantic'
                ? __('carbex.emissions.mode_semantic')
                : __('carbex.emissions.mode_text') }}
        </span>
    @endif
</div>

{{-- Champ de recherche --}}
<input type="text"
       wire:model.live.debounce.300ms="search"
       placeholder="{{ __('carbex.emissions.search_placeholder') }}"
       class="w-full rounded-lg border-gray-300">
```

### Traductions

```php
// lang/fr/carbex.php
'emissions' => [
    'semantic_search' => 'Recherche intelligente (IA)',
    'mode_semantic' => 'Mode semantique',
    'mode_text' => 'Mode texte',
    'search_placeholder' => 'Rechercher un facteur d\'emission...',
],

// lang/en/carbex.php
'emissions' => [
    'semantic_search' => 'Smart search (AI)',
    'mode_semantic' => 'Semantic mode',
    'mode_text' => 'Text mode',
    'search_placeholder' => 'Search for an emission factor...',
],

// lang/de/carbex.php
'emissions' => [
    'semantic_search' => 'Intelligente Suche (KI)',
    'mode_semantic' => 'Semantischer Modus',
    'mode_text' => 'Textmodus',
    'search_placeholder' => 'Emissionsfaktor suchen...',
],
```

---

## Troubleshooting

### Le service uSearch ne repond pas

```bash
# Verifier le conteneur
docker compose ps usearch
docker compose logs usearch

# Redemarrer le service
docker compose restart usearch

# Verifier la sante
curl http://localhost:8001/health
```

### Les embeddings ne sont pas generes

```bash
# Verifier la configuration
php artisan tinker
>>> config('usearch.embeddings.provider')
>>> config('usearch.url')

# Verifier les logs
tail -f storage/logs/laravel.log | grep -i usearch
```

### La recherche retourne des resultats vides

```bash
# Verifier que l'index contient des donnees
php artisan usearch:health

# Reindexer les facteurs
php artisan usearch:index-factors --full --sync
```

### Fallback vers la recherche texte

Si uSearch est indisponible, le systeme bascule automatiquement sur Meilisearch. Verifiez dans les logs :

```
[warning] uSearch unavailable, falling back to text search
```

### Performance lente

```bash
# Optimiser l'index
curl -X POST http://localhost:8001/indexes/emission_factors/optimize \
  -H "X-API-Key: your-api-key"

# Verifier les stats
curl http://localhost:8001/stats
```

---

## References

- [uSearch GitHub](https://github.com/unum-cloud/usearch)
- [Sentence Transformers](https://www.sbert.net/)
- [OpenAI Embeddings](https://platform.openai.com/docs/guides/embeddings)
- [FastAPI Documentation](https://fastapi.tiangolo.com/)

---

## Changelog

| Version | Date | Description |
|---------|------|-------------|
| 1.0 | 2026-01-13 | Version initiale |
