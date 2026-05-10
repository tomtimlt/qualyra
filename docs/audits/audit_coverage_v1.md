# Audit de coverage matrice ↔ moteur ↔ questionnaire

Date : 2026-05-10
Auditeur : Claude Code (Opus 4.7)
Source de vérité : [docs/Matrice de Décision AI Act.md](../Matrice%20de%20Décision%20AI%20Act.md) — version 1.1
Périmètre code : [config/ai_act_rules.php](../../config/ai_act_rules.php), [config/questionnaire.php](../../config/questionnaire.php), [app/Services/AiActClassifier.php](../../app/Services/AiActClassifier.php), [app/Http/Controllers/AssessmentController.php](../../app/Http/Controllers/AssessmentController.php), [app/Services/ReportSnapshotBuilder.php](../../app/Services/ReportSnapshotBuilder.php), [tests/Feature/AssessmentTest.php](../../tests/Feature/AssessmentTest.php)
Statut : audit-only — aucune modification de code applicatif effectuée.

---

## Synthèse

- **Règles matrice :** 23 (8 INACCEPTABLE + 8 HAUT_RISQUE + 1 R-H-BORDERLINE + 6 RISQUE_LIMITE) + 1 fallback DEFAULT.
- **Règles code :** 11 règles configurées (hors fallback). **2 INACCEPTABLE, 6 HAUT_RISQUE, 3 RISQUE_LIMITE.**
- **Règles manquantes :** **13** (R-I-01, R-I-02, R-I-04, R-I-06, R-I-07, R-I-08, R-H-06, R-H-07, R-H-08, R-H-BORDERLINE, R-L-04, R-L-05, R-L-06).
- **Règles présentes mais divergentes / trop laxistes :** **9 sur 11** (toutes sauf un strict respect de R-I-05 et r-l-03 sur le déclencheur, et même celles-ci sont incomplètes).
- **Règle "maison" non présente dans la matrice :** 1 (`haut_risque_decision_automatique_impactante`) — sur-classifie certains cas.
- **Variables matrice :** 21 (7 base + 14 conditionnelles). **6 réellement collectées avec sémantique correcte**, **6 partielles ou mal mappées**, **9 absentes**.
- **Règles INDÉCLENCHABLES en pratique** (variable manquante côté questionnaire, même si la règle était implémentée) : **17 sur 23**.
- **Scénarios de cohérence Annexe 3 de la matrice :** **2 sur 5 échouent** (scénario 4 — émotions employés — classé HAUT_RISQUE au lieu d'INACCEPTABLE ; scénario 5 — Copilot interne — classé RISQUE_LIMITE au lieu de RISQUE_MINIMAL).
- **Comportement "usage sans réponses" :** **OK au niveau du controller** (redirection avant calcul). **Risque résiduel** : `AiActClassifier::classify()` appelé directement (script, console, futur ReportController) retomberait silencieusement sur le fallback `RISQUE_MINIMAL`. Pas de garde dans le service.

> **Verdict global : le moteur n'est pas en état de production.** Sur 23 règles attendues, seules 2 (R-I-05 et — partiellement — R-L-03) sont à la fois présentes et correctes sur leurs conditions de déclenchement. Les autres règles présentes sont systématiquement plus larges que la matrice (déclenchement par `domain` seul, par `type` seul) et produisent des classifications fausses dans les deux sens (sur- et sous-classification).

---

## Convention de notation utilisée dans les tableaux

- **Présente** : OUI = règle identifiable dans `config/ai_act_rules.php`. NON = absente.
- **Conditions OK** : OUI = même tuple de variables que la matrice. PARTIEL = sous-ensemble. KO = trop large / mauvaise variable / mauvaise valeur.
- **Variables collectables** : pour chaque variable testée par la règle de la matrice, vérifier qu'elle est posée par `config/questionnaire.php`.
- **Test Pest** : OUI = couverture explicite dans [AssessmentTest.php](../../tests/Feature/AssessmentTest.php).
- **Statut** : OK / À AFFINER / KO / MANQUANTE.

---

## Détail — Règles INACCEPTABLE (R-I-XX)

| Règle | Clé code | Présente | Conditions OK | Variables collectables | Test Pest | Statut |
|---|---|---|---|---|---|---|
| **R-I-01** Émotions travail/éducation | — | NON | — | `BIO_TYPE=RECOG_EMOTIONS` **non collecté** | NON | **MANQUANTE** |
| **R-I-02** Catégorisation bio attributs sensibles | — | NON | — | `BIO_TYPE=CATEGORISATION` et `BIO_ATTR_SENSIBLES` **non collectés** | NON | **MANQUANTE** |
| **R-I-03** Social scoring global | `art5_social_scoring` | OUI | **KO** : utilise `response.scoring_target='social'` (variable inventée) au lieu de `SCORING_PORTEE=GLOBAL_MULTI_DOMAINES` ; ne vérifie pas `DEC ∈ {SEMI_AUTO, FULL_AUTO}` | `SCORING_PORTEE` **non collectée** ; `DEC` **non collectée** | NON | **KO** |
| **R-I-04** Vulnérabilité marketing | — | NON | — | `PERSUASION_PSYCHOLOGIQUE`, `PUB ∋ VULNERABLES` **non collectés** | NON | **MANQUANTE** |
| **R-I-05** Bio temps réel public | `art5_bio_realtime_public` | OUI | **PARTIEL** : matche `type=IA_BIO + bio_realtime=yes` sans vérifier `BIO_TYPE=IDENTIFICATION`. Article cité Art. 5 §1 (h) ✅ correct (v1.1). **`raison` factuellement fausse** : mentionne « à des fins répressives » alors que la règle vise précisément les acteurs privés (les forces de l'ordre sont *exceptées*) | `BIO_TYPE=IDENTIFICATION` **non collecté** (la réponse `bio_modality` ne distingue pas identification / contrôle d'accès / catégorisation) | OUI | **À AFFINER** |
| **R-I-06** BDD faciale scraping | — | NON | — | `BIO_SOURCE_DONNEES` **non collectée** | NON | **MANQUANTE** |
| **R-I-07** Manipulation subliminale | — | NON | — | `TECHNIQUES_SUBLIMINALES` **non collectée** | NON | **MANQUANTE** |
| **R-I-08** Prédiction criminelle | — | NON | — | `PREDICTION_CRIMINELLE` **non collectée** | NON | **MANQUANTE** |

**Bilan INACCEPTABLE : 1/8 partiellement correcte, 1/8 KO, 6/8 manquantes.**

> ⚠️ **Risque juridique majeur — R-I-01.** Une PME utilisant un outil de reconnaissance d'émotions sur ses salariés (entretien vidéo, surveillance call-center) verra aujourd'hui son usage classé **HAUT_RISQUE** par `annexe3_bio_categorisation`, et lira un message lui indiquant qu'il « suffit » de respecter les obligations Art. 26. La pratique est en réalité **prohibée depuis le 2 février 2025** et expose à 35 M€ ou 7 % du CA mondial (Art. 99). Voir scénario 4 plus bas.

---

## Détail — Règles HAUT_RISQUE (R-H-XX)

| Règle | Clé code | Présente | Conditions OK | Variables collectables | Test Pest | Statut |
|---|---|---|---|---|---|---|
| **R-H-01** Biométrie identif./contrôle accès | `annexe3_bio_categorisation` | OUI | **KO** : matche dès `type=IA_BIO` seul. Ne vérifie ni `BIO_TYPE ∈ {IDENTIFICATION, CONTROLE_ACCES}` ni `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`. Le nom de la clé évoque la *catégorisation* alors que la règle matcheuse couvre toute IA bio | `BIO_TYPE` non collecté ; `DEC` non collectée | NON | **KO** (trop large) |
| **R-H-02** RH recrutement | `annexe3_recrutement` | OUI | **KO** : matche dès `domain=RH` seul. Aucune vérification de `DEC`, `PUB ∋ EMPLOYES`, ni `RH_USAGE ∈ {TRI_CV, SCORING_CANDIDATS, DECISION_EMBAUCHE}`. Classe en HR un usage de simple rédaction d'offres LLM (cf. matrice §C, cas non haut risque RH) | `RH_USAGE`, `PUB`, `DEC` **non collectés** | OUI (test couvre le déclenchement basique) | **KO** (trop large) |
| **R-H-03** RH gestion / surveillance / éval. salariés | `annexe3_recrutement` | OUI (par confusion) | **KO** : couvert par la même règle trop large que R-H-02. Pas de discrimination entre recrutement et gestion. Aucune vérification de `RH_USAGE ∈ {EVAL_PERFORMANCE, SURVEILLANCE_COMPORTEMENT, ATTRIBUTION_TACHES, DECISION_PROMOTION, DECISION_LICENCIEMENT}` | idem R-H-02 | NON | **KO** (fusionnée à tort avec R-H-02) |
| **R-H-04** Éducation admission/évaluation/orientation | `annexe3_education` | OUI | **KO** : matche dès `domain=EDUCATION` seul. Aucune vérification de `DEC` ni `EDUC_USAGE ∈ {ADMISSION, ORIENTATION, EVALUATION_ACADEMIQUE}` (la matrice exclut `RECOMMENDATION_CONTENU`) | `EDUC_USAGE`, `DEC` **non collectés** | NON | **KO** (trop large) |
| **R-H-05** Crédit / solvabilité | `annexe3_credit` | OUI | **KO** : matche dès `domain=CREDIT` seul. Aucune vérification de `DEC` ni `PUB ∋ {CLIENTS, GRAND_PUBLIC}` (la matrice exclut le B2B pur, cf. ZG-06) | `PUB`, `DEC` **non collectées** | NON | **KO** (trop large) |
| **R-H-06** Assurance santé / vie | — | NON | — | `SANTE_FINALITE`, `PUB`, `DEC` **non collectés** | NON | **MANQUANTE** |
| **R-H-07** Services essentiels | — | NON | — | `USAGE_PRESTATIONS_ESSENTIELLES`, `PUB`, `DEC` **non collectés** | NON | **MANQUANTE** |
| **R-H-08** Décision médicale clinique | — | NON | — | `SANTE_FINALITE=DECISION_MEDICALE` **non collecté** | NON | **MANQUANTE** |
| _Doublon_ `annexe3_scoring_recrutement` | OUI | — | Doublon de R-H-02 sur `type=IA_SCORING + scoring_target=recruitment`. Sera systématiquement court-circuité par `annexe3_recrutement` (qui matche d'abord sur `domain=RH`) → règle **morte** dans la pratique | — | NON | **KO** (mort/redondante) |
| _Maison_ `haut_risque_decision_automatique_impactante` | OUI | — | **Hors matrice v1.1.** INTERPRÉTATION qui matche `impact_individual=yes + human_oversight=never`. Risque de sur-classification (la matrice n'utilise nulle part `impact_individual`, et l'absence de contrôle humain est traitée par la matrice comme `AGGRAVATION` *sans changer le niveau de base*) | `impact_individual` collectée ; `human_oversight` collectée | OUI | **À RETIRER ou aligner sur AGGRAVATION** |

**Bilan HAUT_RISQUE : 0/8 strictement conformes, 5/8 trop larges, 3/8 manquantes, +1 doublon mort, +1 règle maison hors matrice.**

---

## Détail — Règles RISQUE_LIMITE (R-L-XX)

| Règle | Clé code | Présente | Conditions OK | Variables collectables | Test Pest | Statut |
|---|---|---|---|---|---|---|
| **R-L-01** Chatbot interaction directe | `art50_chatbot_llm` | OUI | **KO** : matche dès `type=LLM_GEN` seul. Ne vérifie pas `DIFF ∈ {TIERS, PUBLIC}`, ni `PUB ∋ {CLIENTS, GRAND_PUBLIC, VULNERABLES}`, ni `INTERACTION_DIRECTE=OUI`. Conséquence : tout LLM interne (Copilot mail, résumé réunion) est sur-classé `RISQUE_LIMITE` | `DIFF`, `PUB`, `INTERACTION_DIRECTE` **non collectés** | OUI (test couvre une interprétation laxiste) | **KO** (déclenche en interne) |
| **R-L-02** Contenu synthétique image/audio/vidéo | `art50_contenu_genere` | OUI | **PARTIEL** : matche dès `type=IA_GEN`, sans vérifier `GEN_CONTENU ∈ {IMAGE, AUDIO, VIDEO}` ni `DIFF ∈ {TIERS, PUBLIC}`. La valeur `mixed` du questionnaire n'est pas dans la matrice. La valeur `TEXTE` (qui doit aller en R-L-06) ne peut pas être saisie | `GEN_CONTENU` **partiel** (manque `TEXTE`, `DEEPFAKE`) ; `DIFF` **non collectée** | NON | **À AFFINER** |
| **R-L-03** Deepfake | `art50_deepfake` | OUI | **PARTIEL** : matche `type=IA_GEN + gen_deepfake_risk=yes`. La matrice attendait `GEN_CONTENU=DEEPFAKE` (variable factorisée avec le type de contenu). Pas de vérification `DIFF ∈ {TIERS, PUBLIC}`. Logique fonctionnelle mais désalignée avec la matrice | `gen_deepfake_risk` ✅ ; `DIFF` **non collectée** | OUI | **À AFFINER** |
| **R-L-04** Émotions hors RH/éduc | — | NON | — | `BIO_TYPE=RECOG_EMOTIONS` **non collecté** | NON | **MANQUANTE** |
| **R-L-05** Catégorisation bio non sensible | — | NON | — | `BIO_TYPE=CATEGORISATION + BIO_ATTR_SENSIBLES=NON` **non collectés** | NON | **MANQUANTE** |
| **R-L-06** Texte IA public | — | NON | — | `GEN_CONTENU=TEXTE`, `DIFF=PUBLIC`, `INTERACTION_DIRECTE=NON` **non collectés** | NON | **MANQUANTE** |

**Bilan RISQUE_LIMITE : 0/6 strictement conformes, 3/6 trop larges, 3/6 manquantes.**

---

## Détail — R-H-BORDERLINE et exception Art. 6 §3

### R-H-BORDERLINE (zone grise RH déclarée informative)

- **Statut : NON IMPLÉMENTÉE.**
- La matrice attendait : si `DOM=RH` + `RH_USAGE ∈ {TRI_CV, SCORING_CANDIDATS, EVAL_PERFORMANCE, SURVEILLANCE_COMPORTEMENT, DECISION_EMBAUCHE, DECISION_PROMOTION, DECISION_LICENCIEMENT}` + `DEC=INFORMATIF` + `PUB ∋ EMPLOYES` → ajouter une alerte `FLAG_ZONE_GRISE` **sans court-circuiter la classification** (continuer vers défaut RISQUE_MINIMAL avec alerte forte).
- Code actuel : `AiActClassifier::collectAlerts()` est figé en dur sur 4 cas (RGPD art 9, RGPD art 22, LLM sans relecture, IA_GEN non étiquetée). Ne lit pas la matrice. Pas de support du concept `FLAG_ZONE_GRISE`.
- Pas de structure côté `Assessment` pour distinguer `niveau` (classification) d'un éventuel `flags[]` non classificatoire.

### Exception Art. 6 §3 (système Annexe III sans risque significatif)

- **Statut : NON IMPLÉMENTÉE.**
- La matrice est explicite : « ne pas appliquer Art. 6 §3 de façon automatique. Traiter comme HAUT_RISQUE par défaut » jusqu'à publication des actes délégués. Donc l'absence d'implémentation par défaut est *correcte sur le principe*.
- En revanche, **le cas `[NON HAUT_RISQUE — rédaction d'offres uniquement]`** (LLM_GEN + RH + DEC=INFORMATIF + RH_USAGE=REDACTION_OFFRES → sortir du haut risque) **n'est pas géré** : aujourd'hui ce cas est sur-classé `HAUT_RISQUE` par `annexe3_recrutement`.

### AGGRAVATION (CTRL=AUCUN sur HAUT_RISQUE)

- **Statut : NON IMPLÉMENTÉE en tant qu'alerte non classificatoire.** À la place, `haut_risque_decision_automatique_impactante` *bascule* le niveau à HAUT_RISQUE, ce qui inverse l'intention de la matrice (la matrice voulait ajouter une alerte SANS modifier le niveau).

---

## Variables matrice — couverture questionnaire

### Variables de base (toujours posées par la matrice)

| Variable | Stockage attendu | Collectée ? | Mappage actuel | Verdict |
|---|---|---|---|---|
| `TYPE` | TYPE en {LLM_GEN, IA_GEN, IA_SCORING, IA_BIO, AUTRE} | ✅ | `ai_usages.type` (enum aligné) | OK |
| `DOM` | DOM en {RH, EDUCATION, CREDIT, SANTE, SECURITE, MARKETING, PROD_INT, DEV_LOG, AUTRE} | ✅ | `ai_usages.domain` (enum aligné) | OK |
| `DEC` | {INFORMATIF, AIDE_DEC, SEMI_AUTO, FULL_AUTO} | **❌** | Aucune question dédiée. `impact_individual` (yes/no) + `human_oversight` (always/sometimes/never) ne reconstruisent pas la nature décisionnelle | **MANQUANT — bloquant pour 12 règles** |
| `PUB` | multi-valeur dans {AUCUN, EMPLOYES, CLIENTS, GRAND_PUBLIC, VULNERABLES} | **❌** | Aucune question. `impact_individual` (yes/no) ne capture ni la nature ni la multi-valeur | **MANQUANT — bloquant pour 7 règles** |
| `DATA` | {PAS_PERSO, PERSO_STD, SENSIBLE} | ⚠️ partiel | `data_personal` + `data_sensitive` (chacun yes/no/unknown) reconstruisent l'info, mais à 3 valeurs ⨯ 3 valeurs au lieu d'une enum à 3. Mappage exploitable mais lourd | OK reconstructible |
| `DIFF` | {INTERNE, TIERS, PUBLIC} | **❌** | Aucune question commune. Pour LLM_GEN seul : `llm_output_published` est binaire et insuffisant | **MANQUANT — bloquant pour 5 règles** |
| `CTRL` | {SYSTEMATIQUE, ECHANTILLON, AUCUN} | ✅ | `human_oversight` (always/sometimes/never) — équivalence 1-1 | OK |

### Variables conditionnelles

| Variable | Type d'IA / contexte | Collectée ? | Mappage actuel | Règles bloquées si absente |
|---|---|---|---|---|
| `BIO_TYPE` | IA_BIO | ⚠️ partiel | `bio_modality` (face/voice/fingerprint/iris/gait/other) ≠ matrice (IDENTIFICATION/RECOG_EMOTIONS/CATEGORISATION/CONTROLE_ACCES). **Sémantiques orthogonales** | R-I-01, R-I-02, R-I-05 (filtre exact), R-I-06, R-H-01, R-L-04, R-L-05 |
| `BIO_SOURCE_DONNEES` | IA_BIO | **❌** | — | R-I-06 |
| `BIO_ATTR_SENSIBLES` | IA_BIO + CATEGORISATION | **❌** | — | R-I-02, R-L-05 |
| `BIO_TEMPS_REEL_PUBLIC` | IA_BIO + DOM=SECURITE | ✅ | `bio_realtime` (yes/no) | — |
| `SCORING_PORTEE` | IA_SCORING | **❌** | `scoring_target` (credit/recruitment/insurance/education/social/other) **n'est pas la portée**, c'est le secteur | R-I-03 |
| `TECHNIQUES_SUBLIMINALES` | DOM=MARKETING ou PUB∋VULNERABLES | **❌** | — | R-I-07 + alerte FLAG_ZONE_GRISE marketing |
| `PERSUASION_PSYCHOLOGIQUE` | DOM=MARKETING + PUB∋VULNERABLES | **❌** | — | R-I-04 |
| `PREDICTION_CRIMINELLE` | DOM=SECURITE + IA_SCORING | **❌** | — | R-I-08 |
| `RH_USAGE` | DOM=RH + DEC dans aide-décision | **❌** | — | R-H-02, R-H-03, R-H-BORDERLINE, exception RH-rédaction-offres |
| `EDUC_USAGE` | DOM=EDUCATION + DEC dans aide-décision | **❌** | — | R-H-04, exception EDUCATION recommandation |
| `INTERACTION_DIRECTE` | LLM/IA_GEN + DIFF tiers/public | **❌** | — | R-L-01, R-L-06 |
| `GEN_CONTENU` | IA_GEN | ⚠️ partiel | `gen_content_type` (image/audio/video/mixed) — manque `TEXTE` et `DEEPFAKE` (qui sont dans la matrice). `gen_deepfake_risk` (yes/no) est une variable parallèle non prévue par la matrice | R-L-02 (partiel), R-L-03 (logique), R-L-06 |
| `SANTE_FINALITE` | DOM=SANTE + DEC dans aide-décision | **❌** | — | R-H-06, R-H-08 |
| `USAGE_PRESTATIONS_ESSENTIELLES` | DEC + PUB grand public + DOM | **❌** | — | R-H-07 |

**Bilan : 3 variables de base parfaitement collectées (TYPE, DOM, CTRL), 1 reconstructible (DATA), 3 manquantes (DEC, PUB, DIFF). 2 conditionnelles correctes (BIO_TEMPS_REEL_PUBLIC), 3 partielles ou désalignées (BIO_TYPE, GEN_CONTENU, SCORING_PORTEE), 9 absentes.**

---

## Test de coverage par simulation

Pour chaque règle, scénario type → résultat attendu (matrice) → résultat réel (code actuel).

### INACCEPTABLE

| Règle | Scénario test | Variables nécessaires | Collectables ? | Niveau attendu | Niveau code actuel | Test Pest |
|---|---|---|---|---|---|---|
| R-I-01 | Reconnaissance d'émotions sur salariés (entretien vidéo) | TYPE=IA_BIO, BIO_TYPE=RECOG_EMOTIONS, DOM=RH | ❌ BIO_TYPE | INACCEPTABLE | **HAUT_RISQUE** (annexe3_bio_categorisation) | Manque |
| R-I-02 | Caméra catégorisant l'origine ethnique des passants | TYPE=IA_BIO, BIO_TYPE=CATEGORISATION, BIO_ATTR_SENSIBLES=OUI | ❌ | INACCEPTABLE | HAUT_RISQUE | Manque |
| R-I-03 | Notation sociale multi-domaines automatique | TYPE=IA_SCORING, SCORING_PORTEE=GLOBAL, DEC=FULL_AUTO | ❌ | INACCEPTABLE | RISQUE_MINIMAL si `scoring_target≠social`, INACCEPTABLE si `=social` (mais sans contrôle DEC) | Manque |
| R-I-04 | Marketing exploitant la précarité de personnes endettées | DOM=MARKETING, PUB∋VULNERABLES, PERSUASION_PSYCHOLOGIQUE=OUI | ❌ | INACCEPTABLE | RISQUE_MINIMAL | Manque |
| R-I-05 | Caméra reco faciale temps réel dans un magasin | TYPE=IA_BIO, BIO_TYPE=IDENTIFICATION, BIO_TEMPS_REEL_PUBLIC=OUI | ⚠️ (BIO_TYPE pas filtré) | INACCEPTABLE | INACCEPTABLE ✅ (mais matche aussi par erreur sur contrôle d'accès employés) | OUI |
| R-I-06 | Constitution BDD faciale par scraping web | TYPE=IA_BIO, BIO_SOURCE_DONNEES=SCRAPING_INTERNET | ❌ | INACCEPTABLE | HAUT_RISQUE | Manque |
| R-I-07 | Outil avec techniques subliminales | TECHNIQUES_SUBLIMINALES=OUI | ❌ | INACCEPTABLE | RISQUE_MINIMAL | Manque |
| R-I-08 | Profilage criminel d'individus | DOM=SECURITE, TYPE=IA_SCORING, PREDICTION_CRIMINELLE=OUI | ❌ | INACCEPTABLE | RISQUE_MINIMAL | Manque |

### HAUT_RISQUE

| Règle | Scénario test | Variables nécessaires | Collectables ? | Niveau attendu | Niveau code actuel | Test Pest |
|---|---|---|---|---|---|---|
| R-H-01 | Badge d'accès biométrique aux locaux | TYPE=IA_BIO, BIO_TYPE=CONTROLE_ACCES, DEC≥AIDE_DEC | ❌ BIO_TYPE/DEC | HAUT_RISQUE | HAUT_RISQUE ✅ (par accident) | Manque |
| R-H-02 | Logiciel SaaS de tri de CV | DOM=RH, DEC≥AIDE_DEC, PUB∋EMPLOYES, RH_USAGE=TRI_CV | ❌ | HAUT_RISQUE | HAUT_RISQUE ✅ (par accident — déclenche aussi pour rédaction d'offres) | OUI (mais teste le cas trop large) |
| R-H-03 | Évaluation de performance des télétravailleurs | DOM=RH, RH_USAGE=EVAL_PERFORMANCE, DEC≥AIDE_DEC | ❌ | HAUT_RISQUE | HAUT_RISQUE ✅ (par accident) | Manque |
| R-H-04 | Notation automatique de copies | DOM=EDUCATION, EDUC_USAGE=EVALUATION_ACADEMIQUE, DEC≥AIDE_DEC | ❌ | HAUT_RISQUE | HAUT_RISQUE ✅ (par accident) | Manque |
| R-H-05 | Outil fintech de scoring crédit | DOM=CREDIT, DEC≥AIDE_DEC, PUB∋{CLIENTS, GRAND_PUBLIC} | ❌ | HAUT_RISQUE | HAUT_RISQUE ✅ (par accident — déclenche aussi pour B2B pur) | Manque |
| R-H-06 | Insurtech tarifant un contrat santé | DOM=SANTE, SANTE_FINALITE=ASSURANCE_RISQUE, DEC≥AIDE_DEC | ❌ | HAUT_RISQUE | RISQUE_MINIMAL | Manque |
| R-H-07 | Aide sociale conditionnée par IA | USAGE_PRESTATIONS_ESSENTIELLES=OUI | ❌ | HAUT_RISQUE | RISQUE_MINIMAL | Manque |
| R-H-08 | IA d'aide au diagnostic médical | DOM=SANTE, SANTE_FINALITE=DECISION_MEDICALE, DEC≥AIDE_DEC | ❌ | HAUT_RISQUE + ZG | RISQUE_MINIMAL | Manque |
| R-H-BORDERLINE | LLM analysant les CV en RH déclaré informatif | DOM=RH, RH_USAGE=TRI_CV, DEC=INFORMATIF | ❌ | RISQUE_MINIMAL + flag ZG | HAUT_RISQUE (sur-classement) | Manque |

### RISQUE_LIMITE

| Règle | Scénario test | Variables nécessaires | Collectables ? | Niveau attendu | Niveau code actuel | Test Pest |
|---|---|---|---|---|---|---|
| R-L-01 | Chatbot SAV sur site e-commerce | TYPE=LLM_GEN, DIFF∈{TIERS, PUBLIC}, INTERACTION_DIRECTE=OUI | ❌ DIFF/INTERACTION | RISQUE_LIMITE | RISQUE_LIMITE ✅ (mais déclenche aussi pour usage interne) | OUI (mais test laxiste) |
| R-L-02 | Newsletter avec illustrations IA | TYPE=IA_GEN, GEN_CONTENU=IMAGE, DIFF=PUBLIC | ⚠️ | RISQUE_LIMITE | RISQUE_LIMITE ✅ (par accident) | Manque |
| R-L-03 | Vidéo deepfake promotionnelle | TYPE=IA_GEN, GEN_CONTENU=DEEPFAKE, DIFF∈{TIERS, PUBLIC} | ⚠️ (via gen_deepfake_risk) | RISQUE_LIMITE | RISQUE_LIMITE ✅ | OUI |
| R-L-04 | Outil d'analyse d'humeur de visiteurs en magasin | TYPE=IA_BIO, BIO_TYPE=RECOG_EMOTIONS, DOM=MARKETING | ❌ | RISQUE_LIMITE | HAUT_RISQUE (sur-classement) | Manque |
| R-L-05 | Segmentation comportementale d'audience par caméra | TYPE=IA_BIO, BIO_TYPE=CATEGORISATION, BIO_ATTR_SENSIBLES=NON | ❌ | RISQUE_LIMITE | HAUT_RISQUE (sur-classement) | Manque |
| R-L-06 | Article de blog rédigé par LLM publié | TYPE=LLM_GEN, GEN_CONTENU=TEXTE, DIFF=PUBLIC, INTERACTION_DIRECTE=NON | ❌ | RISQUE_LIMITE | RISQUE_LIMITE ✅ (par accident, via art50_chatbot_llm) | Manque |

### Validation Annexe 3 de la matrice (5 scénarios de cohérence)

| # | Scénario matrice | Niveau attendu | Niveau code | Verdict |
|---|---|---|---|---|
| 1 | LLM résumant des CV (informatif) | HAUT_RISQUE + ZG-01 | HAUT_RISQUE (sans ZG) | ⚠️ niveau OK par accident, alerte ZG manquante |
| 2 | IA scorant les CV automatiquement (FULL_AUTO, CTRL=AUCUN) | HAUT_RISQUE + AGGRAVATION | HAUT_RISQUE (mais via `haut_risque_decision_automatique_impactante`, pas via Annexe III §4) | ⚠️ niveau OK, raison incorrecte, alerte AGGRAVATION manquante |
| 3 | Chatbot SAV (LLM, public) | RISQUE_LIMITE | RISQUE_LIMITE | ✅ |
| 4 | **Reconnaissance émotions employés** | **INACCEPTABLE** | **HAUT_RISQUE** | ❌ **BUG MAJEUR** (sous-classement d'une pratique prohibée) |
| 5 | **Copilot pour mails internes** | **RISQUE_MINIMAL** | **RISQUE_LIMITE** | ❌ **BUG** (sur-classement systématique de tout LLM) |

**Score : 3/5 niveaux corrects, dont 2 par accident sans la justification de la matrice. 2/5 faux niveaux dont 1 critique.**

---

## Comportement « usage sans réponses »

### Ce qui se passe aujourd'hui

1. **Bouton "Évaluer" / `POST /usages/{aiUsage}/assessment`** : [AssessmentController::store](../../app/Http/Controllers/AssessmentController.php#L26-L42) vérifie `! $aiUsage->responses()->exists()` et **redirige** vers le questionnaire avec `status=questionnaire-required`. **Pas de crash, pas de classification erronée.** Test Pest existant ([AssessmentTest.php:191](../../tests/Feature/AssessmentTest.php#L191-L201)).

2. **Génération d'un rapport** : [ReportSnapshotBuilder::build](../../app/Services/ReportSnapshotBuilder.php) ne calcule pas, il *lit* le dernier `Assessment` persisté. Si aucun n'existe pour un usage, `'assessment' => null` dans le snapshot, et `summarize()` compte l'usage dans `NON_EVALUE`. **Comportement correct.**

3. **Appel direct `AiActClassifier::classify()`** (script Artisan, futur webhook, futur `ReportController` qui forcerait un calcul) sur un usage *sans* responses : **le service ne fait pas de garde**. Aucune règle hors fallback ne matchera (toutes leurs conditions échoueront sur `null`), et le résultat sera **`RISQUE_MINIMAL` via `fallback_risque_minimal`** — autrement dit un **fallback silencieux** rassurant à tort.

### Verdict

- Côté **flux UI standard : OK.**
- Côté **service : risque latent.** Si demain un bouton "Recalculer toute l'organisation" ou un job de fond appelle `classify()` sans contrôle, il créera des `Assessment` `RISQUE_MINIMAL` factices.
- **Recommandation P1 :** Ajouter en tête de `AiActClassifier::classify()` une garde `if ($aiUsage->responses->isEmpty()) return ['niveau' => 'NON_EVALUE', ...]` plutôt que se reposer sur la discipline des appelants.

---

## Bugs critiques identifiés (à corriger en session dédiée — *non corrigés ici*)

1. **[BLOQUANT]** R-I-01 absent — reconnaissance d'émotions employés sous-classée HAUT_RISQUE au lieu d'INACCEPTABLE. Risque légal direct pour le client (Art. 99, sanctions immédiates depuis 02/2025).
2. **[BLOQUANT]** R-I-04, R-I-06, R-I-07, R-I-08 absents — toutes les pratiques prohibées sans variable conditionnelle dédiée passent en RISQUE_MINIMAL. L'outil rassure à tort sur des pratiques interdites.
3. **[BLOQUANT]** Variable `DEC` jamais collectée — empêche TOUTES les règles haut risque Annexe III de respecter leur condition de déclenchement (qui exige `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`). Les règles actuelles « marchent » uniquement parce qu'elles sont trop laxistes.
4. **[BLOQUANT]** Variable `PUB` jamais collectée — empêche R-H-02, R-H-05, R-H-06, R-H-07, R-I-04, R-L-01, R-L-06 de filtrer correctement.
5. **[GRAVE]** `art50_chatbot_llm` matche dès `type=LLM_GEN` sans condition. Sur-classification systématique de tous les LLM internes (Copilot, GPT pour résumé, Claude pour mails). Crée du faux positif chez tous les clients PME et discrédite l'outil.
6. **[GRAVE]** `art5_bio_realtime_public.raison` factuellement incorrecte (mentionne « à des fins répressives » alors que la règle vise les acteurs privés). Sortira tel quel dans le PDF du client.
7. **[GRAVE]** `art5_bio_realtime_public` matche sans vérifier `BIO_TYPE=IDENTIFICATION`. Un système de contrôle d'accès biométrique en temps réel (badge d'entreprise) sera classé INACCEPTABLE à tort, alors qu'il devrait être HAUT_RISQUE (R-H-01).
8. **[GRAVE]** `annexe3_recrutement` matche dès `domain=RH` sans `RH_USAGE`. Le cas explicite « LLM rédaction d'offres » que la matrice exclut explicitement du HAUT_RISQUE est aujourd'hui faux positif.
9. **[GRAVE]** R-L-04 et R-L-05 absents → reconnaissance d'émotions hors RH et catégorisation bio non sensible sont sur-classées HAUT_RISQUE par `annexe3_bio_categorisation`.
10. **[MOYEN]** `haut_risque_decision_automatique_impactante` est une règle « maison » non présente dans la matrice. Devrait être convertie en `AGGRAVATION` non classificatoire conformément à la spec.
11. **[MOYEN]** `annexe3_scoring_recrutement` est une règle morte (court-circuitée par `annexe3_recrutement`). À supprimer ou réordonner.
12. **[MOYEN]** R-H-BORDERLINE absent — pas de zone grise RH informatif → pas de remontée d'alerte forte sur ZG-01 (le cas le plus probable chez les clients PME : LLM utilisé en RH déclaré « informatif »).
13. **[MOYEN]** AGGRAVATION (CTRL=AUCUN sur HAUT_RISQUE) non émise comme alerte non classificatoire. Article 26 §2 obligation effective de contrôle humain non remontée.
14. **[MINEUR]** `bio_modality` (face/voice/iris…) est orthogonal à la sémantique matrice de `BIO_TYPE` (identification/émotions/catégorisation/contrôle d'accès). Ces deux dimensions sont indépendantes — il faut les deux questions.
15. **[MINEUR]** `gen_content_type` ne propose pas `texte` ni `deepfake` ; `gen_deepfake_risk` est une variable parallèle non prévue par la matrice.
16. **[MINEUR]** `AiActClassifier::classify()` n'a pas de garde « pas de réponses » — fallback silencieux RISQUE_MINIMAL en cas d'appel hors flux UI.

---

## Recommandations priorisées

### P0 — Bloquant pour la prod

1. **Compléter `config/questionnaire.php`** pour collecter les 3 variables de base manquantes (`DEC`, `PUB` multi-valeur, `DIFF`) et les 9 conditionnelles absentes (`BIO_TYPE` au sens matrice, `BIO_SOURCE_DONNEES`, `BIO_ATTR_SENSIBLES`, `SCORING_PORTEE`, `TECHNIQUES_SUBLIMINALES`, `PERSUASION_PSYCHOLOGIQUE`, `PREDICTION_CRIMINELLE`, `RH_USAGE`, `EDUC_USAGE`, `INTERACTION_DIRECTE`, `SANTE_FINALITE`, `USAGE_PRESTATIONS_ESSENTIELLES`).
2. **Refondre `config/ai_act_rules.php`** pour aligner les 23 règles strictement sur la matrice v1.1 (conditions exactes, articles exacts, raisons exactes). Supprimer la règle maison `haut_risque_decision_automatique_impactante` et le doublon `annexe3_scoring_recrutement`.
3. **Implémenter R-I-01, R-I-02, R-I-04, R-I-06, R-I-07, R-I-08** — toutes les pratiques prohibées doivent pouvoir être détectées avant la mise en vente, sinon l'outil expose les clients à 35 M€ de sanction qu'il ne signale pas.
4. **Corriger `art5_bio_realtime_public.raison`** (texte erroné, sortira dans le PDF client).

### P1 — À faire avant la première vente

1. **Implémenter R-H-06, R-H-07, R-H-08** (assurance santé, services essentiels, médical) et R-L-04, R-L-05, R-L-06 (transparence Art. 50 hors les 2 cas couverts).
2. **Implémenter R-H-BORDERLINE** comme alerte `FLAG_ZONE_GRISE` non classificatoire (RH informatif → garder le niveau de base mais ajouter une alerte forte). Cela suppose d'étoffer la structure `Assessment` (ou `alertes`) pour distinguer alertes informatives, AGGRAVATION et FLAG_ZONE_GRISE.
3. **Implémenter AGGRAVATION** (CTRL=AUCUN sur HAUT_RISQUE) comme alerte non classificatoire conformément à la matrice — *sans* changer le niveau (contrairement à la règle maison actuelle).
4. **Garde de service** : ajouter dans `AiActClassifier::classify()` un retour `niveau=NON_EVALUE` si `$aiUsage->responses->isEmpty()`, plutôt que se reposer sur le controller.
5. **Tests Pest par règle** : 1 test par règle de la matrice (au moins pour les 8 INACCEPTABLE et 9 HAUT_RISQUE), couvrant le cas qui matche ET un cas voisin qui ne doit pas matcher (test de non-régression sur les bornes).
6. **Tests des 5 scénarios Annexe 3 de la matrice** comme garde-fou de cohérence.

### P2 — Peut attendre v1.1

1. **Exception Art. 6 §3** côté UI : permettre au déployeur de déclarer une « tâche purement procédurale » avec justification, et générer un `FLAG_ZONE_GRISE` avec maintien provisoire en HAUT_RISQUE.
2. **Cas d'exception explicite RH-rédaction-offres** : reconnaître `RH_USAGE=REDACTION_OFFRES + DEC=INFORMATIF` comme sortie du haut risque (cohérent avec la note du Guide).
3. **Cas d'exception EDUC-recommandation** : `EDUC_USAGE=RECOMMENDATION_CONTENU` → `FLAG_ZONE_GRISE` avec critères « accès libre / pas d'exclusion ».
4. **Couvrir les 10 zones grises ZG-01 à ZG-10** par un système d'alertes « vérification juridique recommandée » sans bloquer l'évaluation.
5. **Refactor `bio_modality` ↔ `BIO_TYPE`** : poser deux questions orthogonales (sous-type fonctionnel + modalité technique) pour ne pas perdre l'info actuelle.

---

## Annexe — Inventaire brut

### Règles présentes dans `config/ai_act_rules.php`

| Clé | Niveau | Article | Conditions | Mappage matrice |
|---|---|---|---|---|
| `art5_bio_realtime_public` | INACCEPTABLE | Art. 5 §1 (h) | `type=IA_BIO` + `bio_realtime=yes` | R-I-05 (partiel, BIO_TYPE non vérifié) |
| `art5_social_scoring` | INACCEPTABLE | Art. 5 §1 (c) | `type=IA_SCORING` + `scoring_target=social` | R-I-03 (KO, mauvaise variable) |
| `annexe3_recrutement` | HAUT_RISQUE | Annexe III §4 | `domain=RH` | R-H-02 / R-H-03 (trop large) |
| `annexe3_credit` | HAUT_RISQUE | Annexe III §5 (b) | `domain=CREDIT` | R-H-05 (trop large) |
| `annexe3_scoring_recrutement` | HAUT_RISQUE | Annexe III §4 | `type=IA_SCORING` + `scoring_target=recruitment` | doublon mort |
| `annexe3_education` | HAUT_RISQUE | Annexe III §3 | `domain=EDUCATION` | R-H-04 (trop large) |
| `annexe3_bio_categorisation` | HAUT_RISQUE | Annexe III §1 | `type=IA_BIO` | R-H-01 (trop large, nom trompeur) |
| `haut_risque_decision_automatique_impactante` | HAUT_RISQUE | Art. 6 §2 | `impact_individual=yes` + `human_oversight=never` | hors matrice — devrait être AGGRAVATION |
| `art50_deepfake` | RISQUE_LIMITE | Art. 50 §4 | `type=IA_GEN` + `gen_deepfake_risk=yes` | R-L-03 (partiel, DIFF non vérifié) |
| `art50_contenu_genere` | RISQUE_LIMITE | Art. 50 §2 | `type=IA_GEN` | R-L-02 (trop large) |
| `art50_chatbot_llm` | RISQUE_LIMITE | Art. 50 §1 | `type=LLM_GEN` | R-L-01 (trop large, sur-classifie) |
| `fallback_risque_minimal` | RISQUE_MINIMAL | Art. 95 | (aucune) | DEFAULT |

### Variables collectables aujourd'hui (toutes types confondus)

`finality`, `data_personal`, `data_sensitive`, `human_oversight`, `impact_individual`, `llm_provider`, `llm_data_input`, `llm_output_published`, `gen_content_type`, `gen_deepfake_risk`, `gen_disclosure`, `scoring_target`, `scoring_decision_binding`, `scoring_explainability`, `bio_modality`, `bio_realtime`, `bio_consent`, `other_description`, `other_automation_level`.

### Variables matrice v1.1 attendues

Base : `TYPE`, `DOM`, `DEC`, `PUB`, `DATA`, `DIFF`, `CTRL`.
Conditionnelles : `BIO_TYPE`, `BIO_SOURCE_DONNEES`, `BIO_ATTR_SENSIBLES`, `BIO_TEMPS_REEL_PUBLIC`, `SCORING_PORTEE`, `TECHNIQUES_SUBLIMINALES`, `PERSUASION_PSYCHOLOGIQUE`, `PREDICTION_CRIMINELLE`, `RH_USAGE`, `EDUC_USAGE`, `INTERACTION_DIRECTE`, `GEN_CONTENU`, `SANTE_FINALITE`, `USAGE_PRESTATIONS_ESSENTIELLES`.
