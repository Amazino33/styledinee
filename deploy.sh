#!/usr/bin/env bash
set -e

PHP=/usr/bin/php
ARTISAN="$PHP artisan"

echo "==> Pulling latest code..."
git pull

echo "==> Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running migrations..."
$ARTISAN migrate --force

echo "==> Linking storage..."
$ARTISAN storage:link --force 2>/dev/null || true

echo "==> Clearing caches..."
$ARTISAN view:clear
$ARTISAN cache:clear

echo "==> Rebuilding caches..."
$ARTISAN config:cache
$ARTISAN route:cache

echo ""
echo "Deploy complete."
