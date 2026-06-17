## Outil de conformité AI Act — Version 1.1

**Règlement (UE) 2024/1689 | Cohérent avec le Guide de Conformité AI Act pour PME Françaises**

---

> ⚠️ **Avis de non-responsabilité :** Ce document est une spécification technique opérationnelle, pas un avis juridique. Pour tout usage de niveau HAUT_RISQUE ou toute situation ambiguë, consulter un juriste spécialisé en droit du numérique.

> 📝 **Notes de version 1.1 (corrections par rapport à 1.0) :**
> 
> - R-I-05 : correction de la base légale (Art. 5 §1 h, et non d).
> - Variables auparavant implicites (`usage_bdd_scraping_signalé`, `usage_subliminal_signalé`, `usage_eligibilite_prestations`) intégrées formellement comme variables conditionnelles : `BIO_SOURCE_DONNEES`, `TECHNIQUES_SUBLIMINALES`, `USAGE_PRESTATIONS_ESSENTIELLES`.
> - Règle R-I-08 (prédiction individuelle de criminalité, Art. 5 §1 d) ajoutée explicitement au pseudo-code.

---

## CONVENTIONS GÉNÉRALES

### Variables d'entrée — Identifiants courts (7 variables de base, toujours posées)

|Variable|Identifiant|Valeurs acceptées|
|---|---|---|
|Type d'outil IA|`TYPE`|`LLM_GEN` · `IA_GEN` · `IA_SCORING` · `IA_BIO` · `AUTRE`|
|Domaine d'usage|`DOM`|`RH` · `EDUCATION` · `CREDIT` · `SANTE` · `SECURITE` · `MARKETING` · `PROD_INT` · `DEV_LOG` · `AUTRE`|
|Nature de la décision|`DEC`|`INFORMATIF` · `AIDE_DEC` · `SEMI_AUTO` · `FULL_AUTO`|
|Public concerné|`PUB`|`AUCUN` · `EMPLOYES` · `CLIENTS` · `GRAND_PUBLIC` · `VULNERABLES` _(multi-valeur)_|
|Données traitées|`DATA`|`PAS_PERSO` · `PERSO_STD` · `SENSIBLE`|
|Diffusion de la sortie|`DIFF`|`INTERNE` · `TIERS` · `PUBLIC`|
|Contrôle humain|`CTRL`|`SYSTEMATIQUE` · `ECHANTILLON` · `AUCUN`|

> **Note développement :** `PUB` est un **ensemble multi-valeur**. `PUB ∋ VULNERABLES` signifie « VULNERABLES est dans l'ensemble sélectionné par le client ». Exemple : un client peut cocher `EMPLOYES` + `VULNERABLES` simultanément.

### Variables conditionnelles — Affichées selon le contexte

Ces variables **ne sont affichées que conditionnellement** selon les réponses aux 7 variables de base. Elles débloquent des règles critiques impossibles à évaluer sans elles.

|Condition d'affichage|Variable conditionnelle|Identifiant|Valeurs|
|---|---|---|---|
|`TYPE = IA_BIO`|Sous-type biométrique|`BIO_TYPE`|`IDENTIFICATION` · `RECOG_EMOTIONS` · `CATEGORISATION` · `CONTROLE_ACCES`|
|`TYPE = IA_BIO`|Source des données biométriques|`BIO_SOURCE_DONNEES`|`FOURNIES_LICITES` · `SCRAPING_INTERNET` · `CCTV_NON_CIBLE` · `AUTRE_SOURCE`|
|`TYPE = IA_BIO` + `BIO_TYPE = CATEGORISATION`|Inférence d'attributs sensibles ?|`BIO_ATTR_SENSIBLES`|`OUI` · `NON`|
|`TYPE = IA_BIO` + `DOM = SECURITE`|Identification temps réel dans espace public ?|`BIO_TEMPS_REEL_PUBLIC`|`OUI` · `NON`|
|`TYPE = IA_SCORING`|Portée du scoring|`SCORING_PORTEE`|`CONTEXTUEL` · `GLOBAL_MULTI_DOMAINES`|
|`DOM = MARKETING` **OU** `PUB ∋ VULNERABLES`|Techniques subliminales ou délibérément trompeuses ?|`TECHNIQUES_SUBLIMINALES`|`OUI` · `NON`|
|`DOM = MARKETING` + `PUB ∋ VULNERABLES`|Persuasion ciblée sur vulnérabilités ?|`PERSUASION_PSYCHOLOGIQUE`|`OUI` · `NON`|
|`DOM = SECURITE` + `TYPE = IA_SCORING`|Évaluation du risque criminel individuel ?|`PREDICTION_CRIMINELLE`|`OUI` · `NON`|
|`DOM = RH` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Usage RH spécifique|`RH_USAGE`|`TRI_CV` · `SCORING_CANDIDATS` · `DECISION_EMBAUCHE` · `EVAL_PERFORMANCE` · `SURVEILLANCE_COMPORTEMENT` · `DECISION_PROMOTION` · `DECISION_LICENCIEMENT` · `ATTRIBUTION_TACHES` · `REDACTION_OFFRES`|
|`DOM = EDUCATION` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Usage éducatif spécifique|`EDUC_USAGE`|`ADMISSION` · `ORIENTATION` · `EVALUATION_ACADEMIQUE` · `RECOMMENDATION_CONTENU` · `AUTRE`|
|`TYPE ∈ {LLM_GEN, IA_GEN}` + `DIFF ∈ {TIERS, PUBLIC}`|Interaction directe temps réel ?|`INTERACTION_DIRECTE`|`OUI` · `NON`|
|`TYPE = IA_GEN`|Type de contenu généré|`GEN_CONTENU`|`TEXTE` · `IMAGE` · `AUDIO` · `VIDEO` · `DEEPFAKE`|
|`DOM = SANTE` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Finalité santé|`SANTE_FINALITE`|`ASSURANCE_RISQUE` · `DECISION_MEDICALE` · `GESTION_INTERNE`|
|`DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}` + `PUB ∋ {GRAND_PUBLIC, VULNERABLES}` + `DOM ∈ {AUTRE, SANTE, CREDIT}`|Détermination d'éligibilité à des prestations essentielles ?|`USAGE_PRESTATIONS_ESSENTIELLES`|`OUI` · `NON`|

### Priorité d'évaluation

```
INACCEPTABLE (0)  >  HAUT_RISQUE (1)  >  RISQUE_LIMITE (2)  >  RISQUE_MINIMAL (3)
```

**Règle d'évaluation :** Les blocs sont évalués dans l'ordre. Dès qu'une règle est déclenchée, on retourne immédiatement le résultat sans évaluer les blocs suivants. Le niveau le plus sévère l'emporte toujours.

### Conventions de notation

- `[TEXTE_EXPLICITE]` — La règle s'appuie directement sur un article précis du règlement.
- `[INTERPRETATION]` — La règle est une interprétation raisonnable ; aucun texte ne la formule littéralement. À signaler en tant que telle dans l'interface.
- `FLAG_ZONE_GRISE` — La combinaison est ambiguë ; l'outil doit signaler un avertissement et recommander une consultation juridique.
- `AGGRAVATION` — Alerte non-classificatoire qui s'ajoute à un niveau, signalant un facteur aggravant (ex : absence de contrôle humain sur un haut risque).

