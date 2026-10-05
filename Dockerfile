# syntax=docker/dockerfile:1

# ── Étape 1 : build des assets front (Webpack Encore) ──
FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY webpack.config.js ./
COPY assets ./assets
RUN npm run build

# ── Étape 2 : image PHP de production (FrankenPHP) ──
FROM dunglas/frankenphp:1-php8.4 AS app

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
    && docker-php-ext-install intl pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod \
    SERVER_NAME=:80 \
    COMPOSER_ALLOW_SUPERUSER=1

COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Dépendances PHP d'abord (couche de cache réutilisable tant que
# composer.json/lock ne changent pas), puis tout le code applicatif.
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
COPY --from=frontend_builder /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && mkdir -p var/cache var/log public/uploads \
    && chown -R www-data:www-data var public/uploads || true

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
