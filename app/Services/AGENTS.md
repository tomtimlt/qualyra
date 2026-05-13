# AGENTS.md — app/Services/

## Rôle

Logique métier de l'application. Contient les 3 services critiques qui implémentent le moteur de classification AI Act et la génération des rapports.

## Fichiers principaux

| Fichier | Rôle | Hot spot |
|---------|------|----------|
| `AiActClassifier.php` | Moteur de classification : détermine le niveau de risque (Inacceptable / Haut / Limité / Minimal) à partir des réponses questionnaire | ✅ |
| `ReportContentBuilder.php` | Assemble le contenu rédactionnel du rapport PDF à partir des templates config et des données d'évaluation | ✅ |
| `ReportSnapshotBuilder.php` | Crée un snapshot figé des données d'évaluation au moment de la génération du rapport (garantie légale) | ✅ |

## Règles de modification

- **AiActClassifier** : toute modification DOIT être testée via `AiActClassifierMatriceTest`. Vérifier que les 22 règles + le cas défaut sont toujours couverts.
- **ReportContentBuilder** : toute modification DOIT être testée via `ReportTest`. Vérifier que le PDF généré contient toujours toutes les sections attendues.
- **ReportSnapshotBuilder** : modification = risque légal. Ne JAMAIS modifier la structure du snapshot sans valider avec l'humain.
- **Pattern** : les services sont des classes stateless (pas de propriétés d'instance). Ils reçoivent leurs données en paramètre et retournent un résultat.

## Tests associés

- `tests/Feature/AiActClassifierMatriceTest.php` — validation exhaustive du moteur
- `tests/Feature/ReportTest.php` — tests de génération PDF
- `tests/Feature/AssessmentTest.php` — tests d'évaluation complets

## NE PAS toucher sans raison

- Les signatures des méthodes publiques (elles sont appelées depuis les controllers)
- Les constantes de niveau de risque (INACCEPTABLE, HAUT_RISQUE, RISQUE_LIMITE, RISQUE_MINIMAL)

## Voir aussi

- `/AGENTS.md` (racine)
- `config/AGENTS.md` — les fichiers config consommés par ces services