---

## A. RÈGLES DE CLASSIFICATION — PSEUDO-CODE

### Fonction principale

```
FONCTION classifier(TYPE, DOM, DEC, PUB, DATA, DIFF, CTRL, vars_conditionnelles):

    ╔══════════════════════════════════════════════════════╗
    ║  BLOC 1 — INACCEPTABLE  (Art. 5)                     ║
    ║  Applicable depuis le 2 février 2025                 ║
    ╚══════════════════════════════════════════════════════╝

    ─── R-I-01 — Inférence d'émotions au travail / en éducation ───────────────
    SI TYPE = IA_BIO
       ET BIO_TYPE = RECOG_EMOTIONS
       ET DOM ∈ {RH, EDUCATION}
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-01",
            article   : "Art. 5 §1 f)",
            raison    : "L'inférence des émotions par IA sur le lieu de travail ou dans
                         l'enseignement est explicitement interdite, sauf usage médical
                         ou sécuritaire dûment justifié.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-02 — Catégorisation biométrique d'attributs sensibles ─────────────
    SI TYPE = IA_BIO
       ET BIO_TYPE = CATEGORISATION
       ET BIO_ATTR_SENSIBLES = OUI
       // Attributs sensibles : race, opinions politiques, syndicat, religion,
       // croyances philosophiques, vie sexuelle, orientation sexuelle
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-02",
            article   : "Art. 5 §1 g)",
            raison    : "La catégorisation biométrique inférant des caractéristiques
                         protégées (race, religion, orientation sexuelle, etc.)
                         est interdite, sauf usage des forces de l'ordre expressément
                         encadré.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-03 — Social scoring global ────────────────────────────────────────
    SI TYPE = IA_SCORING
       ET SCORING_PORTEE = GLOBAL_MULTI_DOMAINES
       ET DEC ∈ {SEMI_AUTO, FULL_AUTO}
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-03",
            article   : "Art. 5 §1 c)",
            raison    : "Un système de notation sociale globale des individus sur plusieurs
                         domaines de vie (social scoring), entraînant un traitement
                         défavorable ou disproportionné, est interdit.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-04 — Exploitation de la vulnérabilité à des fins de manipulation ──
    SI PUB ∋ VULNERABLES
       ET DOM = MARKETING
       ET PERSUASION_PSYCHOLOGIQUE = OUI
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-04",
            article   : "Art. 5 §1 b)",
            raison    : "L'exploitation des vulnérabilités (âge, handicap, précarité
                         socio-économique) pour manipuler substantiellement le comportement
                         commercial est interdite.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-05 — Identification biométrique en temps réel dans l'espace public ─
    SI TYPE = IA_BIO
       ET BIO_TYPE = IDENTIFICATION
       ET BIO_TEMPS_REEL_PUBLIC = OUI
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-05",
            article   : "Art. 5 §1 h)",   // CORRIGÉ : était §1 d) en v1.0 (erreur)
            raison    : "L'identification biométrique à distance en temps réel dans les
                         espaces publics est interdite pour les acteurs privés.
                         (Exceptions réservées aux forces de l'ordre, strictement
                         encadrées — hors scope PME.)",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-06 — Constitution de bases de données faciales par collecte non ciblée
    SI TYPE = IA_BIO
       ET BIO_TYPE = IDENTIFICATION
       ET BIO_SOURCE_DONNEES ∈ {SCRAPING_INTERNET, CCTV_NON_CIBLE}
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-06",
            article   : "Art. 5 §1 e)",
            raison    : "La création ou l'expansion de bases de données de reconnaissance
                         faciale par collecte non ciblée sur Internet ou par CCTV
                         est interdite.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-07 — Manipulation subliminale ou trompeuse ─────────────────────────
    SI TECHNIQUES_SUBLIMINALES = OUI
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-07",
            article   : "Art. 5 §1 a)",
            raison    : "Les techniques subliminales ou délibérément trompeuses altérant
                         substantiellement le comportement et causant un préjudice
                         sont interdites.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-I-08 — Évaluation individuelle du risque criminel par profilage ──────
    SI DOM = SECURITE
       ET TYPE = IA_SCORING
       ET PREDICTION_CRIMINELLE = OUI
    ALORS
        RETOURNER {
            niveau    : "INACCEPTABLE",
            regle_id  : "R-I-08",
            article   : "Art. 5 §1 d)",
            raison    : "L'évaluation du risque qu'une personne commette une infraction
                         pénale, fondée uniquement sur le profilage ou les traits de
                         personnalité, est interdite.",
            type_regle: TEXTE_EXPLICITE
        }

    // ── Cas partiellement détectables — forcer FLAG_ZONE_GRISE ──────────────
    SI TYPE ∈ {LLM_GEN, IA_GEN, IA_SCORING}
       ET DOM = MARKETING
       ET PUB ∋ VULNERABLES
       ET PERSUASION_PSYCHOLOGIQUE = NON
       ET TECHNIQUES_SUBLIMINALES = NON
    ALORS
        AJOUTER_ALERTE {
            type    : "FLAG_ZONE_GRISE",
            message : "Contexte de marketing vers personnes vulnérables. Vérifier l'absence
                       de manipulation subliminale ou d'exploitation de vulnérabilités
                       (Art. 5 §1 a) et b)). Consulter un juriste.",
            article : "Art. 5 §1 a) et b)"
        }
        // Continuer l'évaluation vers le bloc suivant


    ╔══════════════════════════════════════════════════════╗
    ║  BLOC 2 — HAUT RISQUE  (Art. 6 §2 + Annexe III)     ║
    ║  Applicable au 2 août 2026 pour les déployeurs PME   ║
    ╚══════════════════════════════════════════════════════╝

    ─── R-H-01 — Biométrie : identification et contrôle d'accès ────────────────
    SI TYPE = IA_BIO
       ET BIO_TYPE ∈ {IDENTIFICATION, CONTROLE_ACCES}
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-01",
            article   : "Art. 6 §2 + Annexe III §1 a)",
            raison    : "Les systèmes biométriques d'identification des personnes ou de
                         contrôle d'accès sont des systèmes d'IA à haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-02 — RH : recrutement et sélection de candidats ───────────────────
    SI DOM = RH
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET PUB ∋ EMPLOYES
       ET RH_USAGE ∈ {TRI_CV, SCORING_CANDIDATS, DECISION_EMBAUCHE}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-02",
            article   : "Art. 6 §2 + Annexe III §4 a)",
            raison    : "Tout système IA influençant la présélection, le classement ou
                         la sélection des candidats à l'embauche est haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-03 — RH : gestion, surveillance et évaluation des salariés ─────────
    SI DOM = RH
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET PUB ∋ EMPLOYES
       ET RH_USAGE ∈ {EVAL_PERFORMANCE, SURVEILLANCE_COMPORTEMENT,
                      ATTRIBUTION_TACHES, DECISION_PROMOTION, DECISION_LICENCIEMENT}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-03",
            article   : "Art. 6 §2 + Annexe III §4 b)",
            raison    : "Tout système IA influençant la gestion, la surveillance ou
                         l'évaluation des salariés avec impact sur leurs conditions
                         de travail est haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-04 — Éducation : admission, évaluation, orientation ────────────────
    SI DOM = EDUCATION
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET EDUC_USAGE ∈ {ADMISSION, ORIENTATION, EVALUATION_ACADEMIQUE}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-04",
            article   : "Art. 6 §2 + Annexe III §3",
            raison    : "Les systèmes IA déterminant l'accès à la formation, les résultats
                         d'évaluation académique ou l'orientation professionnelle
                         sont haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-05 — Crédit et solvabilité ─────────────────────────────────────────
    SI DOM = CREDIT
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET PUB ∋ {CLIENTS OU GRAND_PUBLIC}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-05",
            article   : "Art. 6 §2 + Annexe III §5 b)",
            raison    : "Les systèmes IA d'évaluation de la solvabilité, de scoring crédit
                         ou d'établissement de profil de risque financier pour
                         des personnes physiques sont haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-06 — Assurance santé / assurance vie ───────────────────────────────
    SI DOM = SANTE
       ET SANTE_FINALITE = ASSURANCE_RISQUE
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET PUB ∋ {CLIENTS OU GRAND_PUBLIC}
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-06",
            article   : "Art. 6 §2 + Annexe III §5 c)",
            raison    : "Les systèmes IA évaluant le risque de santé ou de vie pour
                         la tarification ou l'éligibilité à une assurance sont haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-07 — Services essentiels : accès aux prestations ─────────────────
    SI DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
       ET PUB ∋ {GRAND_PUBLIC, VULNERABLES}
       ET USAGE_PRESTATIONS_ESSENTIELLES = OUI
       // Prestations essentielles : aide sociale, eau, énergie, services d'urgence
    ALORS
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-07",
            article   : "Art. 6 §2 + Annexe III §5 a)",
            raison    : "Les systèmes IA déterminant l'accès à des services ou prestations
                         essentiels pour les personnes physiques sont haut risque.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-H-08 — Décision médicale directe ─────────────────────────────────────
    // NOTE : Les dispositifs médicaux IA relèvent de Art. 6 §1 + Annexe I
    // (en combinaison avec MDR/IVDR). Applicables au 2 août 2027.
    // Inclus ici pour signalement ; ne pas classer HAUT_RISQUE avant 2027
    // sans analyse juridique approfondie.
    SI DOM = SANTE
       ET SANTE_FINALITE = DECISION_MEDICALE
       ET DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}
    ALORS
        AJOUTER_ALERTE {
            type    : "FLAG_ZONE_GRISE",
            message : "Usage médical clinique détecté. Peut relever de Art. 6 §1 +
                       Annexe I (dispositif médical) applicable au 2 août 2027.
                       Consulter un juriste.",
            article : "Art. 6 §1 + Annexe I (MDR/IVDR)"
        }
        // Classer provisoirement HAUT_RISQUE par précaution
        RETOURNER {
            niveau    : "HAUT_RISQUE",
            regle_id  : "R-H-08",
            article   : "Art. 6 §1 + Annexe I (interprétation, Art. 6 §2 par analogie)",
            raison    : "Les systèmes IA influençant des décisions médicales cliniques
                         sont présumés haut risque. Vérifier le régime applicable
                         (MDR 2017/745 ou IVDR 2017/746).",
            type_regle: INTERPRETATION
        }

    ─── Signal d'amplification (non-classificatoire) ────────────────────────────
    SI niveau_courant = HAUT_RISQUE
       ET CTRL = AUCUN
    ALORS
        AJOUTER_ALERTE {
            type    : "AGGRAVATION",
            message : "Absence totale de contrôle humain sur un système haut risque.
                       Art. 26 §2 exige un contrôle humain effectif par des personnes
                       compétentes. Non-conformité caractérisée.",
            article : "Art. 26 §2"
        }
	─── Cas borderline RH avec usage à risque mais déclaré informatif ─── 
	SI DOM = RH 
		ET RH_USAGE ∈ {
			TRI_CV, 
			SCORING_CANDIDATS, 
			EVAL_PERFORMANCE, 
			SURVEILLANCE_COMPORTEMENT, 
			DECISION_EMBAUCHE, 
			DECISION_PROMOTION, 
			DECISION_LICENCIEMENT
			} 
		ET DEC = INFORMATIF
		ET PUB ∋ EMPLOYES 
		
	ALORS AJOUTER_ALERTE { 
			type : "FLAG_ZONE_GRISE", 
			regle_id: "R-H-BORDERLINE", 
			message : "Usage IA en RH déclaré 'informatif' mais portant sur un domaine sensible. 
			ZG-01 applicable : si la sortie IA influence en pratique la décision finale (ce qui est souvent le cas),
			classification HAUT_RISQUE recommandée par précaution. Vérification juridique conseillée.", 
			article : "Annexe III §4 a) + ZG-01" } 
			// Continuer vers défaut (RISQUE_MINIMAL) mais avec alerte forte

    ╔══════════════════════════════════════════════════════╗
    ║  BLOC 3 — RISQUE LIMITÉ  (Art. 50)                   ║
    ║  Applicable au 2 août 2026                           ║
    ╚══════════════════════════════════════════════════════╝

    ─── R-L-01 — Chatbot / assistant interagissant directement avec des personnes
    SI TYPE ∈ {LLM_GEN, IA_GEN}
       ET DIFF ∈ {TIERS, PUBLIC}
       ET PUB ∋ {CLIENTS, GRAND_PUBLIC, VULNERABLES}
       ET INTERACTION_DIRECTE = OUI
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-01",
            article   : "Art. 50 §1",
            raison    : "Tout système IA interagissant directement avec des personnes
                         doit les informer clairement qu'elles interagissent
                         avec une IA (sauf si cela est évident).",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-L-02 — Contenu image / audio / vidéo synthétique diffusé à des tiers ─
    SI TYPE = IA_GEN
       ET GEN_CONTENU ∈ {IMAGE, AUDIO, VIDEO}
       ET DIFF ∈ {TIERS, PUBLIC}
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-02",
            article   : "Art. 50 §2",
            raison    : "Les contenus synthétiques (images, sons, vidéos) générés par IA
                         doivent être signalés comme tels avec un marquage
                         lisible par machine (ex : standard C2PA).",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-L-03 — Deepfake / hypertrucage diffusé ───────────────────────────────
    SI TYPE = IA_GEN
       ET GEN_CONTENU = DEEPFAKE
       ET DIFF ∈ {TIERS, PUBLIC}
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-03",
            article   : "Art. 50 §4",
            raison    : "Les hypertrucages (deepfakes) doivent être explicitement déclarés
                         comme générés par IA lors de leur diffusion, sauf parodie
                         ou satire clairement identifiée.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-L-04 — Reconnaissance d'émotions hors lieu de travail / éducation ────
    SI TYPE = IA_BIO
       ET BIO_TYPE = RECOG_EMOTIONS
       ET DOM ∉ {RH, EDUCATION}    // → INACCEPTABLE si RH ou EDUCATION (R-I-01)
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-04",
            article   : "Art. 50 §3",
            raison    : "Tout système de reconnaissance d'émotions utilisé hors lieu
                         de travail et hors éducation doit informer les personnes
                         de son utilisation.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-L-05 — Catégorisation biométrique sans attributs sensibles ───────────
    SI TYPE = IA_BIO
       ET BIO_TYPE = CATEGORISATION
       ET BIO_ATTR_SENSIBLES = NON    // → INACCEPTABLE si OUI (R-I-02)
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-05",
            article   : "Art. 50 §3",
            raison    : "Tout système de catégorisation biométrique doit informer
                         les personnes concernées de son utilisation.",
            type_regle: TEXTE_EXPLICITE
        }

    ─── R-L-06 — Texte IA généré et diffusé publiquement ───────────────────────
    // [INTERPRÉTATION] Art. 50 §4 vise explicitement image/audio/vidéo.
    // Le texte pur n'est pas formellement inclus, mais la pratique recommande
    // une signalisation par précaution.
    SI TYPE ∈ {LLM_GEN, IA_GEN}
       ET GEN_CONTENU = TEXTE
       ET DIFF = PUBLIC
       ET PUB ∋ {CLIENTS, GRAND_PUBLIC}
       ET INTERACTION_DIRECTE = NON    // → sinon couvert par R-L-01
    ALORS
        RETOURNER {
            niveau    : "RISQUE_LIMITE",
            regle_id  : "R-L-06",
            article   : "Art. 50 §4 (interprétation — texte non couvert explicitement)",
            raison    : "La diffusion publique de contenu textuel généré par IA est
                         recommandée d'être signalée. Le texte n'est pas explicitement
                         visé par Art. 50 §4, mais la bonne pratique l'impose.",
            type_regle: INTERPRETATION
        }


    ╔══════════════════════════════════════════════════════╗
    ║  DÉFAUT — RISQUE MINIMAL                             ║
    ╚══════════════════════════════════════════════════════╝

    RETOURNER {
        niveau    : "RISQUE_MINIMAL",
        regle_id  : "DEFAULT",
        article   : "N/A",
        raison    : "Aucun critère INACCEPTABLE, HAUT_RISQUE ou RISQUE_LIMITE n'est
                     satisfait. Aucune obligation réglementaire AI Act spécifique.",
        type_regle: N/A
    }

FIN FONCTION
```

