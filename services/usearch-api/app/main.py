"""
uSearch API - Semantic Search Microservice for Carbex
======================================================
FastAPI microservice exposing uSearch vector similarity search.
Handles embedding storage, indexing, and semantic queries.
"""

from contextlib import asynccontextmanager
from fastapi import FastAPI, HTTPException, Depends, Header
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import Optional
import os
import logging

from app.search import SearchEngine
from app.embeddings import EmbeddingService
from app.models import (
    SearchRequest,
    SearchResponse,
    IndexRequest,
    IndexResponse,
    BatchIndexRequest,
    BatchIndexResponse,
    HealthResponse,
    StatsResponse,
    SimilarRequest,
    DeleteRequest,
)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s"
)
logger = logging.getLogger(__name__)

# Global instances
search_engine: Optional[SearchEngine] = None
embedding_service: Optional[EmbeddingService] = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan manager - initialize and cleanup resources."""
    global search_engine, embedding_service

    logger.info("Initializing uSearch API...")

    # Initialize embedding service
    embedding_service = EmbeddingService(
        provider=os.getenv("EMBEDDING_PROVIDER", "openai"),
        api_key=os.getenv("EMBEDDING_API_KEY"),
        model=os.getenv("EMBEDDING_MODEL", "text-embedding-3-small"),
    )

    # Initialize search engine
    search_engine = SearchEngine(
        index_path=os.getenv("INDEX_PATH", "/data/indexes"),
        dimensions=int(os.getenv("VECTOR_DIMENSIONS", "1536")),
    )

    # Load existing indexes
    await search_engine.load_indexes()

    logger.info("uSearch API initialized successfully")

    yield

    # Cleanup
    logger.info("Shutting down uSearch API...")
    if search_engine:
        await search_engine.save_indexes()
    logger.info("uSearch API shutdown complete")


app = FastAPI(
    title="Carbex uSearch API",
    description="Semantic search microservice using uSearch vector similarity engine",
    version="1.0.0",
    lifespan=lifespan,
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=os.getenv("CORS_ORIGINS", "http://localhost,http://localhost:8000").split(","),
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# API Key authentication
async def verify_api_key(x_api_key: str = Header(None)):
    """Verify API key for authenticated endpoints."""
    expected_key = os.getenv("USEARCH_API_KEY")
    if expected_key and x_api_key != expected_key:
        raise HTTPException(status_code=401, detail="Invalid API key")
    return x_api_key


# Health & Status Endpoints
@app.get("/health", response_model=HealthResponse, tags=["Health"])
async def health_check():
    """Health check endpoint for monitoring."""
    return HealthResponse(
        status="healthy",
        version="1.0.0",
        indexes_loaded=search_engine.get_index_count() if search_engine else 0,
    )


@app.get("/stats", response_model=StatsResponse, tags=["Health"])
async def get_stats(api_key: str = Depends(verify_api_key)):
    """Get detailed statistics about indexes and vectors."""
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    return await search_engine.get_stats()


# Search Endpoints
@app.post("/search", response_model=SearchResponse, tags=["Search"])
async def semantic_search(
    request: SearchRequest,
    api_key: str = Depends(verify_api_key)
):
    """
    Perform semantic search using natural language query.

    The query is converted to an embedding and compared against stored vectors
    using HNSW approximate nearest neighbor search.
    """
    if not search_engine or not embedding_service:
        raise HTTPException(status_code=503, detail="Services not initialized")

    try:
        # Generate embedding for query
        query_embedding = await embedding_service.generate_embedding(request.query)

        # Search in specified index
        results = await search_engine.search(
            index_name=request.index,
            query_vector=query_embedding,
            top_k=request.top_k,
            filters=request.filters,
            min_score=request.min_score,
        )

        return SearchResponse(
            query=request.query,
            results=results,
            total=len(results),
            index=request.index,
        )

    except Exception as e:
        logger.error(f"Search error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/search/vector", response_model=SearchResponse, tags=["Search"])
async def vector_search(
    index: str,
    vector: list[float],
    top_k: int = 10,
    min_score: float = 0.0,
    api_key: str = Depends(verify_api_key)
):
    """
    Perform search using a pre-computed vector.
    Use this when you already have an embedding from another source.
    """
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        results = await search_engine.search(
            index_name=index,
            query_vector=vector,
            top_k=top_k,
            min_score=min_score,
        )

        return SearchResponse(
            query="[vector search]",
            results=results,
            total=len(results),
            index=index,
        )

    except Exception as e:
        logger.error(f"Vector search error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/similar", response_model=SearchResponse, tags=["Search"])
async def find_similar(
    request: SimilarRequest,
    api_key: str = Depends(verify_api_key)
):
    """
    Find items similar to an existing indexed item.
    Uses the stored vector of the specified item to find neighbors.
    """
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        results = await search_engine.find_similar(
            index_name=request.index,
            item_id=request.item_id,
            top_k=request.top_k,
            exclude_self=request.exclude_self,
        )

        return SearchResponse(
            query=f"similar to {request.item_id}",
            results=results,
            total=len(results),
            index=request.index,
        )

    except Exception as e:
        logger.error(f"Find similar error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


# Indexing Endpoints
@app.post("/index", response_model=IndexResponse, tags=["Indexing"])
async def index_item(
    request: IndexRequest,
    api_key: str = Depends(verify_api_key)
):
    """
    Index a single item with its text content.
    The text is converted to an embedding and stored in the specified index.
    """
    if not search_engine or not embedding_service:
        raise HTTPException(status_code=503, detail="Services not initialized")

    try:
        # Generate embedding from content
        embedding = await embedding_service.generate_embedding(request.content)

        # Store in index
        await search_engine.index_item(
            index_name=request.index,
            item_id=request.id,
            vector=embedding,
            metadata=request.metadata,
        )

        return IndexResponse(
            success=True,
            id=request.id,
            index=request.index,
        )

    except Exception as e:
        logger.error(f"Index error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/index/vector", response_model=IndexResponse, tags=["Indexing"])
async def index_vector(
    index: str,
    id: str,
    vector: list[float],
    metadata: Optional[dict] = None,
    api_key: str = Depends(verify_api_key)
):
    """
    Index an item with a pre-computed vector.
    Use this when embeddings are generated externally.
    """
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        await search_engine.index_item(
            index_name=index,
            item_id=id,
            vector=vector,
            metadata=metadata,
        )

        return IndexResponse(
            success=True,
            id=id,
            index=index,
        )

    except Exception as e:
        logger.error(f"Index vector error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/index/batch", response_model=BatchIndexResponse, tags=["Indexing"])
async def batch_index(
    request: BatchIndexRequest,
    api_key: str = Depends(verify_api_key)
):
    """
    Index multiple items in a single batch operation.
    More efficient than individual indexing for bulk operations.
    """
    if not search_engine or not embedding_service:
        raise HTTPException(status_code=503, detail="Services not initialized")

    try:
        indexed = 0
        errors = []

        # Process items in batches for embedding generation
        batch_size = 100
        items = request.items

        for i in range(0, len(items), batch_size):
            batch = items[i:i + batch_size]
            contents = [item.content for item in batch]

            # Generate embeddings in batch
            embeddings = await embedding_service.generate_embeddings_batch(contents)

            # Index each item
            for j, item in enumerate(batch):
                try:
                    await search_engine.index_item(
                        index_name=request.index,
                        item_id=item.id,
                        vector=embeddings[j],
                        metadata=item.metadata,
                    )
                    indexed += 1
                except Exception as e:
                    errors.append({"id": item.id, "error": str(e)})

        return BatchIndexResponse(
            success=len(errors) == 0,
            indexed=indexed,
            errors=errors if errors else None,
            index=request.index,
        )

    except Exception as e:
        logger.error(f"Batch index error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.delete("/index/{index_name}/{item_id}", tags=["Indexing"])
async def delete_item(
    index_name: str,
    item_id: str,
    api_key: str = Depends(verify_api_key)
):
    """Delete a single item from an index."""
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        await search_engine.delete_item(index_name, item_id)
        return {"success": True, "id": item_id, "index": index_name}

    except Exception as e:
        logger.error(f"Delete error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


# Index Management Endpoints
@app.get("/indexes", tags=["Management"])
async def list_indexes(api_key: str = Depends(verify_api_key)):
    """List all available indexes with their statistics."""
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    return await search_engine.list_indexes()


@app.post("/indexes/{index_name}", tags=["Management"])
async def create_index(
    index_name: str,
    dimensions: int = 1536,
    metric: str = "cos",
    api_key: str = Depends(verify_api_key)
):
    """
    Create a new vector index.

    Args:
        index_name: Unique name for the index
        dimensions: Vector dimensions (1536 for OpenAI, 1024 for Claude)
        metric: Distance metric (cos, l2, ip)
    """
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        await search_engine.create_index(index_name, dimensions, metric)
        return {"success": True, "index": index_name, "dimensions": dimensions}

    except Exception as e:
        logger.error(f"Create index error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.delete("/indexes/{index_name}", tags=["Management"])
async def delete_index(
    index_name: str,
    api_key: str = Depends(verify_api_key)
):
    """Delete an entire index and all its vectors."""
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        await search_engine.delete_index(index_name)
        return {"success": True, "index": index_name}

    except Exception as e:
        logger.error(f"Delete index error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/indexes/{index_name}/optimize", tags=["Management"])
async def optimize_index(
    index_name: str,
    api_key: str = Depends(verify_api_key)
):
    """Optimize an index for better search performance."""
    if not search_engine:
        raise HTTPException(status_code=503, detail="Search engine not initialized")

    try:
        await search_engine.optimize_index(index_name)
        return {"success": True, "index": index_name, "status": "optimized"}

    except Exception as e:
        logger.error(f"Optimize index error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


# Embedding Endpoints
@app.post("/embeddings", tags=["Embeddings"])
async def generate_embedding(
    text: str,
    api_key: str = Depends(verify_api_key)
):
    """Generate an embedding vector for the given text."""
    if not embedding_service:
        raise HTTPException(status_code=503, detail="Embedding service not initialized")

    try:
        embedding = await embedding_service.generate_embedding(text)
        return {
            "embedding": embedding,
            "dimensions": len(embedding),
            "model": embedding_service.model,
        }

    except Exception as e:
        logger.error(f"Embedding error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/embeddings/batch", tags=["Embeddings"])
async def generate_embeddings_batch(
    texts: list[str],
    api_key: str = Depends(verify_api_key)
):
    """Generate embedding vectors for multiple texts."""
    if not embedding_service:
        raise HTTPException(status_code=503, detail="Embedding service not initialized")

    try:
        embeddings = await embedding_service.generate_embeddings_batch(texts)
        return {
            "embeddings": embeddings,
            "count": len(embeddings),
            "dimensions": len(embeddings[0]) if embeddings else 0,
            "model": embedding_service.model,
        }

    except Exception as e:
        logger.error(f"Batch embedding error: {e}")
        raise HTTPException(status_code=500, detail=str(e))


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.main:app",
        host="0.0.0.0",
        port=int(os.getenv("PORT", "8001")),
        reload=os.getenv("DEBUG", "false").lower() == "true",
    )
