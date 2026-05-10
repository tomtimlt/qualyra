# Spécifications Techniques et Référentiel de Conformité : Audit RGPD & AI Act pour les Usages IA en PME

L'adoption des outils d'intelligence artificielle (IA) connaît une accélération sans précédent au sein du tissu économique européen. L'analyse des dynamiques d'intégration technologique révèle que 31 % des TPE-PME françaises utilisent l'IA générative en 2025, une proportion qui a doublé en un an, dont 8 % de façon régulière. Face à cet engouement, tiré par la recherche de productivité, le déploiement s'effectue majoritairement de manière non structurée ("Shadow AI"), exposant les organisations à des risques juridiques et opérationnels majeurs.

Ce rapport d'expertise, conçu comme le référentiel d'un moteur d'audit automatisé, définit les spécifications techniques et juridiques régissant l'usage de l'IA en entreprise. Il fusionne les exigences du Règlement Général sur la Protection des Données (RGPD, UE 2016/679) et du Règlement européen sur l'Intelligence Artificielle (AI Act, UE 2024/1689).

La lecture et l'intégration de ce référentiel reposent sur un marquage de certitude juridique strict, appliqué à chaque affirmation :

- 🟢 **Texte explicite** : Obligation légale claire issue de la lettre du RGPD ou de l'AI Act.
    
- 🟡 **Interprétation stabilisée** : Position doctrinale arrêtée par la CNIL, le Comité Européen de la Protection des Données (CEPD/EDPB), ou la jurisprudence (CJUE).
    
- 🔴 **Zone grise** : Incertitude technico-juridique, qualification non tranchée, nécessitant une gestion des risques au cas par cas.
    

---

## Section 1 : Analyse d'Impact sur la Protection des Données (AIPD) — Article 35 RGPD

L'Analyse d'Impact sur la Protection des Données (AIPD), définie à l'article 35 du RGPD, constitue l'outil fondamental de la conformité algorithmique 🟢. Elle impose au responsable de traitement (la PME) d'évaluer la nécessité, la proportionnalité et les risques d'un traitement avant son déploiement.

### 1.1 Grille d'évaluation selon les lignes directrices du CEPD (WP248)

L'obligation de mener une AIPD n'est pas absolue, mais résulte d'un faisceau d'indices. 🟢 Le traitement requiert une AIPD s'il remplit au moins deux des neuf critères définis par le CEPD dans ses lignes directrices WP248. 🟡 La CNIL précise néanmoins que dans le contexte spécifique des systèmes d'IA, un seul de ces critères peut s'avérer suffisant si le risque inhérent à la technologie est jugé particulièrement critique (par exemple, un usage massif de données sensibles).

Le tableau ci-dessous détaille ces critères, leur traduction concrète dans les usages des PME, et leur stricte articulation avec la taxonomie des risques de l'AI Act.

