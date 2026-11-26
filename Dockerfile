FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl mbstring opcache pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY --chown=www-data:www-data composer.json composer.lock ./
COPY --chown=www-data:www-data . .
COPY --from=assets /app/public/build /opt/rodagest-build
COPY docker/entrypoint.sh /usr/local/bin/rodagest-entrypoint

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache storage/app/public \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache \
    && chmod 0755 /usr/local/bin/rodagest-entrypoint \
    && php artisan storage:link

USER www-data
EXPOSE 9000
ENTRYPOINT ["rodagest-entrypoint"]
CMD ["php-fpm", "-F"]
