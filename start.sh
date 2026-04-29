#!/usr/bin/env sh

# Ensure environment file exists
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  else
    echo "Warning: No .env.example found"
  fi
fi

# Install PHP dependencies only if needed
if [ ! -d vendor ]; then
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || exit 1
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
  php artisan key:generate --force || true
fi

# Clear caches (non-fatal)
php artisan config:clear || true
php artisan cache:clear || true

# Install frontend dependencies
if [ -f package.json ] && [ ! -d node_modules ]; then
  npm install --ignore-scripts || true
fi

# Build frontend assets (non-fatal)
if [ -f package.json ] && [ -f vite.config.js ]; then
  npm run build || true
fi

# Run migrations (non-fatal, may fail if DB not yet available)
php artisan migrate --force --no-interaction || true

# Start the server
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
