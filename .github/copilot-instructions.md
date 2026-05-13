# GitHub Copilot Instructions — Qualyra

> Read `/AGENTS.md` first for the complete project context.

## Copilot-specific rules

- **Minimal impact**: only edit files that are strictly necessary for the task
- **Hot spots**: see §4 in AGENTS.md — do NOT modify these files without following §5 protocol
- **No spontaneous refactoring**: fix what's asked, nothing more
- **No exotic packages**: Laravel conventions only, prefer existing patterns
- **PHP**: `declare(strict_types=1)`, Form Requests, named routes
- **Frontend**: Blade + Tailwind + Alpine.js only — no React/Vue/Svelte
- **Tests**: Pest 4 — run `php artisan test` after every change

## References

- `docs/MAP.md` — exhaustive project map
- `docs/AI_GUIDE.md` — AI guide with concrete examples (French)
