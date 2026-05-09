# Audit de coverage matrice ↔ moteur ↔ questionnaire

Date : 9 mai 2026
Auditeur : Claude Code (Gemini CLI)
Source de vérité : docs/Matrice de Décision AI Act.md v1.1

## Synthèse

- **Règles :** 22 règles identifiées dans la matrice (8 INA, 8 HR, 6 RL), seulement **11 implémentées** dans `config/ai_act_rules.php`. **11 règles sont MANQUANTES**.
- **Variables :** 21 variables identifiées dans la matrice (7 base + 14 cond.), **16 collectées** par le questionnaire (certaines avec des noms différents). **5 variables sont MANQUANTES**.
- **Indéclenchabilité :** La quasi-totalité des règles INACCEPTABLE (sauf R-I-05 et R-I-03 partiellement) sont **INDÉCLENCHABLES** car les variables conditionnelles spécifiques ne sont pas présentes dans le questionnaire.
- **État du comportement "usage sans réponses" :** **OK**. Le controller bloque l'évaluation et redirige vers le questionnaire. Le snapshot gère gracieusement le cas `NON_EVALUE`.

## Détail — Règles INACCEPTABLE (R-I-XX)

| Règle matrice | Clé code | Présente | Conditions OK | Variables collectées | Test Pest existant | Statut |
|---|---|---|---|---|---|---|
| R-I-01 (Émotions Travail) | - | **NON** | - | BIO_TYPE (OUI), DOM (OUI) | - | **KO** |
| R-I-02 (Bio Sensible) | - | **NON** | - | BIO_ATTR_SENSIBLES (**NON**) | - | **KO** |
| R-I-03 (Social Scoring) | art5_social_scoring | OUI | Partiel (manque DEC) | SCORING_PORTEE (**NON**) | NON | **KO** |
| R-I-04 (Vulnérabilité) | - | **NON** | - | PERSUASION_PSYCHOLOGIQUE (**NON**) | - | **KO** |
| R-I-05 (Bio Temps Réel Pub) | art5_bio_realtime_public | OUI | OK | BIO_REALTIME (OUI) | OUI | OK |
| R-I-06 (BDD Faciale) | - | **NON** | - | BIO_SOURCE_DONNEES (**NON**) | - | **KO** |
| R-I-07 (Subliminal) | - | **NON** | - | TECHNIQUES_SUBLIMINALES (**NON**) | - | **KO** |
| R-I-08 (Prédic. Criminelle) | - | **NON** | - | PREDICTION_CRIMINELLE (**NON**) | - | **KO** |

## Détail — Règles HAUT_RISQUE (R-H-XX)

| Règle matrice | Clé code | Présente | Conditions OK | Variables collectées | Test Pest existant | Statut |
|---|---|---|---|---|---|---|
| R-H-01 (Biométrie) | annexe3_bio_categorisation | OUI | Trop large (pas de check DEC) | BIO_TYPE (OUI), DEC (OUI) | NON | **A affiner** |
| R-H-02 (RH Recrutement) | annexe3_recrutement | OUI | Trop large (tout le domaine RH) | RH_USAGE (NON), PUB (NON) | OUI | **A affiner** |
| R-H-03 (RH Gestion) | annexe3_recrutement | OUI | Confondue avec R-H-02 | RH_USAGE (NON) | NON | **KO** |
| R-H-04 (Éducation) | annexe3_education | OUI | Trop large (tout le domaine) | EDUC_USAGE (NON) | NON | **A affiner** |
| R-H-05 (Crédit) | annexe3_credit | OUI | Trop large | PUB (OUI) | NON | **A affiner** |
| R-H-06 (Assurance) | - | **NON** | - | SANTE_FINALITE (NON) | - | **KO** |
| R-H-07 (Serv. Essentiels) | - | **NON** | - | USAGE_PRESTATIONS_ESSENTIELLES (NON) | - | **KO** |
| R-H-08 (Médical) | - | **NON** | - | SANTE_FINALITE (NON) | - | **KO** |

## Détail — Règles RISQUE_LIMITE (R-L-XX)

| Règle matrice | Clé code | Présente | Conditions OK | Variables collectées | Test Pest existant | Statut |
|---|---|---|---|---|---|---|
| R-L-01 (Chatbot) | art50_chatbot_llm | OUI | OK (simplifié) | INTERACTION_DIRECTE (NON) | OUI | OK |
| R-L-02 (Contenu Synthé) | art50_contenu_genere | OUI | OK | GEN_CONTENT_TYPE (OUI) | NON | OK |
| R-L-03 (Deepfake) | art50_deepfake | OUI | OK | GEN_DEEPFAKE_RISK (OUI) | OUI | OK |
| R-L-04 (Émotions Hors RH) | - | **NON** | - | BIO_TYPE (OUI) | - | **KO** |
| R-L-05 (Bio Non Sensible) | - | **NON** | - | BIO_ATTR_SENSIBLES (NON) | - | **KO** |
| R-L-06 (Texte Public) | - | **NON** | - | INTERACTION_DIRECTE (NON) | - | **KO** |

