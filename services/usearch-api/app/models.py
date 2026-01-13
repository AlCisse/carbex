"""
Pydantic models for uSearch API requests and responses.
"""

from pydantic import BaseModel, Field
from typing import Optional
from datetime import datetime


# Health & Stats
class HealthResponse(BaseModel):
    """Health check response."""
    status: str = Field(..., description="Service health status")
    version: str = Field(..., description="API version")
    indexes_loaded: int = Field(..., description="Number of loaded indexes")


class IndexStats(BaseModel):
    """Statistics for a single index."""
    name: str
    vector_count: int
    dimensions: int
    metric: str
    size_bytes: int
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None


class StatsResponse(BaseModel):
    """Detailed statistics response."""
    total_vectors: int
    total_indexes: int
    indexes: list[IndexStats]
    memory_usage_mb: float
    uptime_seconds: float


# Search
class SearchRequest(BaseModel):
    """Semantic search request."""
    query: str = Field(..., description="Natural language search query", min_length=1)
    index: str = Field(..., description="Index to search in")
    top_k: int = Field(default=10, description="Number of results to return", ge=1, le=100)
    filters: Optional[dict] = Field(default=None, description="Metadata filters")
    min_score: float = Field(default=0.0, description="Minimum similarity score", ge=0.0, le=1.0)


class SearchResult(BaseModel):
    """Single search result."""
    id: str = Field(..., description="Item ID")
    score: float = Field(..., description="Similarity score (0-1)")
    metadata: Optional[dict] = Field(default=None, description="Item metadata")


class SearchResponse(BaseModel):
    """Search response with results."""
    query: str
    results: list[SearchResult]
    total: int
    index: str


class SimilarRequest(BaseModel):
    """Find similar items request."""
    index: str = Field(..., description="Index to search in")
    item_id: str = Field(..., description="ID of the item to find similar items for")
    top_k: int = Field(default=10, description="Number of results to return", ge=1, le=100)
    exclude_self: bool = Field(default=True, description="Exclude the query item from results")


# Indexing
class IndexRequest(BaseModel):
    """Index a single item request."""
    id: str = Field(..., description="Unique item ID")
    content: str = Field(..., description="Text content to embed and index")
    index: str = Field(..., description="Index to store in")
    metadata: Optional[dict] = Field(default=None, description="Additional metadata")


class IndexResponse(BaseModel):
    """Index operation response."""
    success: bool
    id: str
    index: str


class BatchIndexItem(BaseModel):
    """Single item for batch indexing."""
    id: str = Field(..., description="Unique item ID")
    content: str = Field(..., description="Text content to embed")
    metadata: Optional[dict] = Field(default=None, description="Additional metadata")


class BatchIndexRequest(BaseModel):
    """Batch index request."""
    index: str = Field(..., description="Index to store in")
    items: list[BatchIndexItem] = Field(..., description="Items to index", min_length=1)


class BatchIndexResponse(BaseModel):
    """Batch index operation response."""
    success: bool
    indexed: int
    errors: Optional[list[dict]] = None
    index: str


class DeleteRequest(BaseModel):
    """Delete items request."""
    index: str = Field(..., description="Index name")
    ids: list[str] = Field(..., description="Item IDs to delete")


# Index Management
class CreateIndexRequest(BaseModel):
    """Create new index request."""
    name: str = Field(..., description="Index name", min_length=1, max_length=64)
    dimensions: int = Field(default=1536, description="Vector dimensions", ge=64, le=4096)
    metric: str = Field(default="cos", description="Distance metric: cos, l2, ip")


class IndexInfo(BaseModel):
    """Index information."""
    name: str
    dimensions: int
    metric: str
    vector_count: int
    size_bytes: int
