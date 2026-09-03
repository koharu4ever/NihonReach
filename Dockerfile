# syntax=docker/dockerfile:1
FROM node:24.20.0-bookworm-slim AS node
FROM composer:2.10.2 AS composer

FROM php:8.5.9-apache-bookworm AS php-base
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libonig5 \
    && docker-php-ext-install -j"$(nproc)" bcmath pdo_mysql mbstring \
    && apt-get purge -y --auto-remove libonig-dev \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /var/www/html

FROM php-base AS build
RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
ENV COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=production APP_DEBUG=false \
    APP_NAME=NihonReach VITE_APP_NAME=NihonReach
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress
COPY app ./app
COPY bootstrap/app.php bootstrap/providers.php ./bootstrap/
COPY config ./config
COPY database/migrations ./database/migrations
COPY database/seeders ./database/seeders
COPY lang ./lang
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY artisan vite.config.ts tsconfig.json ./
RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && composer dump-autoload --no-dev --optimize \
    && composer check-platform-reqs --no-dev \
    && npm ci --no-audit --no-fund \
    && npm run build

FROM php-base AS production
COPY docker/production/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/production/php.ini /usr/local/etc/php/conf.d/nihonreach.ini
RUN printf 'Listen 8080\n' > /etc/apache2/ports.conf \
    && printf 'ServerName localhost\nServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/nihonreach.conf \
    && a2enconf nihonreach \
    && mkdir -p /var/run/apache2 /var/lock/apache2 \
    && chown -R www-data:www-data /var/run/apache2 /var/lock/apache2
COPY --from=build /var/www/html/app ./app
COPY --from=build /var/www/html/bootstrap ./bootstrap
COPY --from=build /var/www/html/config ./config
COPY --from=build /var/www/html/database ./database
COPY --from=build /var/www/html/lang ./lang
COPY --from=build /var/www/html/public ./public
COPY --from=build /var/www/html/resources/views ./resources/views
COPY --from=build /var/www/html/routes ./routes
COPY --from=build /var/www/html/vendor ./vendor
COPY --from=build /var/www/html/artisan /var/www/html/composer.json /var/www/html/composer.lock ./
COPY --chmod=755 docker/production/start.sh /usr/local/bin/nihonreach-start
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr
USER www-data
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1:8080/up > /dev/null || exit 1
CMD ["nihonreach-start"]
