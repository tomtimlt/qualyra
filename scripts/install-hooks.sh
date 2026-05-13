#!/usr/bin/env bash
set -euo pipefail
# scripts/install-hooks.sh — Installe le hook pre-commit git
# Usage: ./scripts/install-hooks.sh

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

HOOKS_DIR=".git/hooks"
HOOK_PATH="$HOOKS_DIR/pre-commit"
SCRIPT_PATH="scripts/precommit.sh"

if [ ! -d "$HOOKS_DIR" ]; then
    echo "❌ $HOOKS_DIR introuvable — es-tu dans la racine du repo ?"
    exit 1
fi

if [ -f "$HOOK_PATH" ]; then
    echo "⚠ Un hook pre-commit existe déjà : $HOOK_PATH"
    read -p "  Remplacer ? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "  Installation annulée."
        exit 0
    fi
fi

cat > "$HOOK_PATH" << 'HOOK'
#!/usr/bin/env bash
set -euo pipefail
# Git pre-commit hook — installé par scripts/install-hooks.sh
cd "$(git rev-parse --show-toplevel)"
exec ./scripts/precommit.sh
HOOK

chmod +x "$HOOK_PATH"
echo "✓ Hook pre-commit installé dans $HOOK_PATH"
echo "  Les vérifications s'exécuteront automatiquement avant chaque commit."