## Détail — R-H-BORDERLINE et exception Art. 6 §3

- **R-H-BORDERLINE :** **NON IMPLÉMENTÉ**. Le code ne gère pas encore de logique d'alerte "Zone Grise" qui s'ajoute sans changer le niveau de base. `AiActClassifier` a une méthode `collectAlerts` mais elle est câblée en dur et ne lit pas la matrice.
- **Exception Art. 6 §3 :** **NON IMPLÉMENTÉE**. Aucune règle dans le code ne permet de rétrograder ou de marquer comme Zone Grise un usage Haut Risque sur la base de critères de "tâche procédurale".

## Variables matrice — couverture questionnaire

| Variable | Type d'IA concerné | Collectée | Nom dans Questionnaire |
|---|---|---|---|
| TYPE | Base | OUI | (via AiUsage) |
| DOM | Base | OUI | (via AiUsage) |
| DEC | Base | OUI | (via AiUsage - mappage partiel) |
| PUB | Base | OUI | (Partiel: `impact_individual`) |
| DATA | Base | OUI | `data_personal`, `data_sensitive` |
| DIFF | Base | OUI | (Partiel: `llm_output_published`) |
| CTRL | Base | OUI | `human_oversight` |
| BIO_TYPE | IA_BIO | OUI | `bio_modality` (mappage partiel) |
| BIO_SOURCE_DONNEES | IA_BIO | **NON** | - |
| BIO_ATTR_SENSIBLES | IA_BIO | **NON** | - |
| BIO_TEMPS_REEL_PUBLIC | IA_BIO | OUI | `bio_realtime` |
| SCORING_PORTEE | IA_SCORING | **NON** | - |
| TECHNIQUES_SUBLIMINALES | Marketing/Vulner. | **NON** | - |
| PERSUASION_PSYCHOLOGIQUE | Marketing/Vulner. | **NON** | - |
| PREDICTION_CRIMINELLE | Sécurité | **NON** | - |
| RH_USAGE | RH | **NON** | - |
| EDUC_USAGE | Education | **NON** | - |
| INTERACTION_DIRECTE | LLM/Gen | **NON** | - |
| GEN_CONTENU | IA_GEN | OUI | `gen_content_type` |
| SANTE_FINALITE | Santé | **NON** | - |
| USAGE_PRESTATIONS_ESSENTIELLES | Divers | **NON** | - |

## Comportement "usage sans réponses"

- **Constat :** Dans `AssessmentController::store`, un check `! $aiUsage->responses()->exists()` bloque le calcul. C'est robuste.
- **Snapshot :** `ReportSnapshotBuilder` gère le cas où l'évaluation est nulle en retournant `NON_EVALUE`. C'est correct.
- **Recommandation :** Ajouter une validation au niveau du bouton "Générer le rapport" dans l'UI (index des rapports) pour prévenir l'utilisateur qu'un rapport sur des usages non évalués sera incomplet.

## Recommandations priorisées

### P0 (bloquant pour la prod)
1. **Mise à jour de `config/questionnaire.php`** pour inclure les ~10 variables conditionnelles manquantes (RH_USAGE, BIO_SOURCE_DONNEES, etc.).
2. **Refonte de `config/ai_act_rules.php`** pour implémenter les 22 règles de la matrice avec leurs conditions exactes (croisement variables de base + conditionnelles).
3. **Implémentation de `R-H-BORDERLINE`** : ajouter le support des alertes `FLAG_ZONE_GRISE` dans le moteur et la structure de l'évaluation.

### P1 (à faire avant la première vente)
1. **Précision des règles Haut Risque** : ne pas classer tout un domaine (ex: RH) en Haut Risque, mais uniquement si la variable `RH_USAGE` correspond à un cas de l'Annexe III (ex: triage, pas rédaction d'offres).
2. **Audit des tests Pest** : créer des tests pour chaque règle manquante (R-I-01 à R-I-08 en priorité).

### P2 (peut attendre v1.1)
1. **Support de l'exception Art. 6 §3** : permettre à l'utilisateur de déclarer une "tâche purement procédurale" pour justifier une sortie du haut risque.
2. **Amélioration du mappage `DEC` (Nature de la décision)** entre le questionnaire (radio) et la matrice (pseudo-code).
