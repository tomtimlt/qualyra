# Qualyra · Audit de Conformité AI Act + RGPD

<p align="center">
  <img src="public/qualyra/brand/qualyra-mark-original.png" alt="Qualyra logo" width="120">
</p>

Application web d'audit de conformité des usages d'intelligence artificielle pour les PME françaises, conforme au **Règlement (UE) 2024/1689 (AI Act)** et au **RGPD**.

## ✦ Objectif

Permettre aux PME de déclarer leurs outils d'IA, d'évaluer automatiquement leur niveau de risque selon la matrice AI Act, et de générer un rapport de conformité détaillé avec plan d'action 1 mois / 6 mois / 1 an.

## ✦ Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8.4 + Laravel 13 |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js + Vite |
| Base de données | SQLite (dev/Docker), MySQL 8 optionnel |
| Authentification | Laravel Breeze (Blade stack) |
| Tests | Pest 4 |
| Autorisation | Policies Laravel (auto-discovery) |
| Génération PDF | spatie/browsershot + Chromium/Chrome headless |
| Paiement | Stripe Checkout (stripe/stripe-php) |
| Design System | Qualyra (propre systême de tokens CSS) |

## ✦ Démarrage rapide

### Un seul conteneur Docker (recommandé)

```bash
# Builder l'image
docker build -t ai-assistant .

# Lancer le conteneur
docker run -d --name ai-assistant -p 8000:8000 ai-assistant
```

L'application est accessible sur **http://localhost:8000**.

Au premier démarrage, le conteneur :
1. Crée le fichier `.env` depuis `.env.example`
2. Génère la clé d'application (`APP_KEY`)
3. Crée la base SQLite et exécute les migrations
4. Peuple la base avec les données de démonstration
5. Démarre le serveur PHP

### Avec Docker Compose (développement)

```bash
docker compose up -d
```

Compatible avec le bind mount des sources pour le hot-reload.

### En local (sans Docker)

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

# Serveur
php artisan serve
npm run dev     # terminal séparé
```

## ✦ Comptes de démonstration

Après `php artisan db:seed` (automatique au premier démarrage Docker) :

| Email | Mot de passe | Description |
|-------|--------------|-------------|
| demo@example.com | password | Compte démo avec données préremplies (organisation + usages IA + rapports) |

## ✦ Architecture

```
app/
├── Http/Controllers/
│   ├── AiUsageController.php        # CRUD usages IA déclarés
│   ├── QuestionnaireController.php  # Questionnaire dynamique par type d'IA
│   ├── AssessmentController.php     # Calcul et persistance du niveau AI Act
│   ├── CheckoutController.php       # Paiement Stripe (ou mode dev)
│   ├── ReportController.php         # Génération PDF via Browsershot
│   └── OrganizationController.php   # Onboarding PME
├── Services/
│   ├── AiActClassifier.php          # Moteur de classification (4 niveaux)
│   ├── ReportContentBuilder.php     # Contenu rédactionnel du rapport
│   └── ReportSnapshotBuilder.php    # Snapshot figé (garantie légale)
└── Policies/
    └── AiUsagePolicy.php            # Isolation tenant stricte

config/
├── ai_act_rules.php                 # Matrice de décision AI Act (22 règles)
├── questionnaire.php                # Questions dynamiques par type/domaine
└── report_templates.php             # Templates rédactionnels du rapport

database/
├── migrations/                      # 5 tables custom + 3 Laravel
└── seeders/
    ├── DatabaseSeeder.php           # Comptes de référence
    └── DemoSeeder.php               # Jeu de données complet

