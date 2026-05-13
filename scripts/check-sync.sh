#!/usr/bin/env bash
set -euo pipefail
# scripts/check-sync.sh — Vérifie la cohérence config ↔ seeder
# Usage: ./scripts/check-sync.sh

echo "→ Vérification synchronisation config / seeder…"

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

# Vérifier que PHP est disponible
if ! command -v php &>/dev/null; then
    echo "❌ PHP introuvable"
    exit 1
fi

# Utiliser un script PHP dédié pour l'analyse
php scripts/check-sync.php
