**Règlement UE 2024/1689 — Texte officiel publié le 12 juillet 2024**
*Document destiné aux déployeurs PME — Perspective non-juridique, usage opérationnel*

***

## 1. Les 4 Niveaux de Risque

### Vue d'ensemble

| Niveau | Base légale | Description courte | Exemples PME | Obligations déployeur |
|--------|-------------|-------------------|--------------|----------------------|
| **Risque inacceptable** | Art. 5 | Pratiques **interdites** — utilisation totalement prohibée | Système de notation sociale des employés basé sur comportements ; manipulation subliminale de clients ; exploitation de vulnérabilités (âge, handicap) pour modifier le comportement | **Interdiction absolue** — aucun usage autorisé |
| **Haut risque** | Art. 6 + Annexe III | Systèmes pouvant affecter droits fondamentaux, santé, sécurité | Outil IA de sélection de CV / scoring RH ; évaluation solvabilité clients par IA ; IA pour accès formation ou orientation professionnelle | Art. 26 : nombreuses obligations (voir § 1.3) |
| **Risque limité** | Art. 50 | Systèmes nécessitant une **transparence** envers l'utilisateur | Chatbot client (type ChatGPT intégré au SAV) ; générateur de contenu IA (deepfakes, images) ; système de recommandation déclaré | Obligation d'information : indiquer que l'utilisateur interagit avec une IA |
| **Risque minimal** | (pas d'article dédié) | Usages sans risque significatif — aucune obligation spécifique | IA de filtrage spam, outil de traduction automatique, assistant rédaction interne (Copilot, Claude), analyse de données internes | **Aucune obligation réglementaire** — bonne pratique recommandée |

***

### 1.1 Risque Inacceptable (Art. 5)

**Définition :** Pratiques considérées comme incompatibles avec les valeurs de l'UE. Interdites dès le **2 février 2025**.

**Pratiques interdites listées (Art. 5, §1) :**
- Techniques subliminales ou manipulatrices altérant substantiellement le comportement
- Exploitation de vulnérabilités liées à l'âge, au handicap, à la situation socio-économique
- Notation sociale (« social scoring ») par entités publiques ou privées
- Identification biométrique en temps réel dans les espaces publics (sauf exceptions forces de l'ordre strictement encadrées)
- Systèmes d'inférence des émotions sur le lieu de travail ou dans l'enseignement
- Catégorisation biométrique inférant opinions politiques, religion, orientation sexuelle

**Exemples PME à proscrire absolument :**
- Logiciel qui analyse les micro-expressions des candidats en entretien vidéo pour inférer leur « fiabilité »
- Système de scoring comportemental des salariés affectant leurs conditions de travail
- Outil de persuasion ciblant des clients vulnérables (personnes âgées, endettées) de façon manipulatrice

***

### 1.2 Haut Risque (Art. 6 + Annexe III)

**Définition :** Systèmes dont les sorties influencent des décisions importantes concernant des personnes physiques, dans des domaines sensibles listés à l'Annexe III.

**Domaines haut risque pertinents pour PME (Annexe III) :**

| Domaine | Exemple d'usage PME |
|---------|-------------------|
| Biométrie (point 1) | Contrôle d'accès par reconnaissance faciale |
| Emploi, RH (point 4) | Outil de tri/classement de CV, évaluation performance salariés par IA |
| Éducation/formation (point 3) | Système d'admission ou orientation dans une école privée |
| Services essentiels (point 5) | IA de scoring crédit (banques, assurances, fintech) |
| Administration de la justice (point 8) | IA d'aide à la décision dans litiges (si sortie produit effet juridique) |

> ⚠️ **Note importante :** ChatGPT, Copilot ou Claude utilisés comme assistants de rédaction générale ou d'analyse interne **ne sont PAS haut risque** par défaut. Ils le deviendraient si leur sortie détermine directement une décision affectant des personnes dans un domaine listé ci-dessus.

***

### 1.3 Obligations Déployeur — Systèmes Haut Risque (Art. 26)

| Obligation | Détail (Art. 26) | Applicable PME |
|------------|-----------------|---------------|
| Respect de la notice d'utilisation | Utiliser le système conformément aux instructions du fournisseur | Oui — tous |
| Contrôle humain | Confier la supervision à des personnes compétentes et formées (§2) | Oui — tous |
| Conservation des journaux (logs) | Conserver les logs auto-générés ≥ 6 mois (§6) | Oui — si logs sous contrôle déployeur |
| Information des travailleurs | Informer les salariés et leurs représentants avant déploiement sur lieu de travail (§7) | Oui — employeurs |
| Information des personnes concernées | Informer les personnes qu'elles font l'objet d'une décision par IA haut risque (§11) | Oui — si décision sur personnes |
| Signalement incidents graves | Informer fournisseur + autorité de surveillance du marché (§5) | Oui — tous |
| Analyse d'impact droits fondamentaux | Avant déploiement, pour organismes publics et entités privées fournissant services publics + certains secteurs (Art. 27) | Conditionnel (voir Art. 27) |
| Maîtrise de l'IA | Former le personnel concerné (Art. 4) | Oui — tous |

> ℹ️ **Allègements PME :** L'Art. 17 §2 précise que la mise en œuvre des obligations est **proportionnée à la taille de l'organisation**. Les organismes notifiés doivent aussi réduire « la charge administrative et les coûts de mise en conformité pour les microentreprises et petites entreprises » (Art. 34).

***

### 1.4 Risque Limité (Art. 50) — Obligations de Transparence

**Définition :** Systèmes interagissant directement avec des personnes ou générant du contenu, sans être haut risque.

**Obligations (Art. 50) :**
- Informer l'utilisateur qu'il interagit avec une IA (chatbots, assistants)
- Signaler les contenus générés par IA (images, audio, vidéo synthétiques)
- Signaler les deepfakes

Applicable dès le **2 août 2026** (date d'application générale).

***

### 1.5 Risque Minimal

Aucune obligation spécifique du règlement. Concerne la majorité des usages IA bureautiques des PME :
- Copilot pour rédaction d'e-mails
- Claude / ChatGPT pour résumé de documents
- Outils de traduction (DeepL)
- Filtres anti-spam, recommandations produits sans impact sur droits

***

## 2. Fournisseur vs Déployeur

### Définitions légales (Art. 3)

| Rôle | Définition (Art. 3) | Exemples PME |
|------|--------------------|----|
| **Fournisseur** | Personne qui **développe** ou fait développer un système d'IA et le met sur le marché sous son nom/marque (§3) | Une PME qui crée un SaaS avec IA intégrée vendu à des clients ; une startup qui entraîne un modèle de scoring crédit |
| **Déployeur** | Personne qui **utilise** un système d'IA sous sa propre autorité dans un contexte professionnel (§4) | Une PME qui intègre ChatGPT/Claude dans son SAV ; une PME RH utilisant un outil de tri de CV tiers ; un cabinet comptable utilisant Copilot |

### Règle clé de basculement fournisseur → déployeur (Art. 25)

Un déployeur **devient fournisseur** (et assume toutes les obligations afférentes) s'il :
1. Commercialise le système sous son propre nom/marque
2. Apporte une **modification substantielle** au système haut risque
3. Modifie la destination d'un système non haut-risque de façon à le rendre haut risque

**Exemple PME concret :**
- Utiliser l'API d'OpenAI pour construire un outil de scoring RH vendu à d'autres entreprises = **fournisseur** (obligations lourdes)
- Utiliser ChatGPT en interne pour rédiger des offres d'emploi = **déployeur** (obligations allégées)

### Tableau comparatif des obligations

| Obligation | Fournisseur | Déployeur |
|-----------|-------------|-----------|
| Évaluation de conformité | ✅ Obligatoire (Art. 43) | ❌ Non |
| Documentation technique | ✅ Obligatoire (Art. 11, 18) | ❌ Non |
| Marquage CE | ✅ Obligatoire (Art. 48) | ❌ Non |
| Système de gestion qualité | ✅ Obligatoire (Art. 17) | ❌ Non |
| Respect notice d'utilisation | ❌ N/A (est l'auteur) | ✅ Obligatoire (Art. 26§1) |
| Contrôle humain | ✅ Concevoir les mécanismes | ✅ Mettre en œuvre (Art. 26§2) |
| Conservation des logs | ✅ Conserver si sous contrôle (Art. 19) | ✅ Conserver si sous contrôle ≥6 mois (Art. 26§6) |
| Information travailleurs | ❌ N/A | ✅ Avant déploiement (Art. 26§7) |
| Maîtrise de l'IA | ✅ Obligatoire (Art. 4) | ✅ Obligatoire (Art. 4) |
| Analyse impact droits fondamentaux | ❌ N/A | ✅ Conditionnel (Art. 27) |

***

## 3. Calendrier d'Application

### Chronologie globale (Art. 113)

| Date | Ce qui s'applique | Concerne les déployeurs PME ? |
|------|-------------------|-------------------------------|
| **2 février 2025** ✅ | **Chapitres I et II** : dispositions générales + **pratiques interdites (Art. 5)** | ✅ OUI — les interdictions s'appliquent immédiatement à tous |
| **2 août 2025** ✅ | Chapitre III §4 (organismes notifiés), Chapitre V (modèles IA usage général), Chapitre VII (gouvernance), Art. 78 + **sanctions** | Indirect — structure de gouvernance opérationnelle ; obligations fournisseurs de modèles GPAI (OpenAI, Anthropic…) |
| **2 août 2026** ⚠️ *(dans 3 mois)* | **Application générale du règlement** : systèmes haut risque (Annexe III), obligations transparence (Art. 50), obligations déployeurs (Art. 26) | ✅ OUI — **date clé pour les déployeurs PME** |
| **2 août 2027** | Art. 6 §1 : systèmes haut risque liés à des produits harmonisés (Annexe I, ex : dispositifs médicaux, machines) | Marginal pour PME sauf secteurs industriels spécifiques |
| **2 août 2030** | Systèmes haut risque utilisés par autorités publiques | Hors scope PME privées |

### Ce qui concerne spécifiquement les déployeurs PME au 2 août 2026

À partir du **2 août 2026**, toute PME déployant un système d'IA haut risque (Annexe III) doit avoir :

- [ ] Mis en place les **mesures techniques et organisationnelles** pour utiliser le système selon la notice d'utilisation (Art. 26§1)
- [ ] Désigné des **personnes responsables du contrôle humain** avec compétences et formation adéquates (Art. 26§2)
- [ ] Configuré la **conservation des logs** ≥ 6 mois si les logs sont sous son contrôle (Art. 26§6)
- [ ] Informé les **travailleurs concernés** (Art. 26§7)
- [ ] Informé les **personnes soumises aux décisions IA** (Art. 26§11)
- [ ] Mis en place une procédure de **signalement d'incidents graves** (Art. 26§5)
- [ ] Assuré la **maîtrise de l'IA** par le personnel (Art. 4)

> ⚠️ **Systèmes déjà en service avant le 2 août 2026 :** Si ces systèmes ne subissent pas de modification substantielle après cette date, ils ne sont soumis aux obligations qu'à partir de cette date. Les systèmes haut risque destinés aux autorités publiques bénéficient d'un délai jusqu'au **2 août 2030** (Art. 111 §2).

> ⚠️ **Modèles GPAI (ChatGPT, Claude, Copilot…) :** Leurs fournisseurs (OpenAI, Anthropic, Microsoft) devaient se conformer depuis le **2 août 2025**. Pour la PME déployeuse, c'est la responsabilité du fournisseur — mais la PME reste responsable de son propre usage.

***

## 4. Obligations de Transparence — Applicables au 2 août 2026

### Article 50 — Systèmes concernés et obligations

| Type de système | Obligation | Qui est tenu |
|----------------|------------|--------------|
| **Système interagissant avec des personnes** (chatbots, agents IA, assistants) | Informer de manière **claire et compréhensible** que l'utilisateur interagit avec une IA — sauf si c'est évident | Fournisseur ET déployeur |
| **Système de reconnaissance d'émotions** | Informer les personnes de son utilisation | Fournisseur ET déployeur |
| **Système de catégorisation biométrique** | Informer les personnes de son utilisation | Fournisseur ET déployeur |
| **Contenu généré par IA** (texte, image, audio, vidéo synthétique) | Signaler que le contenu est généré par IA (marquage machine-readable) | Fournisseur du modèle |
| **Deepfakes (hypertrucages)** | Divulguer explicitement le caractère artificiel — sauf exceptions (parodie, satire clairement labellisée) | Toute personne diffusant |

### Cas pratiques PME au 2 août 2026

**Chatbot SAV alimenté par ChatGPT/Claude :**
→ Obligation d'indiquer clairement « Vous êtes en conversation avec un assistant IA » dès le début de l'interaction.

**Newsletter générée par IA :**
→ Le déployeur (la PME) doit s'assurer que le contenu est identifié comme généré par IA si diffusé à des tiers (Art. 50 §4).

**Outil interne d'aide à la décision RH haut risque :**
→ En plus de l'Art. 50, l'Art. 26 §11 impose d'informer les candidats/salariés qu'ils sont soumis à un système d'IA haut risque.

**Copilot/Claude pour rédaction interne uniquement :**
→ Usage entre professionnels sans interaction directe avec des tiers : **pas d'obligation Art. 50** dans la plupart des cas — vérifier si une sortie est ensuite communiquée à des personnes externes.

### Exceptions à l'obligation de transparence (Art. 50 §5)

L'obligation de divulguer ne s'applique **pas** aux systèmes d'IA autorisés à des fins répressives (détection d'infractions) ni aux cas où la divulgation compromettrait l'enquête.

***

## 5. Récapitulatif — Sanctions PME (Art. 99)

| Infraction | Amende maximale (entreprise) | Plafond PME |
|-----------|------------------------------|-------------|
| Pratiques interdites (Art. 5) | 35 M€ ou 7% CA mondial | Montant le plus faible retenu |
| Manquements obligations haut risque / déployeurs | 15 M€ ou 3% CA mondial | Montant le plus faible retenu |
| Informations inexactes aux autorités | 7,5 M€ ou 1% CA mondial | Montant le plus faible retenu |

> ℹ️ L'Art. 99 §6 précise explicitement que **pour les PME (y compris jeunes pousses), chaque amende est plafonnée au montant le plus faible** entre les pourcentages et montants fixes, afin de ne pas pénaliser disproportionnellement les petites structures.

***

## 6. Points d'Incertitude — À Surveiller

> Les éléments suivants restent à préciser par des actes délégués, des normes harmonisées ou des orientations du Bureau de l'IA :

- **Classification précise des usages haut risque IA générative :** Un LLM utilisé pour automatiser des décisions RH est-il systématiquement haut risque ? La jurisprudence applicative manque à ce jour.
- **Périmètre exact de l'Art. 50 pour usages B2B :** L'obligation de transparence s'applique-t-elle aux seules interactions avec le grand public ou aussi inter-entreprises ? Pas encore clarifié par le Bureau de l'IA.
- **Modèles GPAI intégrés (API OpenAI dans un produit PME) :** La répartition des responsabilités fournisseur/déployeur dans les chaînes d'intégration API dépendra des clauses contractuelles et des actes délégués à venir.
- **Codes de bonne pratique :** Les codes devaient être prêts au 2 mai 2025 (Considérant 179). Leur contenu précis pour les PME déployeuses reste à vérifier sur le site du Bureau européen de l'IA (digital-strategy.ec.europa.eu).
- **Normes harmonisées :** Les standards CEN/CENELEC pour l'évaluation des systèmes haut risque ne sont pas encore tous publiés — leur disponibilité conditionne la facilité de mise en conformité pratique.

***

*Document généré sur la base du texte officiel du Règlement (UE) 2024/1689 (Journal officiel de l'UE, 12 juillet 2024). Ne constitue pas un conseil juridique. Pour les usages haut risque ou en cas de doute, consulter un juriste spécialisé en droit du numérique.*