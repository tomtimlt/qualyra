# MAP.md — Carte exhaustive du projet Qualyra

> Document de référence pour tous les dossiers qui n'ont pas d'AGENTS.md scoped.
> Une IA doit pouvoir y trouver toute l'info nécessaire sur n'importe quelle zone.

---

## Section 1 — Vue d'ensemble

```
[Arrivée] → public/index.php
    ↓
bootstrap/app.php → routes/web.php
    ↓
Controllers (app/Http/Controllers/)
    ├── Validation via Form Requests
    ├── Logique métier via Services (app/Services/)
    └── Réponse → View Blade (resources/views/)
    ↓
Services (app/Services/)
    ├── AiActClassifier → lit config/ai_act_rules.php
    ├── ReportContentBuilder → lit config/report_templates.php
    └── ReportSnapshotBuilder → snapshot Assessment + Responses
    ↓
Models (app/Models/) → SQLite (database/database.sqlite)
    ↓
PDF (optionnel) → Browsershot → resources/views/reports/pdf.blade.php
```

---

## Section 2 — Tableau exhaustif des dossiers

| Dossier | Rôle | Hot spot ? | Conventions clés | Tests |
|---------|------|-----------|------------------|-------|
| `app/Http/Controllers/` | Controllers mince (10 fichiers) | ❌ | Form Request obligatoire, routes nommées, `__invoke` pour les actions simples | Tests Feature du controller |
| `app/Http/Requests/` | Form Requests validation | ❌ | 1 classe par action, règles dans `rules()`, messages FR dans `messages()` | Testé indirectement via Feature tests |
| `app/Models/` | Eloquent (User, AiUsage, Assessment, Org, Report, Response) | ❌ | `$fillable` strict, FK jamais en fillable, relations explicites | factories unit + Feature |
| `app/Policies/` | Autorisation (AiUsagePolicy) | ⚠️ Isolation tenant | Vérifier `$user->organization_id === $model->organization_id` | `tests/Feature/AiUsage*` |
| `app/Services/` | Logique métier | ✅ AiActClassifier + Report* | Voir `app/Services/AGENTS.md` | AiActClassifierMatriceTest, ReportTest |
| `app/View/Components/` | Blade components (4) | ❌ | Conventions Laravel, props typées | Feature tests |
| `config/` | Configuration | ✅ 3 fichiers réglementaires | Voir `config/AGENTS.md` | Indirect (AiActClassifier) |
| `database/migrations/` | Schéma DB (9 migrations) | ❌ | Ordre chronologique, FK explicites, `constrained()->cascadeOnDelete()` | RefreshDatabase |
| `database/seeders/` | Seeds (DatabaseSeeder, DemoSeeder) | ⚠️ DemoSeeder | **DOIT** refléter `config/ai_act_rules.php` + `config/questionnaire.php` | RefreshDatabase + seed |
| `database/factories/` | Factories Pest (5) | ❌ | Conventions Laravel, états (`state()`) pour les cas spécifiques | Utilisées par Feature tests |
| `resources/views/` | Blade templates | ❌ (sauf reports/) | Pas de Livewire, Tailwind utilities, Alpine `x-data` | Feature tests E2E |
| `resources/views/reports/` | Vues rapport + PDF | ✅ pdf.blade.php | Voir `resources/views/reports/AGENTS.md` | ReportTest |
| `resources/css/` | Styles (app.css) | ❌ | Tailwind directives + custom CSS minimal | `npm run build` |
| `resources/js/` | JS (app.js, brain.js, bootstrap.js, custom-scrollbar.js) | ❌ | Alpine.js, Vite build, pas de framework JS lourd | `npm run build` |
| `routes/` | Routing (web.php, auth.php, console.php) | ❌ | Routes nommées, middleware `auth`, controller `__invoke` pour actions simples | Feature tests |
| `tests/Feature/` | Tests fonctionnels (12 fichiers) | ❌ | Pest 4, helper `userWithOrgAndUsage()`, `usageWithAnswers()`, RefreshDatabase | Auto |
| `tests/Unit/` | Tests unitaires (1 fichier) | ❌ | Pest 4, pas de DB | Auto |
| `docker/` | Build Docker + entrypoint | ❌ | Self-contained, Chromium pour Browsershot | Manuel |
| `public/` | Assets statiques + build Vite | ❌ | `public/build/` = sortie Vite (gitignoré) | — |
| `scripts/` | Scripts d'automatisation | ❌ | Voir `scripts/README.md` | — |
| `docs/` | Documentation projet | ❌ | FR sauf mention contraire | — |
| `storage/` | Logs, cache, PDF générés | ❌ | Géré par Laravel | — |

