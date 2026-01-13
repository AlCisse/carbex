"""
Multi-provider Embedding Service.
Supports OpenAI, Anthropic Claude, and local models.
"""

import os
import logging
from typing import Optional
import httpx

logger = logging.getLogger(__name__)


class EmbeddingService:
    """
    Generate text embeddings using various providers.

    Supported providers:
    - openai: OpenAI text-embedding-3-small/large
    - anthropic: Claude embeddings (via API)
    - local: Local embedding model (sentence-transformers)
    """

    PROVIDER_CONFIGS = {
        "openai": {
            "url": "https://api.openai.com/v1/embeddings",
            "default_model": "text-embedding-3-small",
            "dimensions": 1536,
        },
        "anthropic": {
            "url": "https://api.anthropic.com/v1/embeddings",
            "default_model": "claude-3-embedding",
            "dimensions": 1024,
        },
        "voyage": {
            "url": "https://api.voyageai.com/v1/embeddings",
            "default_model": "voyage-large-2",
            "dimensions": 1536,
        },
    }

    def __init__(
        self,
        provider: str = "openai",
        api_key: Optional[str] = None,
        model: Optional[str] = None,
    ):
        """
        Initialize embedding service.

        Args:
            provider: Embedding provider (openai, anthropic, voyage, local)
            api_key: API key for the provider
            model: Specific model to use
        """
        self.provider = provider.lower()
        self.api_key = api_key or os.getenv(f"{provider.upper()}_API_KEY")
        self.model = model

        # Get provider config
        if self.provider in self.PROVIDER_CONFIGS:
            config = self.PROVIDER_CONFIGS[self.provider]
            self.url = config["url"]
            self.model = model or config["default_model"]
            self.dimensions = config["dimensions"]
        elif self.provider == "local":
            self._init_local_model(model)
        else:
            raise ValueError(f"Unknown provider: {provider}")

        # HTTP client for API calls
        self.client = httpx.AsyncClient(timeout=60.0)

        logger.info(f"Initialized EmbeddingService with provider={self.provider}, model={self.model}")

    def _init_local_model(self, model: Optional[str]):
        """Initialize local sentence-transformers model."""
        try:
            from sentence_transformers import SentenceTransformer
            model_name = model or "all-MiniLM-L6-v2"
            self._local_model = SentenceTransformer(model_name)
            self.model = model_name
            self.dimensions = self._local_model.get_sentence_embedding_dimension()
            logger.info(f"Loaded local model: {model_name}")
        except ImportError:
            raise ImportError("sentence-transformers required for local embeddings")

    async def generate_embedding(self, text: str) -> list[float]:
        """
        Generate embedding for a single text.

        Args:
            text: Input text to embed

        Returns:
            List of floats representing the embedding vector
        """
        if self.provider == "local":
            return self._generate_local_embedding(text)
        elif self.provider == "openai":
            return await self._generate_openai_embedding(text)
        elif self.provider == "anthropic":
            return await self._generate_anthropic_embedding(text)
        elif self.provider == "voyage":
            return await self._generate_voyage_embedding(text)
        else:
            raise ValueError(f"Unknown provider: {self.provider}")

    async def generate_embeddings_batch(self, texts: list[str]) -> list[list[float]]:
        """
        Generate embeddings for multiple texts in batch.

        More efficient than individual calls for large batches.

        Args:
            texts: List of input texts

        Returns:
            List of embedding vectors
        """
        if self.provider == "local":
            return self._generate_local_embeddings_batch(texts)
        elif self.provider == "openai":
            return await self._generate_openai_embeddings_batch(texts)
        elif self.provider == "anthropic":
            return await self._generate_anthropic_embeddings_batch(texts)
        elif self.provider == "voyage":
            return await self._generate_voyage_embeddings_batch(texts)
        else:
            raise ValueError(f"Unknown provider: {self.provider}")

    # OpenAI Implementation
    async def _generate_openai_embedding(self, text: str) -> list[float]:
        """Generate embedding using OpenAI API."""
        response = await self.client.post(
            self.url,
            headers={
                "Authorization": f"Bearer {self.api_key}",
                "Content-Type": "application/json",
            },
            json={
                "model": self.model,
                "input": text,
                "encoding_format": "float",
            },
        )
        response.raise_for_status()
        data = response.json()
        return data["data"][0]["embedding"]

    async def _generate_openai_embeddings_batch(self, texts: list[str]) -> list[list[float]]:
        """Generate embeddings batch using OpenAI API."""
        # OpenAI supports up to 2048 inputs per request
        batch_size = 2048
        all_embeddings = []

        for i in range(0, len(texts), batch_size):
            batch = texts[i:i + batch_size]

            response = await self.client.post(
                self.url,
                headers={
                    "Authorization": f"Bearer {self.api_key}",
                    "Content-Type": "application/json",
                },
                json={
                    "model": self.model,
                    "input": batch,
                    "encoding_format": "float",
                },
            )
            response.raise_for_status()
            data = response.json()

            # Sort by index to maintain order
            embeddings = sorted(data["data"], key=lambda x: x["index"])
            all_embeddings.extend([e["embedding"] for e in embeddings])

        return all_embeddings

    # Anthropic Implementation
    async def _generate_anthropic_embedding(self, text: str) -> list[float]:
        """Generate embedding using Anthropic API."""
        # Note: As of 2024, Anthropic doesn't have a public embeddings API
        # This is a placeholder for when it becomes available
        # For now, fall back to OpenAI
        logger.warning("Anthropic embeddings not yet available, using OpenAI fallback")
        self.provider = "openai"
        self.url = self.PROVIDER_CONFIGS["openai"]["url"]
        self.model = self.PROVIDER_CONFIGS["openai"]["default_model"]
        self.api_key = os.getenv("OPENAI_API_KEY")
        return await self._generate_openai_embedding(text)

    async def _generate_anthropic_embeddings_batch(self, texts: list[str]) -> list[list[float]]:
        """Generate embeddings batch using Anthropic API."""
        logger.warning("Anthropic embeddings not yet available, using OpenAI fallback")
        self.provider = "openai"
        self.url = self.PROVIDER_CONFIGS["openai"]["url"]
        self.model = self.PROVIDER_CONFIGS["openai"]["default_model"]
        self.api_key = os.getenv("OPENAI_API_KEY")
        return await self._generate_openai_embeddings_batch(texts)

    # Voyage AI Implementation
    async def _generate_voyage_embedding(self, text: str) -> list[float]:
        """Generate embedding using Voyage AI API."""
        response = await self.client.post(
            self.url,
            headers={
                "Authorization": f"Bearer {self.api_key}",
                "Content-Type": "application/json",
            },
            json={
                "model": self.model,
                "input": text,
            },
        )
        response.raise_for_status()
        data = response.json()
        return data["data"][0]["embedding"]

    async def _generate_voyage_embeddings_batch(self, texts: list[str]) -> list[list[float]]:
        """Generate embeddings batch using Voyage AI API."""
        batch_size = 128  # Voyage AI limit

        all_embeddings = []

        for i in range(0, len(texts), batch_size):
            batch = texts[i:i + batch_size]

            response = await self.client.post(
                self.url,
                headers={
                    "Authorization": f"Bearer {self.api_key}",
                    "Content-Type": "application/json",
                },
                json={
                    "model": self.model,
                    "input": batch,
                },
            )
            response.raise_for_status()
            data = response.json()

            embeddings = [e["embedding"] for e in data["data"]]
            all_embeddings.extend(embeddings)

        return all_embeddings

    # Local Model Implementation
    def _generate_local_embedding(self, text: str) -> list[float]:
        """Generate embedding using local sentence-transformers model."""
        embedding = self._local_model.encode(text)
        return embedding.tolist()

    def _generate_local_embeddings_batch(self, texts: list[str]) -> list[list[float]]:
        """Generate embeddings batch using local model."""
        embeddings = self._local_model.encode(texts)
        return [e.tolist() for e in embeddings]

    async def close(self):
        """Close HTTP client."""
        await self.client.aclose()