---

## B. CAS DÉCLENCHANT AUTOMATIQUEMENT « INACCEPTABLE »

**Base légale :** Art. 5 §1 — **Applicable depuis le 2 février 2025** **Sanction :** Art. 99 — jusqu'à **35 M€ ou 7% du CA mondial** (montant le plus faible pour PME)

|ID|Pratique interdite|Conditions de déclenchement|Article|Type de règle|
|---|---|---|---|---|
|**I-01**|Inférence des émotions sur le lieu de travail ou dans l'enseignement|`TYPE=IA_BIO` + `BIO_TYPE=RECOG_EMOTIONS` + `DOM ∈ {RH, EDUCATION}`|Art. 5 §1 f)|TEXTE EXPLICITE|
|**I-02**|Catégorisation biométrique inférant des attributs sensibles (race, religion, orientation sexuelle, opinions politiques, syndicat)|`TYPE=IA_BIO` + `BIO_TYPE=CATEGORISATION` + `BIO_ATTR_SENSIBLES=OUI`|Art. 5 §1 g)|TEXTE EXPLICITE|
|**I-03**|Social scoring global (notation sociale multi-domaines affectant les droits)|`TYPE=IA_SCORING` + `SCORING_PORTEE=GLOBAL_MULTI_DOMAINES` + `DEC ∈ {SEMI_AUTO, FULL_AUTO}`|Art. 5 §1 c)|TEXTE EXPLICITE|
|**I-04**|Exploitation de la vulnérabilité à des fins de manipulation comportementale|`PUB ∋ VULNERABLES` + `DOM=MARKETING` + `PERSUASION_PSYCHOLOGIQUE=OUI`|Art. 5 §1 b)|TEXTE EXPLICITE|
|**I-05**|Identification biométrique à distance en temps réel dans un espace public (acteurs privés)|`TYPE=IA_BIO` + `BIO_TYPE=IDENTIFICATION` + `BIO_TEMPS_REEL_PUBLIC=OUI`|Art. 5 §1 h)|TEXTE EXPLICITE|
|**I-06**|Constitution de BDD de reconnaissance faciale par collecte non ciblée (Internet, CCTV)|`TYPE=IA_BIO` + `BIO_TYPE=IDENTIFICATION` + `BIO_SOURCE_DONNEES ∈ {SCRAPING_INTERNET, CCTV_NON_CIBLE}`|Art. 5 §1 e)|TEXTE EXPLICITE|
|**I-07**|Manipulation subliminale ou délibérément trompeuse causant un préjudice|`TECHNIQUES_SUBLIMINALES=OUI`|Art. 5 §1 a)|TEXTE EXPLICITE|
|**I-08**|Évaluation du risque criminel d'une personne basée uniquement sur profilage|`DOM=SECURITE` + `TYPE=IA_SCORING` + `PREDICTION_CRIMINELLE=OUI`|Art. 5 §1 d)|TEXTE EXPLICITE|

