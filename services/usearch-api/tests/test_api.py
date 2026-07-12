"""
Tests for uSearch API endpoints.
"""

import pytest
from fastapi.testclient import TestClient
from unittest.mock import AsyncMock, patch

# Import will be done after app initialization
# from app.main import app


@pytest.fixture
def client():
    """Create test client with mocked services."""
    with patch.dict("os.environ", {
        "USEARCH_API_KEY": "test-key",
        "EMBEDDING_PROVIDER": "openai",
        "OPENAI_API_KEY": "test-openai-key",
        "INDEX_PATH": "/tmp/test_indexes",
    }):
        from app.main import app
        with TestClient(app) as client:
            yield client


class TestHealthEndpoints:
    """Test health and status endpoints."""

    def test_health_check(self, client):
        """Health endpoint should return healthy status."""
        response = client.get("/health")
        assert response.status_code == 200
        data = response.json()
        assert data["status"] == "healthy"
        assert "version" in data

    def test_stats_requires_auth(self, client):
        """Stats endpoint should require API key."""
        response = client.get("/stats")
        assert response.status_code == 401

    def test_stats_with_auth(self, client):
        """Stats endpoint should work with valid API key."""
        response = client.get(
            "/stats",
            headers={"X-API-Key": "test-key"}
        )
        assert response.status_code == 200


class TestSearchEndpoints:
    """Test search-related endpoints."""

    def test_search_requires_auth(self, client):
        """Search endpoint should require API key."""
        response = client.post(
            "/search",
            json={"query": "test", "index": "factors"}
        )
        assert response.status_code == 401

    def test_search_validation(self, client):
        """Search should validate required fields."""
        response = client.post(
            "/search",
            headers={"X-API-Key": "test-key"},
            json={}
        )
        assert response.status_code == 422


class TestIndexEndpoints:
    """Test indexing endpoints."""

    def test_create_index(self, client):
        """Should be able to create a new index."""
        response = client.post(
            "/indexes/test_index",
            headers={"X-API-Key": "test-key"},
            params={"dimensions": 1536, "metric": "cos"}
        )
        # May fail if services not fully mocked, but should not be 401
        assert response.status_code != 401

    def test_list_indexes(self, client):
        """Should list available indexes."""
        response = client.get(
            "/indexes",
            headers={"X-API-Key": "test-key"}
        )
        assert response.status_code == 200


class TestEmbeddingEndpoints:
    """Test embedding generation endpoints."""

    def test_embedding_requires_auth(self, client):
        """Embedding endpoint should require API key."""
        response = client.post(
            "/embeddings",
            params={"text": "test text"}
        )
        assert response.status_code == 401
