#!/usr/bin/env sh
set -e

cd Rentivator

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force --no-interaction || true

# Start Laravel server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
