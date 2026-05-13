#!/usr/bin/env bash
set -euo pipefail
# scripts/README.md — Documentation des scripts

cat << 'README'
# Scripts Qualyra

Tous les scripts sont exécutables (`chmod +x`) et doivent être lancés depuis la
racine du projet.

## Index

| Script | Usage | Description |
|--------|-------|-------------|
| `setup.sh` | `./scripts/setup.sh` | Premier setup complet du projet (dépendances, .env, DB, build) |
| `test.sh` | `./scripts/test.sh [--filter X\|--hot-spots]` | Lance les tests Pest |
| `lint.sh` | `./scripts/lint.sh [--test\|--fix]` | Laravel Pint (vérification ou correction) |
| `check-sync.sh` | `./scripts/check-sync.sh` | Vérifie la cohérence config/ ↔ database/seeders/ |
| `ai-context.sh` | `./scripts/ai-context.sh [services\|config\|views\|tests\|all]` | Génère un contexte IA pour une zone |
| `precommit.sh` | `./scripts/precommit.sh` | Exécute lint + sync + tests + build |
| `install-hooks.sh` | `./scripts/install-hooks.sh` | Installe le hook git pre-commit |

## workflow IA recommandé

```bash
# 1. Setup initial (une fois)
./scripts/setup.sh

# 2. Avant chaque modification
./scripts/ai-context.sh config   # contexte pour la zone

# 3. Après modification
./scripts/precommit.sh           # tout vérifier

# 4. Commit
git add -A && git commit -m "feat: ma modification"
```

## Notes

- `check-sync.sh` est CRITIQUE pour les modifications de config/ — il garantit
  que le DemoSeeder reste synchronisé avec le moteur de règles.
- `precommit.sh` est la porte d'entrée unique : si elle passe, tout est bon.
- Les scripts sont conçus pour être lancés par des IA aussi bien que par des humains.
README
