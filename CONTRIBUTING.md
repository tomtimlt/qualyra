# Contribuer à Qualyra

## Premiers pas

```bash
git clone https://github.com/tomtimlt/qualyra.git
cd qualyra
./scripts/setup.sh
```

## Workflow Git

1. Créer une branche depuis `dev` : `git checkout dev && git checkout -b feature/ma-feature`
2. Travailler dans un worktree dédié : `git worktree add ../qualyra-ma-feature feature/ma-feature`
3. Commiter avec Conventional Commits
4. Ouvrir une Pull Request vers `dev`
5. Les releases sont taguées depuis `main`

## Conventions de commit

```
feat:     nouvelle fonctionnalité
fix:      correction de bug
docs:     documentation
refactor: refactoring sans changement fonctionnel
test:     ajout ou modification de tests
chore:    maintenance (CI, config, dépendances)
```

Exemples :
```
feat: ajoute la règle R-I-09 pour les systèmes de crédit social
fix: corrige le calcul du score dans AiActClassifier
docs: met à jour le calendrier AI Act dans le README
```

## Checklist PR

- [ ] `./scripts/check-sync.sh` passe
- [ ] `php artisan test` passe
- [ ] `./vendor/bin/pint --test` passe
- [ ] `npm run build` passe
- [ ] Documentation mise à jour si nécessaire

## Licence des contributions

Qualyra est distribué sous licence [AGPL-3.0](LICENSE). En proposant une
contribution, vous acceptez qu'elle soit publiée sous cette même licence.
