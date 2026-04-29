#!/usr/bin/env sh
set -e

# Change to Rentivator directory
cd Rentivator

# Ensure environment file exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Install PHP dependencies if needed
if [ -f composer.json ] && [ ! -d vendor ]; then
  # Check if composer is available
  if ! command -v composer &> /dev/null; then
    # Download and install composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "@unlink('composer-setup.php');"
  fi
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

# Optional frontend build step for Vite assets
if [ -f package.json ]; then
  npm install --ignore-scripts
  npm run build
fi

# Start Laravel's built-in server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
