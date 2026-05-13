# CLAUDE.md — Conventions Laravel Qualyra

> Ce fichier contient les conventions Laravel détaillées et les hot spots.
> Pour le guide universel IA, voir `/AGENTS.md` (branche `dev` uniquement).

---

## Stack mise à jour

- **PHP 8.3+** (testé 8.4)
- **Laravel 13**
- Blade + Tailwind CSS 3 + Alpine.js 3 + Vite 8
- SQLite / Pest 4 / Laravel Pint
- Browsershot (Chrome headless) / Stripe

## Règles de code

- PHP 8.3+, Laravel 13, conventions Laravel par défaut
- Blade pour les vues (pas de SPA)
- Tailwind CSS via Vite
- Type hints stricts (`declare(strict_types=1)` en haut de chaque fichier PHP)
- Form Requests pour la validation, pas de validation inline dans les controllers
- Eloquent pour les modèles, migrations propres avec foreign keys
- Pas de query builder brut sauf si vraiment nécessaire
- Tests : Pest, pas PHPUnit direct
- Quand tu modifies `config/questionnaire.php` ou `config/ai_act_rules.php`, vérifie systématiquement que `database/seeders/DemoSeeder.php` est toujours cohérent. Le DemoSeeder est la démo client, il doit refléter l'état exact du moteur.

## Conventions du projet

- Tous les commentaires de code en français
- Variables et méthodes en anglais (convention Laravel)
- Models au singulier (User, AiUsage, Assessment)
- Tables au pluriel (users, ai_usages, assessments)
- Routes nommées (route('usages.index'))

## Hot spots

| Fichier | Pourquoi sensible |
|---------|-------------------|
| `app/Services/AiActClassifier.php` | Cœur du moteur de classification — modif = re-tester `AiActClassifierMatriceTest` |
| `config/ai_act_rules.php` | 22 règles réglementaires — toute modif doit synchroniser `DemoSeeder` |
| `config/questionnaire.php` | Questionnaire dynamique — toute modif doit synchroniser `DemoSeeder` + `AiActClassifier` |
| `config/report_templates.php` | Templates rapport PDF — modif = vérifier `resources/views/reports/pdf.blade.php` |
| `database/seeders/DemoSeeder.php` | Démo client — DOIT refléter l'état du moteur |
| `app/Services/ReportContentBuilder.php` | Assemblage rapport — modif = tester génération PDF |
| `app/Services/ReportSnapshotBuilder.php` | Snapshot légal figé — modif = risque légal |
| `app/Policies/AiUsagePolicy.php` | Isolation tenant — modif = re-tester les 4 vecteurs IDOR |
| `resources/views/reports/pdf.blade.php` | Template PDF standalone (Chrome) — pas de variables CSS modernes |

## AGENTS.md scoped

- `config/AGENTS.md` — règles de modification des fichiers config
- `app/Services/AGENTS.md` — services métier (AiActClassifier, Report*)
- `resources/views/reports/AGENTS.md` — contraintes du PDF headless

## Référence

- `/AGENTS.md` — guide universel pour toutes les IA
- `docs/MAP.md` — carte exhaustive du projet (compense les AGENTS.md non créés)
- `docs/ARCHITECTURE.md` — architecture détaillée
- `docs/AI_GUIDE.md` — guide IA avec exemples concrets
- `docs/DB_SCHEMA.md` — schéma de base de données

## Ce qu'il faut éviter

- Pas de Livewire ni Inertia, juste du Blade classique
- Pas de packages exotiques sans validation préalable
- Pas de refactoring spontané du code existant
- Pas de design custom : utiliser des composants Tailwind UI / TallStackUI basiques

## Quand tu hésites

- Demander avant d'agir si la décision a un impact structurel
- Si une feature ressemble à du scope creep, le signaler
- Toujours expliquer en français les choix non triviaux
