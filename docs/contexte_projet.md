# Contexte Projet — Outil de conformité AI Act + RGPD pour PME

**Document de référence à charger dans le workspace AnythingLLM**
**Dernière mise à jour : 5 mai 2026**

---

## Pitch en une phrase

Un outil web qui audite les usages d'IA d'une PME française, classifie chaque usage selon les 4 niveaux de risque AI Act (règlement UE 2024/1689) et produit un rapport PDF avec plan d'action de mise en conformité, livré pour environ 1 500 € en one-shot.

---

## Qui je suis

- **Prénom :** Thomas
- **Profil :** étudiant ingénieur en cybersécurité au CESI Nancy
- **Localisation :** Nancy, France
- **Compétences clés :**
  - Cybersécurité (formation en cours + stage en entreprise)
  - PHP (bonne maîtrise) — préfère cette stack au JS
  - GRC : déploiement de CISO Assistant en stage + intégration d'analyses de risques
  - Vocabulaire technique cyber : à l'aise (DGX Spark, prompt injection, traçabilité, etc.)
- **Mode de travail :** solo, assisté par Claude Code et autres IA pour le développement
- **Disponibilité :** 10 à 15h par semaine, en parallèle des cours et exams

---

## Pourquoi ce projet

### Genèse

Lors d'un stage, j'ai découvert et déployé CISO Assistant (outil GRC open source). J'ai trouvé l'approche structurante intéressante : centraliser, modéliser, suivre. Mais CISO Assistant cible la cyber/ISO 27001 et demande un expert pour le déployer.

J'ai voulu transposer cette logique à un sujet plus chaud et moins outillé : **la gouvernance des usages IA en PME**, qui devient critique avec :
- l'**AI Act** (échéance majeure : 2 août 2026)
- l'usage massif de ChatGPT/Claude/Copilot dans les entreprises sans cadre

### Objectif personnel

Gagner suffisamment d'argent cet été (objectif ~4 500 € net, soit 3 ventes à 1 500 €) tout en validant un produit qui pourrait grandir ensuite.

---

## État du projet (au 5 mai 2026)

### Ce qui est fait

