# Changelog

Tous les changements notables de Qualyra sont documentés ici.

Format basé sur [Keep a Changelog](https://keepachangelog.com/fr/),
et [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### Changed

- Passage en open source sous licence AGPL-3.0 (fichier `LICENSE`, README, `composer.json`)
- Calendrier AI Act aligné sur le Digital Omnibus (en vigueur le 27/07/2026) : haut risque Annexe III au 2027-12-02, Annexe I au 2028-08-02 ; textes du rapport et landing mis à jour
- Chemin Chrome du rendu PDF configurable via `CHROME_PATH`

### Added

- Avertissement « pas un conseil juridique » et captures d'écran dans le README
- Workflow CI `tests.yml` (Pint + Pest, PDF compris)
- Test qui verrouille les dates `applicable_from` des règles

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
