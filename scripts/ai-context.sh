#!/usr/bin/env bash
set -euo pipefail
# scripts/ai-context.sh — Génère un contexte minimal pour l'IA selon la zone
# Usage: ./scripts/ai-context.sh [services|config|views|tests|all]

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

show_file() {
    local path="$1"
    if [ -f "$path" ]; then
        echo "=== $path ==="
        cat "$path"
        echo ""
    fi
}

show_dir() {
    local dir="$1"
    local label="$2"
    echo "=== $label ==="
    ls -la "$dir" 2>/dev/null | grep -v "^total\|^\.\|^\.\.$" | awk '{print $NF "  \t" $5}'
    echo ""
}

case "${1:-all}" in
    services)
        show_file "app/Services/AGENTS.md"
        show_dir "app/Services" "Fichiers dans app/Services/"
        echo "Tests associés: php artisan test --filter AiActClassifier"
        echo "              php artisan test --filter Report"
        ;;
    config)
        show_file "config/AGENTS.md"
        show_dir "config" "Fichiers dans config/"
        echo "Tests associés: php artisan test --filter AiActClassifier"
        echo "              php artisan test --filter Questionnaire"
        ;;
    views)
        show_file "resources/views/reports/AGENTS.md"
        echo "Note: seul reports/ a un AGENTS.md scoped"
        show_dir "resources/views" "Fichiers dans resources/views/"
        echo "Tests associés: php artisan test"
        ;;
    tests)
        show_file "tests/AGENTS.md" 2>/dev/null || echo "(pas de AGENTS.md scoped — voir docs/MAP.md)"
        show_dir "tests" "Structure des tests"
        echo "Commande: php artisan test"
        ;;
    all)
        echo "# Contexte IA — Qualyra"
        echo ""
        echo "## AGENTS.md racine"
        cat "AGENTS.md" 2>/dev/null | head -100
        echo ""
        echo "## AGENTS.md scoped"
        for f in config/AGENTS.md app/Services/AGENTS.md resources/views/reports/AGENTS.md; do
            show_file "$f"
        done
        echo "## Carte complète: docs/MAP.md"
        echo "## Architecture: docs/ARCHITECTURE.md"
        echo "## Guide IA: docs/AI_GUIDE.md"
        ;;
    *)
        echo "Usage: $0 [services|config|views|tests|all]"
        exit 1
        ;;
esac