**Validation marché**
- 1 retour CTO (**Vincent**) : a validé le besoin et le vocabulaire (Shadow AI, sécurisation de la donnée). A donné des questions pertinentes pour affiner le scope.
- 1 retour RSSI externalisé (**Nicolas Dolisy**) : a confirmé qu'il est sollicité par ses clients PME sur tout type d'usage IA. A explicité son outil idéal (DLP prompts, traçabilité, tests d'injection, conformité RGPD/NIS2). A proposé de rester en contact.

**Spécification réglementaire (semaine 1 du projet)**

Documents produits et présents dans le workspace :

1. **Guide de Conformité AI Act PME** : synthèse des 4 niveaux de risque, distinction fournisseur/déployeur, calendrier d'application, sanctions, cas pratiques PME.
2. **Matrice de Décision AI Act v1.1** : spec technique avec règles formelles (R-I-01 à R-I-08 pour inacceptable, R-H-01 à R-H-08 pour haut risque, R-L-01 à R-L-06 pour risque limité), 7 variables de base + variables conditionnelles, zones grises documentées, 5 tests de cohérence à valider.
3. **Mini-référentiel RGPD pour usages IA en PME** : AIPD (critères, matrice par usage), bases légales (alerte sur consentement salarié, intérêt légitime), droits renforcés (article 22), recommandations CNIL/EDPB, position sur les transferts hors UE (DPF, fournisseurs LLM US), checklist opérationnelle 10 points.

### Ce qui reste à faire (cadrage)

- Prompt 5 : textes du rapport PDF (paragraphes types par niveau de risque, encadrés explicatifs des obligations, plan 30/60/90 jours, disclaimer juridique)
- Validation par les 5 tests de cohérence de l'annexe 3 de la matrice

### Ce qui reste à faire (développement)

Calendrier prévisionnel à 10-15h/semaine :
- Semaine 2 (12-18 mai) : Setup Laravel + auth + modèle de données
- Semaine 3 (19-25 mai) : Déclaration d'usages + questionnaire dynamique
- Semaine 4 (26 mai - 1 juin) : Moteur de classification + tableau de synthèse
- Semaine 5 (2-8 juin) : Génération PDF + intégration Stripe
- Semaine 6 (9-15 juin) : Polish + pages légales + tests end-to-end
- Semaine 7 (16-22 juin) : Beta avec Vincent ou autre design partner
- Fin juin / début juillet : premier client payant

---

## Cible client

### Profil idéal

- **Taille** : PME 20 à 150 personnes
- **Types** : agences (web/marketing/communication), cabinets (conseil/comptable/juridique), écoles privées, organismes de formation, startups tech en croissance, PME industrielles digitalisées
- **Caractéristiques** : utilise déjà ChatGPT/Claude/Copilot mais sans cadre formel, pas de DPO ni RSSI dédiés en interne
- **Géographie** : France, départ à Nancy et Grand Est puis extension

### Personas validés

- **CTO de PME tech** (type Vincent) : sensible au sujet Shadow AI, cherche à structurer
- **RSSI externalisé / vCISO** (type Nicolas) : a un portefeuille de clients PME, peut être un canal de revente ou de recommandation
- **Dirigeant de PME en croissance** : prend conscience de la pression réglementaire (AI Act août 2026)

### Personas à ne PAS cibler en v1

- Grandes entreprises (cycle de vente trop long, exigences trop fortes)
- ETI réglementées (banque, assurance, santé) qui ont déjà des consultants
- Entités NIS2 essentielles/importantes (scope trop spécifique)

---

## Modèle économique

- **Format** : vente one-shot (pas SaaS récurrent en v1)
- **Prix** : ~1 500 € pour les premiers clients établis ; possibilité d'early bird à 490 € pour les 2-3 premiers en échange de retours détaillés
- **Livrables** :
  - Accès web temporaire à l'outil (compte client)
  - Rapport PDF d'audit (15-30 pages)
  - Plan d'action priorisé P0/P1/P2
  - Optionnellement : 1h de restitution en visio
- **Pourquoi pas SaaS** : un étudiant solo ne peut pas assurer support, hébergement et maintenance pour un SaaS. Le one-shot est plus propre, fini, payé.

---

## Stack technique choisie

### Pour la v1

- **Backend + frontend** : PHP 8.3 + **Laravel 11** (Blade pour les templates)
- **Auth** : Laravel Breeze (gratuit, intégré, 5 minutes de setup)
- **Base de données** : MySQL ou PostgreSQL
- **PDF** : DomPDF ou mPDF
- **Paiement** : Stripe Checkout (ne pas coder de tunnel custom)
- **Hébergement** : OVH, Infomaniak ou équivalent (~5 €/mois)
- **CSS** : Tailwind ou un thème Bootstrap, **pas de design custom**
- **Versioning** : Git + GitHub privé

### Pourquoi PHP/Laravel et pas Next.js

Thomas connaît déjà PHP. À 10-15h/semaine, chaque heure de friction technique coûte cher. Laravel est très bien documenté, l'IA code aussi bien en Laravel qu'en Next.js, et l'app à construire (formulaires + base + PDF + paiement) est parfaite pour du PHP server-side classique. Pas besoin de SPA.

### Ce qui est REJETÉ pour la v1

- Microservices ou architecture distribuée
- Containers Docker en prod (compliquent le déploiement)
- Authentification avancée (SSO, OAuth multi-provider)
- Mode multi-utilisateur par compte client
- API publique
- Mobile app
- Multi-langue (français only)
- Tests automatisés exhaustifs (juste end-to-end manuels)

---

## Différenciation produit

### Vis-à-vis de CISO Assistant

CISO Assistant est gratuit, open source, mais :
- demande un expert GRC pour le déployer
- est généraliste cyber/ISO, pas spécifique à l'IA
- requiert plusieurs jours de paramétrage initial

L'outil de Thomas :
- est spécifique à l'IA (AI Act + RGPD)
- est pré-paramétré : un client sans expert produit son rapport en 30 minutes
- est livré avec la matrice juridique déjà construite

### Vis-à-vis des outils AI governance américains (Credo AI, Holistic AI, Harmonic)

- Ils ciblent l'enterprise, pas la PME
- Leur pricing commence à plusieurs milliers d'euros par mois
- Ils ne sont pas conçus pour le contexte français/européen

### Vis-à-vis d'un audit cabinet conseil

- Un cabinet facture 5 000 à 15 000 € pour le même type d'audit
- L'outil de Thomas est 3 à 10 fois moins cher
- Le client garde l'autonomie pour refaire l'audit ensuite

---

## Risques et limitations connus

### Risques juridiques

- L'outil porte sur de la conformité réglementaire : risque si analyse erronée
- Mitigation : disclaimer fort dans le rapport ("ne remplace pas un avis juridique"), zones grises explicitement signalées, recommandation de consulter un juriste pour les cas complexes
- Validation idéale : faire relire la matrice par un prof de droit numérique

### Risques business

- Cycle de vente B2B PME en France : long et incertain
- "Trop tôt" : certaines PME ne sentent pas encore la pression AI Act
- Mitigation : commencer par PME tech (Vincent, Nicolas comme entrée) et écoles privées (sensibles à l'enjeu) ; positionner comme préparation à l'échéance août 2026

### Risques techniques

- Étudiant solo + 10-15h/semaine = scope serré
- Mitigation : tout ce qui n'est pas dans la spec v1 va dans un backlog "v2", pas implémenté
- Code assisté par IA : risque de spaghetti si pas de cadrage strict
- Mitigation : la matrice et le référentiel servent de spec ferme, pas de dérive

### Risques temporels

- Mai-juin : période d'examens et projets école
- Si retard : repousser le premier client à septembre, pas dramatique
- Première vente n'est pas une obligation absolue, c'est l'objectif

---

## Disclaimer pour l'IA assistante

Cet outil est un projet étudiant. Il a vocation à apporter une première analyse de conformité, pas à remplacer un audit juridique professionnel. Tout rapport généré doit comporter un disclaimer clair sur ce point.

L'utilisateur (Thomas) est étudiant ingénieur en cybersécurité, pas juriste. Pour toute question juridique pointue, il sait qu'il doit consulter une source officielle (CNIL, EUR-Lex) ou un juriste spécialisé.

---

**Fin du contexte projet.**