> **⚠️ Note de développement :** Les règles I-03, I-04, I-06, I-07, I-08 dépendent toutes de variables conditionnelles déclaratives. Si le client ne sélectionne pas la condition d'affichage de ces variables (ex : `DOM ≠ MARKETING` et `PUB ⊅ VULNERABLES` → la question `TECHNIQUES_SUBLIMINALES` n'est pas posée), la règle ne pourra pas se déclencher. C'est volontaire : on évite de poser des questions agressives à des clients dont le contexte n'est pas à risque.

> **Exemples PME à proscrire absolument :**
> 
> - Logiciel analysant les micro-expressions des candidats en entretien vidéo → **I-01**
> - Système de scoring comportemental des salariés (ponctualité, productivité, humeur) affectant leur évaluation → **I-03**
> - Outil de persuasion ciblant des personnes âgées ou endettées → **I-04**
> - Caméra IA identifiant en temps réel les clients dans un espace commercial public → **I-05**

---

## C. CAS DÉCLENCHANT AUTOMATIQUEMENT « HAUT RISQUE »

**Base légale :** Art. 6 §2 + Annexe III — **Applicable au 2 août 2026** **Sanction :** Art. 99 — jusqu'à **15 M€ ou 3% du CA mondial** (montant le plus faible pour PME)

### Tableau Annexe III × Conditions PME

