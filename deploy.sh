#!/bin/bash
set -e

echo "=== Deployment Started ==="

# 1. Pull latest changes
echo "Pulling latest code changes..."
git pull origin main

# 2. Build and restart Docker containers
echo "Building and starting Docker containers..."
docker compose down
docker compose up -d --build

# 3. Wait for DB to be ready
echo "Waiting for database to be ready..."
for i in {1..30}; do
    if docker compose exec -T app php artisan db:show >/dev/null 2>&1; then
        echo "Database is ready!"
        break
    fi
    echo "Database is not ready yet. Waiting 3 seconds..."
    sleep 3
done

# 4. Run database migrations inside the app container
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force

# 5. Clear and optimize caches
echo "Optimizing application cache..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo "=== Deployment Finished Successfully ==="
