# Mini-référentiel RGPD pour usages IA en PME françaises

**Version :** 1.0 — Mai 2026  
**Cible métier :** PME 20-150 personnes (agences, cabinets, écoles privées, startups tech) sans DPO/RSSI dédié  
**Cible technique :** spécification pour outil web de conformité AI Act + RGPD (one-shot ~1 500 €, livrable PDF client)  
**Articulation :** ce référentiel se branche sur la matrice AI Act existante (4 niveaux : INACCEPTABLE / HAUT_RISQUE / RISQUE_LIMITÉ / RISQUE_MINIMAL). Pour chaque usage IA déclaré (LLM généraliste, IA générative, IA scoring, IA biométrique), il détermine les obligations RGPD additionnelles à celles de l'AI Act.

**Conventions de marquage utilisées dans ce document :**

- 🟢 **TEXTE EXPLICITE** = obligation directement issue du règlement, d'une délibération CNIL ou d'un avis EDPB officiel
- 🟡 **INTERPRÉTATION** = lecture combinée de plusieurs textes ou position doctrinale stabilisée
- 🔴 **ZONE GRISE** = frontière juridique actuellement débattue, non tranchée à ce jour

**Hors scope assumé :** DORA, e-Privacy. **NIS2** n'est mentionnée que dans l'encadré final dédié, car elle ne s'applique pas à toutes les PME du segment cible.

---

## 1. AIPD — Analyse d'Impact RGPD (article 35 RGPD)

### 1.1 Cadre juridique de référence

🟢 **Article 35.1 RGPD** : AIPD obligatoire dès qu'un traitement, "en particulier par le recours à de nouvelles technologies", est "susceptible d'engendrer un risque élevé pour les droits et libertés des personnes physiques".

🟢 **Article 35.3 RGPD** : trois cas où l'AIPD est de droit :

- **a)** évaluation systématique et approfondie d'aspects personnels fondée sur un traitement automatisé, y compris le profilage, et sur la base de laquelle sont prises des décisions produisant des effets juridiques ou affectant significativement la personne ;
- **b)** traitement à grande échelle de données sensibles (art. 9) ou relatives à des condamnations (art. 10) ;
- **c)** surveillance systématique à grande échelle d'une zone accessible au public.

🟢 **Listes CNIL de référence :**

- Délibération n° 2018-327 du 11 octobre 2018 — **liste des 14 types de traitements pour lesquels l'AIPD est REQUISE** (https://www.cnil.fr/sites/default/files/atoms/files/liste-traitements-aipd-requise.pdf)
- Délibération n° 2019-118 du 12 septembre 2019 — **liste des 12 types de traitements DISPENSÉS d'AIPD** (https://www.cnil.fr/sites/default/files/atoms/files/liste-traitements-aipd-non-requise.pdf)
- Lignes directrices CEPD/G29 WP248 rev.01 — **9 critères** : 2 critères suffisent à présumer un risque élevé.

### 1.2 Les 9 critères CEPD applicables aux usages IA

|#|Critère CEPD|Pertinence IA typique|
|---|---|---|
|1|Évaluation / scoring|Scoring CV, scoring crédit, scoring lead, scoring élève|
|2|Décision automatisée avec effet significatif|ATS rejetant un CV, refus de crédit, modération de contenu|
|3|Surveillance systématique|Monitoring salariés, vidéo intelligente|
|4|Données sensibles (art. 9) ou hautement personnelles|Données santé, opinions, biométrie|
|5|Traitement à grande échelle|Datasets clients massifs, training LLM|
|6|Croisement / combinaison de jeux de données|Fusion CRM + données externes pour profilage|
|7|Personnes vulnérables|Mineurs (écoles), patients, salariés (déséquilibre)|
|8|Usage innovant ou nouvelle solution technologique|LLM génératifs, IA générative, agents autonomes|
|9|Exclusion d'un droit / contrat / service|Filtrage automatique de candidatures, refus assurance|

