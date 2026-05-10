#!/usr/bin/env bash
set -e

cd /app

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    echo "→ Installing PHP dependencies (first run, this can take a few minutes)…"
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "→ Generating application key…"
    php artisan key:generate --force
fi

mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

echo "→ Running migrations…"
php artisan migrate --force --no-interaction

php artisan storage:link 2>/dev/null || true

if [ ! -f node_modules/.installed ]; then
    echo "→ Installing Node dependencies (first run)…"
    # The host's package-lock.json may be pinned to a different OS (Windows native
    # bindings, etc.) — see npm/cli#4828. Regenerate inside the container.
    rm -f package-lock.json
    npm install --no-audit --no-fund
    touch node_modules/.installed
fi

if [ ! -d public/build ]; then
    echo "→ Building frontend assets…"
    npm run build
fi

echo "→ Ready. http://localhost:8000  ·  /design-system for the design system."
exec "$@"
