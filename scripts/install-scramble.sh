#!/bin/bash
# Install Scramble for OpenAPI documentation
# Run this script with Laravel Sail: ./vendor/bin/sail shell
# Then execute: bash scripts/install-scramble.sh

set -e

echo "Installing Scramble for OpenAPI documentation..."

# Install Scramble
composer require dedoc/scramble

# Publish the config (optional, already created)
# php artisan vendor:publish --tag=scramble-config

echo ""
echo "Scramble installed successfully!"
echo ""
echo "Access your API documentation at:"
echo "  - Interactive docs: http://localhost/docs/api"
echo "  - OpenAPI JSON: http://localhost/docs/api.json"
echo ""
echo "Configuration file: config/scramble.php"
