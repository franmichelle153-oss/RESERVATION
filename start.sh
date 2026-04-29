#!/usr/bin/env sh
set -e

# Ensure environment file exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Install PHP dependencies
if [ -f composer.json ] && [ ! -d vendor ]; then
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

# Generate APP_KEY if missing
if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

# Clear any stale caches
php artisan config:clear
php artisan cache:clear

# Optional frontend build step for Vite assets
if [ -f package.json ]; then
  npm install --ignore-scripts
  npm run build
fi

# Run migrations
php artisan migrate --force --no-interaction

# Start Laravel's built-in server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
