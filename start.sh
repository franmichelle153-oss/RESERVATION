#!/usr/bin/env sh
set -e

# Install system dependencies
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

# Optional frontend build step for Vite assets
if [ -f package.json ]; then
  npm install --ignore-scripts
  npm run build
fi

# Start Laravel's built-in server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
