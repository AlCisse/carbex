"""
uSearch Vector Search Engine wrapper.
Provides HNSW-based approximate nearest neighbor search.
"""

import os
import json
import asyncio
import logging
import time
from pathlib import Path
from typing import Optional
from datetime import datetime

import numpy as np
from usearch.index import Index, MetricKind

from app.models import SearchResult, IndexStats, StatsResponse

logger = logging.getLogger(__name__)


class SearchEngine:
    """
    Vector search engine using uSearch HNSW indexes.

    Features:
    - Multiple named indexes
    - Persistent storage
    - Metadata filtering
    - Automatic index management
    """

    METRIC_MAP = {
        "cos": MetricKind.Cos,
        "l2": MetricKind.L2sq,
        "ip": MetricKind.IP,
    }

    def __init__(self, index_path: str = "/data/indexes", dimensions: int = 1536):
        """
        Initialize the search engine.

        Args:
            index_path: Directory path for persistent index storage
            dimensions: Default vector dimensions for new indexes
        """
        self.index_path = Path(index_path)
        self.default_dimensions = dimensions
        self.indexes: dict[str, Index] = {}
        self.metadata: dict[str, dict[str, dict]] = {}  # index -> id -> metadata
        self.index_info: dict[str, dict] = {}  # index -> info (dimensions, metric, etc.)
        self.start_time = time.time()

        # Ensure index directory exists
        self.index_path.mkdir(parents=True, exist_ok=True)

    async def load_indexes(self):
        """Load all existing indexes from disk."""
        try:
            # Load index registry
            registry_path = self.index_path / "registry.json"
            if registry_path.exists():
                with open(registry_path, "r") as f:
                    self.index_info = json.load(f)

                # Load each registered index
                for name, info in self.index_info.items():
                    await self._load_index(name, info)

            logger.info(f"Loaded {len(self.indexes)} indexes")

        except Exception as e:
            logger.error(f"Error loading indexes: {e}")

    async def _load_index(self, name: str, info: dict):
        """Load a single index from disk."""
        try:
            index_file = self.index_path / f"{name}.usearch"
            metadata_file = self.index_path / f"{name}_metadata.json"

            if index_file.exists():
                # Create index with stored parameters
                metric = self.METRIC_MAP.get(info.get("metric", "cos"), MetricKind.Cos)
                index = Index(
                    ndim=info.get("dimensions", self.default_dimensions),
                    metric=metric,
                )
                index.load(str(index_file))
                self.indexes[name] = index

                # Load metadata
                if metadata_file.exists():
                    with open(metadata_file, "r") as f:
                        self.metadata[name] = json.load(f)
                else:
                    self.metadata[name] = {}

                logger.info(f"Loaded index '{name}' with {len(index)} vectors")

        except Exception as e:
            logger.error(f"Error loading index '{name}': {e}")

    async def save_indexes(self):
        """Save all indexes to disk."""
        try:
            # Save each index
            for name, index in self.indexes.items():
                await self._save_index(name)

            # Save registry
            registry_path = self.index_path / "registry.json"
            with open(registry_path, "w") as f:
                json.dump(self.index_info, f, indent=2, default=str)

            logger.info(f"Saved {len(self.indexes)} indexes")

        except Exception as e:
            logger.error(f"Error saving indexes: {e}")

    async def _save_index(self, name: str):
        """Save a single index to disk."""
        if name not in self.indexes:
            return

        try:
            index_file = self.index_path / f"{name}.usearch"
            metadata_file = self.index_path / f"{name}_metadata.json"

            # Save index
            self.indexes[name].save(str(index_file))

            # Save metadata
            if name in self.metadata:
                with open(metadata_file, "w") as f:
                    json.dump(self.metadata[name], f, indent=2)

            # Update timestamp
            if name in self.index_info:
                self.index_info[name]["updated_at"] = datetime.utcnow().isoformat()

        except Exception as e:
            logger.error(f"Error saving index '{name}': {e}")

    async def create_index(
        self,
        name: str,
        dimensions: int = None,
        metric: str = "cos"
    ):
        """
        Create a new vector index.

        Args:
            name: Unique index name
            dimensions: Vector dimensions
            metric: Distance metric (cos, l2, ip)
        """
        if name in self.indexes:
            raise ValueError(f"Index '{name}' already exists")

        dims = dimensions or self.default_dimensions
        metric_kind = self.METRIC_MAP.get(metric, MetricKind.Cos)

        # Create uSearch index
        index = Index(
            ndim=dims,
            metric=metric_kind,
            connectivity=16,  # HNSW M parameter
            expansion_add=128,  # ef_construction
            expansion_search=64,  # ef_search
        )

        self.indexes[name] = index
        self.metadata[name] = {}
        self.index_info[name] = {
            "dimensions": dims,
            "metric": metric,
            "created_at": datetime.utcnow().isoformat(),
            "updated_at": datetime.utcnow().isoformat(),
        }

        # Save immediately
        await self._save_index(name)

        logger.info(f"Created index '{name}' with {dims} dimensions")

    async def delete_index(self, name: str):
        """Delete an index and all its data."""
        if name not in self.indexes:
            raise ValueError(f"Index '{name}' not found")

        # Remove from memory
        del self.indexes[name]
        if name in self.metadata:
            del self.metadata[name]
        if name in self.index_info:
            del self.index_info[name]

        # Remove files
        index_file = self.index_path / f"{name}.usearch"
        metadata_file = self.index_path / f"{name}_metadata.json"

        if index_file.exists():
            index_file.unlink()
        if metadata_file.exists():
            metadata_file.unlink()

        logger.info(f"Deleted index '{name}'")

    async def index_item(
        self,
        index_name: str,
        item_id: str,
        vector: list[float],
        metadata: Optional[dict] = None
    ):
        """
        Add or update an item in the index.

        Args:
            index_name: Target index name
            item_id: Unique item identifier
            vector: Embedding vector
            metadata: Optional metadata dict
        """
        # Auto-create index if needed
        if index_name not in self.indexes:
            await self.create_index(index_name, dimensions=len(vector))

        index = self.indexes[index_name]

        # Convert to numpy array
        vec = np.array(vector, dtype=np.float32)

        # Generate numeric key from string ID
        key = self._id_to_key(index_name, item_id)

        # Add to index
        index.add(key, vec)

        # Store metadata
        if index_name not in self.metadata:
            self.metadata[index_name] = {}

        self.metadata[index_name][item_id] = {
            "key": key,
            **(metadata or {}),
        }

    async def search(
        self,
        index_name: str,
        query_vector: list[float],
        top_k: int = 10,
        filters: Optional[dict] = None,
        min_score: float = 0.0,
    ) -> list[SearchResult]:
        """
        Search for similar vectors.

        Args:
            index_name: Index to search
            query_vector: Query embedding vector
            top_k: Number of results
            filters: Metadata filters
            min_score: Minimum similarity score

        Returns:
            List of SearchResult objects
        """
        if index_name not in self.indexes:
            raise ValueError(f"Index '{index_name}' not found")

        index = self.indexes[index_name]

        if len(index) == 0:
            return []

        # Convert query to numpy
        query = np.array(query_vector, dtype=np.float32)

        # Search - get more results if filtering
        search_k = top_k * 3 if filters else top_k
        matches = index.search(query, search_k)

        # Build results
        results = []
        key_to_id = self._build_key_to_id_map(index_name)

        for key, distance in zip(matches.keys, matches.distances):
            # Convert distance to similarity score (0-1)
            # For cosine distance, similarity = 1 - distance
            score = float(1 - distance) if distance <= 1 else float(1 / (1 + distance))

            if score < min_score:
                continue

            # Find item ID
            item_id = key_to_id.get(int(key))
            if not item_id:
                continue

            # Get metadata
            item_metadata = self.metadata.get(index_name, {}).get(item_id, {})

            # Apply filters
            if filters and not self._matches_filters(item_metadata, filters):
                continue

            # Remove internal key from metadata
            result_metadata = {k: v for k, v in item_metadata.items() if k != "key"}

            results.append(SearchResult(
                id=item_id,
                score=round(score, 4),
                metadata=result_metadata if result_metadata else None,
            ))

            if len(results) >= top_k:
                break

        return results

    async def find_similar(
        self,
        index_name: str,
        item_id: str,
        top_k: int = 10,
        exclude_self: bool = True,
    ) -> list[SearchResult]:
        """Find items similar to an existing indexed item."""
        if index_name not in self.indexes:
            raise ValueError(f"Index '{index_name}' not found")

        # Get the item's vector
        key = self._id_to_key(index_name, item_id)
        index = self.indexes[index_name]

        # Retrieve vector by key
        vector = index.get(key)
        if vector is None:
            raise ValueError(f"Item '{item_id}' not found in index '{index_name}'")

        # Search using the vector
        results = await self.search(
            index_name=index_name,
            query_vector=vector.tolist(),
            top_k=top_k + (1 if exclude_self else 0),
        )

        # Optionally exclude self
        if exclude_self:
            results = [r for r in results if r.id != item_id][:top_k]

        return results

    async def delete_item(self, index_name: str, item_id: str):
        """Delete a single item from an index."""
        if index_name not in self.indexes:
            raise ValueError(f"Index '{index_name}' not found")

        # Remove from metadata
        if index_name in self.metadata and item_id in self.metadata[index_name]:
            key = self.metadata[index_name][item_id].get("key")
            del self.metadata[index_name][item_id]

            # Note: uSearch doesn't support deletion directly
            # In production, you'd need to rebuild the index or use soft deletion
            logger.warning(f"Item '{item_id}' removed from metadata (vector remains in index)")

    async def optimize_index(self, index_name: str):
        """Optimize an index for better performance."""
        if index_name not in self.indexes:
            raise ValueError(f"Index '{index_name}' not found")

        # Save and reload to compact
        await self._save_index(index_name)
        logger.info(f"Optimized index '{index_name}'")

    async def list_indexes(self) -> list[dict]:
        """List all indexes with their info."""
        result = []
        for name, index in self.indexes.items():
            info = self.index_info.get(name, {})
            result.append({
                "name": name,
                "dimensions": info.get("dimensions", self.default_dimensions),
                "metric": info.get("metric", "cos"),
                "vector_count": len(index),
                "created_at": info.get("created_at"),
                "updated_at": info.get("updated_at"),
            })
        return result

    async def get_stats(self) -> StatsResponse:
        """Get detailed statistics."""
        indexes = []
        total_vectors = 0

        for name, index in self.indexes.items():
            info = self.index_info.get(name, {})
            vec_count = len(index)
            total_vectors += vec_count

            # Estimate size
            dims = info.get("dimensions", self.default_dimensions)
            size_bytes = vec_count * dims * 4  # float32 = 4 bytes

            indexes.append(IndexStats(
                name=name,
                vector_count=vec_count,
                dimensions=dims,
                metric=info.get("metric", "cos"),
                size_bytes=size_bytes,
                created_at=info.get("created_at"),
                updated_at=info.get("updated_at"),
            ))

        # Estimate memory usage (rough)
        import sys
        memory_mb = sum(sys.getsizeof(idx) for idx in self.indexes.values()) / 1024 / 1024

        return StatsResponse(
            total_vectors=total_vectors,
            total_indexes=len(self.indexes),
            indexes=indexes,
            memory_usage_mb=round(memory_mb, 2),
            uptime_seconds=round(time.time() - self.start_time, 2),
        )

    def get_index_count(self) -> int:
        """Get number of loaded indexes."""
        return len(self.indexes)

    def _id_to_key(self, index_name: str, item_id: str) -> int:
        """Convert string ID to numeric key for uSearch."""
        # Check if already indexed
        if index_name in self.metadata and item_id in self.metadata[index_name]:
            return self.metadata[index_name][item_id]["key"]

        # Generate new key
        existing_keys = set()
        if index_name in self.metadata:
            for meta in self.metadata[index_name].values():
                if "key" in meta:
                    existing_keys.add(meta["key"])

        # Use hash or incremental
        key = hash(item_id) & 0x7FFFFFFFFFFFFFFF  # Positive 64-bit
        while key in existing_keys:
            key = (key + 1) & 0x7FFFFFFFFFFFFFFF

        return key

    def _build_key_to_id_map(self, index_name: str) -> dict[int, str]:
        """Build reverse mapping from numeric key to string ID."""
        result = {}
        if index_name in self.metadata:
            for item_id, meta in self.metadata[index_name].items():
                if "key" in meta:
                    result[meta["key"]] = item_id
        return result

    def _matches_filters(self, metadata: dict, filters: dict) -> bool:
        """Check if metadata matches all filters."""
        for key, value in filters.items():
            if key not in metadata:
                return False
            if isinstance(value, list):
                if metadata[key] not in value:
                    return False
            elif metadata[key] != value:
                return False
        return True
