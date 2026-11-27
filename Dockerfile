# syntax=docker/dockerfile:1.7

FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN mkdir -p storage/framework/views && npm run build

FROM php:8.5-fpm-alpine AS app
RUN docker-php-ext-install -j"$(nproc)" opcache pcntl pdo_mysql
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-lumiere.ini
COPY --chmod=755 docker/php/entrypoint.sh /usr/local/bin/lumiere-entrypoint
RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data bootstrap/cache storage
ENTRYPOINT ["lumiere-entrypoint"]
CMD ["php-fpm", "-F"]

FROM nginx:1.28-alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public /var/www/html/public
COPY --from=assets /app/public/build /var/www/html/public/build
EXPOSE 80