|Annexe III|Domaine|Conditions de déclenchement|Règle|Exemple concret PME|
|---|---|---|---|---|
|**§1 a)**|Biométrie — identification|`TYPE=IA_BIO` + `BIO_TYPE ∈ {IDENTIFICATION, CONTROLE_ACCES}` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|R-H-01|Badge de présence par reconnaissance faciale ; accès aux locaux par biométrie|
|**§3**|Éducation — admission|`DOM=EDUCATION` + `DEC ∈ {AIDE_DEC...}` + `EDUC_USAGE=ADMISSION`|R-H-04|Outil de sélection pour entrée dans une école privée ou formation payante|
|**§3**|Éducation — évaluation|`DOM=EDUCATION` + `DEC ∈ {AIDE_DEC...}` + `EDUC_USAGE=EVALUATION_ACADEMIQUE`|R-H-04|Notation automatique de copies ou d'exercices par IA dans un organisme de formation|
|**§3**|Éducation — orientation|`DOM=EDUCATION` + `DEC ∈ {AIDE_DEC...}` + `EDUC_USAGE=ORIENTATION`|R-H-04|Outil IA d'orientation vers des filières professionnelles pour les apprenants|
|**§4 a)**|Emploi — recrutement|`DOM=RH` + `DEC ∈ {AIDE_DEC...}` + `PUB ∋ EMPLOYES` + `RH_USAGE ∈ {TRI_CV, SCORING_CANDIDATS, DECISION_EMBAUCHE}`|R-H-02|Logiciel SaaS de tri de CV ; chatbot de pré-qualification de candidats ; outil de scoring entretien|
|**§4 b)**|Emploi — gestion salariés|`DOM=RH` + `DEC ∈ {AIDE_DEC...}` + `PUB ∋ EMPLOYES` + `RH_USAGE ∈ {EVAL_PERFORMANCE, SURVEILLANCE, DECISION_PROMOTION, DECISION_LICENCIEMENT}`|R-H-03|Mesure de productivité IA ; évaluation des performances avec recommandation automatique ; surveillance télétravail|
|**§5 a)**|Services essentiels — éligibilité|`DEC ∈ {AIDE_DEC...}` + `PUB ∋ {GRAND_PUBLIC, VULNERABLES}` + `USAGE_PRESTATIONS_ESSENTIELLES=OUI`|R-H-07|Outil d'assistance sociale IA ; accès aux services d'urgence déterminé par IA|
|**§5 b)**|Crédit / solvabilité|`DOM=CREDIT` + `DEC ∈ {AIDE_DEC...}` + `PUB ∋ {CLIENTS, GRAND_PUBLIC}`|R-H-05|Outil fintech de scoring crédit ; IA d'évaluation de risque pour paiement fractionné (BNPL)|
|**§5 c)**|Assurance santé / vie|`DOM=SANTE` + `SANTE_FINALITE=ASSURANCE_RISQUE` + `DEC ∈ {AIDE_DEC...}` + `PUB ∋ {CLIENTS, GRAND_PUBLIC}`|R-H-06|Outil insurtech d'analyse de dossier médical pour tarification ; scoring de risque vie par IA|

### Exception Art. 6 §3 — Système Annexe III sans risque significatif

```
[EXCEPTION — Art. 6 §3]
SI règle HAUT_RISQUE déclenchée
   ET le_système_ne_contribue_pas_materiellement_à_la_decision = VRAI
   // Condition : l'IA ne détermine pas et n'influence pas substantiellement
   // la décision finale ; aucun risque pour santé, sécurité ou droits fondamentaux.
ALORS
    REMPLACER HAUT_RISQUE PAR FLAG_ZONE_GRISE
    MESSAGE = "Exception Art. 6 §3 potentiellement applicable. La charge de la preuve
               incombe au déployeur. Sans justification documentée solide, maintenir
               HAUT_RISQUE. Vérification juridique impérative."
```

> **Important :** Sans actes délégués précisant les critères (non encore publiés à ce jour), **ne pas appliquer Art. 6 §3 de façon automatique**. Traiter comme HAUT_RISQUE par défaut.

### Cas non haut risque malgré domaine RH/Éducation (à documenter)

```
[NON HAUT_RISQUE — rédaction d'offres d'emploi uniquement]
SI DOM = RH
   ET TYPE = LLM_GEN
   ET DEC = INFORMATIF
   ET RH_USAGE = REDACTION_OFFRES
   // L'IA rédige le texte de l'annonce ; elle ne voit pas les candidats
   // et n'influence pas leur sélection.
ALORS
    PAS de HAUT_RISQUE → évaluer RISQUE_LIMITE ou RISQUE_MINIMAL
    // [INTERPRETATION] Cohérent avec la note du Guide :
    // "LLM généraliste n'est PAS haut risque par défaut si sa sortie ne détermine
    // pas une décision sur des personnes dans un domaine Annexe III."
```

```
[FLAG_ZONE_GRISE — recommandation de contenu éducatif (sans exclusion)]
SI DOM = EDUCATION
   ET EDUC_USAGE = RECOMMENDATION_CONTENU
   ET l'apprenant_peut_librement_ignorer_la_recommandation = VRAI
   ET l'IA_n_exclut_aucun_apprenant = VRAI
ALORS
    FLAG_ZONE_GRISE → peut relever de Art. 6 §3 si absence de risque réel.
    // [INTERPRETATION] Si la recommandation est systématiquement suivie sans
    // alternative réelle, se rapproche d'une orientation → HAUT_RISQUE.
```

---

## D. CAS « RISQUE LIMITÉ » — Art. 50

**Applicable au 2 août 2026.**

|ID|Situation déclencheuse|Obligation principale|Article|Exemple PME|
|---|---|---|---|---|
|**L-01**|Chatbot / agent IA interagissant directement avec des clients ou le grand public|Informer clairement que l'interlocuteur est une IA|Art. 50 §1|Chatbot SAV alimenté par ChatGPT/Claude ; assistant virtuel sur site e-commerce|
|**L-02**|Génération d'images synthétiques diffusées à des tiers ou au public|Marquage machine-readable (ex: C2PA)|Art. 50 §2|Visuels publicitaires IA ; illustrations de newsletters générées par Midjourney/DALL-E|
|**L-03**|Génération d'audio ou vidéo synthétiques diffusés à des tiers|Marquage machine-readable + divulgation|Art. 50 §2|Voix IA dans un podcast commercial ; vidéo avatar synthétique ; doublage automatique|
|**L-04**|Deepfake / hypertrucage diffusé|Divulgation explicite du caractère artificiel|Art. 50 §4|Vidéo promotionnelle avec personne virtuelle réaliste ; deepfake pour campagne marketing|
|**L-05**|Reconnaissance d'émotions hors lieu de travail et hors éducation|Informer les personnes de l'utilisation|Art. 50 §3|Outil d'analyse d'émotions clients pour UX ; détection d'humeur dans un espace retail (hors RH)|
|**L-06**|Catégorisation biométrique sans attributs sensibles|Informer les personnes de l'utilisation|Art. 50 §3|Segmentation comportementale d'audience par caméra IA sans inférence d'attributs protégés|
|**L-07**|Texte généré par IA diffusé publiquement sans interaction temps réel|Mention recommandée (bonne pratique)|Art. 50 §4 (interprétation)|Article de blog, newsletter, fiche produit rédigés par IA et publiés en nom propre de la PME|

### Conditions d'exemption Art. 50

|Situation|Règle d'exemption|Base|
|---|---|---|
|Interaction où la nature IA est évidente du point de vue d'une personne raisonnable|L'obligation d'information (§1) peut être considérée satisfaite implicitement|Art. 50 §1 in fine|
|Parodie, satire ou œuvre artistique clairement identifiée|Deepfake exempt de divulgation explicite|Art. 50 §4 in fine|
|Usage strictement interne sans interaction avec des personnes extérieures|Art. 50 ne s'applique pas|Art. 50 §1 (portée limitée aux tiers)|
|Systèmes autorisés pour des finalités répressives légales|Non applicable aux forces de l'ordre|Art. 50 §6|

---

## E. CAS « RISQUE MINIMAL » — Règle par défaut

**Applicable quand aucune règle des sections B, C, D n'est déclenchée.** **Aucune obligation réglementaire AI Act spécifique.**

### Profil type d'un usage RISQUE_MINIMAL

