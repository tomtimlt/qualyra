FROM php:8.4-cli-bookworm

ENV DEBIAN_FRONTEND=noninteractive

# System dependencies + Node.js + Chromium (for Browsershot PDF)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        ca-certificates \
        curl \
        gnupg \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        sqlite3 \
        libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs chromium \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite zip gd intl bcmath exif

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy source code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install Node dependencies and build frontend
RUN npm install --no-audit --no-fund \
    && touch node_modules/.installed \
    && npm run build

# Ensure writable directories & clean storage symlink (entrypoint recreates it)
RUN mkdir -p storage/logs storage/framework/cache/data \
    storage/framework/sessions storage/framework/views \
    storage/app/public database \
    && chmod -R 775 storage bootstrap/cache \
    && rm -f public/storage

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
