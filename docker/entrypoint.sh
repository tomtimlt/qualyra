#!/usr/bin/env bash
set -e

cd /app

# ── Environment ──────────────────────────────────────────
if [ ! -f .env ]; then
    echo "→ Creating .env from .env.example…"
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "→ Generating application key…"
    php artisan key:generate --force
fi

# ── PHP dependencies (dev fallback: when vendor/ is a Docker volume) ──
if [ ! -f vendor/autoload.php ]; then
    echo "→ Installing Composer dependencies…"
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# ── Node dependencies + build (dev fallback: when node_modules is a volume) ──
if [ ! -f node_modules/.installed ]; then
    echo "→ Installing Node dependencies…"
    rm -f package-lock.json
    npm install --no-audit --no-fund
    touch node_modules/.installed
fi

if [ ! -d public/build ]; then
    echo "→ Building frontend assets…"
    npm run build
fi

# ── Database ──────────────────────────────────────────────
mkdir -p database

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    DB_FRESH=true
else
    DB_FRESH=false
fi

echo "→ Running migrations…"
php artisan migrate --force --no-interaction

if [ "$DB_FRESH" = true ]; then
    echo "→ Seeding database…"
    php artisan db:seed --force --no-interaction
fi

# ── Storage link ──────────────────────────────────────────
php artisan storage:link 2>/dev/null || true

echo "→ Ready. http://localhost:8000"
exec "$@"
