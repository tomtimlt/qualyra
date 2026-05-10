# Instructions pour Claude Code

## Rôle
Tu es l'assistant de développement d'une application Laravel 11. Le projet 
est un outil de conformité AI Act + RGPD pour PME françaises, vendu en 
one-shot ~1500€.

## Règles de code
- PHP 8.3, Laravel 11, conventions Laravel par défaut
- Blade pour les vues (pas de SPA)
- Tailwind CSS via Vite
- Type hints stricts (declare(strict_types=1) en haut de chaque fichier PHP)
- Form Requests pour la validation, pas de validation inline dans les controllers
- Eloquent pour les modèles, migrations propres avec foreign keys
- Pas de query builder brut sauf si vraiment nécessaire
- Tests : Pest, pas PHPUnit direct
- Quand tu modifies config/questionnaire.php ou config/ai_act_rules.php, 
  vérifie systématiquement que database/seeders/DemoSeeder.php est 
  toujours cohérent. Le DemoSeeder est la démo client, il doit refléter 
  l'état exact du moteur. Si tu ne peux pas le maintenir, signale-le.


## Conventions du projet
- Tous les commentaires de code en français
- Variables et méthodes en anglais (convention Laravel)
- Models au singulier (User, AiUsage, Assessment)
- Tables au pluriel (users, ai_usages, assessments)
- Routes nommées (route('usages.index'))

## Ce qu'il faut éviter
- Pas de Livewire ni Inertia en v1, juste du Blade classique
- Pas de packages exotiques sans validation préalable
- Pas de refactoring spontané du code existant
- Pas de "et tant qu'on y est, j'ai aussi modifié X"
- Pas de design custom : utiliser des composants Tailwind UI / TallStackUI 
  basiques

## Quand tu hésites
- Demander avant d'agir si la décision a un impact structurel
- Si une feature ressemble à du scope creep, le signaler
- Toujours expliquer en français les choix non triviaux

