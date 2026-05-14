# START_HERE.md — Point d'entrée IA pour Qualyra

> Tu es une IA qui vient d'arriver sur le repo Qualyra.
> Ce fichier est ton **point de départ unique**.
> Lis-le en entier (5 min) avant toute modification.

---

## §0 — Note sur l'état du repo

Ce repo est en **refonte active** vers une infrastructure IA-friendly.
Deux fichiers décrivent le projet à différents niveaux de granularité :

| Fichier | Rôle | Priorité de lecture |
|---------|------|---------------------|
| `AGENTS.md` | Guide universel complet (hot spots, protocoles, interdictions) | **À lire après START_HERE.md** |
| `docs/MAP.md` | Carte exhaustive de tous les dossiers | Lecture ciblée par zone |

> **Si tu es l'IA chargée d'exécuter le plan de refonte** : regarde le fichier
> `/Users/nethunt3r/.claude/plans/review-tout-le-github-wild-globe.md`
> et exécute-le phase par phase.

> **Si tu es une IA de feature/bug** : ce fichier + AGENTS.md suffisent.

---

## §1 — Identité du projet (10s)

- **Qualyra** : audit de conformité AI Act + RGPD pour PME françaises
- **Stack** : Laravel 13 / PHP 8.4 / Blade / Tailwind 3 / Alpine.js / Vite 8 / SQLite / Pest 4
- **PDF** : Browsershot (Chrome headless)
- **Paiement** : Stripe Checkout
- **Solo dev** — vente one-shot ~1500€
- **Branches** : `main` (prod) | `dev` (intégration — **travailler ici**)

---

## §2 — Commandes pour situer l'environnement

```bash
git status                          # où suis-je ?
git branch                          # branche courante
git log --oneline -5                # 5 derniers commits
php artisan test                    # les tests passent-ils ?
npm run build                       # le build passe ?
```

---

## §3 — Ordre de lecture des docs

```
START_HERE.md   ← toi ici (5 min)
    ↓
AGENTS.md       ← guide universel (10 min)
    ↓
docs/MAP.md     ← carte des dossiers (lecture ciblée)
    ↓
docs/AI_GUIDE.md    ← exemples concrets (5 min)
docs/ARCHITECTURE.md ← architecture détaillée (5 min)
    ↓
config/AGENTS.md            ← si tu modifies une config
app/Services/AGENTS.md      ← si tu modifies un service
resources/views/reports/AGENTS.md ← si tu touches au PDF
```

