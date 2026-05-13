#!/usr/bin/env bash
set -euo pipefail
# scripts/precommit.sh — Exécute toutes les vérifications avant commit
# Usage: ./scripts/precommit.sh

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "$(dirname "$0")/..")"

echo "========================================="
echo "  PRE-COMMIT — Vérifications automatisées"
echo "========================================="

FAILED=0

run_step() {
    local step_name="$1"
    shift
    echo ""
    echo "→ [$step_name] $@"
    if "$@"; then
        echo "  ✓ $step_name OK"
    else
        echo "  ❌ $step_name ÉCHOUÉ"
        FAILED=1
    fi
}

run_step "Lint"       ./scripts/lint.sh --test
run_step "Sync"       ./scripts/check-sync.sh
run_step "Tests"      ./scripts/test.sh
run_step "Build"      npm run build

echo ""
echo "========================================="
if [ $FAILED -eq 0 ]; then
    echo "  ✓ TOUT OK — prêt à commiter"
else
    echo "  ❌ AU MOINS UNE VÉRIFICATION A ÉCHOUÉ"
    echo "     Corrige les erreurs avant de commiter."
fi
echo "========================================="
exit $FAILED
