# Changelog

Tous les changements notables de Qualyra sont documentés ici.

Format basé sur [Keep a Changelog](https://keepachangelog.com/fr/),
et [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### Added

- Infrastructure IA universelle (`AGENTS.md`, `.cursorrules`, `.windsurfrules`, etc.)
- Scripts d'automatisation (`scripts/check-sync.sh`, `precommit.sh`, etc.)
- Templates GitHub Issues + PR
- GitHub Actions CI (tests, lint, security, sync-check)
- CODEOWNERS, Dependabot
- Documentation projet complète (`docs/ARCHITECTURE.md`, `docs/AI_GUIDE.md`, `docs/MAP.md`)

---

## [2.0.0] — 2026-XX-XX

### Added

- Composant Alpine.js custom scrollbar
- Refonte UI dashboard + onboarding
- Personnalisation du brain canvas (couleurs, animation)
- Layout public + layout app avec scrollbar custom
- AGENTS.md racine + templates GitHub

### Changed

- Migration du design system Cervus → Qualyra
- Mise à jour Laravel 11 → 13
- Mise à jour PHP 8.3 → 8.4
- Refonte complète du moteur AI Act (22 règles)
- Optimisation Docker (self-contained)
- Amélioration des tests (102 tests, 290 assertions)

### Fixed

- Génération PDF via Browsershot (remplace DomPDF)
- Formulaires de création organisation
- Routes nommées et isolation tenant
