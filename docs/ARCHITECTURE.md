# Architecture — Qualyra

## Vue d'ensemble

Qualyra est une application Laravel monolithique avec rendu côté serveur (Blade)
et interactivité côté client (Alpine.js). Pas de SPA, pas d'API REST.

```
Utilisateur → HTTP → Controller → Service → Model → DB
                    ↓
                 View (Blade + Alpine)
                    ↓
                 PDF (Browsershot) [optionnel]
```

## Flux utilisateur principal

```
1. Onboarding : Création organisation (SIRET, taille, secteur)
2. Déclaration : Ajout d'un usage IA (nom, type, domaine, description)
3. Questionnaire : Réponses aux questions dynamiques selon type/domaine
4. Évaluation : AiActClassifier calcule le niveau de risque
5. Rapport : Génération PDF + vue web du rapport
6. Paiement : Stripe Checkout (optionnel en dev)
```

## Couches

### Controllers → Services

Les controllers sont **minces** : ils reçoivent la requête, délèguent au service,
retournent une réponse. Toute la logique métier est dans `app/Services/`.

```
Controller → Form Request (validation) → Service → Model
```

### Services

3 services principaux, tous **stateless** :

| Service | Rôle | Dépendances |
|---------|------|-------------|
| `AiActClassifier` | Classifie le niveau de risque selon 22 règles | `config/ai_act_rules.php` |
| `ReportContentBuilder` | Assemble le contenu du rapport | `config/report_templates.php`, snapshot |
| `ReportSnapshotBuilder` | Crée un snapshot figé des données | Assessment, Responses, Organization |

### Policies (autorisation)

L'isolation tenant est assurée par `AiUsagePolicy`. Chaque utilisateur ne voit
que les données de son organisation. Les policies vérifient systématiquement :
- `$user->organization_id === $aiUsage->organization_id`
- Pas de rôles ou permissions granulaires en v1

## Hot spots

Voir `docs/MAP.md` (section 3) pour la table complète.

## Décisions architecturales

### Pourquoi Blade pas SPA ?
- Application majoritairement CRUD, peu d'interactions temps réel
- Équipe solo — moins de complexité = plus de fiabilité
- Alpine.js pour les interactions critiques (dropdown, modales, scrollbar)

### Pourquoi Browsershot pas DomPDF ?
- DomPDF ne supporte pas CSS Grid, Flexbox, les polices modernes
- Browsershot = Chrome headless → rendu pixel-perfect
- Inconvénient : dépendance Chromium (build Docker plus lourd)

### Pourquoi SQLite ?
- Données tenant-isolées par utilisateur/organisation (peu de concurrents)
- Zéro administration de base de données
- Backup = copie d'un fichier
- Migration vers MySQL/PostgreSQL possible via Laravel Eloquent

### Pourquoi pas de queue worker ?
- Application synchrone en v1 (les rapports PDF sont générés en temps réel)
- Les traitements sont courts (< 5s)
- Possibilité d'ajouter Laravel Horizon en v2