Si l'un de ces fichiers n'existe pas encore (plan pas totalement exécuté) :
→ `docs/CLAUDE.md` (conventions Laravel historiques)
→ `README.md` (vue d'ensemble du projet)
→ Le code source lui-même

---

## §4 — Workflow Git complet

```bash
# 1. Partir de dev
git checkout dev
git pull origin dev

# 2. Créer une branche feature
git checkout -b feature/ma-modif

# 3. (Optionnel) Worktree dédié
git worktree add ../qualyra-ma-modif feature/ma-modif

# 4. Modifier, tester, commit
./scripts/precommit.sh
git add .
git commit -m "feat: description concise"

# 5. Push + PR
git push -u origin feature/ma-modif
# → Ouvrir PR vers dev sur GitHub
```

**Conventions de commit** : `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`

---

## §5 — Hot spots (tables des mines)

Ces fichiers ont un **impact large** — toute modification doit être suivie des tests associés :

| Fichier | Impact | Tests à relancer |
|---------|--------|------------------|
| `config/ai_act_rules.php` | 22 règles AI Act — doit synchroniser DemoSeeder | `./scripts/check-sync.sh` + `--filter AiActClassifier` |
| `config/questionnaire.php` | Questionnaire — doit synchroniser DemoSeeder + Classifier | `./scripts/check-sync.sh` + `--filter Questionnaire` |
| `config/report_templates.php` | Templates rapport — doit vérifier le PDF | `--filter Report` |
| `app/Services/AiActClassifier.php` | Moteur de classification | `--filter AiActClassifier` |
| `app/Services/ReportContentBuilder.php` | Assemblage rapport | `--filter Report` |
| `app/Services/ReportSnapshotBuilder.php` | Snapshot légal figé | Risque légal — demander |
| `app/Policies/AiUsagePolicy.php` | Isolation tenant | `--filter AiUsage` |
| `database/seeders/DemoSeeder.php` | Démo client — miroir des configs | `./scripts/check-sync.sh` |
| `resources/views/reports/pdf.blade.php` | Template PDF standalone Chrome | `--filter Report` |

---

## §6 — Commandes pre-commit (obligatoires)

Exécuter **avant chaque commit** :

```bash
# Si les scripts existent (recommandé) :
./scripts/lint.sh --test && ./scripts/check-sync.sh && ./scripts/test.sh && npm run build

# Fallback manuel (si scripts pas encore créés) :
./vendor/bin/pint --test              # lint PHP
php artisan test                       # tests Pest (102 tests, 290 assertions)
npm run build                          # build Vite
# Vérification manuelle sync seeder : s'assurer que DemoSeeder reflète config/
```

---

## §7 — Procédure PR

1. Ouvrir une Pull Request vers `dev`
2. Vérifier que le template PR s'affiche
3. Cocher les hot spots touchés
4. Si GitHub Actions CI activée : attendre le check vert
5. Assigner @tomtimlt en reviewer

---

## §8 — 8 interdictions absolues

1. ❌ **Pas de Livewire, Inertia, packages exotiques** — Blade + Alpine seulement
2. ❌ **Pas de refactoring spontané** — ne pas modifier ce qui n'est pas demandé
3. ❌ **Pas de design custom** — Tailwind UI / TallStackUI basiques uniquement
4. ❌ **Pas de validation inline** — toujours une Form Request
5. ❌ **Pas de FK en fillable** — `organization_id` JAMAIS dans `$fillable`
6. ❌ **Pas de query builder brut** — Eloquent seulement, sauf justification
7. ❌ **Pas de modification de `.env`, `.env.example`, `composer.lock`** sans demande
8. ❌ **Pas de commit de `node_modules/`, `vendor/`, `public/build/`**

---

## §9 — Conventions de code

| Règle | Valeur |
|-------|--------|
| Langue commentaires | 🇫🇷 français |
| Variables / méthodes | 🇬🇧 anglais |
| Models | singulier (`User`, `AiUsage`) |
| Tables | pluriel (`users`, `ai_usages`) |
| Routes | nommées (`route('usages.index')`) |
| PHP | `declare(strict_types=1)` partout |
| Controllers | minces → logique dans Services |
| Tests | Pest 4, pas PHPUnit direct |
| Frontend | Alpine `x-data`, Tailwind utilities |

---

## §10 — Table « doute → où chercher »

| Situation | Aller voir |
|-----------|------------|
| Je ne sais pas quel fichier modifier | `docs/MAP.md` §5 (table intention → fichiers) |
| Je veux ajouter une règle AI Act | `config/AGENTS.md` + `AGENTS.md` §5.1 |
| Je veux modifier le PDF | `resources/views/reports/AGENTS.md` |
| Je veux comprendre l'architecture | `docs/ARCHITECTURE.md` |
| Je veux un exemple de prompt IA | `docs/AI_GUIDE.md` |
| Je ne suis pas sûr d'une convention | `AGENTS.md` §7 |
| Je pense que ma modif a un gros impact | `AGENTS.md` §4 (hot spots) |
| Je veux savoir quoi NE PAS toucher | `AGENTS.md` §6 (interdictions) |

---

## §11 — Setup initial (premier clone)

```bash
git clone https://github.com/tomtimlt/qualyra.git
cd qualyra
git checkout dev

# Installation
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed

# Lancement
php artisan serve           # http://localhost:8000
# ou tout en un :
composer run dev            # serve + queue + logs + vite
```

---

## §12 — TL;DR pour IA pressée

1. **Branche** : `dev` (jamais `main`)
2. **Stack** : Laravel 13 / Blade / Alpine / Tailwind / SQLite / Pest
3. **Lis** `AGENTS.md` avant de coder
4. **Hot spots** : config/ + app/Services/ = toujours vérifier DemoSeeder
5. **Avant commit** : lint + tests + build
6. **Interdits** : pas de Livewire/Inertia, pas de refacto spontané, pas de FK en fillable
7. **Doute** → `docs/MAP.md` §5