```
TYPIQUEMENT RISQUE_MINIMAL SI :
  DIFF = INTERNE
  ET DEC ∈ {INFORMATIF, AIDE_DEC}
  ET DOM ∈ {PROD_INT, DEV_LOG}
  ET PUB = AUCUN  OU  (PUB ∋ EMPLOYES ET DEC = INFORMATIF)
  ET CTRL ∈ {SYSTEMATIQUE, ECHANTILLON}
  ET DATA ∈ {PAS_PERSO, PERSO_STD}
```

### Exemples PME typiques

|Usage IA|Justification du risque minimal|
|---|---|
|Copilot / Claude pour rédaction d'e-mails internes|Usage interne, informatif, aucune décision sur des personnes|
|ChatGPT pour résumé de documents ou de réunions|Usage interne, informatif, humain décide|
|DeepL / Google Translate pour traductions internes|Aucune décision sur des personnes physiques|
|Filtres anti-spam IA|Impact négligeable sur les droits fondamentaux|
|GitHub Copilot pour assistance au développement logiciel|Usage interne, le développeur valide chaque ligne|
|Outil IA d'analyse de données commerciales agrégées|Pas de données personnelles individuelles, pas de décision sur personnes|
|Chatbot FAQ interne (répondant aux employés)|Usage interne, employés informés, pas de décision automatisée impactante|
|Recommandation produit interne (catalogue, stock)|Pas d'impact direct sur droits ou accès à des services essentiels|
|IA de génération d'images pour usage interne (présentation, document)|Usage interne, pas de diffusion à des tiers|
|Outil IA de planification de planning ou d'agenda|Aide à la décision interne, humain reste décisionnaire|

> **Recommandation bonne pratique :** Même sans obligation légale, tenir un **registre interne des usages IA** et maintenir un contrôle humain effectif. Prépare la PME à d'éventuelles évolutions réglementaires.

> **RGPD indépendant :** Le classement RISQUE_MINIMAL ne dispense pas de respecter le RGPD si des données personnelles sont traitées. Les deux règlements s'appliquent indépendamment.

---

## F. MATRICE DES OBLIGATIONS

### F.1 — Niveau INACCEPTABLE

```
OBLIGATION = INTERDICTION ABSOLUE D'UTILISATION
```

|#|Action requise|Base légale|Délai|
|---|---|---|---|
|INA-01|Arrêt immédiat de l'usage ou du déploiement du système|Art. 5 §1|Depuis le **2 février 2025**|
|INA-02|Retrait ou résiliation de tout contrat lié à cet usage spécifique|Art. 5 §1|Immédiat|
|INA-03|Documentation écrite de la décision d'arrêt (preuve de conformité)|Bonne pratique|Immédiat|
|INA-04|Information du DPO et de la direction générale|Bonne pratique + RGPD si données perso|Immédiat|
|INA-05|Vérification qu'aucun sous-traitant ou partenaire n'utilise ce système pour le compte de la PME|Art. 5 §1 (responsabilité du déployeur)|Immédiat|

---

### F.2 — Niveau HAUT RISQUE

**Applicable à partir du 2 août 2026 pour les déployeurs PME** (Art. 26 du règlement) **Allègement PME :** Obligations proportionnées à la taille de l'organisation (Art. 17 §2 ; Art. 34).

|#|Obligation|Article AI Act|Description|Action PME concrète|
|---|---|---|---|---|
|HR-01|Respect de la notice d'utilisation|Art. 26 §1|Utiliser le système strictement selon les instructions fournies par le fournisseur|Lire, conserver et appliquer la documentation technique du fournisseur ; ne pas utiliser hors des cas d'usage prévus|
|HR-02|Contrôle humain effectif|Art. 26 §2|Désigner des personnes compétentes et formées pour superviser le système|Nommer un responsable IA ; documenter le processus de validation humaine de chaque output sensible|
|HR-03|Conservation des logs ≥ 6 mois|Art. 26 §6|Conserver les journaux auto-générés qui sont sous le contrôle du déployeur|Configurer la politique de rétention des logs dans l'outil utilisé ; vérifier leur accessibilité en cas de contrôle|
|HR-04|Information des travailleurs|Art. 26 §7|Informer les salariés et leurs représentants avant tout déploiement d'IA sur le lieu de travail|Rédiger une note de service ; informer le CSE (si applicable) avant le déploiement|
|HR-05|Information des personnes soumises aux décisions IA|Art. 26 §11|Informer les personnes qu'elles font l'objet d'une décision influencée par un système d'IA haut risque|Ajouter une mention dans les contrats, formulaires d'accueil candidats, politiques de confidentialité|
|HR-06|Signalement d'incidents graves|Art. 26 §5|Informer le fournisseur ET l'autorité nationale compétente en cas d'incident ou de risque grave|Mettre en place une procédure d'escalade interne ; identifier l'autorité compétente (CNIL + autorité de surveillance du marché)|
|HR-07|Maîtrise de l'IA (AI literacy)|Art. 4|Assurer que le personnel a le niveau de compétence adéquat pour utiliser le système|Organiser des formations avant déploiement ; conserver les attestations de formation|
|HR-08|Analyse d'impact droits fondamentaux (AIFR)|Art. 27|Obligatoire pour : opérateurs d'infrastructure critique, prestataires de services publics, et certains secteurs définis|Vérifier si l'organisation entre dans le périmètre de Art. 27 ; si oui, réaliser et documenter l'analyse avant déploiement|

> **Note Art. 27 — Périmètre pour PME :** L'AIFR est requise pour les organismes publics et les entités privées qui fournissent des services publics, ainsi que pour les secteurs bancaires, assurantiels et certains services essentiels. Vérifier au cas par cas.

---

### F.3 — Niveau RISQUE LIMITÉ

**Applicable à partir du 2 août 2026** (Art. 50)

|#|Obligation|Article AI Act|Description|Action PME concrète|
|---|---|---|---|---|
|RL-01|Information utilisateur — interaction IA|Art. 50 §1|Informer de manière claire et compréhensible que l'interlocuteur est une IA|Afficher « Vous êtes en conversation avec un assistant IA » dès le début de toute interaction ; mention permanente visible|
|RL-02|Marquage contenu synthétique image/audio/vidéo|Art. 50 §2|Signaler les contenus générés par IA avec un marquage lisible par machine|Intégrer le marquage technique (standard C2PA recommandé) + mention visuelle « Contenu généré par IA » sur tout contenu diffusé|
|RL-03|Divulgation deepfake|Art. 50 §4|Déclarer explicitement le caractère artificiel des hypertrucages diffusés|Mention explicite et visible « Cette vidéo/image est intégralement générée par IA » sur chaque contenu|
|RL-04|Information sur usage biométrique|Art. 50 §3|Informer les personnes de l'utilisation d'un système de reconnaissance d'émotions ou de catégorisation biométrique|Mention dans la politique de confidentialité + information active (panneau, pop-up, bandeau) avant toute collecte|

---

### F.4 — Niveau RISQUE MINIMAL

|#|Situation|Action requise|
|---|---|---|
|RM-01|Aucune obligation légale AI Act|Aucune démarche réglementaire imposée par le règlement|
|RM-02|Bonne pratique recommandée|Tenir un registre interne des usages IA (type, objectif, données traitées, responsable)|
|RM-03|RGPD toujours applicable|Si données personnelles traitées : vérifier la base légale RGPD indépendamment de l'AI Act|
|RM-04|Préparation au futur|Documenter les usages dès maintenant facilite la conformité si le périmètre réglementaire s'élargit|

