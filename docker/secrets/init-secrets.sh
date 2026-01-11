#!/bin/bash
# ===========================================
# Carbex - Initialize Docker Secrets
# ===========================================
# Usage: ./init-secrets.sh [environment]
# environment: dev, staging, prod (default: dev)
# ===========================================

set -e

ENV=${1:-dev}
PREFIX=""

if [ "$ENV" != "prod" ]; then
    PREFIX="${ENV}_"
fi

echo "🔐 Initializing Carbex Docker Secrets for: $ENV"
echo "================================================"

# Check if running in Swarm mode
if ! docker info 2>/dev/null | grep -q "Swarm: active"; then
    echo "❌ Docker Swarm is not active. Initialize with: docker swarm init"
    exit 1
fi

# Function to create secret if it doesn't exist
create_secret() {
    local name="${PREFIX}$1"
    local value="$2"

    if docker secret inspect "$name" >/dev/null 2>&1; then
        echo "⏭️  Secret '$name' already exists, skipping..."
    else
        echo "$value" | docker secret create "$name" -
        echo "✅ Created secret: $name"
    fi
}

# Function to generate random value
generate_random() {
    openssl rand -base64 32 | tr -d '\n'
}

# Application Key
echo ""
echo "📦 Creating application secrets..."
create_secret "app_key" "base64:$(generate_random)"

# Database
echo ""
echo "🗄️  Creating database secrets..."
create_secret "db_password" "$(generate_random)"

# Redis
echo ""
echo "📮 Creating Redis secrets..."
create_secret "redis_password" "$(generate_random)"

# Meilisearch
echo ""
echo "🔍 Creating Meilisearch secrets..."
create_secret "meilisearch_key" "$(generate_random)"

# External services (require manual input for production)
if [ "$ENV" == "prod" ]; then
    echo ""
    echo "⚠️  External service secrets must be set manually for production:"
    echo "   - stripe_secret"
    echo "   - anthropic_api_key"
    echo "   - bridge_client_secret"
    echo "   - finapi_client_secret"
    echo ""
    echo "Use: echo 'your_secret_value' | docker secret create secret_name -"
else
    echo ""
    echo "🧪 Creating placeholder secrets for $ENV environment..."
    create_secret "stripe_secret" "sk_test_placeholder"
    create_secret "anthropic_api_key" "sk-ant-test-placeholder"
    create_secret "bridge_client_secret" "bridge_test_placeholder"
    create_secret "finapi_client_secret" "finapi_test_placeholder"
fi

echo ""
echo "================================================"
echo "✅ Secrets initialization complete!"
echo ""
echo "📋 List all secrets: docker secret ls"
echo "🚀 Deploy stack: docker stack deploy -c docker/stack.yml carbex"
