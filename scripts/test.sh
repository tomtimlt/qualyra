#!/usr/bin/env bash
set -euo pipefail
# scripts/test.sh — Wrapper Pest
# Usage: ./scripts/test.sh [--filter X] [--hot-spots]

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

if [ "${1:-}" = "--hot-spots" ]; then
    echo "→ Tests des hot spots uniquement…"
    php artisan test --filter "AiActClassifier|Report|AiUsage"
elif [ $# -gt 0 ]; then
    echo "→ php artisan test $@"
    php artisan test "$@"
else
    echo "→ Lancement de tous les tests…"
    php artisan test
fi