---

## G. ZONES GRISES

Cas ambigus que la matrice ne peut pas trancher automatiquement. Ces cas **doivent être signalés au client** avec le message : _« Classification incertaine — vérification recommandée avec un juriste spécialisé en droit du numérique. »_

|#|Scénario PME|Ambiguïté|Articles en jeu|Recommandation provisoire|
|---|---|---|---|---|
|**ZG-01**|LLM (Claude/ChatGPT) utilisé pour analyser et résumer des CV sans scoring automatique|L'IA ne classe pas formellement, mais oriente l'attention du recruteur. Si le résumé influe systématiquement sur la décision → potentiellement Annexe III §4. Art. 6 §3 pourrait exempter si le recruteur décide librement.|Art. 6 §2 + §3 + Annexe III §4|Documenter que la décision finale est humaine et indépendante du résumé IA. Si l'IA écarte ou filtre des candidats → traiter comme HAUT_RISQUE.|
|**ZG-02**|Scoring de leads CRM (probabilité d'achat) pour prioriser les contacts commerciaux|Influence les décisions commerciales mais ne crée pas d'effet juridique direct sur les clients. Ressemble au scoring Annexe III §5 mais pour un usage purement commercial, non financier.|Art. 6 §2 + Annexe III §5 b)|Si le scoring influence l'accès à des produits financiers ou crée des offres différenciées discriminatoires → HAUT_RISQUE. Sinon probablement RISQUE_MINIMAL. Vérifier.|
|**ZG-03**|Logiciel de suivi de productivité IA (captures d'écran, frappes clavier, temps actif) pour télétravailleurs|Surveillance comportementale (potentiellement Annexe III §4 b) ou simple outil de gestion opérationnelle ? Dépend du niveau d'automatisation des décisions RH qui en découlent.|Art. 6 §2 + Annexe III §4 b)|Si les données alimentent directement des décisions d'évaluation, promotion ou licenciement → HAUT_RISQUE. Si usage purement opérationnel sans impact RH → RISQUE_MINIMAL (mais vérifier conformité RGPD et jurisprudence CNIL).|
|**ZG-04**|Chatbot de recrutement (pré-qualification, premier contact candidat)|Est-ce un système de sélection (→ HAUT_RISQUE, Annexe III §4 a) ou un simple chatbot informatif (→ RISQUE_LIMITE, Art. 50 §1) ? La frontière est ténue si le chatbot élimine ou classe des candidats.|Art. 6 §2 + Annexe III §4 a) vs Art. 50 §1|Si le chatbot écarte ou classe des candidats (même partiellement) → HAUT_RISQUE. S'il collecte des informations transmises sans filtrage à un humain → RISQUE_LIMITE minimum. Vérifier la logique de tri.|
|**ZG-05**|PME qui intègre une API LLM (ex: OpenAI API) dans un produit SaaS revendu à d'autres entreprises|Risque de basculement déployeur → fournisseur (Art. 25). Si le SaaS est utilisé dans un domaine Annexe III par des clients tiers, la PME peut devenir fournisseur avec toutes les obligations associées.|Art. 3 §3/§4 + Art. 25|Analyser l'usage final du produit par les clients. Si usage Annexe III par des tiers → probable statut de fournisseur. Vérification impérative avec juriste avant commercialisation.|
|**ZG-06**|Scoring de « crédit » interne B2B (évaluation de la solvabilité d'un client professionnel avant délai de paiement)|Annexe III §5 b) vise les personnes physiques. Le B2B pur entre sociétés pourrait être hors scope, sauf si la « société » est en réalité un micro-entrepreneur ou un indépendant (personne physique).|Art. 6 §2 + Annexe III §5 b)|B2B pur entre sociétés distinctes → probablement hors scope. Si la contrepartie est une personne physique (auto-entrepreneur) → HAUT_RISQUE. Vérifier la nature juridique de la contrepartie.|
|**ZG-07**|IA de recommandation de parcours de formation (suggère des modules) sans bloquer l'accès|La distinction « recommander » vs « déterminer » est floue en pratique. Si les recommandations sont systématiquement suivies sans alternative réelle → équivalent fonctionnel d'une orientation (Annexe III §3).|Art. 6 §2 + §3 + Annexe III §3|Si l'apprenant accède librement à tous les contenus et peut ignorer la recommandation → Art. 6 §3 applicable (documenter). Si l'IA crée un chemin contraint sans alternative → HAUT_RISQUE.|
|**ZG-08**|Exception Art. 6 §3 : déployeur estimant que son système Annexe III ne présente pas de risque significatif|Les critères du « risque significatif » ne sont pas encore précisés par les actes délégués de la Commission (non publiés). La charge de la preuve est sur le déployeur mais les seuils restent flous.|Art. 6 §3|Ne pas appliquer cette exception sans documentation solide et avis juridique. Jusqu'à publication des actes délégués, maintenir la classification HAUT_RISQUE par précaution.|
|**ZG-09**|Marketing personnalisé utilisant données comportementales et socio-démographiques pour cibler des segments clients|Frontière entre segmentation marketing classique (autorisée) et « social scoring » interdit (Art. 5 §1 c). L'interdiction vise les scores créant des traitements défavorables dans des domaines sans rapport avec la source des données.|Art. 5 §1 c) + Annexe III|Si le scoring n'est utilisé qu'à des fins publicitaires dans le contexte commercial original → probablement RISQUE_LIMITE. Si le scoring conditionne l'accès à des services, tarifs ou droits → risque Art. 5. Vérifier la portée d'utilisation.|
|**ZG-10**|Obligation de transparence Art. 50 en contexte B2B (interlocuteur professionnel représentant une entreprise)|Art. 50 §1 s'applique-t-il quand l'utilisateur est un professionnel mandaté par son entreprise ? Le Bureau de l'IA n'a pas encore clarifié le périmètre B2B de l'Art. 50.|Art. 50 §1|Par précaution, informer systématiquement tout interlocuteur — y compris B2B — que l'échange implique une IA. Attendre les clarifications officielles du Bureau européen de l'IA.|

---

## ANNEXE 1 — Questions du questionnaire

### Questions de base (toujours posées)

|Ordre|Question|Variable|
|---|---|---|
|1|Quel type d'outil IA utilisez-vous principalement ?|`TYPE`|
|2|Dans quel domaine d'activité cet usage IA s'inscrit-il ?|`DOM`|
|3|Comment qualifieriez-vous la nature des décisions ou sorties produites par cette IA ?|`DEC`|
|4|Quel(s) public(s) est/sont concerné(s) par les sorties de cette IA ? _(plusieurs choix possibles)_|`PUB`|
|5|Quelles données sont traitées par cette IA ?|`DATA`|
|6|Comment les sorties de cette IA sont-elles diffusées ?|`DIFF`|
|7|Quel niveau de contrôle humain s'exerce sur les sorties de cette IA ?|`CTRL`|

### Questions conditionnelles (affichées selon les réponses)

|Condition d'affichage|Question|Variable|
|---|---|---|
|`TYPE = IA_BIO`|Quel est le sous-type de ce système biométrique ?|`BIO_TYPE`|
|`TYPE = IA_BIO`|Quelle est la source des données biométriques utilisées (datasets fournis, scraping internet, vidéosurveillance, autre) ?|`BIO_SOURCE_DONNEES`|
|`TYPE = IA_BIO` + `BIO_TYPE = CATEGORISATION`|Ce système infère-t-il des attributs sensibles (race, religion, orientation sexuelle, opinions politiques, appartenance syndicale) ?|`BIO_ATTR_SENSIBLES`|
|`TYPE = IA_BIO` + `DOM = SECURITE`|L'identification est-elle effectuée en temps réel dans un espace accessible au public ?|`BIO_TEMPS_REEL_PUBLIC`|
|`TYPE = IA_SCORING`|Ce scoring couvre-t-il plusieurs domaines de vie pour établir un profil global de la personne (emploi + finances + comportement social…) ?|`SCORING_PORTEE`|
|`DOM = MARKETING` **OU** `PUB ∋ VULNERABLES`|Le système utilise-t-il des techniques subliminales ou délibérément trompeuses pour influencer le comportement des personnes ?|`TECHNIQUES_SUBLIMINALES`|
|`DOM = MARKETING` + `PUB ∋ VULNERABLES`|Le système utilise-t-il des techniques de persuasion ciblées sur les vulnérabilités des personnes (âge, handicap, précarité) ?|`PERSUASION_PSYCHOLOGIQUE`|
|`DOM = SECURITE` + `TYPE = IA_SCORING`|Ce système évalue-t-il le risque qu'une personne commette une infraction pénale, sur la base de son profil ou de ses traits de personnalité ?|`PREDICTION_CRIMINELLE`|
|`DOM = RH` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Quel est l'usage RH spécifique de ce système ?|`RH_USAGE`|
|`DOM = EDUCATION` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Quel est l'usage éducatif spécifique de ce système ?|`EDUC_USAGE`|
|`TYPE ∈ {LLM_GEN, IA_GEN}` + `DIFF ∈ {TIERS, PUBLIC}`|Ce système interagit-il directement avec des utilisateurs en temps réel (chatbot, agent conversationnel) ?|`INTERACTION_DIRECTE`|
|`TYPE = IA_GEN`|Quel type de contenu ce système génère-t-il ?|`GEN_CONTENU`|
|`DOM = SANTE` + `DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}`|Quelle est la finalité de cet outil dans le domaine de la santé ?|`SANTE_FINALITE`|
|`DEC ∈ {AIDE_DEC, SEMI_AUTO, FULL_AUTO}` + `PUB ∋ {GRAND_PUBLIC, VULNERABLES}` + `DOM ∈ {AUTRE, SANTE, CREDIT}`|Cet outil détermine-t-il l'accès des personnes physiques à des prestations essentielles (aide sociale, énergie, eau, services d'urgence, etc.) ?|`USAGE_PRESTATIONS_ESSENTIELLES`|

