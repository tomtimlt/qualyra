# Qualyra — Contexte pour agents IA

> ⚠️ **Fichier réservé à la branche `dev`.** Ne pas merger vers `main`.

---

## Stack technique

| Composant | Version |
|---|---|
| PHP | 8.4 |
| Laravel | 13 |
| Alpine.js | 3.15 |
| Tailwind CSS | 3 |
| Vite | 8 |
| Base de données | SQLite (dev) |
| Tests | Pest 4 |
| PDF | Browsershot (Chrome headless) |
| Paiement | Stripe |

## Premiers pas

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed
php artisan serve
# ou tout en un :
composer run dev
```

## Architecture du projet

```
app/
├── Http/Controllers/    # CRUD usages, questionnaire, assessment, rapports, org
├── Models/               # AiUsage, Assessment, Organization, Report, Response, User
├── Policies/             # AiUsagePolicy (isolation tenant)
├── Services/
│   ├── AiActClassifier.php        # Moteur de classification AI Act (4 niveaux, 22 règles)
│   ├── ReportContentBuilder.php    # Contenu rédactionnel du rapport PDF
│   └── ReportSnapshotBuilder.php   # Snapshot figé (garantie légale)
└── View/Components/      # Blade components

config/
├── ai_act_rules.php          # Matrice de décision (22 règles)
├── questionnaire.php         # Questions dynamiques par type/domaine
└── report_templates.php      # Templates du rapport

resources/
├── js/
│   ├── app.js               # Entry point Vite (Alpine.start)
│   ├── brain.js             # Canvas animation brain (home page)
│   ├── bootstrap.js         # Axios bootstrap
│   └── custom-scrollbar.js  # Alpine component scrollbar
├── css/app.css              # Styles Tailwind + custom
└── views/                   # Blade templates
```

## Conventions de code

- **PHP** : `declare(strict_types=1)`, Form Requests pour validation, routes nommées
- **Frontend** : Alpine `x-data`, Tailwind utilities, pas de composant JS lourd
- **Tests** : Pest, pas PHPUnit direct (`php artisan test`)
- **Commentaires** : en français
- **Variables/méthodes** : en anglais (convention Laravel)
- **DemoSeeder** : à vérifier systématiquement après modification de `config/questionnaire.php` ou `config/ai_act_rules.php`

## Worktrees

```bash
# Projet principal
cd ~/Documents/Projet/Code/ai-assistant           # main (port 8000)

# Worktree dev
git worktree add ../qualyra-dev dev                # dev (port 8001)

# Worktree feature
git worktree add ../qualyra-ma-feature feature/ma-feature  # feature (port 8002+)
```

Chaque worktree a son propre `.env`, sa propre base SQLite, et son port dédié.

## Ce que les IA doivent faire

1. Lire `AGENTS.md` (ce fichier) pour le contexte projet
2. Consulter les [Issues GitHub](https://github.com/tomtimlt/ai-assistant/issues) ouvertes
3. Créer une branche depuis `dev` :
   ```bash
   git checkout dev && git checkout -b feature/ma-feature
   git worktree add ../qualyra-ma-feature feature/ma-feature
   ```
4. Travailler, tester, commit, push
5. Ouvrir une Pull Request vers `dev`

## Règles strictes

- Ne jamais modifier `.env`, `.env.example`, `composer.lock` sans demande explicite
- Ne jamais commit `node_modules/`, `vendor/`, `public/build/`
- Si une modification impacte `config/questionnaire.php` ou `config/ai_act_rules.php`, vérifier `DemoSeeder`
- Avant d'ajouter un package (Composer ou npm), demander
- Le design system Qualyra est dans `public/qualyra/` — ne pas le modifier

## Changelog des décisions IA

*Date — Décision — Auteur*
