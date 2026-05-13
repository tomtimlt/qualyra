# Guide IA — Qualyra

Comment lire le projet et exécuter les tâches les plus fréquentes.

## Ordre de lecture recommandé

1. `/AGENTS.md` — guide universel (hot spots, protocoles, interdictions)
2. `docs/MAP.md` — carte exhaustive de tous les dossiers
3. `docs/ARCHITECTURE.md` — architecture détaillée
4. `docs/CLAUDE.md` — conventions Laravel historiques
5. Le(s) `AGENTS.md` scoped du dossier que vous modifiez

## Exemples concrets de prompts

### "Ajoute une règle AI Act"

```markdown
Ajoute une règle AI Act pour "notation sociale basée sur le comportement"
dans la catégorie INACCEPTABLE.

Fichiers à modifier :
- config/ai_act_rules.php : ajouter la règle avec un ID R-I-XX
- database/seeders/DemoSeeder.php : ajouter un usage IA qui déclenche cette règle
- tests/Feature/AiActClassifierMatriceTest.php : ajouter un cas de test

Protocole : §5.1 de AGENTS.md
Validation : ./scripts/check-sync.sh + php artisan test --filter AiActClassifier
```

### "Change le texte du bouton dashboard"

```markdown
Remplace le texte du bouton "Déclarer un usage" par "Nouvel usage IA"
dans le tableau de bord.

Fichier : resources/views/dashboard.blade.php
Validation : php artisan test --filter Dashboard
```

### "Ajoute un test pour le questionnaire"

```markdown
Ajoute un test Pest pour vérifier que le questionnaire s'affiche correctement
pour un usage de type "IA générative" dans le domaine "RH".

Fichier : tests/Feature/QuestionnaireTest.php
Validation : php artisan test --filter Questionnaire
```

## Anti-patterns observés

| Anti-pattern | Pourquoi c'est problématique |
|-------------|------------------------------|
| Modifier `DemoSeeder` sans vérifier `config/` | Les données démo deviennent incohérentes avec le moteur |
| Ajouter une validation inline dans un controller | Illisible, non réutilisable, pas testable |
| Utiliser `dd()` ou `dump()` dans du code commité | Casse le rendu, oubli fréquent |
| Modifier `AiActClassifier` sans ajouter de test | Impossible de vérifier que les 22 règles sont toujours couvertes |
| Ajouter un package sans demander | Alourdit la stack, risque de conflit de dépendances |
| Refactorer "tant qu'on y est" | Augmente le risque de régression, rend la review impossible |
