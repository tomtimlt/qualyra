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
| Tests | Pest (à venir) |

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
│   │   ├── Controllers/     # Contrôleurs (Auth, Profile, etc.)
│   │   └── Requests/        # Form Requests (validation)
│   ├── Models/              # Modèles Eloquent
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── AiUsage.php
│   │   ├── Response.php
│   │   └── Assessment.php
│   └── View/                # Composants Blade
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
│       └── home.blade.php   # Page d'accueil
├── routes/
│   ├── web.php              # Routes web
│   └── auth.php             # Routes d'authentification
└── tests/                   # Tests (Pest)
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

### ✅ Semaines 1-2 : Setup initial (EN COURS)

- [x] Initialisation projet Laravel 11
- [x] Installation Laravel Breeze (Blade + Tailwind)
- [x] Configuration MySQL via Docker
- [x] Création des migrations (5 tables)
- [x] Modèles Eloquent avec relations
- [x] Factories et Seeders
- [x] Page d'accueil avec navbar/footer
- [x] Authentification fonctionnelle

### 🔄 Semaine 3 : Formulaire de déclaration

- [ ] Formulaire de déclaration d'usages IA
- [ ] Formulaire d'édition/suppression d'usages
- [ ] Dashboard utilisateur

### 📅 Semaine 4 : Questionnaire dynamique

- [ ] Questions dynamiques selon le type d'IA
- [ ] Sauvegarde des réponses
- [ ] Navigation entre les questions

### 📅 Semaine 5 : Moteur de classification

- [ ] Encodage de la matrice AI Act
- [ ] Algorithme de classification (4 niveaux de risque)
- [ ] Détection des règles applicables

### 📅 Semaine 6 : Génération PDF + Paiement

- [ ] Génération du rapport PDF
- [ ] Intégration Stripe Checkout
- [ ] Historique des rapports

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

# Lancer les tests
php artisan test

# Formatter le code
./vendor/bin/pint
```

## 📝 Conventions de code

- **Langue** : Commentaires et textes en français
- **Models** : Singulier (ex: `AiUsage`)
- **Tables** : Pluriel (ex: `ai_usages`)
- **Type hints** : Strict (`declare(strict_types=1)`)
- **Validation** : Form Requests (pas de validation inline)

## 📄 Licence

Projet propriétaire - Tous droits réservés

---

**Projet développé par** : tomtimlt  
**Contact** : thomas.lhostete@viacesi.fr
