# AI Assistant - Outil de Conformité AI Act + RGPD

Application web d'audit de conformité des usages IA pour les PME françaises au regard du règlement européen AI Act et du RGPD.

## 🎯 Objectif

Permettre aux PME de déclarer leurs usages d'intelligence artificielle, d'évaluer leur niveau de risque selon l'AI Act, et de générer un rapport de conformité avec plan d'action.

## 🛠️ Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8.3 + Laravel 11 |
| Frontend | Blade + Tailwind CSS + Vite |
| Base de données | MySQL 8 (Docker) |
| Authentification | Laravel Breeze |
| Tests | Pest 4 |
| Autorisation | Policies Laravel (auto-discovery) |
| PDF | barryvdh/laravel-dompdf |
| Paiement | Stripe Checkout (stripe/stripe-php) |

## 📋 Prérequis

- PHP 8.3+
- Composer
- Node.js 18+
- Docker (pour MySQL)
- Git

## 🚀 Démarrage rapide

### 1. Installation des dépendances

```bash
# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install
```

### 2. Configuration de l'environnement

```bash
# Copier le fichier .env.example et générer la clé d'application
cp .env.example .env
php artisan key:generate
```

### 3. Démarrage de la base de données

```bash
# Lancer le conteneur MySQL
docker start ai-assistant-mysql

# Ou le créer s'il n'existe pas
docker run --name ai-assistant-mysql \
  -e MYSQL_ROOT_PASSWORD=secret \
  -e MYSQL_DATABASE=ai_assistant \
  -e MYSQL_USER=aiuser \
  -e MYSQL_PASSWORD=secret \
  -p 3306:3306 \
  -d mysql:8
```

### 4. Migrations et seeders

```bash
# Exécuter les migrations
php artisan migrate

# (Optionnel) Peupler la base avec des données de test
php artisan db:seed
```

### 5. Lancement du serveur

```bash
# Terminal 1 : Serveur Laravel
php artisan serve

# Terminal 2 : Build des assets (développement)
npm run dev
```

L'application est accessible sur : **http://localhost:8000**

## 🛑 Arrêter le projet

```bash
# Arrêter le serveur Laravel (Ctrl+C dans le terminal)

# Arrêter le build Vite (Ctrl+C dans le terminal)

# Arrêter le conteneur MySQL
docker stop ai-assistant-mysql
```

## 🏗️ Architecture du projet

### Structure des dossiers

```
ai-assistant/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # AiUsage, Assessment, Checkout, Dashboard,
│   │   │                    # Organization, Profile, Questionnaire, Report
│   │   └── Requests/        # Form Requests (validation)
│   ├── Models/              # Modèles Eloquent
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── AiUsage.php
│   │   ├── Response.php
│   │   ├── Assessment.php
│   │   └── Report.php
│   ├── Policies/            # Policies (autorisation par organisation)
│   │   └── AiUsagePolicy.php
│   ├── Services/            # Logique métier
│   │   ├── AiActClassifier.php       # Classification AI Act (4 niveaux)
│   │   └── ReportSnapshotBuilder.php # Snapshot figé pour les rapports PDF
│   └── View/                # Composants Blade
├── config/
│   ├── ai_act_rules.php     # Matrice des règles AI Act
│   └── questionnaire.php    # Questions par type d'IA
├── database/
│   ├── factories/           # Factories pour les tests/seeders
│   ├── migrations/          # Migrations de la base de données
│   └── seeders/             # Seeders pour peupler la BDD
├── resources/
│   ├── css/                 # Styles Tailwind
│   ├── js/                  # JavaScript (Alpine.js, Axios)
│   └── views/               # Vues Blade
│       ├── auth/            # Pages d'authentification
│       ├── components/      # Composants réutilisables
│       ├── layouts/         # Layouts (app, guest, public)
│       ├── questionnaire/   # Formulaire AI Act dynamique
│       ├── reports/         # Rapports HTML + template PDF (dompdf)
│       └── home.blade.php   # Page d'accueil
├── routes/
│   ├── web.php              # Routes web
│   └── auth.php             # Routes d'authentification
└── tests/                   # Tests Pest 4 (82 tests, 221 assertions)
```

### Modèle de données

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │ 1:1
       ▼
┌─────────────┐      ┌──────────────┐
│organizations│──────│  ai_usages   │
└─────────────┘ 1:N  └──────┬───────┘
                           │ 1:N
              ┌────────────┼────────────┐
              ▼            ▼            ▼
       ┌──────────┐  ┌──────────┐  ┌──────────┐
       │ responses│  │responses │  │assessments│
       └──────────┘  └──────────┘  └──────────┘