|**Critère CEPD (WP248)**|**Application typique aux usages IA en PME**|**Articulation AI Act (UE 2024/1689)**|
|---|---|---|
|**1. Évaluation ou scoring**|🟡 Analyse sémantique des performances d'un employé via Microsoft Copilot ou prédiction de "churn" (départ) client.|🟢 Classification en HAUT RISQUE (Annexe III) si utilisé pour le recrutement, la promotion ou l'évaluation dans le domaine de l'emploi (Art. 6).|
|**2. Décision automatisée**|🟢 Tri automatisé de candidatures écartant des profils sans validation humaine significative (Art. 22 RGPD).|🟢 Exigence d'une supervision humaine (_Human Oversight_) garantie par conception (Art. 14).|
|**3. Surveillance systématique**|🟡 Outils de cybersécurité pilotés par l'IA analysant en continu les requêtes et comportements réseau des salariés.|🟡 Impose des mesures de transparence accrues envers les travailleurs et les représentants syndicaux.|
|**4. Données sensibles (Art. 9)**|🟢 Résumés d'entretiens RH générés par l'IA contenant des informations sur l'état de santé, les affiliations syndicales ou les croyances.|🟢 Le traitement de ces données pour corriger les biais est exceptionnellement toléré sous des conditions de sécurité draconiennes (Art. 10.5).|
|**5. Traitement à grande échelle**|🔴 La notion de "grande échelle" reste une zone grise pour une PME. L'utilisation d'un chatbot traitant le CRM complet peut s'en rapprocher.|🟡 Impacte les obligations du fournisseur de modèles d'IA à usage général (GPAI) plutôt que la PME (déployeur).|
|**6. Croisement de données**|🟡 Combinaison de bases de données internes avec du _web scraping_ automatisé pour enrichir des fiches prospects.|🟢 Obligation d'établir une documentation technique démontrant la qualité et la provenance des données (Art. 11).|
|**7. Personnes vulnérables**|🟢 Utilisation de l'IA sur des données d'employés (lien de subordination limitant la liberté de choix) ou d'élèves.|🟢 Les systèmes interagissant avec l'éducation, la formation professionnelle ou les employés relèvent d'emblée du HAUT RISQUE (Annexe III).|
|**8. Usage innovant**|🟡 Le déploiement de modèles de fondation génératifs (LLM) est qualifié d'usage technologique innovant par nature.|🟢 Touche à la définition même du système d'IA (Art. 3) et à la classification des risques.|
|**9. Entrave à un droit**|🟢 Algorithme filtrant l'accès à un crédit, à un service d'assurance ou rejetant l'accès à un portail client.|🟢 Renforce l'exigence d'exactitude, de robustesse technique et de cybersécurité pour éviter tout rejet abusif (Art. 15).|

### 1.2 Matrice décisionnelle des usages IA courants en PME

Afin d'outiller les dirigeants, l'évaluation du besoin d'AIPD doit être rationalisée. L'analyse des usages dominants, tels qu'identifiés par Bpifrance (génération de contenu pour 68 % des utilisateurs, recherche d'informations pour 57 %) , permet d'établir une matrice de conformité directe.

