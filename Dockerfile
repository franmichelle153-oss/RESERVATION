FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    curl \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy entire project
COPY . .

# Change to Rentivator subdirectory for remaining setup
WORKDIR /app/Rentivator

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Generate APP_KEY if not present
RUN if ! grep -q "^APP_KEY=" .env; then php artisan key:generate --force; fi || true

# Install and build frontend assets
RUN npm install --ignore-scripts && npm run build

# Expose port
EXPOSE 8000

# Run migrations and start server
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force --no-interaction || true && \
    php artisan serve --host=0.0.0.0 --port=8000
