#!/usr/bin/env sh
set -e

# Install system dependencies - handle both Alpine and Debian/Ubuntu
if command -v apk &> /dev/null; then
  # Alpine Linux
  apk update && apk add --no-cache \
    php \
    php-cli \
    php-fpm \
    php-pdo \
    php-pdo_mysql \
    php-mbstring \
    php-xml \
    php-curl \
    php-zip \
    php-json \
    composer \
    npm \
    git
elif command -v apt-get &> /dev/null; then
  # Debian/Ubuntu
  apt-get update && apt-get install -y \
    php \
    php-cli \
    php-fpm \
    php-mysql \
    php-mbstring \
    php-xml \
    php-curl \
    php-zip \
    composer \
    npm \
    git
else
  echo "Warning: Could not detect package manager. Assuming PHP is already installed."
fi

# Change to Rentivator directory
cd Rentivator

# Ensure environment file exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Install PHP dependencies
if [ -f composer.json ] && [ ! -d vendor ]; then
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

# Generate APP_KEY if missing
if ! grep -q "^APP_KEY=" .env; then
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
