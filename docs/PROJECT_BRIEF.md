# Projet : Outil de Conformité AI Act + RGPD

## Objectif produit

Application web qui audite les usages IA d'une entreprise française au regard 
de l'AI Act (règlement UE 2024/1689) et du RGPD. Le client final remplit 
un questionnaire en ligne, l'outil classifie chaque usage selon 4 niveaux 
de risque (INACCEPTABLE, HAUT_RISQUE, RISQUE_LIMITE, RISQUE_MINIMAL), 
et génère un rapport PDF avec plan d'action.

Modèle : vente one-shot ~1500€. Premier client visé début juillet 2026.

## Stack imposée

- PHP 8.3
- Laravel 11
- Blade (pas de SPA, pas de Vue, pas de React)
- MySQL 8 ou PostgreSQL 16 (au choix dev)
- Tailwind CSS (via Vite)
- Laravel Breeze pour l'authentification
- DomPDF ou mPDF pour la génération PDF
- Stripe Checkout pour le paiement
- Hébergement OVH ou Infomaniak (mutualisé PHP, pas de Docker en prod)

## Contraintes strictes

- Solo développeur, 10-15h/semaine
- Premier MVP fin juin 2026
- PAS de microservices, PAS de Docker en prod, PAS d'API publique
- PAS de multi-utilisateurs par compte client (1 compte = 1 organisation)
- PAS de mode collaboratif
- PAS de versioning des analyses
- PAS de multi-langue (français only)
- Code simple, lisible, maintenable par une seule personne
- Privilégier les conventions Laravel, ne pas réinventer la roue

## Phasage

- Semaine 2 (12-18 mai) : setup Laravel + Breeze + modèle de données 
  + déploiement initial — EN COURS
- Semaine 3 : formulaire de déclaration d'usages + questionnaire dynamique
- Semaine 4 : moteur de classification (encoder la matrice AI Act)
- Semaine 5 : génération PDF + Stripe Checkout
- Semaine 6 : polish + pages légales + tests end-to-end
- Semaine 7 : beta avec design partner
- Semaine 8 : premier client payant

## Ce que Claude Code doit savoir sur moi

- Étudiant ingénieur cyber, je code en PHP mais je ne suis pas senior 
  Laravel
- Je veux du code Laravel idiomatique, conforme aux conventions
- Préférer la simplicité à la sophistication
- Si un choix peut casser plus tard, m'avertir, ne pas décider seul
- Pas de sur-ingénierie : si une feature peut attendre, ne pas la coder
- Réponses et commentaires de code en français
