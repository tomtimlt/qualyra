<p align="center">
  <img src="public/qualyra/brand/qualyra-mark-original.png" alt="Qualyra" width="140">
</p>

<h1 align="center">Qualyra</h1>

<p align="center">
  <strong>Audit de conformité AI Act + RGPD pour PME françaises.</strong><br>
  Déclarez vos usages d'IA, classez-les automatiquement selon les 4 niveaux du Règlement (UE) 2024/1689,<br>
  générez un rapport PDF avec plan d'action 1 mois / 6 mois / 1 an.
</p>

<p align="center">
  <a href="https://www.php.net/releases/8.4/"><img alt="PHP" src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white"></a>
  <a href="https://laravel.com"><img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white"></a>
  <a href="https://tailwindcss.com"><img alt="Tailwind CSS" src="https://img.shields.io/badge/Tailwind-4-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white"></a>
  <a href="https://github.com/tomtimlt/qualyra/actions/workflows/tests.yml"><img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/tomtimlt/qualyra/tests.yml?branch=main&style=flat-square&label=tests"></a>
  <a href="LICENSE"><img alt="License: AGPL-3.0" src="https://img.shields.io/badge/license-AGPL--3.0-blue?style=flat-square"></a>
</p>

<p align="center">
  <a href="#démarrage-rapide">Démarrage rapide</a> ·
  <a href="docs/ARCHITECTURE.md">Architecture</a> ·
  <a href="CONTRIBUTING.md">Contribuer</a> ·
  <a href="SECURITY.md">Sécurité</a> ·
  <a href="CHANGELOG.md">Changelog</a>
</p>

> [!WARNING]
> **Qualyra est un outil d'aide au diagnostic, pas un conseil juridique.**
> La classification repose uniquement sur les déclarations de l'utilisateur et sur une lecture du Règlement (UE) 2024/1689 et du RGPD à une date donnée. Elle peut être incomplète, erronée ou dépassée par l'évolution des textes et de leur interprétation. Faites valider toute décision de conformité par un avocat ou un DPO. Le logiciel est fourni « en l'état », sans aucune garantie (voir [LICENSE](LICENSE)).

<p align="center">
  <img src="docs/screenshots/dashboard.png" alt="Tableau de bord Qualyra" width="900">
</p>

---

## Sommaire

