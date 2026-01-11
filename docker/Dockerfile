# ===========================================
# Carbex - Multi-stage Dockerfile
# ===========================================

# ---------------------------------------------
# Stage 1: Composer Dependencies
# ---------------------------------------------
FROM composer:2 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ---------------------------------------------
# Stage 2: Node.js Assets Builder
# ---------------------------------------------
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json* ./

RUN npm ci --ignore-scripts

COPY resources ./resources
COPY vite.config.js ./
COPY --from=composer-builder /app/vendor ./vendor

RUN npm run build

# ---------------------------------------------
# Stage 3: Production Image
# ---------------------------------------------
FROM php:8.4-fpm-alpine AS production

LABEL maintainer="Carbex <dev@carbex.eu>"
LABEL description="Carbex - Carbon Footprint Calculator for SMEs"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpq \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    oniguruma \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        intl \
        opcache \
        pcntl \
        mbstring \
        bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-carbex.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application from builder stages
COPY --from=composer-builder /app/vendor ./vendor
COPY --from=node-builder /app/public/build ./public/build
COPY . .

# Remove development files
RUN rm -rf \
    docker \
    tests \
    .git \
    .github \
    node_modules \
    .env.example \
    phpunit.xml \
    phpstan.neon \
    pint.json \
    vite.config.js \
    package.json \
    package-lock.json

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create required directories
RUN mkdir -p \
    /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/run/nginx \
    /var/log/supervisor

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose port
EXPOSE 80

# Start supervisor (manages nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# ---------------------------------------------
# Stage 4: Development Image (optional)
# ---------------------------------------------
FROM php:8.4-fpm-alpine AS development

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpq \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    oniguruma \
    git \
    nodejs \
    npm \
    $PHPIZE_DEPS

# Install PHP extensions (same as production + xdebug)
RUN apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        intl \
        opcache \
        pcntl \
        mbstring \
        bcmath \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Set permissions for development
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