---

## ANNEXE 2 — Calendrier de mise en conformité

|Date|Obligation applicable|Concerne les PME déployeuses ?|
|---|---|---|
|**2 février 2025** ✅|Interdictions Art. 5 (pratiques inacceptables)|✅ OUI — applicable immédiatement|
|**2 août 2025** ✅|Obligations fournisseurs de modèles GPAI (OpenAI, Anthropic…)|Indirect — responsabilité des fournisseurs|
|**2 août 2026** ⚠️|Application générale : Art. 26 (déployeurs haut risque), Art. 50 (transparence), Art. 4 (AI literacy)|✅ OUI — **date clé pour les déployeurs PME**|
|**2 août 2027**|Art. 6 §1 : systèmes haut risque liés à des produits Annexe I (dispositifs médicaux, machines)|Conditionnel (secteurs industriels et santé spécifiques)|
|**2 août 2030**|Systèmes haut risque utilisés par autorités publiques|Hors scope PME privées|

---

## ANNEXE 3 — Tests de cohérence (à exécuter avant développement)

Avant de coder le moteur, valider que la matrice produit le résultat attendu sur les 5 scénarios suivants.

|#|Scénario|Variables saisies|Résultat attendu|Règle|
|---|---|---|---|---|
|1|PME utilise ChatGPT pour résumer des CV — décision finale humaine indépendante|`TYPE=LLM_GEN, DOM=RH, DEC=INFORMATIF, PUB={EMPLOYES}, DATA=PERSO_STD, DIFF=INTERNE, CTRL=SYSTEMATIQUE, RH_USAGE=TRI_CV`|**RISQUE_MINIMAL** + alerte FLAG_ZONE_GRISE forte (ZG-01)|R-H-BORDERLINE (non classificatoire — Option B validée en semaine 1)|
|2|PME utilise IA qui score automatiquement les CV|`TYPE=IA_SCORING, DOM=RH, DEC=FULL_AUTO, PUB={EMPLOYES}, DATA=PERSO_STD, DIFF=INTERNE, CTRL=AUCUN, RH_USAGE=SCORING_CANDIDATS`|**HAUT_RISQUE** + alerte AGGRAVATION (CTRL=AUCUN)|R-H-02|
|3|Chatbot SAV sur le site de la PME|`TYPE=LLM_GEN, DOM=MARKETING, DEC=INFORMATIF, PUB={CLIENTS}, DATA=PERSO_STD, DIFF=PUBLIC, CTRL=ECHANTILLON, INTERACTION_DIRECTE=OUI`|**RISQUE_LIMITE**|R-L-01|
|4|Reconnaissance émotions des employés en visio|`TYPE=IA_BIO, DOM=RH, DEC=AIDE_DEC, PUB={EMPLOYES}, DATA=SENSIBLE, DIFF=INTERNE, CTRL=ECHANTILLON, BIO_TYPE=RECOG_EMOTIONS`|**INACCEPTABLE**|R-I-01|
|5|Copilot pour mails internes|`TYPE=LLM_GEN, DOM=PROD_INT, DEC=INFORMATIF, PUB=AUCUN, DATA=PERSO_STD, DIFF=INTERNE, CTRL=SYSTEMATIQUE`|**RISQUE_MINIMAL**|DEFAULT|

> **Validation :** si un de ces 5 scénarios ne donne pas le résultat attendu, identifier la règle défaillante et corriger avant de poursuivre le développement.

> **Note d'arbitrage (scénario 1) :** la matrice v1.0 énonçait HAUT_RISQUE pour ce cas. Le pseudo-code patché v1.1 (R-H-BORDERLINE non classificatoire) a été retenu en semaine 1 comme étant juridiquement plus défendable : si la PME déclare honnêtement DEC=INFORMATIF, le niveau de base reste RISQUE_MINIMAL. L'alerte forte ZG-01 attire l'attention du déployeur sur le risque de requalification factuelle si la sortie IA influence en pratique la décision finale. Cette ligne reflète désormais la version qui fait foi pour le moteur.

---

_Document généré sur la base du Règlement (UE) 2024/1689 (JO de l'UE, 12 juillet 2024) et du Guide de Conformité AI Act pour PME Françaises. Ne constitue pas un conseil juridique. Pour tout usage haut risque ou toute situation ambiguë, consulter un juriste spécialisé en droit du numérique._