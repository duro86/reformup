
# --- Base PHP ---
FROM php:8.2-fpm AS phpbase

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath mbstring exif pcntl gd \
    && docker-php-source delete

WORKDIR /var/www/html

# --- Composer (dependencias PHP) ---
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts
COPY . .
# evita caches rotos de artisan en la imagen
RUN rm -f bootstrap/cache/*.php
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts --optimize-autoloader

# --- Frontend (Vite) ---
FROM node:22 AS assets
WORKDIR /app
COPY package*.json ./
RUN (npm ci) || (npm install)
COPY resources/ resources/
COPY vite.config.* ./
RUN npm run build

# --- Imagen final de la app (PHP-FPM) ---
FROM phpbase AS app
WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build
# permisos runtime
RUN chown -R www-data:www-data storage bootstrap/cache
# Garantiza compatibilidad Laravel Sanctum con Spatie
CMD ["php-fpm"]
