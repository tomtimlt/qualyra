#!/usr/bin/env bash
set -e
cd /app
[ -f .env ] || cp .env.example .env
grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force
mkdir -p database; [ -f database/database.sqlite ] || touch database/database.sqlite
php artisan migrate --force --no-interaction
php artisan config:clear
php artisan config:cache
exec "$@"
