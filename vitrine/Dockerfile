FROM php:8.4-cli-bookworm
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev sqlite3 libsqlite3-dev libicu-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite zip intl \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
    && mkdir -p storage/framework/{cache/data,sessions,views} storage/logs database \
    && chmod -R 775 storage bootstrap/cache
EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
