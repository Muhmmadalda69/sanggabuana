#!/bin/bash
set -e

echo "=== Deployment Started ==="

# 0. Ensure .env file exists
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        echo ".env file not found! Copying from .env.production..."
        cp .env.production .env
        echo "Please edit the newly created .env file with your production credentials, then run ./deploy.sh again."
        exit 1
    else
        echo "Error: Neither .env nor .env.production was found. Please create a .env file."
        exit 1
    fi
fi

# 1. Pull latest changes
echo "Pulling latest code changes..."
git pull origin main

# 2. Build and restart Docker containers
echo "Building and starting Docker containers..."
docker compose down
docker compose up -d --build

# 2.5 Ensure APP_KEY is generated
if ! grep -q "APP_KEY=base64" .env; then
    echo "APP_KEY is empty. Generating a new application key..."
    docker compose exec -T app php artisan key:generate
fi

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
docker compose exec -T app php artisan migrate

# 4.5 Link storage
echo "Linking storage..."
docker compose exec -T app php artisan storage:link

# 5. Clear and optimize caches
echo "Optimizing application cache..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo "=== Deployment Finished Successfully ==="
