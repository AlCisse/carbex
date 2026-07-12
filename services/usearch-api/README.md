# uSearch API - Semantic Search Microservice

Microservice Python/FastAPI exposant uSearch pour la recherche semantique vectorielle dans LinsCarbon.

## Architecture

```
services/usearch-api/
├── app/
│   ├── __init__.py
│   ├── main.py          # FastAPI application & endpoints
│   ├── models.py        # Pydantic schemas
│   ├── search.py        # uSearch wrapper (HNSW indexes)
│   └── embeddings.py    # Multi-provider embedding generation
├── tests/
│   └── test_api.py      # API tests
├── Dockerfile
├── requirements.txt
└── .env.example
```

## Features

- **Vector Search**: uSearch HNSW for sub-100ms queries on millions of vectors
- **Multi-Provider Embeddings**: OpenAI, Anthropic (future), Voyage AI, local models
- **Multiple Indexes**: Named indexes for different data types (factors, transactions, documents)
- **Metadata Filtering**: Filter search results by metadata attributes
- **Batch Operations**: Efficient bulk indexing for large datasets
- **Persistent Storage**: Indexes saved to disk and loaded on startup

## API Endpoints

### Health & Status
- `GET /health` - Health check
- `GET /stats` - Detailed statistics (requires auth)

### Search
- `POST /search` - Semantic search with natural language query
- `POST /search/vector` - Search with pre-computed vector
- `POST /similar` - Find similar items to an existing item

### Indexing
- `POST /index` - Index a single item (text -> embedding -> store)
- `POST /index/vector` - Index with pre-computed vector
- `POST /index/batch` - Batch index multiple items
- `DELETE /index/{index}/{id}` - Delete an item

### Index Management
- `GET /indexes` - List all indexes
- `POST /indexes/{name}` - Create new index
- `DELETE /indexes/{name}` - Delete index
- `POST /indexes/{name}/optimize` - Optimize index

### Embeddings
- `POST /embeddings` - Generate embedding for text
- `POST /embeddings/batch` - Batch embedding generation

## Configuration

```env
# Server
PORT=8001
DEBUG=false

# Security
USEARCH_API_KEY=your-api-key

# Embedding Provider
EMBEDDING_PROVIDER=openai  # openai, anthropic, voyage, local
EMBEDDING_MODEL=text-embedding-3-small
OPENAI_API_KEY=sk-...

# Storage
INDEX_PATH=/data/indexes
VECTOR_DIMENSIONS=1536
```

## Development

```bash
# Install dependencies
pip install -r requirements.txt

# Run locally
uvicorn app.main:app --reload --port 8001

# Run tests
pytest tests/ -v

# Docker build
docker build -t linscarbon-usearch .

# Docker run
docker run -p 8001:8001 -e OPENAI_API_KEY=sk-... linscarbon-usearch
```

## Usage Example

```python
import httpx

# Search for emission factors
response = httpx.post(
    "http://localhost:8001/search",
    headers={"X-API-Key": "your-key"},
    json={
        "query": "electricity consumption office building",
        "index": "emission_factors",
        "top_k": 10
    }
)

results = response.json()["results"]
for r in results:
    print(f"{r['id']}: {r['score']:.3f}")
```

## Integration with Laravel

The Laravel app communicates with this microservice via HTTP:

```php
// config/usearch.php
return [
    'url' => env('USEARCH_URL', 'http://localhost:8001'),
    'api_key' => env('USEARCH_API_KEY'),
];

// app/Services/Search/USearchClient.php
$response = Http::withHeaders([
    'X-API-Key' => config('usearch.api_key'),
])->post(config('usearch.url') . '/search', [
    'query' => $query,
    'index' => 'emission_factors',
    'top_k' => 10,
]);
```
