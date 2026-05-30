#!/bin/bash

# Change to Laravel app directory
cd src

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Install dependencies if not cached
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    npm ci
    npm run build
fi

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed database if needed
php artisan db:seed --force

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000} 