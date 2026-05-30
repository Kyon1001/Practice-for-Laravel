# Multi-stage build for Laravel EC Site optimized for Railway
FROM node:18-alpine AS frontend-build

WORKDIR /app

# Copy package files
COPY src/package*.json ./

# Install dependencies with reduced memory usage
RUN npm ci --only=production --no-audit --no-fund

# Copy source files
COPY src/ .

# Build assets
RUN npm run build

# PHP Stage - Optimized for Railway
FROM php:8.2-fpm-alpine AS backend

# Install essential system dependencies first (split to reduce memory usage)
RUN apk add --no-cache \
    nginx \
    supervisor

# Install database and utility tools
RUN apk add --no-cache \
    mysql-client \
    zip \
    unzip \
    git \
    curl

# Install image processing dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev

# Install additional PHP dependencies
RUN apk add --no-cache \
    icu-dev \
    oniguruma-dev

# Configure and install PHP extensions (split for memory efficiency)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install core PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif

# Install additional PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pcntl \
    bcmath \
    gd

# Install final PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    zip \
    intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY src/ .

# Copy built assets from frontend stage
COPY --from=frontend-build /app/public/build ./public/build

# Install PHP dependencies with optimizations
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Create necessary directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Clean up to reduce image size
RUN apk del --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev

# Expose port
EXPOSE $PORT

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Health check optimized for Railway
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:$PORT/ || exit 1 