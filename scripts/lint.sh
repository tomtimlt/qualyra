#!/usr/bin/env bash
set -euo pipefail
# scripts/lint.sh — Laravel Pint
# Usage: ./scripts/lint.sh [--test|--fix]

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

if [ ! -f vendor/bin/pint ]; then
    echo "❌ Pint pas installé — lance 'composer install' d'abord"
    exit 1
fi

case "${1:-}" in
    --test)
        echo "→ Vérification Pint (sans modification)…"
        ./vendor/bin/pint --test
        ;;
    --fix|-f)
        echo "→ Correction Pint…"
        ./vendor/bin/pint
        ;;
    *)
        echo "→ Correction Pint…"
        ./vendor/bin/pint
        ;;
esac
