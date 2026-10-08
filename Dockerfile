FROM node:22-alpine AS assets
WORKDIR /build
ENV NPM_CONFIG_CACHE=/tmp/npm-cache
COPY package*.json .npmrc vite.config.js ./
COPY frontend ./frontend
COPY public/static ./public/static
RUN npm ci --ignore-scripts --no-fund && npm run build

FROM php:8.4-fpm-bookworm AS app
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libonig-dev libzip-dev libpng-dev libjpeg62-turbo-dev libwebp-dev libavif-dev ffmpeg unzip \
 && docker-php-ext-configure gd --with-jpeg --with-webp --with-avif \
 && docker-php-ext-install -j2 intl mbstring pdo_mysql zip gd bcmath opcache exif \
 && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_HOME=/tmp/composer
WORKDIR /var/www/html
COPY . .
COPY --from=assets /build/public/static/build ./public/static/build
COPY deploy/php-production.ini /usr/local/etc/php/conf.d/legion.ini
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/media \
 && composer install --no-dev --prefer-dist --no-interaction --no-progress --classmap-authoritative \
 && chown -R www-data:www-data storage bootstrap/cache public/media
CMD ["php-fpm"]

FROM nginx:1.28-alpine AS web
COPY --from=app /var/www/html/public /var/www/html/public
COPY deploy/nginx.conf /etc/nginx/conf.d/default.conf