resources/views/
├── reports/
│   ├── pdf.blade.php                # Template PDF standalone (Chrome headless)
│   └── show.blade.php               # Vue web du rapport
└── questionnaire/                   # Formulaire dynamique AI Act
```

## ✦ Parcours utilisateur

1. **Inscription** → Création de compte Breeze
2. **Onboarding** → Création de l'organisation (nom, SIRET, secteur)
3. **Déclaration** → Ajout des usages IA de l'entreprise (type, domaine, description)
4. **Questionnaire** → Réponses aux questions dynamiques selon le type d'IA
5. **Classification** → Évaluation automatique par le moteur AiActClassifier
6. **Rapport** → Génération du PDF avec snapshot figé (valeur légale)
7. **Paiement** → Stripe Checkout (ou mode dev gratuit)

## ✦ Moteur de classification AI Act

Le service `AiActClassifier` implémente la matrice de décision officielle :

### Niveaux

| Niveau | Description | Délai | Sanction max (PME) |
|--------|-------------|-------|-------------------|
| Inacceptable | Pratiques prohibées (Art. 5) | Interdit depuis le 02/02/2025 | 35 M€ ou 7 % CA |
| Haut risque | Systèmes critiques (Annexe III) | 02/08/2026 | 15 M€ ou 3 % CA |
| Risque limité | Chatbots, deepfakes (Art. 50) | 02/02/2027 | — |
| Risque minimal | Autres systèmes | Aucune obligation AI Act | — |

### Règles (22 au total)

- **8 règles INACCEPTABLE** : notation sociale, subliminal, biométrie temps réel, profiling sensible, etc.
- **8 règles HAUT_RISQUE** : recrutement, éducation, crédit, santé, biométrie, infrastructures, justice, migration
- **6 règles RISQUE_LIMITE** : chatbots, émotions, catégorisation biométrique, deepfakes, synthèse
- **1 règle DEFAULT** : aucun critère satisfait = risque minimal

Algorithme : premier match gagnant dans l'ordre (INACCEPTABLE > HAUT_RISQUE > RISQUE_LIMITE > DEFAULT) + alertes RGPD complémentaires.

## ✦ Génération PDF

Le projet utilise **spatie/browsershot** (headless Chrome/Chromium via Puppeteer) pour générer des PDF identiques au rendu web, contrairement à DomPDF qui ne supporte ni flexbox ni les variables CSS.

- Template standalone dans `resources/views/reports/pdf.blade.php`
- Logo embarqué en base64
- Détection automatique du navigateur (Chrome sur macOS, Chromium sur Linux/Docker)
- Sections : couverture, synthèse exécutive, détail par usage, plan d'action 1 mois / 6 mois / 1 an, checklist, zones grises, disclaimer

## ✦ Docker

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

## ✦ Configuration Stripe

Le paiement Stripe est optionnel. En l'absence de `STRIPE_SECRET`, les rapports sont générés gratuitement en mode dev.

```env
# Activation du paiement
STRIPE_SECRET=sk_test_...
STRIPE_CURRENCY=eur
STRIPE_REPORT_PRICE=4900     # 49 € en centimes
```

## ✦ Tests (102 tests — 290 assertions)

```bash
# Tout lancer
php artisan test

# Par module
php artisan test --filter="AiUsage"
php artisan test --filter="Report"
php artisan test --filter="télécharge le PDF"
```

| Module | Tests | Couverture |
|--------|-------|------------|
| AiUsage | ~20 | CRUD + 4 vecteurs IDOR (isolation tenant) |
| Questionnaire | ~10 | Questions dynamiques, persistence, upsert, validation |
| Assessment | ~14 | Classification 4 niveaux, alertes, isolation |
| Matrice AI Act | 5 | Scénarios Annexe III (tests de cohérence) |
| Report | ~9 | Checkout, snapshot, PDF, tenant isolation, contenus conditionnels |
| Dashboard | ~3 | Affichage flux tendu |
| Auth | ~35 | Breeze (par défaut) |

## ✦ Sécurité

- **Isolation tenant stricte** : `AiUsagePolicy` + contrainte `user_id` UNIQUE sur `organizations`
- **Snapshot figé** : le rapport stocke un JSON immuable des données au moment de la génération
- **Pas de mass assignment** : les FK sont injectées via les relations Eloquent
- **Validation** : enums stricts via Form Requests, jamais de validation inline
- **CSRF** : natif Laravel sur tous les formulaires

## ✦ Conventions

- Langue : français (commentaires, textes UI, documentation)
- Modèles : singulier (`AiUsage`) — Tables : pluriel (`ai_usages`)
- PHP : `declare(strict_types=1)` partout
- Tests : Pest 4 avec helper functions (`userWithOrgAndUsage()`, `usageWithAnswers()`)

## ✦ Licence

Projet propriétaire — Tous droits réservés

---

**Projet développé par** : tomtimlt · **Contact** : thomas.lhostete@viacesi.fr