---

## Section 3 — Hot spots (table consolidée)

| Fichier | Pourquoi sensible | Test |
|---------|-------------------|------|
| `app/Services/AiActClassifier.php` | Cœur classification — 22 règles | `AiActClassifierMatriceTest` |
| `config/ai_act_rules.php` | Règles réglementaires — synchro DemoSeeder | `./scripts/check-sync.sh` |
| `config/questionnaire.php` | Questionnaire dynamique — synchro DemoSeeder + Classifier | `./scripts/check-sync.sh` |
| `config/report_templates.php` | Templates rapport — synchro PDF | `ReportTest` |
| `database/seeders/DemoSeeder.php` | Démo client — miroir config | `./scripts/check-sync.sh` |
| `app/Services/ReportContentBuilder.php` | Assemblage rapport | `ReportTest` |
| `app/Services/ReportSnapshotBuilder.php` | Snapshot légal figé | Risque légal |
| `app/Policies/AiUsagePolicy.php` | Isolation tenant — 4 vecteurs IDOR | `--filter AiUsage` |
| `resources/views/reports/pdf.blade.php` | PDF standalone Chrome | `ReportTest` |

---

## Section 4 — Conventions par couche

### Controllers (`app/Http/Controllers/`)
- **Toujours** utiliser un Form Request pour la validation
- Pas de `$request->validate()` inline
- Retourner `redirect()->route('nom_de_route')` après succès
- Les actions simples (1-3 lignes) peuvent utiliser `__invoke`

### Models (`app/Models/`)
- `$fillable` : listes explicites, jamais les FK (`organization_id`, `user_id`)
- `$casts` : pour les enums, dates, tableaux JSON
- Relations : `belongsTo`, `hasMany`, toujours avec `(Foreign key, Owner key)`

### Views (`resources/views/`)
- Blade seulement — **pas de Livewire, Inertia, Vue, React**
- Tailwind CSS utility classes
- Alpine.js pour l'interactivité : `x-data`, `x-show`, `@click`
- Composants Blade dans `resources/views/components/` ou `app/View/Components/`

### Routes (`routes/web.php`)
- Routes nommées : `route('ressource.action')`
- Middleware `auth` sur toutes les routes protégées
- Groupes par préfixe : `Route::prefix('admin')->name('admin.')->…`

### Tests (`tests/`)
- Pest 4, pas PHPUnit direct
- Helper `userWithOrgAndUsage()` dans `tests/Pest.php`
- RefreshDatabase pour les tests qui touchent la DB
- Un fichier par entité (AiUsageTest, AssessmentTest, etc.)

---

## Section 5 — « Où aller pour quoi ? »

| Je veux… | Fichiers à modifier | Tests à lancer |
|----------|-------------------|----------------|
| Ajouter une règle AI Act | `config/ai_act_rules.php` + `DemoSeeder` + `AiActClassifierMatriceTest` | `--filter AiActClassifier` |
| Ajouter une question questionnaire | `config/questionnaire.php` + `DemoSeeder` + tests questionnaire | `--filter Questionnaire` |
| Changer le texte d'un bouton | Vue Blade concernée | Feature tests de la page |
| Ajouter une route | `routes/web.php` + Controller + vue | Feature tests de la route |
| Modifier le PDF | `resources/views/reports/pdf.blade.php` + `ReportContentBuilder` | `--filter Report` |
| Ajouter une migration | `database/migrations/` + Model + Factory | `php artisan migrate:fresh --seed` |
| Modifier policy tenant | `app/Policies/AiUsagePolicy.php` | `--filter AiUsage` (4 vecteurs IDOR) |
| Ajouter un champ à un formulaire | Controller + Form Request + Vue + Migration | Tests Feature du formulaire |
| Modifier le layout global | `resources/views/layouts/` + CSS | `npm run build` |
| Configurer Stripe | `config/services.php` + Controller + Vue | Manuel (sandbox Stripe) |
| Modifier le build Vite | `vite.config.js` + `package.json` | `npm run build` |
| Ajouter un script JS | `resources/js/` + `app.js` | `npm run build` |
| Ajouter un style CSS | `resources/css/app.css` | `npm run build` |
| Modifier les tokens de design | `public/qualyra/css/qualyra.css` | Manuel |

---

*Document maintenu manuellement — dernière mise à jour : 2026-05-13.*