- [À propos](#à-propos)
- [Captures d'écran](#captures-décran)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Démarrage rapide](#démarrage-rapide)
- [Installation locale](#installation-locale)
- [Parcours utilisateur](#parcours-utilisateur)
- [Moteur de classification AI Act](#moteur-de-classification-ai-act)
- [Architecture](#architecture)
- [Génération PDF](#génération-pdf)
- [Docker](#docker)
- [Configuration Stripe](#configuration-stripe)
- [Tests](#tests)
- [Sécurité](#sécurité)
- [Conventions](#conventions)
- [Documentation](#documentation)
- [Contribution](#contribution)
- [Licence](#licence)

---

## À propos

**Qualyra** est une application web qui audite les usages d'IA d'une PME, les classe selon les 4 niveaux du **Règlement (UE) 2024/1689 (AI Act)**, croise avec les obligations **RGPD**, et génère un rapport PDF de conformité avec plan d'action chiffré.


### Cible

PME de **20 à 500 personnes** sans DPO ni RSSI dédié : cabinets, agences, écoles privées, startups tech. Marché : France.

### Pourquoi maintenant

L'AI Act entre en application progressive : pratiques interdites depuis le **2 février 2025**, transparence (Art. 50) depuis le **2 août 2026**, et systèmes à haut risque de l'Annexe III à partir du **2 décembre 2027** (date reportée par le Digital Omnibus, en vigueur depuis le 27 juillet 2026). Les sanctions atteignent **35 M€ ou 7 % du CA mondial** pour les pratiques inacceptables. La plupart des PME n'ont ni les ressources juridiques internes ni le budget consulting (10–50 k€) pour s'y conformer.

### Statut du projet

Qualyra a été conçu comme un produit commercial (rapport vendu ~1 500 €). Le projet commercial n'a pas abouti : il est donc publié en open source sous licence AGPL-3.0 pour que le moteur de règles et l'approche puissent servir à d'autres. Il est maintenu sur mon temps libre, sans garantie de suivi.

---

## Captures d'écran

| Landing | Cartographie des risques |
|---|---|
| ![Landing](docs/screenshots/landing.png) | ![Vision Sankey](docs/screenshots/vision.png) |
| **Tableau de bord** | **Rapport** |
| ![Tableau de bord](docs/screenshots/dashboard.png) | ![Rapport](docs/screenshots/report.png) |

---

## Fonctionnalités

- **Déclaration des usages IA** — formulaire structuré par type (LLM, IA générative, scoring, biométrie), domaine et description
- **Questionnaire dynamique** — 13 variables conditionnelles selon le type d'IA et le contexte d'usage
- **Classification automatique** — 22 règles officielles encodées (8 inacceptable + 8 haut risque + 6 risque limité + default)
- **Rapport PDF légal** — couverture, synthèse exécutive, détail par usage, plan d'action 1m / 6m / 1an, checklist RGPD, zones grises, disclaimer
- **Snapshot figé** — JSON immuable au moment de la génération (valeur juridique opposable)
- **Paiement Stripe** — Checkout intégré, mode gratuit en développement
- **Multi-organisations** — isolation tenant stricte (1 user = 1 organisation)

---

## Stack technique

| Couche | Technologie | Version |
|--------|-------------|---------|
| Langage | PHP | 8.4 |
| Framework | Laravel | 13 |
| Vues | Blade | — |
| CSS | Tailwind CSS | 4 |
| JS | Alpine.js + Vite | 3 / 8 |
| Base de données | SQLite (dev) · MySQL 8 (prod optionnelle) | — |
| Auth | Laravel Breeze (Blade stack) | 2 |
| Tests | Pest | 4 |
| PDF | spatie/browsershot (Chrome headless) | 5 |
| Paiement | stripe/stripe-php | 20 |
| Conteneur | Docker (mono-conteneur self-contained) | — |

---

## Démarrage rapide

Avec Docker, sans aucune config :

```bash
docker build -t qualyra .
docker run -d --name qualyra -p 8000:8000 qualyra
```

L'application est accessible sur **http://localhost:8000**.

Au premier lancement, le conteneur :

1. Génère le `.env` depuis `.env.example`
2. Crée la clé d'application (`APP_KEY`)
3. Crée la base SQLite + joue les migrations
4. Seed les données de démonstration
5. Démarre le serveur PHP

### Comptes de démonstration

| Email | Mot de passe | Contenu |
|-------|--------------|---------|
| `demo@example.com` | `password` | PME radiologie · 6 usages IA pré-déclarés · rapport pré-généré |

---

## Installation locale

Prérequis : **PHP 8.4**, **Composer 2**, **Node 20+**, **npm**.

```bash
# Dépendances
composer install
npm install && npm run build

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données (SQLite)
touch database/database.sqlite
php artisan migrate --seed

# Serveur + queue + logs + Vite, en parallèle
composer dev
```

Pour le développement avec hot-reload via Docker :

```bash
docker compose up -d
```

---

## Parcours utilisateur

```
1. Inscription (Breeze)
   └─→ 2. Onboarding organisation (nom, SIRET, secteur)
       └─→ 3. Déclaration des usages IA (type, domaine, description)
           └─→ 4. Questionnaire dynamique (13 variables conditionnelles)
               └─→ 5. Classification automatique (AiActClassifier)
                   └─→ 6. Rapport PDF avec plan d'action chiffré
                       └─→ 7. Paiement Stripe (ou gratuit en dev)
```

---

## Moteur de classification AI Act

Le service [`AiActClassifier`](app/Services/AiActClassifier.php) implémente la matrice de décision officielle du Règlement (UE) 2024/1689.

### 4 niveaux de risque

| Niveau | Description | Délai d'application | Sanction max (PME) |
|--------|-------------|---------------------|--------------------|
| **Inacceptable** | Pratiques prohibées (Art. 5) | Depuis 02/02/2025 | 35 M€ ou 7 % CA |
| **Haut risque** | Systèmes critiques (Annexe III) | 02/12/2027 | 15 M€ ou 3 % CA |
| **Haut risque** | Produits réglementés (Annexe I, ex. dispositifs médicaux) | 02/08/2028 | 15 M€ ou 3 % CA |
| **Risque limité** | Chatbots, deepfakes (Art. 50) | Depuis 02/08/2026 | 15 M€ ou 3 % CA |
| **Risque minimal** | Autres systèmes | Aucune obligation | — |

Calendrier à jour du **Digital Omnibus sur l'IA** (adopté en juin 2026, en vigueur depuis le 27/07/2026), qui a reporté les échéances haut risque initialement fixées au 02/08/2026 (Annexe III) et au 02/08/2027 (Annexe I). Chaque règle porte une date `applicable_from` : le moteur ignore les règles pas encore en vigueur à la date d'audit, et la timeline projette la classification à +1 et +2 ans.

### 22 règles encodées

- **8 INACCEPTABLE** — notation sociale, manipulation subliminale, biométrie temps réel, profiling sensible, etc.
- **8 HAUT_RISQUE** — recrutement, éducation, crédit, santé, biométrie, infrastructures critiques, justice, migration
- **6 RISQUE_LIMITE** — chatbots, détection d'émotions, catégorisation biométrique, deepfakes, contenu synthétique
- **1 DEFAULT** — aucun critère satisfait ⇒ risque minimal

**Algorithme** : premier match gagnant dans l'ordre `INACCEPTABLE > HAUT_RISQUE > RISQUE_LIMITE > DEFAULT`, complété par les alertes RGPD croisées.

Détails réglementaires : [`docs/Guide de Conformité AI Act.md`](docs/Guide%20de%20Conformité%20AI%20Act.md) · [`docs/Matrice de Décision AI Act.md`](docs/Matrice%20de%20Décision%20AI%20Act.md).

---

## Architecture

```
app/
├── Http/Controllers/   # CRUD usages, questionnaire, assessment, report, checkout
├── Services/           # AiActClassifier, ReportContentBuilder, ReportSnapshotBuilder
├── Models/             # User, Organization, AiUsage, Response, Assessment, Report
└── Policies/           # AiUsagePolicy (isolation tenant)

config/
├── ai_act_rules.php       # 22 règles AI Act (matrice officielle)
├── questionnaire.php      # Questions dynamiques par type/domaine
└── report_templates.php   # Templates rédactionnels du rapport

resources/views/
├── reports/pdf.blade.php  # Template PDF standalone (Chrome headless)
└── questionnaire/         # Formulaire dynamique
```

Plus de détails : [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) · [`docs/DB_SCHEMA.md`](docs/DB_SCHEMA.md).

---

## Génération PDF

Le projet utilise **spatie/browsershot** (headless Chrome/Chromium via Puppeteer) pour générer des PDF identiques au rendu web, contrairement à DomPDF qui ne supporte ni flexbox ni les variables CSS.

- Template standalone dans `resources/views/reports/pdf.blade.php`
- Logo embarqué en base64
- Détection automatique du navigateur (Chrome sur macOS, Chromium sur Linux/Docker)
- Sections : couverture, synthèse exécutive, détail par usage, plan d'action 1 mois / 6 mois / 1 an, checklist, zones grises, disclaimer

---

## Docker

### Image autonome

Le `Dockerfile` build une image self-contained avec tout le nécessaire :

- PHP 8.4 + extensions (pdo_sqlite, gd, intl, bcmath, zip, exif)
- Node.js 20 + Chromium (pour le rendu PDF)
- Dépendances Composer installées au build (`--no-dev`)
- Assets frontend compilés (Vite build)

### docker-compose.yml

Pour le développement avec bind mount et volumes persistants (`vendor`, `node_modules`).

### entrypoint.sh

Au démarrage du conteneur :
1. Crée `.env` depuis `.env.example` si absent
2. Génère `APP_KEY`
3. Installe les dépendances si les volumes sont vides (fallback dev)
4. Crée la base SQLite, migrations, seed (uniquement si base vierge)
5. Démarre `php artisan serve`

---

## Configuration Stripe

Le paiement Stripe est optionnel. En l'absence de `STRIPE_SECRET`, les rapports sont générés gratuitement.

```env
# Activation du paiement
STRIPE_SECRET=sk_test_...
STRIPE_CURRENCY=eur
STRIPE_REPORT_PRICE=4900     # 49 € en centimes
```

---

## Tests

Le projet est couvert par une suite [Pest 4](https://pestphp.com) (136 tests), exécutée par la CI GitHub Actions à chaque push et pull request, avec Pint pour le style.

```bash
php artisan test                    # toute la suite
php artisan test --filter Report    # par filtre
php artisan test --parallel         # parallélisé
```

Les tests de téléchargement PDF lancent un vrai Chrome headless. Si Chromium n'est pas dans `/usr/bin/chromium`, indiquez son chemin : `CHROME_PATH=/chemin/vers/chrome php artisan test`.

| Module | Couverture |
|--------|------------|
| AiUsage | CRUD + 4 vecteurs IDOR (isolation tenant) |
| Questionnaire | Questions dynamiques, persistence, upsert, validation |
| Assessment | Classification 4 niveaux, alertes, isolation |
| Matrice AI Act | 5 scénarios Annexe III (tests de cohérence) |
| Report | Checkout, snapshot, PDF, contenus conditionnels |
| Auth | Breeze (par défaut) |

---

## Sécurité

- **Isolation tenant stricte** — `AiUsagePolicy` + contrainte `user_id` UNIQUE sur `organizations`
- **Snapshot figé** — le rapport stocke un JSON immuable au moment de la génération (valeur juridique opposable)
- **Pas de mass assignment** — les FK sont injectées via les relations Eloquent
- **Validation centralisée** — Form Requests typés, aucune validation inline dans les controllers
- **CSRF** — natif Laravel sur tous les formulaires
- **Audit dépendances** — Dependabot configuré sur Composer, npm, GitHub Actions

Signalement de vulnérabilité : voir [`SECURITY.md`](SECURITY.md).

---

## Conventions

- Langue : français (commentaires, textes UI, documentation)
- Modèles : singulier (`AiUsage`) — Tables : pluriel (`ai_usages`)
- PHP : `declare(strict_types=1)` partout
- Tests : Pest 4 avec helper functions (`userWithOrgAndUsage()`, `usageWithAnswers()`)

---

## Documentation

| Fichier | Public | Description |
|---------|--------|-------------|
| [`CONTRIBUTING.md`](CONTRIBUTING.md) | Devs | Workflow Git, conventions de commit, checklist PR |
| [`SECURITY.md`](SECURITY.md) | Tous | Politique de signalement de vulnérabilités |
| [`CHANGELOG.md`](CHANGELOG.md) | Tous | Historique des versions (format Keep a Changelog) |
| [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) | Devs | Architecture détaillée et décisions techniques |
| [`docs/DB_SCHEMA.md`](docs/DB_SCHEMA.md) | Devs | Schéma de base de données |
| [`docs/Guide de Conformité AI Act.md`](docs/Guide%20de%20Conformité%20AI%20Act.md) | Métier | Référentiel AI Act exhaustif |
| [`docs/Matrices et Référentiels d'Audit.md`](docs/Matrices%20et%20Référentiels%20d'Audit.md) | Métier | Matrices opérationnelles d'audit |

---

## Contribution

Les contributions sont bienvenues, en particulier sur l'exactitude réglementaire : une règle mal encodée, une date dépassée ou une zone grise mal décrite sont des bugs à part entière.

Pour signaler un bug ou proposer une amélioration, ouvrez une [Issue](https://github.com/tomtimlt/qualyra/issues) en utilisant les templates `bug_report` ou `feature_request`. Pour une correction, ouvrez directement une pull request.

Conventions de code, workflow Git et conventions de commit : [`CONTRIBUTING.md`](CONTRIBUTING.md).

---

## Licence

© 2026 Thomas Lhostete. Distribué sous licence **[GNU AGPL-3.0](LICENSE)**.

Vous pouvez utiliser, modifier et redistribuer Qualyra librement. Si vous le proposez comme service en ligne (SaaS), vous devez publier le code source de votre version modifiée sous la même licence.

Le logiciel est fourni sans aucune garantie. Les rapports générés ne constituent pas un avis juridique.

Contact : **thomas.lhostete@viacesi.fr**

---

<p align="center">
  Conçu pour les PME françaises · Développé par <a href="https://github.com/tomtimlt">@tomtimlt</a> · CESI Nancy
</p>
