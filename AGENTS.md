# AGENTS.md — Guide universel pour assistants IA (Qualyra)

> ⚠️ **Fichier réservé à la branche `dev`.** Ne pas merger vers `main`.

> Fichier lu par : Claude Code, Cursor, Windsurf, Aider, Codex, Copilot, etc.
> Si tu es une IA : lis ce fichier **EN ENTIER** avant toute modification.

---

## 1. Identité du projet

- **Nom** : Qualyra — audit conformité AI Act + RGPD pour PME françaises
- **Stack** : Laravel 13 + PHP 8.3+ (testé 8.4) + Blade + Tailwind 3 + Alpine.js 3 + Vite 8
- **Base de données** : SQLite
- **Tests** : Pest 4 (102 tests, 290 assertions)
- **Lint** : Laravel Pint
- **PDF** : spatie/browsershot (Chrome headless)
- **Paiement** : Stripe Checkout
- **Solo dev**, vente one-shot ~1500€
- **Branches** : `main` (prod) | `dev` (intégration)

## 2. Règle d'or : impact minimal

Avant toute modification :
1. Lis l'`AGENTS.md` du dossier que tu vas toucher (si existe)
2. Identifie si tu touches un **HOT SPOT** (voir §4)
3. Si HOT SPOT → applique le protocole §5
4. Sinon → modifie **uniquement les fichiers nécessaires**, JAMAIS plus

## 3. Carte du projet (où aller pour quoi)

| Intention | Dossier | Fichier clé | AGENTS.md scoped |
|-----------|---------|-------------|------------------|
| Modifier le moteur de classification | `app/Services/` | `AiActClassifier.php` | ✅ `app/Services/AGENTS.md` |
| Modifier une règle AI Act | `config/` | `ai_act_rules.php` | ✅ `config/AGENTS.md` |
| Modifier le questionnaire | `config/` | `questionnaire.php` | ✅ `config/AGENTS.md` |
| Changer le template rapport | `config/` | `report_templates.php` | ✅ `config/AGENTS.md` |
| Modifier le PDF | `resources/views/reports/` | `pdf.blade.php` | ✅ `resources/views/reports/AGENTS.md` |
| Ajouter une route | `routes/web.php` + Controller | — | ❌ voir `docs/MAP.md` |
| Modifier une vue | `resources/views/` | — | ❌ voir `docs/MAP.md` |
| Ajouter un test | `tests/` | — | ❌ voir `docs/MAP.md` |

## 4. Hot spots (impact large — toujours vérifier)

| Fichier | Pourquoi sensible | Test associé |
|---------|-------------------|-------------|
| `app/Services/AiActClassifier.php` | Cœur du moteur de classification | `AiActClassifierMatriceTest` |
| `config/ai_act_rules.php` | 22 règles réglementaires | Toute modif **doit** synchroniser `DemoSeeder` |
| `config/questionnaire.php` | Questionnaire dynamique | Toute modif **doit** synchroniser `DemoSeeder` + `AiActClassifier` |
| `config/report_templates.php` | Templates rapport PDF | Vérifier `resources/views/reports/pdf.blade.php` |
| `database/seeders/DemoSeeder.php` | Démo client — DOIT refléter l'état du moteur | Voir `tests/Feature/Demo*` |
| `app/Services/ReportContentBuilder.php` | Assemblage rapport | `ReportTest` |
| `app/Services/ReportSnapshotBuilder.php` | Snapshot légal figé | Risque légal si modifié sans précaution |
| `app/Policies/AiUsagePolicy.php` | Isolation tenant | Re-tester les 4 vecteurs IDOR |
| `resources/views/reports/pdf.blade.php` | Template PDF standalone (Chrome) | Pas de variables CSS modernes |

## 5. Protocoles

### 5.1 Modifier une règle AI Act

1. Modifier `config/ai_act_rules.php` (ajouter/modifier la règle)
2. Ajouter un cas correspondant dans `database/seeders/DemoSeeder.php`
3. Ajouter un test dans `tests/Feature/AiActClassifierMatriceTest.php`
4. Lancer : `./scripts/check-sync.sh` + `php artisan test --filter AiActClassifier`

### 5.2 Ajouter une question questionnaire

1. Modifier `config/questionnaire.php` (ajouter la question et ses dépendances)
2. Mettre à jour `database/seeders/DemoSeeder.php` si nécessaire
3. Vérifier que `AiActClassifier` utilise bien la nouvelle variable
4. Lancer : `./scripts/check-sync.sh` + `php artisan test --filter Questionnaire`

### 5.3 Changer le template PDF

1. Modifier `resources/views/reports/pdf.blade.php`
2. Si le contenu change, vérifier `app/Services/ReportContentBuilder.php`
3. Lancer : `php artisan test --filter Report`
4. **Attention** : le PDF est généré par Chrome headless — pas de `display: grid`, `gap`, `backdrop-filter`, `var()`, `aspect-ratio`. Utiliser table-based layout, polices system-only, images en base64.

### 5.4 Ajouter une route / vue

1. Ajouter la route dans `routes/web.php` (nommée, middleware `auth`)
2. Créer le Controller (mince, Form Request, pas de validation inline)
3. Créer la vue Blade (Tailwind utilities, Alpine si besoin)
4. Ajouter un test Feature
5. Lancer : `php artisan test`

## 6. Interdictions strictes

- ❌ Pas de Livewire, Inertia, packages exotiques
- ❌ Pas de refactoring spontané ("tant qu'on y est…")
- ❌ Pas de design custom (Tailwind UI / TallStackUI basiques OK)
- ❌ Pas de validation inline dans les controllers (toujours Form Request)
- ❌ Pas de mass assignment des FK (`organization_id` JAMAIS en `$fillable`)
- ❌ Pas de query builder brut sauf justification écrite
- ❌ Ne pas toucher : `.env`, `.env.example`, `composer.lock` sans demande
- ❌ Ne pas commit : `node_modules/`, `vendor/`, `public/build/`

## 7. Conventions

- **PHP** : `declare(strict_types=1)` en haut de chaque fichier
- **Commentaires** : en français
- **Variables/méthodes** : en anglais (convention Laravel)
- **Models** : singulier (`User`, `AiUsage`, `Assessment`)
- **Tables** : pluriel (`users`, `ai_usages`, `assessments`)
- **Routes** : nommées (`route('usages.index')`)
- **Controllers** : minces — la logique métier va dans `app/Services/`
- **Validation** : Form Requests, pas de `$request->validate()` dans les controllers
- **Tests** : Pest, pas PHPUnit direct
- **Frontend** : Alpine `x-data`, Tailwind utilities, pas de composant JS lourd

## 8. Avant chaque commit

Exécuter dans l'ordre :
```bash
./scripts/lint.sh --test    # Pint (vérification sans auto-fix)
./scripts/check-sync.sh     # Sync config ↔ seeder
./scripts/test.sh           # Pest (102+ tests)
npm run build               # Vite (assets)
```

Ou tout en un :
```bash
./scripts/precommit.sh
```

## 9. Tags spécifiques par outil

- **Claude Code** : voir aussi `CLAUDE.md`
- **Cursor** : voir aussi `.cursorrules`
- **Windsurf** : voir aussi `.windsurfrules`
- **GitHub Copilot** : voir aussi `.github/copilot-instructions.md`

## 10. Doute ?

Demande à l'humain avant d'agir si :
- La modif touche un **hot spot** (§4)
- Le scope ressemble à du **scope creep**
- Tu n'es pas sûr de la **convention** (§7)
- Tu veux ajouter un **package** Composer ou npm
- Tu penses qu'une **modification structurelle** est nécessaire
