#!/usr/bin/env bash
set -euo pipefail
# scripts/setup.sh — Premier setup du projet
# Usage: ./scripts/setup.sh

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

echo "→ Vérification des prérequis…"

check_command() {
    if ! command -v "$1" &>/dev/null; then
        echo "❌ $1 introuvable. Installe-le d'abord."
        exit 1
    fi
    echo "  ✓ $1 trouvé"
}

check_command php
check_command composer
check_command npm

# Version PHP
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "  PHP $PHP_VERSION"
if [[ "$PHP_VERSION" =~ ^(8\.[3-9]|9\.[0-9]) ]]; then
    echo "  ✓ Version PHP OK"
else
    echo "  ⚠ PHP 8.3+ recommandé (actuel: $PHP_VERSION)"
fi

echo "→ Installation des dépendances…"

if [ ! -d vendor ]; then
    echo "  Composer install…"
    composer install --no-interaction
else
    echo "  ✓ vendor/ existe déjà"
fi

if [ ! -d node_modules ]; then
    echo "  npm install…"
    npm install --no-audit --no-fund
    touch node_modules/.installed
else
    echo "  ✓ node_modules/ existe déjà"
fi

echo "→ Configuration de l'environnement…"

if [ ! -f .env ]; then
    echo "  Création de .env depuis .env.example…"
    cp .env.example .env
    php artisan key:generate
else
    echo "  ✓ .env existe déjà"
fi

# Vérifier APP_KEY
if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "  Génération de la clé…"
    php artisan key:generate
fi

echo "→ Base de données…"

if [ ! -f database/database.sqlite ]; then
    echo "  Création de la base SQLite…"
    touch database/database.sqlite
    echo "  Migration + seed…"
    php artisan migrate --seed --force
else
    echo "  ✓ database.sqlite existe déjà"
fi

echo "→ Assets…"

if [ ! -d public/build ]; then
    npm run build
    echo "  ✓ Build Vite terminé"
else
    echo "  ✓ public/build/ existe déjà"
fi

echo ""
echo "✓ Setup terminé."
echo ""
echo "Pour lancer l'application :"
echo "  php artisan serve"
echo "  # ou tout en un :"
echo "  composer run dev"
