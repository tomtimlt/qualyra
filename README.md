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
  <a href="https://tailwindcss.com"><img alt="Tailwind CSS" src="https://img.shields.io/badge/Tailwind-3-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white"></a>
  <a href="https://pestphp.com"><img alt="Pest" src="https://img.shields.io/badge/tests-Pest_4-8b5cf6?style=flat-square"></a>
  <a href="#licence"><img alt="License" src="https://img.shields.io/badge/license-Propri%C3%A9taire-red?style=flat-square"></a>
</p>

<p align="center">
  <a href="#démarrage-rapide">Démarrage rapide</a> ·
  <a href="docs/ARCHITECTURE.md">Architecture</a> ·
  <a href="CONTRIBUTING.md">Contribuer</a> ·
  <a href="SECURITY.md">Sécurité</a> ·
  <a href="CHANGELOG.md">Changelog</a>
</p>

---

## Sommaire

- [À propos](#à-propos)
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

L'AI Act entre en application progressive depuis le **2 février 2025** (pratiques interdites) et le **2 août 2026** (haut risque). Les sanctions atteignent **35 M€ ou 7 % du CA mondial** pour les pratiques inacceptables. La plupart des PME n'ont ni les ressources juridiques internes ni le budget consulting (10–50 k€) pour s'y conformer. Qualyra livre une réponse opérationnelle pour ~1 500 €.

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
| CSS | Tailwind CSS | 3 |
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
| **Haut risque** | Systèmes critiques (Annexe III) | 02/08/2026 | 15 M€ ou 3 % CA |
| **Risque limité** | Chatbots, deepfakes (Art. 50) | 02/02/2027 | Obligation de transparence |
| **Risque minimal** | Autres systèmes | Aucune obligation | — |

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

Le paiement Stripe est optionnel. En l'absence de `STRIPE_SECRET`, les rapports sont générés gratuitement en mode dev.

```env
# Activation du paiement
STRIPE_SECRET=sk_test_...
STRIPE_CURRENCY=eur
STRIPE_REPORT_PRICE=4900     # 49 € en centimes
```

---

## Tests

Le projet est couvert par une suite [Pest 4](https://pestphp.com) (~100 tests, 288+ assertions).

```bash
php artisan test                    # toute la suite
php artisan test --filter Report    # par filtre
php artisan test --parallel         # parallélisé
```

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

Projet en développement solo pour l'instant — les contributions externes ne sont pas ouvertes.

Pour signaler un bug ou proposer une amélioration, ouvrez une [Issue](https://github.com/tomtimlt/qualyra/issues) en utilisant les templates `bug_report` ou `feature_request`.

Conventions de code, workflow Git et conventions de commit : [`CONTRIBUTING.md`](CONTRIBUTING.md).

---

## Licence

**Projet propriétaire** — Tous droits réservés © 2026 Thomas Lhostete.

Le code source est consultable à des fins **d'audit**, **d'évaluation** ou de **portfolio**. Il ne peut être ni utilisé en production, ni modifié, ni redistribué sans **autorisation écrite préalable**.

Pour une demande de licence commerciale, un audit AI Act professionnel ou un partenariat :

📧 **thomas.lhostete@viacesi.fr**

---

<p align="center">
  Conçu pour les PME françaises · Développé par <a href="https://github.com/tomtimlt">@tomtimlt</a> · CESI Nancy
</p>