🟢 **Règle opérationnelle CNIL (fiche IA dédiée, https://www.cnil.fr/fr/realiser-une-analyse-dimpact-si-necessaire) :** au moins **2 critères sur 9** ⇒ AIPD obligatoire. La CNIL précise explicitement que "les systèmes d'IA générative reposant sur un apprentissage portant sur de grandes quantités de données et dont le comportement ne peut être anticipé dans tous les cas d'usage" peuvent constituer un "usage innovant" (critère 8).

### 1.3 Matrice AIPD pour usages IA typiques en PME

|Usage IA|Niveau AI Act|AIPD obligatoire ?|Justification|Recommandation outil|
|---|---|---|---|---|
|**ChatGPT/Claude/Copilot pour productivité interne** (rédaction, résumé, code) sans données personnelles dans les prompts|Risque minimal|**Non obligatoire**|Aucun traitement de données personnelles si politique de prompt respectée|AIPD recommandée si déploiement large et risque de fuite de données RH/clients|
|**ChatGPT/Claude/Copilot avec données personnelles dans les prompts** (résumer un mail client, analyser un CV)|Risque limité à minimal|**Recommandée → souvent obligatoire**|Critères 5 (échelle) + 8 (innovant) potentiellement remplis ; transferts hors UE possibles|AIPD à déclencher dès que volumétrie significative|
|**Scoring de CV / tri automatisé candidatures**|**HAUT RISQUE** (Annexe III AI Act)|**OBLIGATOIRE** 🟢|Critères 1 + 2 + 9 systématiquement remplis ; figure dans la liste CNIL 2018-327 (profilage RH, exclusion d'un contrat)|Bloquer le déploiement si AIPD non produite|
|**IA biométrique** (reconnaissance faciale, contrôle d'accès biométrique)|HAUT RISQUE voire INACCEPTABLE selon usage|**OBLIGATOIRE** 🟢|Données art. 9 + critères 3 + 4 ; explicitement listée par la CNIL|AIPD + consultation préalable CNIL si risque résiduel élevé (art. 36)|
|**Chatbot SAV / support client** (LLM généraliste sur site web)|Risque limité (transparence art. 50 AI Act)|**Recommandée** 🟡|Critère 8 (innovant) seul + éventuellement 5 (échelle) ; AIPD obligatoire si conservation longue ou profilage des conversations|AIPD recommandée par défaut|
|**Génération de contenu publié** (articles SEO, visuels marketing) sans données personnelles|Risque limité|**Non obligatoire**|Pas de traitement de données personnelles si le prompt n'en contient pas|À tracer dans le registre, sans AIPD|
|**Recommandation produit / personnalisation marketing** par IA|Risque minimal à limité|**Recommandée → obligatoire si profilage à grande échelle**|Critères 1 + 5 + éventuellement 6 ; la CNIL a explicitement cité la "personnalisation des publicités en ligne" comme cas typique d'AIPD obligatoire|AIPD obligatoire dès personnalisation comportementale automatisée à grande échelle|
|**IA scoring crédit / financier**|HAUT RISQUE|**OBLIGATOIRE** 🟢|Critères 1 + 2 + 9 ; profilage avec exclusion possible d'un contrat|AIPD + garanties art. 22 RGPD|
|**Surveillance / monitoring salariés assisté par IA**|HAUT RISQUE (Annexe III AI Act)|**OBLIGATOIRE** 🟢|Listée explicitement CNIL 2018-327 ("surveillance systématique des activités des employés")|AIPD + consultation CSE|
|**IA détection anomalies cyber / SOC** sur logs internes|Risque minimal|**Non obligatoire** en règle générale|Critère 8 seul ; sauf si traitement à grande échelle de données salariés|AIPD recommandée pour SOC monitoring poste de travail|

### 1.4 Cas d'AIPD recommandée mais non obligatoire

🟡 La CNIL et le CEPD recommandent expressément l'AIPD "en cas de doute". Pour le scope PME, l'outil doit la **suggérer par défaut** dès qu'un seul critère sur 9 est rempli ET qu'au moins l'un des éléments suivants est présent :

- déploiement à plus de 50 personnes (clients ou salariés) ;
- LLM externe non européen utilisé en production ;
- conservation de prompts ou conversations > 30 jours.

### 1.5 Logique d'intégration avec la matrice AI Act

Règle de jonction proposée pour le moteur :

```
SI niveau_AI_Act == HAUT_RISQUE :
    AIPD = OBLIGATOIRE (quasi-systématique : un système à haut risque coche
    presque toujours ≥ 2 critères CEPD)

SI niveau_AI_Act == RISQUE_LIMITE :
    Compter les critères CEPD remplis
    SI critères ≥ 2 : AIPD = OBLIGATOIRE
    SINON : AIPD = RECOMMANDÉE

SI niveau_AI_Act == RISQUE_MINIMAL :
    SI données personnelles dans le traitement :
        AIPD = RECOMMANDÉE si critères ≥ 1
    SINON : AIPD = NON REQUISE (mais registre obligatoire)

SI niveau_AI_Act == INACCEPTABLE :
    Sortir du flux : usage interdit, AIPD sans objet.
```

---

## 2. Bases légales (article 6 RGPD) compatibles avec les usages IA en PME

### 2.1 Tableau des bases légales applicables par usage IA

🟢 = base directement supportée par CNIL/EDPB ; 🔴 = base **problématique** voire à proscrire.

|Usage IA en PME|Base légale recommandée|Base légale problématique|Référence|
|---|---|---|---|
|**Utilisation LLM pour productivité interne** (collaborateurs)|**Intérêt légitime** (art. 6.1.f) — gestion de l'organisation 🟢|🔴 Consentement salarié (déséquilibre employeur-salarié)|CNIL fiches pratiques IA, EDPB Opinion 28/2024|
|**Scoring RH / tri CV automatisé**|**Mesures précontractuelles** (art. 6.1.b) limité, **intérêt légitime** (art. 6.1.f) avec garanties fortes 🟡|🔴 Consentement candidat (déséquilibre + non libre)|Lignes directrices EDPB sur recrutement|
|**Marketing personnalisé / scoring lead**|**Consentement** (art. 6.1.a) si prospection B2C ; **intérêt légitime** B2B avec opt-out 🟡|🔴 Intérêt légitime sans test de balance documenté|Cumul avec art. L34-5 CPCE (e-Privacy)|
|**Chatbot SAV** (assistance client)|**Exécution du contrat** (art. 6.1.b) ou **intérêt légitime** (art. 6.1.f) 🟢|Consentement non requis pour le service lui-même, mais utile pour la **réutilisation des conversations** pour amélioration du modèle|EDPB Opinion 28/2024 cite explicitement le "conversational agent" comme cas légitime|
|**Analyse de CV par IA** (avec décision humaine finale)|**Intérêt légitime** (art. 6.1.f) avec AIPD + garanties art. 22 🟡|🔴 Consentement candidat|Projet guide CNIL recrutement|
|**Génération de contenu publié** (sans données perso en entrée)|Pas de base RGPD nécessaire si pas de données personnelles|—|—|
|**Génération de contenu intégrant des données personnelles** (mailing personnalisé, fiches client)|Base légale du traitement support (généralement art. 6.1.b ou 6.1.f)|🔴 Réutilisation de données collectées pour une autre finalité sans test de compatibilité (art. 6.4)|—|
|**Entraînement / fine-tuning d'un modèle sur données internes**|**Intérêt légitime** (art. 6.1.f) avec test de balance + garanties 🟢|🔴 Consentement employés sans alternative crédible|EDPB Opinion 28/2024, recommandations CNIL intérêt légitime|

### 2.2 Le piège du consentement en entreprise

🟢 **Texte explicite (art. 4.11 RGPD) :** le consentement doit être _libre_, _spécifique_, _éclairé_, _univoque_. Le considérant 43 RGPD précise qu'il y a présomption d'absence de liberté en cas de déséquilibre manifeste entre la personne concernée et le responsable du traitement.

🟢 **Position CNIL et EDPB constante :** dans la relation **employeur-salarié** et dans une moindre mesure **prestataire de service-client captif**, le consentement n'est en règle générale **pas une base légale valide** :

- un salarié ne peut pas refuser librement l'usage d'un outil IA imposé par l'employeur (peur de représailles, hiérarchie) ;
- un candidat à l'embauche n'est pas en position de refuser le passage de son CV par une IA de tri.

➡ **Règle pour l'outil :** si le client déclare "consentement salarié" comme base légale, lever un **WARNING bloquant** dans le rapport et proposer la bascule vers **intérêt légitime** ou **exécution du contrat de travail** (art. 6.1.b) selon la finalité.

### 2.3 Intérêt légitime (art. 6.1.f) — base la plus fréquente pour l'IA en PME

🟢 **Position CNIL** (Recommandations sur l'intérêt légitime pour l'IA, juin 2025, https://www.cnil.fr/fr/recommandations-developpement-ia-interet-legitime) : _"La base légale de l'intérêt légitime sera la plus couramment utilisée pour le développement de systèmes d'IA."_

🟢 **Test de balance en 3 étapes** (EDPB Opinion 28/2024 + Lignes directrices 1/2024 sur l'art. 6.1.f) :

1. **Identification de l'intérêt légitime** : réel, licite, défini précisément, non spéculatif (ex. : amélioration productivité, sécurité informatique, lutte contre la fraude, développement produit) ;
2. **Test de nécessité** : pas de moyen moins intrusif (ex. : peut-on anonymiser ? pseudonymiser ? réduire le périmètre ?) ;
3. **Test de mise en balance** : les droits et libertés des personnes ne doivent pas prévaloir, en tenant compte :
    - des **attentes raisonnables** des personnes (les données étaient-elles publiquement disponibles ? quelle relation avec le responsable ?) ;
    - des **risques** (sentiment de surveillance, profilage, biais, ré-identification depuis le modèle) ;
    - des **mesures correctrices** (pseudonymisation, opt-out facilité, transparence renforcée, limitation des sorties).

🟡 **Documentation à produire (LIA — Legitimate Interest Assessment) :** l'outil doit générer un canevas LIA prérempli avec ces 3 étapes pour chaque usage IA déclaré sous base légale "intérêt légitime".

### 2.4 Données sensibles (article 9 RGPD)

🟢 **Principe :** le traitement de catégories particulières de données (santé, origines, opinions, biométrie aux fins d'identification, orientation sexuelle, etc.) est **interdit par défaut**, sauf exception listée à l'art. 9.2.

⚠ **Pour un usage IA en PME, les exceptions opérationnelles réalistes sont très restreintes :**

- art. 9.2.a — **consentement explicite** (et non implicite) ;
- art. 9.2.b — droit du travail / sécurité sociale (cadre strict) ;
- art. 9.2.h — finalité médicale (réservé professionnels de santé) ;
- art. 9.2.f — constatation/exercice/défense d'un droit en justice.

🔴 **ZONE GRISE — biométrie au travail :** la CNIL a une doctrine très stricte. Le contrôle d'accès biométrique en entreprise n'est généralement admis que pour des locaux à très haute sécurité (règlement type CNIL 2019). Tout usage d'IA biométrique en PME doit être considéré comme **HAUT RISQUE** par défaut.

🔴 **ZONE GRISE — détection d'émotions par IA :** interdite sur le lieu de travail et dans les établissements d'enseignement par l'**art. 5.1.f de l'AI Act** (pratique INACCEPTABLE), sauf raisons médicales ou de sécurité. À bloquer dans la matrice de décision.

➡ **Règle pour l'outil :** si l'usage IA touche des données art. 9, déclencher automatiquement **AIPD obligatoire** + **alerte juridique de niveau supérieur** dans le rapport, et exiger une exception art. 9.2 dûment identifiée.

---

## 3. Droits RGPD renforcés pour les traitements IA

### 3.1 Article 22 RGPD — décision individuelle automatisée

🟢 **Texte explicite (art. 22.1) :** _"La personne concernée a le droit de ne pas faire l'objet d'une décision fondée exclusivement sur un traitement automatisé, y compris le profilage, produisant des effets juridiques la concernant ou l'affectant de manière significative de façon similaire."_

🟢 **Conditions cumulatives d'application :**

1. décision **fondée exclusivement** sur un traitement automatisé (pas d'intervention humaine significative) ;
2. produisant **des effets juridiques** OU **affectant significativement** la personne.

🟡 **Critère "intervention humaine significative" (lignes directrices WP251 rev.01) :** il ne suffit pas qu'un humain valide formellement la décision ; il doit avoir l'**autorité, la compétence et le temps** de réviser la décision. Une simple validation "tampon" d'une recommandation algorithmique reste une décision automatisée au sens de l'art. 22.

🟢 **Exceptions (art. 22.2) — la décision est autorisée si :**

- a) nécessaire à la conclusion ou exécution d'un contrat ;
- b) autorisée par le droit de l'UE ou d'un État membre ;
- c) fondée sur le **consentement explicite** de la personne.

🟢 **Garanties obligatoires (art. 22.3)** dans les cas a) et c) :

- droit d'obtenir une **intervention humaine** ;
- droit d'**exprimer son point de vue** ;
- droit de **contester la décision**.

🟢 **Interdiction renforcée (art. 22.4) :** une décision automatisée ne peut reposer sur des données art. 9 sauf consentement explicite ou motif d'intérêt public substantiel.

### 3.2 Articulation art. 22 RGPD ↔ AI Act

🟢 **Principe de cumul (art. 2.7 AI Act) :** _"Le présent règlement s'applique sans préjudice du règlement (UE) 2016/679 [RGPD]."_ Les obligations sont **cumulatives**, pas alternatives.

|Obligation|Source RGPD|Source AI Act|Articulation|
|---|---|---|---|
|Information de la personne sur l'usage d'IA|Art. 13/14 (informations à fournir)|**Art. 50** (transparence : la personne doit savoir qu'elle interagit avec une IA, et que le contenu est généré ou manipulé par IA)|RGPD = qui, quoi, pourquoi ; AI Act = "c'est une IA"|
|Intervention humaine|Art. 22.3|**Art. 14** AI Act (contrôle humain pour systèmes haut risque) + **art. 26** (obligations des déployeurs : assurer le contrôle humain)|RGPD = droit individuel ; AI Act = obligation organisationnelle|
|Information sur la logique du traitement|Art. 13.2.f / 14.2.g / 15.1.h ("informations utiles concernant la logique sous-jacente")|Art. 13 AI Act (transparence vers les déployeurs) + art. 86 (droit à l'explication des décisions individuelles)|Convergence ; le RGPD seul est insuffisant pour les systèmes haut risque|
|Évaluation des risques|AIPD (art. 35)|FRIA — Fundamental Rights Impact Assessment (**art. 27** AI Act, pour certains déployeurs haut risque)|AIPD et FRIA peuvent être combinées dans un même document|

🟡 **Recommandation pour l'outil :** générer un **bloc unifié "transparence + contrôle humain"** dans le rapport PDF, qui couvre simultanément les exigences RGPD (art. 13-14, 22) et AI Act (art. 13, 26, 50, 86). Éviter de séparer les deux dans le livrable : le dirigeant PME doit voir une seule check-list cohérente.

### 3.3 Droit à l'information renforcé pour l'IA (articles 13 et 14)

🟢 **Mentions obligatoires standard** + **mentions spécifiques IA** :

|Élément|Source|Spécificité IA|
|---|---|---|
|Identité du responsable, finalités, base légale|Art. 13.1|Inchangé|
|Destinataires (sous-traitants)|Art. 13.1.e|**Citer explicitement le fournisseur du LLM** (OpenAI, Anthropic, Microsoft, Google, Mistral, etc.) en tant que sous-traitant art. 28|
|Transferts hors UE|Art. 13.1.f|**Indiquer le mécanisme** (DPF, CCT, dérogation art. 49) — voir §4|
|Existence d'une décision automatisée|Art. 13.2.f|**Information ACTIVE** (et non passive) sur l'existence du profilage / décision automatisée|
|Logique sous-jacente|Art. 13.2.f|"Informations utiles concernant la logique sous-jacente, l'importance et les conséquences prévues" — la CNIL admet une explication intelligible non technique|
|Droit d'opposition|Art. 21|Spécialement mis en avant si base légale = intérêt légitime|
|Source des données (cas IA entraînées sur données tierces)|Art. 14.2.f|EDPB Opinion 28/2024 : information générale (site web) admise si contact individuel impossible|

### 3.4 Droits d'accès, rectification, effacement, opposition — spécificités IA

🟢 **Article 15 — droit d'accès** : la personne peut demander si ses données ont été utilisées pour entraîner ou faire fonctionner un système d'IA, et obtenir des "informations utiles concernant la logique sous-jacente" (art. 15.1.h).

🟡 **Article 16 — rectification** et 🟡 **Article 17 — effacement** : la CNIL (fiche IA dédiée, https://www.cnil.fr/fr/intelligence-artificielle/ia-comment-etre-en-conformite-avec-le-rgpd) précise que _"se conformer à une demande de rectification ou d'effacement de données d'apprentissage n'implique pas nécessairement la rectification ou l'effacement du ou des modèles d'IA ayant été produits à partir de ces données"_. La position est que :

- les données d'entrée (training set) doivent être rectifiées/effacées ;
- pour le modèle lui-même, l'EDPB (Opinion 28/2024) admet des techniques alternatives : unlearning, output filtering, post-training removal.

🔴 **ZONE GRISE — exercice des droits sur le modèle :** aucune jurisprudence ni guidance définitive sur le caractère exécutable d'une demande d'effacement portant sur les **poids** d'un modèle entraîné. À ce jour, l'EDPB renvoie à une appréciation au cas par cas (Opinion 28/2024 §§ 28-39).

🟢 **Article 21 — opposition** : si la base légale est l'intérêt légitime, le droit d'opposition est inconditionnel pour la prospection (art. 21.2) et conditionné à la situation particulière dans les autres cas (art. 21.1). La CNIL recommande pour l'IA un **opt-out facilité, voire inconditionnel** comme garantie compensatoire dans le test de balance.

### 3.5 Droits de la personne face à une décision IA (synthèse opérationnelle)

Ce que le rapport PDF doit faire apparaître pour chaque usage IA produisant une décision :

1. **Information préalable** sur l'usage d'une IA (art. 50 AI Act + art. 13/14 RGPD) ;
2. **Possibilité de demander l'intervention humaine** (art. 22.3 RGPD + art. 26 AI Act) ;
3. **Droit à l'explication** (art. 86 AI Act pour décisions haut risque + art. 15.1.h RGPD) ;
4. **Droit de contester** la décision auprès du responsable, puis recours CNIL ;
5. **Droit d'opposition** si base = intérêt légitime ;
6. **Logs et traçabilité** côté responsable (art. 12 AI Act pour haut risque + accountability art. 5.2 RGPD).

---

## 4. Recommandations CNIL et EDPB sur les LLM en entreprise

### 4.1 Plan IA de la CNIL et fiches pratiques

🟢 **Plan IA de la CNIL** : lancé en mai 2023, structuré autour de 4 axes (compréhension, accompagnement, audit, soutien à l'innovation).

🟢 **Fiches pratiques publiées par la CNIL — référence centrale** : https://www.cnil.fr/fr/les-fiches-pratiques-ia

Périmètre : ces fiches portent **principalement sur la phase de développement** des systèmes d'IA, mais sont applicables par analogie à la phase de déploiement par les PME. Sujets couverts à ce jour :

- détermination du régime juridique applicable ;
- définition des finalités ;
- qualification (responsable / sous-traitant / responsable conjoint) ;
- base légale et intérêt légitime (recommandations dédiées juin 2025) ;
- moissonnage de données (web scraping) ;
- AIPD pour systèmes IA ;
- minimisation et durée de conservation ;
- information des personnes et exercice des droits ;
- annotation des données ;
- sécurité des systèmes d'IA (en cours).

🟢 **Guide France Num × CPME × CNIL** : _"Utiliser l'IA générative dans les TPE et PME"_ — quatre fiches pratiques opérationnelles, https://www.cnil.fr/fr/utiliser-lia-generative-dans-les-tpe-et-pme. **À citer en référence dans le rapport client comme socle de bonnes pratiques.**

### 4.2 EDPB Opinion 28/2024 du 17 décembre 2024

🟢 **Référence officielle** : https://www.edpb.europa.eu/our-work-tools/our-documents/opinion-board-art-64/opinion-282024-certain-data-protection-aspects_en

Trois apports clés :

**(1) Anonymat d'un modèle d'IA** — appréciation au cas par cas. Un modèle est anonyme si :

- la probabilité d'extraction directe ou probabiliste de données personnelles d'entraînement est _insignificante_ ;
- la probabilité d'obtenir des données personnelles via des requêtes (queries / inversion attacks) est _insignificante_. 👉 **Conséquence pour la PME utilisatrice** : ne pas présumer qu'un LLM est "anonyme" parce que son fournisseur l'affirme.

**(2) Intérêt légitime comme base légale du développement et du déploiement** : confirmé comme possible, mais soumis au test en 3 étapes (cf. §2.3). L'EDPB cite explicitement comme exemples acceptables : un agent conversationnel d'assistance utilisateurs, l'IA pour améliorer la cybersécurité.

**(3) Conséquences d'un traitement illicite en phase de développement** : peuvent contaminer le déploiement aval. Si une PME déploie un modèle entraîné illégalement, sa propre licéité peut être remise en cause si elle savait ou ne pouvait ignorer cette illégalité.

🔴 **ZONE GRISE :** l'opinion utilise 161 fois "may/might" et 16 fois "case by case". Pas de réponse binaire ; chaque déploiement reste une appréciation in concreto.

### 4.3 Transferts hors UE — situation des LLM US

🟢 **Cadre :** chapitre V RGPD (art. 44 à 49). Trois mécanismes principaux :

1. **Décision d'adéquation** (art. 45) — pour les USA : Data Privacy Framework (DPF) depuis le 10 juillet 2023.
2. **Garanties appropriées** (art. 46) — clauses contractuelles types (CCT/SCC), BCR, codes de conduite.
3. **Dérogations** (art. 49) — usage très restreint (consentement explicite, exécution contractuelle ponctuelle).

🟢 **Statut du DPF en mai 2026 :**

- validé par le Tribunal de l'UE le **3 septembre 2025** (affaire T-553/23, _Latombe c. Commission_) ;
- 🔴 **ZONE GRISE :** un recours "Schrems III" est annoncé par NOYB / Max Schrems ; risque d'invalidation à moyen terme par la CJUE non écarté.

🟢 **Conséquences pratiques pour les LLM US grand public :**

|Fournisseur|Statut DPF|Hébergement UE possible ?|DPA disponible|Position recommandée|
|---|---|---|---|---|
|**OpenAI (ChatGPT)**|Certifié DPF|Oui depuis février 2025 (Enterprise, Edu, API)|Oui (DPA officiel mis à jour 1er janvier 2026)|Version Enterprise/Business avec DPA + résidence UE acceptable ; **version gratuite à proscrire** en contexte pro|
|**Anthropic (Claude)**|Certifié DPF|Oui (offre Enterprise)|Oui|Idem : Enterprise + DPA|
|**Microsoft Copilot / Azure OpenAI**|Certifié DPF|Oui (Azure régions UE)|Oui|Recommandé pour PME déjà en environnement Microsoft 365|
|**Google Gemini**|Certifié DPF|Oui (Workspace)|Oui|Acceptable avec contrat Workspace pro|
|**Mistral AI (Le Chat, La Plateforme)**|Hors DPF (pas nécessaire — France)|Oui (par défaut)|Oui|**Alternative française à privilégier** quand pertinent|

🟢 **Position CNIL constante :** un transfert vers un fournisseur **non certifié DPF** doit reposer sur des **CCT** complétées par une **TIA** (Transfer Impact Assessment) selon les recommandations CEPD 01/2020 + mesures supplémentaires (chiffrement, pseudonymisation, hébergement UE).

➡ **Règle pour l'outil :** demander explicitement le fournisseur LLM utilisé, vérifier son statut DPF, alerter si version gratuite/grand public utilisée pour des données professionnelles.

### 4.4 Données entrées dans les prompts — minimisation et secrets

🟢 **Position CNIL** (fiches IA + bilan bac à sable IA services publics, France Travail) : **principe de minimisation (art. 5.1.c RGPD)** s'applique pleinement à la rédaction des prompts. La CNIL insiste sur la nécessité d'une formation des utilisateurs et de procédures internes.

🟡 **Bonnes pratiques recommandées (CNIL + France Num + EDPB) :**

- **anonymiser ou pseudonymiser** les données personnelles avant injection dans le prompt ;
- **interdire** l'injection de catégories particulières (art. 9) sans base légale spécifique ;
- **interdire** l'injection de secrets professionnels couverts (médical, avocat, code de la propriété intellectuelle, secret des affaires) ;
- **paramétrer l'opt-out d'entraînement** quand le fournisseur le permet (par défaut activé sur ChatGPT Enterprise, Claude for Work, Copilot) ;
- **journaliser** les usages (au moins en agrégé) pour pouvoir répondre aux droits d'accès.

### 4.5 Position sur la base légale d'entraînement des modèles

🟢 **CNIL (recommandations juin 2025)** : _"La base légale de l'intérêt légitime sera la plus couramment utilisée pour le développement de systèmes d'IA"_, sous réserve de garanties fortes (information, opt-out discrétionnaire, exclusion de certaines données, pseudonymisation).

🟢 **EDPB (Opinion 28/2024)** : confirme l'applicabilité de l'intérêt légitime sous condition de passage du test en 3 étapes.

🟡 **Pour la PME déployeuse (et non développeuse)** : la PME n'a en principe **pas à se prononcer** sur la base légale du training initial du modèle qu'elle utilise — c'est la responsabilité du fournisseur. **Mais** l'EDPB a posé que l'illicéité amont peut contaminer l'aval ; d'où l'importance de **choisir un fournisseur dont la documentation RGPD est solide** (DPA, model card, transparence sur les données d'entraînement).

🔴 **ZONE GRISE — fine-tuning interne sur données salariés/clients** : licite sur la base de l'intérêt légitime à condition de :

- réaliser une AIPD ;
- documenter une LIA ;
- offrir un opt-out aux personnes concernées ;
- exclure les données art. 9. La CNIL n'a pas publié de doctrine définitive PME-spécifique à ce jour.

---

## 5. Checklist opérationnelle PME — utiliser un LLM externe en règle

**Objet :** plan de mise en conformité directement intégrable au rapport PDF client. 10 points actionnables pour une PME utilisant ChatGPT, Claude, Copilot, Gemini ou équivalent.

|#|Exigence|Base juridique|Action concrète|Effort|Priorité|
|---|---|---|---|---|---|
|**1**|**Choisir une offre professionnelle avec DPA et opt-out d'entraînement**|Art. 28 RGPD (sous-traitance) ; art. 5.1.b (limitation finalités)|Souscrire à ChatGPT Enterprise/Business, Claude for Work, Copilot for Microsoft 365, ou équivalent. Signer le DPA. Vérifier que l'opt-out training est activé. **Bannir les versions gratuites pour usage pro.**|Faible (½ journée + budget)|🔴 P0|
|**2**|**Cartographier les usages IA et tenir le registre des activités de traitement**|Art. 30 RGPD|Lister chaque cas d'usage IA, sa finalité, les données traitées, le fournisseur, la durée de conservation, les transferts hors UE. Intégrer au registre RGPD existant.|Moyen (1-2 jours)|🔴 P0|
|**3**|**Réaliser une AIPD pour les usages le justifiant**|Art. 35 RGPD ; délibération CNIL 2018-327 ; 9 critères CEPD|Utiliser l'outil PIA de la CNIL (gratuit, open source, https://www.cnil.fr/fr/outil-pia-telechargez-et-installez-le-logiciel-de-la-cnil). Une AIPD par usage IA à risque élevé (scoring RH, biométrie, surveillance, marketing personnalisé à grande échelle).|Élevé (2-5 jours par AIPD)|🔴 P0 si usage haut risque|
|**4**|**Définir et documenter la base légale pour chaque usage**|Art. 6 RGPD ; art. 9 si données sensibles|Pour chaque usage : choisir une base légale conforme, rédiger la LIA si intérêt légitime. **Bannir le consentement salarié.**|Moyen (1 jour)|🟠 P1|
|**5**|**Mettre à jour les mentions d'information** (politique de confidentialité, mentions sur les outils, notes internes salariés)|Art. 13-14 RGPD ; art. 50 AI Act|Indiquer : usage d'IA, fournisseur(s), finalités, base légale, transferts hors UE et mécanisme (DPF/CCT), droits, durée de conservation des prompts.|Moyen (1 jour)|🟠 P1|
|**6**|**Encadrer les prompts par une charte interne d'usage de l'IA**|Art. 5.1.c (minimisation) ; art. 32 (sécurité) ; secrets professionnels|Rédiger une charte (1-2 pages) interdisant : données art. 9 dans les prompts, données clients identifiantes sans pseudonymisation, secrets professionnels, données soumises à NDA. Faire signer aux salariés concernés. Former en 1h.|Faible (½ journée + 1h formation)|🔴 P0|
|**7**|**Garantir l'intervention humaine pour toute décision impactante**|Art. 22 RGPD ; art. 14 et 26 AI Act|Pour scoring CV, scoring crédit, modération, etc. : process documenté avec décision finale humaine, possibilité de contestation, traçabilité. Pas de "rubber-stamping" algorithmique.|Moyen (1-2 jours process)|🔴 P0 si décisions automatisées|
|**8**|**Vérifier et documenter les transferts hors UE**|Art. 44-49 RGPD ; chap. V|Pour chaque fournisseur LLM US : vérifier la certification DPF active sur https://www.dataprivacyframework.gov ; à défaut, exiger des CCT + TIA. Mentionner dans la politique de confidentialité.|Faible (½ journée)|🟠 P1|
|**9**|**Sécuriser techniquement**|Art. 32 RGPD|SSO + MFA sur les comptes IA, journalisation des accès, comptes nominatifs (pas de comptes partagés), revue trimestrielle des accès, chiffrement des données en transit (TLS) et au repos côté fournisseur, paramétrage de la durée de rétention des conversations (7 à 90 jours selon offre).|Faible à moyen|🟠 P1|
|**10**|**Procédure d'exercice des droits adaptée à l'IA**|Art. 15-22 RGPD|Boîte mail dédiée (dpo@... ou rgpd@...), délai de réponse 1 mois, procédure spécifique pour les demandes touchant à un traitement IA (intervention humaine, explication de la logique, opposition). Tester la procédure une fois.|Faible (½ journée)|🟢 P2|

**Effort total estimé pour une PME 20-150 salariés :** 7 à 15 jours·homme la première année, puis 2-4 jours/an de maintenance (revue annuelle des usages, mise à jour AIPD, contrôle des fournisseurs).

---

## Annexe — Encadré NIS2 (à conserver dans l'outil uniquement si le client est concerné)

🟢 **NIS2 (directive UE 2022/2555, transposée en France par la loi du 30 mars 2025 dite "loi de résilience")** ne s'applique **pas à toutes les PME**. Elle vise les **entités essentielles (EE)** et **entités importantes (EI)** dépassant des seuils de taille (≥ 50 salariés ET ≥ 10 M€ CA OU ≥ 10 M€ bilan) **ET** opérant dans des secteurs listés (annexes I et II : énergie, santé, transports, eaux, finance, fournisseurs numériques, infrastructures TIC, espace, etc.).

➡ **Règle pour l'outil :** ne déclencher le module NIS2 que si la PME coche simultanément :

- les seuils de taille NIS2 ;
- un secteur NIS2 (annexes I ou II).

Sinon, NIS2 reste hors scope du rapport. Pour les PME concernées, l'usage d'IA en production renforce notamment les obligations de **gestion des risques cyber**, **notification d'incidents** (24h pré-notification, 72h notification, 1 mois rapport final) et **chaîne d'approvisionnement** (le LLM externe devient un fournisseur critique).

🔴 **ZONE GRISE :** l'articulation précise entre les obligations NIS2 sur la chaîne d'approvisionnement et le statut des fournisseurs LLM (OpenAI, Anthropic, etc. ne sont pas eux-mêmes EE/EI au sens NIS2 sauf à se qualifier comme "fournisseurs de services informatiques en nuage") n'est pas définitivement tranchée par l'ANSSI à la date de rédaction.

---

## Annexe technique — schéma d'intégration dans l'outil

```
[Saisie client : usage IA déclaré]
        │
        ▼
[Matrice AI Act existante : niveau de risque]
        │
        ▼
[Module RGPD (ce référentiel) :]
   ├─► §1 AIPD : compter critères CEPD → OBLIGATOIRE / RECOMMANDÉE / NON REQUISE
   ├─► §2 Base légale : proposer + détecter incompatibilités (consentement salarié, art. 9)
   ├─► §3 Droits : générer bloc unifié transparence + contrôle humain
   ├─► §4 LLM externe : vérifier fournisseur, DPF, hébergement UE
   └─► §5 Checklist : générer plan de mise en conformité personnalisé
        │
        ▼
[Rapport PDF client : 
   - tableau de synthèse AI Act + RGPD
   - AIPD : oui/non + raisons
   - bases légales recommandées
   - liste des actions prioritaires (P0/P1/P2)
   - références CNIL/EDPB cliquables]
```

**Sources juridiques principales mobilisées dans l'outil (à intégrer en bas du rapport sous forme de liens) :**

- RGPD : règlement (UE) 2016/679 ;
- AI Act : règlement (UE) 2024/1689 ;
- Délibération CNIL 2018-327 (liste AIPD requise) ;
- Délibération CNIL 2019-118 (liste AIPD non requise) ;
- Lignes directrices CEPD WP248 rev.01 (AIPD) ;
- Lignes directrices CEPD WP251 rev.01 (décisions automatisées) ;
- EDPB Opinion 28/2024 du 17 décembre 2024 ;
- EDPB Lignes directrices 1/2024 sur l'art. 6.1.f ;
- CNIL — Fiches pratiques IA (https://www.cnil.fr/fr/les-fiches-pratiques-ia) ;
- CNIL × CPME × France Num — IA générative TPE/PME (https://www.cnil.fr/fr/utiliser-lia-generative-dans-les-tpe-et-pme) ;
- CNIL — Recommandations intérêt légitime IA (juin 2025).

**Fin du référentiel — version 1.0.**