|**Typologie d'Usage IA par la PME**|**Critères CEPD activés (WP248)**|**Statut de l'AIPD**|**Qualification AI Act**|
|---|---|---|---|
|**Rédaction / Idéation Marketing** (Création de posts sociaux, traduction de plaquettes sans données clients).|Aucun. La donnée personnelle n'est pas l'objet du traitement.|🟢 **Non requise**|🟢 RISQUE MINIMAL.|
|**Chatbot Support Client** (IA de type RAG connectée à la base de connaissances publique de l'entreprise).|Aucun, à condition que le chatbot ne consolide pas de profil client.|🟡 **Non requise**, sauf si le _prompt_ capte des données identifiantes ou sensibles (🔴).|🟢 RISQUE LIMITÉ. Soumis aux obligations de transparence de l'Article 50 (Informer de la nature de l'IA).|
|**Assistant de Productivité Interne** (Analyse de notes de réunions via Copilot, tri d'emails).|Vulnérables (7), Usage innovant (8), Données sensibles potentielles (4).|🟡 **Fortement recommandée**. Le risque majeur est le "Sur-partage" (_Over-sharing_) des données internes.|🟢 RISQUE MINIMAL ou LIMITÉ, selon l'interface d'interaction.|
|**Outil de Tri de Candidatures (RH)** ou d'évaluation de la performance.|Scoring (1), Décision auto (2), Vulnérables (7), Entrave (9).|🟢 **OBLIGATOIRE**. Le traitement présente structurellement un risque élevé.|🔴 HAUT RISQUE. Implique des obligations lourdes pour le déployeur (Supervision, Logs, Information).|

---

## Section 2 : Bases Légales et Catégories de Données — Articles 6 et 9 RGPD

La licéité de tout traitement algorithmique impliquant des données personnelles exige de s'appuyer sur l'une des six bases légales de l'article 6 du RGPD 🟢. La singularité de l'IA réside dans la dissociation nécessaire entre les finalités : fournir le service au client n'équivaut pas juridiquement à entraîner le modèle.

### 2.1 L'Intérêt Légitime (Article 6.1.f) et l'Arrêt KNLTB

L'intérêt légitime s'impose comme la base légale privilégiée et la plus pragmatique pour le développement, le déploiement et l'utilisation de modèles d'IA en entreprise. L'incertitude planant sur la nature des intérêts valables a été récemment levée par la jurisprudence européenne.

🟢 L'arrêt de la Cour de justice de l'Union européenne (CJUE) du 4 octobre 2024, affaire C-621/22 (_Koninklijke Nederlandse Lawn Tennisbond - KNLTB_), stipule formellement qu'un intérêt de nature purement commerciale peut légitimement fonder un traitement, à la stricte condition que cet intérêt soit licite.

Cependant, l'invocation de cet intérêt commercial pour l'IA est sévèrement encadrée. 🟡 Le Comité Européen de la Protection des Données (CEPD), dans son Opinion 28/2024 du 17 décembre 2024, détaille le "Test en trois étapes" obligatoire que toute PME doit documenter :

|**Étape du Test CEPD**|**Exigences pour les modèles d'IA**|**Évaluation et Impact PME**|
|---|---|---|
|**1. Identification de l'intérêt**|L'intérêt doit être licite, déterminé de façon claire, actuel et non spéculatif.|🔴 Formuler l'intérêt comme "utiliser l'IA pour améliorer l'entreprise" est irrecevable. 🟡 Il faut cibler : "Optimiser le délai de réponse au support client de 30 % via l'analyse sémantique".|
|**2. Test de nécessité**|Le traitement est-il strictement requis pour atteindre l'intérêt? N'y a-t-il pas d'alternative moins intrusive?|🟡 Impose l'application du principe de minimisation (Art. 5.1.c RGPD). L'usage de l'IA générative n'est justifié que si des méthodes algorithmiques classiques s'avèrent inopérantes pour l'objectif visé.|
|**3. Mise en balance des droits**|Les attentes raisonnables des personnes et leurs droits fondamentaux ne doivent pas être supplantés par l'intérêt de la PME.|🟢 La PME est tenue de mettre en place des **garanties compensatoires** robustes : option de retrait (_opt-out_) immédiate, anonymisation contextuelle avant requête, et absence d'impact négatif.|

_Articulation avec l'AI Act_ : 🟢 L'exigence de nécessité et de minimisation du RGPD se prolonge dans l'AI Act à l'Article 10 (Gouvernance des données). Les systèmes à haut risque imposent que les jeux de données d'entraînement, de validation et de test soient pertinents, suffisamment représentatifs, et dans la mesure du possible, exempts d'erreurs, validant ainsi la pertinence de la base légale choisie.

### 2.2 Le Contrat (Art. 6.1.b) et le Piège du Consentement Salarié (Art. 6.1.a)

Les autres bases légales présentent des écueils majeurs dans le contexte algorithmique :

- **Le Consentement (Article 6.1.a) :**
    
    - 🟡 Il peut être exploité pour des clients externes (ex: validation avant de soumettre un document à une IA de synthèse).
        
    - 🟢 **Il constitue un piège juridique absolu pour les salariés.** Conformément aux Lignes directrices du CEPD (WP259), le lien de subordination inhérent au contrat de travail rend le consentement présumé contraint. Une PME ne peut donc pas se baser sur le consentement de ses employés pour déployer des outils de monitoring IA ou analyser leurs performances.
        
- **L'Exécution du Contrat (Article 6.1.b) :**
    
    - 🟢 Elle est valide si l'IA est l'essence même du service délivré (ex: une PME fournissant un service de traduction automatique par IA à ses clients).
        
    - 🟡 Elle est en revanche irrecevable pour justifier la réutilisation des données clients à des fins d'amélioration (entraînement) du modèle, cette finalité ultérieure n'étant pas strictement nécessaire à l'exécution du contrat initial.
        

### 2.3 Les Données Sensibles (Article 9) et le Risque d'Inférence

🟢 L'article 9 du RGPD prohibe par principe le traitement des données révélant l'origine raciale, les opinions politiques, religieuses, l'appartenance syndicale, ainsi que les données biométriques et de santé.

L'usage des LLM introduit ici un risque majeur de non-conformité par déduction. 🔴 **La zone grise de l'inférence algorithmique :** Un modèle d'IA générative peut analyser des données textuelles en apparence anodines (échanges d'emails, habitudes d'achat, temps de pause) et en déduire statistiquement une fragilité psychologique, une orientation sexuelle ou une pathologie médicale. 🟡 Dès lors qu'un système d'IA est utilisé pour inférer ces catégories particulières de données, les interdictions et exceptions strictes de l'Article 9 s'appliquent pleinement, rendant la pratique illégale sans le consentement explicite de la personne, y compris pour les analyses prédictives RH.

---

## Section 3 : Droits Renforcés, Zones Grises et Conflits Technologiques

L'architecture probabiliste des modèles de fondation heurte frontalement certains principes déterministes du RGPD. Les PME doivent naviguer dans ces zones grises technico-juridiques.

### 3.1 Article 22 : La Décision Automatisée et le "Human-Washing"

🟢 L'article 22 du RGPD consacre le droit pour toute personne de ne pas faire l'objet d'une décision fondée exclusivement sur un traitement automatisé, produisant des effets juridiques la concernant ou l'affectant de manière significative.

Pour contourner cette interdiction, les entreprises intègrent souvent un validateur humain en bout de chaîne algorithmique.

- **Le concept de "Human-Washing" :** 🟡 La CNIL et le CEPD dénoncent cette pratique qui consiste à utiliser un humain comme simple "chambre d'enregistrement" ou "caution morale" des recommandations de l'IA. Si le collaborateur de la PME chargé du recrutement ne fait que valider les rejets suggérés par l'algorithme de tri de CV, par manque de temps, de formation ou de capacité à comprendre la logique de l'IA, le traitement est juridiquement requalifié comme "exclusivement automatisé", constituant une infraction 🟢.
    

_Articulation avec l'AI Act_ : 🟢 L'AI Act formalise la réponse au human-washing via l'Article 14 (_Human Oversight_ ou Contrôle Humain). Les concepteurs et déployeurs de systèmes à Haut Risque doivent garantir que la personne chargée de la supervision possède les compétences, l'autorité, et les outils techniques nécessaires pour ignorer, modifier ou annuler les décisions du système d'IA.

### 3.2 Article 17 (Droit à l'Effacement) vs Entraînement des LLM

Le droit à l'oubli (Article 17) constitue le point de friction technique le plus complexe pour les IA génératives.

- 🔴 **La zone grise de l'effacement synaptique :** Une fois qu'une donnée personnelle est ingérée durant la phase d'entraînement d'un LLM, elle est transformée en représentations mathématiques réparties à travers des milliards de paramètres. Il est actuellement impossible d'identifier et "d'effacer" l'influence spécifique d'une seule donnée sans réentraîner entièrement le modèle, une opération au coût prohibitif.
    
- 🟡 **La position du CEPD (Opinion 28/2024) :** Le CEPD réfute la défense selon laquelle un modèle entraîné serait purement statistique. Il indique que les modèles d'IA entraînés sur des données personnelles ne peuvent pas, par défaut, être considérés comme anonymes. Le risque de régurgitation (mémorisation) ou d'extraction d'informations par des attaques (ex: inversion de modèle) implique que le RGPD continue de s'appliquer au modèle lui-même.
    
- **Implication pour la PME :** La PME n'ayant pas la main sur l'architecture des modèles tiers (OpenAI, Anthropic), elle ne peut garantir l'exercice de l'Article 17. 🟢 L'unique voie de conformité consiste à bloquer la transmission des données personnelles vers l'entraînement des modèles tiers (via les réglages de confidentialité ou les contrats commerciaux adaptés).
    

### 3.3 Article 5.1.d (Principe d'Exactitude) vs "Hallucinations"

🟢 Le principe d'exactitude (Art. 5.1.d RGPD) oblige le responsable de traitement à s'assurer que les données personnelles sont exactes et tenues à jour.

- 🔴 **La contradiction probabiliste :** Les LLM sont conçus pour générer des séquences de mots statistiquement probables, et non pour restituer une vérité factuelle. Ils produisent intrinsèquement des "hallucinations". Si un chatbot RH génère une synthèse inventant un avertissement fictif dans le dossier d'un employé, la PME viole immédiatement l'Article 5.1.d.
    
- 🟡 **Mesures de mitigation (Recommandations CNIL) :** Pour gérer ce risque, la CNIL recommande de limiter l'autonomie du modèle. L'utilisation d'architectures RAG (_Retrieval-Augmented Generation_) ancrant les réponses de l'IA exclusivement sur des corpus documentaires vérifiés par l'entreprise, couplée à une mention explicite avertissant l'utilisateur de la nature faillible de l'outil, permet d'atténuer la non-conformité.
    

### 3.4 Articles 13 et 14 (Transparence) et Articulation avec l'AI Act

La transparence de l'algorithme est exigée de concert par les deux réglementations.

|**Norme**|**Obligation de Transparence**|**Impact et Implémentation PME**|
|---|---|---|
|**RGPD** (Art. 13 & 14)|🟢 Fournir l'identité du RT, la finalité, et informer de l'existence d'une prise de décision automatisée en fournissant des "informations utiles concernant la logique sous-jacente" (Art. 13.2.f).|🟡 La PME doit rédiger des mentions légales vulgarisées explicitant quels algorithmes traitent les données, à quelles fins, et sur quelles logiques décisionnelles.|
|**AI Act** (Art. 13 & 26)|🟢 Le fournisseur doit livrer une documentation technique claire au déployeur (PME) pour qu'il comprenne les limites du système (Art. 13). La PME doit l'utiliser pour surveiller l'outil (Art. 26).|🟢 La PME doit archiver les notices d'utilisation des modèles tiers et s'en servir pour instruire son AIPD et former ses collaborateurs aux limites du système.|
|**AI Act** (Art. 50)|🟢 Transparence systémique : les personnes physiques doivent être informées qu'elles interagissent avec un système d'IA.|🟢 Obligation d'ajouter un bandeau explicite ("Ce contenu a été généré par une IA" ou "Vous parlez à un assistant virtuel") sur tout chatbot ou contenu synthétique généré par la PME.|

---

## Section 4 : Recommandations CNIL/EDPB sur les LLM en Entreprise

La mise en conformité de l'usage des IA génératives en PME dépend structurellement des contrats liant l'entreprise aux fournisseurs technologiques.

### 4.1 La dichotomie critique : "Consumer Terms" vs "Commercial Terms"

L'erreur majeure des PME réside dans l'utilisation de comptes grand public pour traiter des données d'entreprise. L'outil d'audit doit tracer une ligne rouge stricte sur ce point.

- 🔴 **Consumer Terms (Conditions Grand Public) :** Les versions gratuites ou d'abonnement individuel (ex: ChatGPT Free/Plus, Claude Pro) sont soumises à des conditions d'utilisation orientées consommateur. Les fournisseurs s'arrogent contractuellement le droit de conserver et d'utiliser les conversations et documents insérés pour ré-entraîner leurs propres modèles (avec des fenêtres de rétention de 30 jours à 5 ans). 🟢 **Insérer des données clients ou RH dans ces interfaces constitue une rupture de confidentialité, une violation de la finalité (Art. 5.1.b) et une absence d'encadrement sous-traitant (Art. 28).**
    
- 🟢 **Commercial / Enterprise Terms (Conditions Commerciales) :** Les offres dédiées aux entreprises (ex: ChatGPT Enterprise/Team, Claude for Work, Microsoft Copilot M365, Google Gemini Workspace) inversent ce paradigme. Le fournisseur s'engage via un Addendum de Traitement des Données (DPA) conforme à l'article 28 RGPD. 🟢 La garantie fondamentale ("Zero Data Retention" ou "No Training") assure que les entrées (_prompts_) et sorties ne sont ni conservées au-delà de la session, ni utilisées pour améliorer les modèles de base du fournisseur.
    

**Tableau comparatif des engagements des fournisseurs (Offres Entreprises/API) :**

|**Fournisseur IA**|**Conformité Art. 28 (DPA Intégré)**|**Entraînement sur Prompts / Données clients**|**Résidence des Données (Hébergement)**|
|---|---|---|---|
|**Microsoft Azure / Copilot**|🟢 Oui, DPA M365.|🟢 Non. Isolé dans le tenant client.|🟢 Serveurs UE configurables ("EU Data Boundary").|
|**OpenAI (Enterprise / API)**|🟢 Oui.|🟢 Non (par défaut).|🟡 Serveurs US principalement. Nécessite encadrement des transferts.|
|**Anthropic (Claude API)**|🟢 Oui.|🟢 Non (par défaut).|🟡 Déploiement possible sur infrastructures européennes via AWS/GCP.|
|**Google (Gemini Workspace)**|🟢 Oui, via Cloud DPA.|🟢 Non (par défaut).|🟢 Contrôles "Data Regions" activables pour l'UE.|
|**Mistral AI (La Plateforme)**|🟢 Oui, document DPA clair.|🟢 Non (via API ou opt-out).|🟢 Souveraineté européenne / Hébergement physique en Europe.|

### 4.2 L'encadrement des transferts hors UE (Articles 44 et 49)

Le recours à des API de modèles de fondation hébergées aux États-Unis initie un transfert de données hors de l'Espace Économique Européen.

- 🟢 **Cadre légal requis :** La PME doit vérifier que le fournisseur est inscrit sur la liste du _Data Privacy Framework_ (DPF) valant décision d'adéquation, ou qu'il inclut des Clauses Contractuelles Types (CCT/SCC) modulées (Module 4) dans son DPA, à l'instar des pratiques de Mistral AI pour les pays tiers.
    
- 🟡 **Bonne pratique ("Data Residency") :** Pour limiter les risques de surveillance extranationale (FISA/Cloud Act américain), l'utilisation d'instances européennes est fortement recommandée. Le déploiement d'OpenAI via les nœuds de Microsoft Azure en France ou en Suède, ou l'adoption d'acteurs comme Mistral AI, permet de circonscrire physiquement la donnée.
    

### 4.3 Anonymisation Contextuelle et Prompt Engineering Sécurisé

Face à l'impossibilité d'assurer un effacement (_droit à l'oubli_) une fois la donnée ingérée, la sécurisation des flux entrants (_prompts_) est la ligne de défense ultime.

- **Anonymisation contextuelle :** 🟡 Il s'agit d'une technique de masquage automatisée visant à expurger dynamiquement les éléments identifiants (noms, adresses, IBAN, numéros de sécurité sociale) d'un texte _avant_ qu'il ne soit soumis à l'API du modèle de langage. Cette méthode permet de requalifier la donnée transitoire en information "anonyme" (échappant ainsi au RGPD), préservant le secret professionnel ou la confidentialité métier tout en bénéficiant de l'analyse IA.
    
- **Risque de sur-partage (_Over-sharing_) :** Pour les agents autonomes intégrés aux environnements de travail (type Copilot pour Microsoft 365), la PME s'expose au risque de voir l'IA synthétiser et restituer des données sensibles à des employés n'ayant pas l'habilitation métier nécessaire (ex: requêtes sur les salaires ou évaluations). 🟢 L'application stricte du principe de sécurité (Art. 32 RGPD) impose un nettoyage et un durcissement des droits d'accès (_Access Control Lists_) de l'Active Directory interne avant le déploiement de ces outils.
    

---

## Section 5 : Checklist Opérationnelle PME (Génération Automatisée)

L'outil web produira cette checklist hiérarchisée, permettant au dirigeant de PME d'amorcer sa mise en conformité de manière pragmatique.

|**Priorité**|**Base Juridique (RGPD / AI Act)**|**Action Concrète à mettre en œuvre (Checklist PME)**|**Effort**|
|---|---|---|---|
|**P0**<br><br>  <br><br>(Critique)|**Art. 28 RGPD**<br><br>  <br><br>(Sous-traitance)|**Auditer et interdire les outils grand public ("Consumer Terms").** Basculer les collaborateurs vers des licences professionnelles (Enterprise, Team, API) adossées à un accord DPA garantissant le non-entraînement sur les données (Zero Data Retention).|Faible<br><br>  <br><br>_(Licencing)_|
|**P0**<br><br>  <br><br>(Critique)|**Art. 35 RGPD**<br><br>  <br><br>(AIPD)|**Réaliser une AIPD formelle** avant tout déploiement d'IA touchant aux ressources humaines (tri de CV, évaluation) ou à des prises de décision automatisées affectant les clients.|Fort|
|**P0**<br><br>  <br><br>(Critique)|**Art. 50 AI Act**<br><br>  <br><br>(Transparence)|**Intégrer une mention visible d'interaction avec une machine.** Tout chatbot ou contenu généré artificiellement (textes clients, deepfakes) doit être marqué clairement pour l'utilisateur final.|Faible|
|**P1**<br><br>  <br><br>(Nécessaire)|**Art. 6.1.f RGPD**<br><br>  <br><br>(Intérêt Légitime)|**Formaliser le test en trois étapes du CEPD** (Opinion 28/2024). Documenter en quoi l'usage de l'IA (gain d'efficacité) est nécessaire et supérieur aux risques pour les droits des personnes.|Moyen|
|**P1**<br><br>  <br><br>(Nécessaire)|**Art. 13 & 14 RGPD**<br><br>  <br><br>(Information)|**Mettre à jour la Politique de Confidentialité.** Déclarer explicitement l'utilisation d'outils d'IA, les logiques sous-jacentes, et l'identité des fournisseurs agissant comme sous-traitants.|Faible|
|**P1**<br><br>  <br><br>(Nécessaire)|**Art. 32 RGPD**<br><br>  <br><br>(Sécurité)|**Restreindre les droits d'accès internes (Active Directory).** Avant de déployer un agent de type Copilot, vérifier les permissions sur les serveurs de fichiers pour éviter le sur-partage (_over-sharing_) de données sensibles RH ou financières.|Fort<br><br>  <br><br>_(Gouvernance)_|
|**P1**<br><br>  <br><br>(Nécessaire)|**Art. 22 RGPD** &<br><br>  <br><br>**Art. 14 AI Act**|**Désigner un garant humain ("Human-in-the-loop").** Assurer qu'une autorité humaine compétente peut annuler les recommandations de l'IA sans subir de pressions (contrer le "human-washing").|Moyen|
|**P2**<br><br>  <br><br>(Bonne Pratique)|**Art. 5.1.c RGPD**<br><br>  <br><br>(Minimisation)|**Déployer une charte d'usage interne (Prompt Engineering).** Interdire formellement aux collaborateurs la saisie de données personnelles ou confidentielles dans les requêtes IA, ou implémenter un outil d'anonymisation contextuelle à la volée.|Moyen<br><br>  <br><br>_(Formation)_|
|**P2**<br><br>  <br><br>(Bonne Pratique)|**Art. 30 RGPD**<br><br>  <br><br>(Registre)|**Identifier les systèmes d'IA dans le Registre.** Créer une catégorie de traitement spécifique cartographiant les outils, les bases légales et les flux de données sortants.|Faible|
|**P2**<br><br>  <br><br>(Bonne Pratique)|**Art. 44 RGPD**<br><br>  <br><br>(Transferts UE)|**Configurer la "Data Residency" européenne.** Activer les options permettant l'exécution des modèles et le stockage des données sur des serveurs situés géographiquement dans l'Espace Économique Européen.|Faible|

---

## Section 6 : Annexes Techniques pour l'Outil Web

### 6.1 Schéma d'intégration logique de l'outil d'audit SaaS

L'architecture du moteur de décision de l'application web doit s'articuler autour d'un algorithme de classification captant les attributs des traitements déclarés par la PME, afin de générer dynamiquement la checklist :

1. **Module d'Évaluation d'Éligibilité (Inputs) :**
    
    - _Question_ : "L'outil IA traite-t-il des données relatives à des personnes physiques identifiées ou identifiables?"
        
        - _Si Non_ : Désactivation du module RGPD. Activation du module de qualification AI Act seul (Sécurité des produits).
            
        - _Si Oui_ : Poursuite de l'évaluation RGPD.
            
2. **Module d'Analyse Fournisseur :**
    
    - _Question_ : "Quel type d'abonnement utilisez-vous (Gratuit/Grand public vs Payant/Entreprise)?"
        
        - _Si Grand Public_ : Génération d'une alerte rouge 🔴 (Violation Art. 28 RGPD). Injection du point P0 "Bannir les Consumer Terms".
            
3. **Module de Calcul du Risque (Critères WP248 & AI Act) :**
    
    - Sélection des cas d'usages via un système de tags (RH, Scoring, Support, Rédaction).
        
    - _Moteur de règles_ : Si (Somme des critères CEPD activés $\ge$ 2) $\rightarrow$ Déclenchement de la recommandation P0 "Réaliser une AIPD".
        
    - _Moteur de règles_ : Si (Tag "RH/Emploi") $\rightarrow$ Attribution automatique du niveau 🔴 HAUT RISQUE (AI Act Annexe III) et injection des alertes sur le _human-washing_ (Art. 22 RGPD / Art. 14 AI Act).
        

### 6.2 Articulation matricielle RGPD vs AI Act

La conformité des systèmes d'IA requiert une double grille de lecture. Ce tableau synthétise les équivalences pour faciliter la génération du rapport PDF :

|**Enjeu de Conformité**|**Exigence RGPD (UE 2016/679)**|**Complément / Exigence AI Act (UE 2024/1689)**|
|---|---|---|
|**Documentation & Évaluation**|Art. 35 : AIPD centrée sur les droits des personnes. Art. 30 : Registre des activités.|Art. 11 : Documentation technique détaillée. Art. 27 : Analyse d'Impact sur les Droits Fondamentaux (FRIA) pour les systèmes à Haut Risque.|
|**Supervision Décisionnelle**|Art. 22 : Droit à l'intervention humaine (Interdiction du tout-automatique).|Art. 14 : Obligation systémique de _Human Oversight_ par conception.|
|**Transparence / Information**|Art. 13 & 14 : Information sur le traitement des données (Logique, bases légales).|Art. 13 : Transparence du fonctionnement pour le déployeur. Art. 50 : Avertissement explicite d'interaction avec une machine.|
|**Fiabilité des Données**|Art. 5.1.d : Exactitude. Art. 32 : Sécurité du traitement.|Art. 10 : Gouvernance rigoureuse des données (absence d'erreurs). Art. 15 : Robustesse technique et cybersécurité de l'algorithme.|
|**Gouvernance de la Chaîne**|Art. 28 : Contrat Responsable de Traitement / Sous-traitant.|Art. 16 & 26 : Partage des obligations entre Fournisseurs, Importateurs et Déployeurs (PME).|

### 6.3 Encadré Opérationnel : La Convergence avec la Directive NIS2

> 🟢 **Avertissement de Cybersécurité et Supply Chain (Directive NIS2)** :
> 
> L'audit des usages d'IA doit anticiper l'application de la directive européenne NIS2 (transposition en cours). Si la PME auditée opère dans un secteur hautement critique ou critique (Énergie, Transports, Santé, Infrastructures numériques, etc.) et satisfait aux seuils de taille (selon la classification "Entité Essentielle" ou "Entité Importante"), la réglementation impose des mesures de gestion des risques en matière de sécurité des systèmes d'information.
> 
> _Impact IA_ : L'intégration d'API d'intelligence artificielle tierces (SaaS de type OpenAI, Anthropic, Mistral) relève directement du **risque lié à la chaîne d'approvisionnement (Supply Chain Risk)**. La PME devra contractuellement s'assurer que ses fournisseurs de modèles d'IA garantissent des protocoles de gestion des incidents stricts, du chiffrement des données (au repos et en transit) et la réalisation d'audits de sécurité réguliers. Le recours à des modèles dont l'opacité technique ne permet pas d'évaluer la surface d'attaque constitue une non-conformité structurelle au titre de NIS2.