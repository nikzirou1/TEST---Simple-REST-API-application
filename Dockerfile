# ── Stage: base PHP-FPM image ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS base

# System dependencies and PHP extensions required by Laravel + MySQL
RUN apk add --no-cache \
        bash \
        curl \
        git \
        unzip \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        mysql-client \
    && docker-php-ext-install \
        intl \
        pdo \
        pdo_mysql \
        zip \
        opcache \
        mbstring

# Install Composer 2 from its official image
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# ── Stage: development ────────────────────────────────────────────────────
FROM base AS dev

# Copy dependency manifests first (better Docker layer caching)
COPY composer.json composer.lock* ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy full application source
COPY . .

# Optimise the autoloader and run post-install scripts
RUN composer dump-autoload --optimize

# Ensure storage and cache directories are writable by the FPM user
RUN mkdir -p storage/framework/{cache/data,sessions,testing,views} \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]

# ── Stage: production ─────────────────────────────────────────────────────
FROM base AS prod

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/{cache/data,sessions,testing,views} \
                storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 9000
CMD ["php-fpm"]
