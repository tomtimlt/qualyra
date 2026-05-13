# AGENTS.md — config/

## Rôle

Configuration réglementaire et fonctionnelle de l'application. **3 fichiers critiques** dont toute modification a un impact large (moteur de classification + seeder démo).

## Fichiers principaux

| Fichier | Rôle | Hot spot |
|---------|------|----------|
| `ai_act_rules.php` | 22 règles AI Act (8 Inacceptable + 8 Haut risque + 6 Risque limité + 1 défaut) | ✅ |
| `questionnaire.php` | Questions dynamiques par type d'IA et domaine | ✅ |
| `report_templates.php` | Templates rédactionnels du rapport PDF | ✅ |

## Règles de modification

- **Ne JAMAIS modifier un fichier config sans mettre à jour les autres** (ils sont interdépendants)
- **Toute modification** de `ai_act_rules.php` ou `questionnaire.php` **doit** synchroniser `database/seeders/DemoSeeder.php`
- **Toute modification** de `report_templates.php` **doit** être testée avec `php artisan test --filter Report`
- Lire et exécuter le script `scripts/check-sync.sh` APRES chaque modification

## Protocole modification

1. Modifier le(s) fichier(s) config
2. Mettre à jour `database/seeders/DemoSeeder.php` pour refléter les changements
3. Lancer : `./scripts/check-sync.sh` (doit passer)
4. Lancer : `php artisan test` (tous les tests doivent passer)
5. Lancer : `php artisan db:seed --force` (vérifier manuel)

## Tests associés

- `tests/Feature/AiActClassifierMatriceTest.php` — test du moteur de classification
- `tests/Feature/QuestionnaireTest.php` — test du questionnaire
- `tests/Feature/QuestionnaireE2ETest.php` — test E2E

## NE PAS toucher sans raison

- L'ordre des IDs de règles (R-I-01, R-H-01...) — les IDs sont référencés dans les tests et le seeder

## Voir aussi

- `/AGENTS.md` (racine)
- `app/Services/AGENTS.md` — le service qui consomme ces configs