```

### Tables de la base de données

| Table | Description |
|-------|-------------|
| `users` | Utilisateurs (Laravel Breeze) |
| `organizations` | PME clientes (1 user = 1 org) |
| `ai_usages` | Outils IA déclarés par l'organisation |
| `responses` | Réponses au questionnaire par usage IA |
| `assessments` | Résultats de classification AI Act |

## 📊 Avancement du projet

### ✅ Semaines 1-2 : Setup initial (TERMINÉ)

- [x] Initialisation projet Laravel 11
- [x] Installation Laravel Breeze (Blade + Tailwind)
- [x] Configuration MySQL via Docker
- [x] Création des migrations (5 tables)
- [x] Modèles Eloquent avec relations
- [x] Factories et Seeders
- [x] Page d'accueil avec navbar/footer
- [x] Authentification fonctionnelle

### ✅ Semaine 3 : Formulaire de déclaration (TERMINÉ)

- [x] Routes dashboard / profile / organization / usages
- [x] Onboarding Organization depuis le dashboard (1 user = 1 PME)
- [x] CRUD complet des usages IA (Route::resource)
- [x] Form Requests dédiés (StoreOrganization, StoreAiUsage, UpdateAiUsage)
- [x] AiUsagePolicy (isolation stricte entre organisations)
- [x] Vues Blade (index, create, edit, show + partial _form)
- [x] Pest 4 + tests Feature (Dashboard, Organization, AiUsage)
- [x] Tests sécurité IDOR cross-organisation (4 cas : show/edit/update/delete)
- [x] Migration : `siret` et `user_id` uniques en BDD

### ✅ Semaine 4 : Questionnaire dynamique (TERMINÉ)

- [x] Questions dynamiques selon le type d'IA (config/questionnaire.php — communes + spécifiques par type)
- [x] Sauvegarde des réponses (upsert via contrainte unique `ai_usage_id, variable_key`)
- [x] Navigation entre les questions (formulaire unique multi-sections, pré-rempli si déjà répondu)
- [x] FormRequest dynamique (StoreQuestionnaireRequest) avec `Rule::in` sur les options
- [x] Tests Pest (10) : affichage par type, persistence, upsert, validation, isolation tenant cross-org

### ✅ Semaine 5 : Moteur de classification (TERMINÉ)

- [x] Encodage de la matrice AI Act dans `config/ai_act_rules.php` (Article 5, Annexe III, Article 50, fallback)
- [x] Service `App\Services\AiActClassifier` — algorithme à 4 niveaux : INACCEPTABLE / HAUT_RISQUE / RISQUE_LIMITE / RISQUE_MINIMAL
- [x] Évaluation séquentielle (priorité au plus sévère) + alertes RGPD complémentaires (Article 9, Article 22)
- [x] `AssessmentController` — POST `/usages/{aiUsage}/assessment` calcule + persiste l'évaluation
- [x] UI : badge coloré + raison + article + alertes sur la fiche usage
- [x] Tests Pest (14) : couverture des 4 niveaux + priorité INACCEPTABLE > HAUT_RISQUE + alertes + isolation tenant

### ✅ Semaine 6 : Génération PDF + Paiement (TERMINÉ)

- [x] Génération du rapport PDF (`barryvdh/laravel-dompdf`) — synthèse + détail par usage avec niveau, raison, alertes
- [x] Snapshot figé sur `Report::snapshot` (JSON) → préservé même si l'usage est modifié/supprimé après
- [x] Intégration Stripe Checkout (`stripe/stripe-php`) — session de paiement one-shot, prix configurable via `STRIPE_REPORT_PRICE`
- [x] Mode dev (sans `STRIPE_SECRET` configuré) : paiement court-circuité pour faciliter les tests bout-en-bout
- [x] Historique des rapports (`/reports`) avec statut payé/en attente
- [x] Téléchargement PDF verrouillé tant que `paid_at` est nul (HTTP 402)
- [x] Tests Pest (9) : checkout en mode dev, capture snapshot, gating PDF, isolation tenant cross-org

### 📅 Semaine 7-8 : Finalisation

- [ ] Pages légales (CGV, confidentialité)
- [ ] Tests end-to-end
- [ ] Déploiement OVH/Infomaniak
- [ ] Beta avec design partner

## 👥 Comptes de test

Après avoir lancé `php artisan db:seed` :

| Email | Mot de passe |
|-------|--------------|
| test@example.com | password |
| demo@example.com | password |

## 🔧 Commandes utiles

```bash
# Voir toutes les routes
php artisan route:list

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Lancer les tests (Pest)
./vendor/bin/pest
# ou via Laravel
php artisan test

# Lancer un seul fichier de test
./vendor/bin/pest tests/Feature/AiUsageTest.php

# Formatter le code
./vendor/bin/pint
```

## 🔐 Sécurité

L'application traite des données sensibles de PME (déclarations d'usages IA, SIRET).
Quelques garde-fous structurels :

- **Isolation tenant stricte** : `AiUsagePolicy` empêche tout accès croisé entre organisations.
  Les tests `tests/Feature/AiUsageTest.php` couvrent les 4 vecteurs IDOR (show, edit, update, delete).
- **Pas de mass assignment des FK** : `organization_id` (AiUsage) et `user_id` (Organization)
  sont volontairement hors de `$fillable`. Les FK sont injectées via les relations Eloquent.
- **Contraintes BDD** : `user_id` unique sur `organizations` (1 user = 1 PME), `siret` unique.
- **CSRF** : protection native Laravel sur tous les formulaires Blade.
- **Validation** : enums stricts via Form Requests (jamais de validation inline).

## 📝 Conventions de code

- **Langue** : Commentaires et textes en français
- **Models** : Singulier (ex: `AiUsage`)
- **Tables** : Pluriel (ex: `ai_usages`)
- **Type hints** : Strict (`declare(strict_types=1)`)
- **Validation** : Form Requests (pas de validation inline)

## 💳 Configuration Stripe (paiement des rapports)

L'intégration Stripe Checkout est branchée mais inactive par défaut.
Tant que `STRIPE_SECRET` est vide dans `.env`, le paiement est court-circuité :
le rapport est généré et marqué `paid_at = now()` sans appel à Stripe.

Pour activer le paiement réel :

```env
STRIPE_SECRET=sk_test_...    # clé secrète Stripe (test ou live)
STRIPE_CURRENCY=eur
STRIPE_REPORT_PRICE=4900     # en centimes (49 €)
```

## 📄 Licence

Projet propriétaire - Tous droits réservés

---

**Projet développé par** : tomtimlt  
**Contact** : thomas.lhostete@viacesi.fr
