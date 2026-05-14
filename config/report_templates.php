<?php

declare(strict_types=1);

/*
 * Bibliothèque rédactionnelle du rapport d'audit AI Act + RGPD.
 *
 * Source de vérité : docs/Matrices et Référentiels d'Audit.md
 * (8 blocs de templates + dictionnaire des variables d'injection).
 *
 * Les variables {nom_pme}, {date_audit}, {nb_usages_declares}, etc. sont
 * substituées au moment de la résolution par App\Services\ReportContentBuilder.
 *
 * Logique conditionnelle des encadrés : chaque encadré déclare un mini-DSL
 * `declenche_si` (liste de prédicats OR'és). Prédicats supportés (évaluation
 * par usage, sauf si préfixé `report:`) :
 *   - 'niveau:HAUT_RISQUE'                    → assessment.niveau == valeur
 *   - 'niveau:HAUT_RISQUE+domain:RH'          → niveau ET domain (AND interne)
 *   - 'niveau:HAUT_RISQUE+pub:EMPLOYES'       → niveau ET pub contient valeur
 *   - 'regle:R-L-01'                          → assessment.regle_id == valeur
 *   - 'regle_in:R-L-02,R-L-03,R-L-06'         → assessment.regle_id ∈ liste
 *   - 'alerte:rgpd_art9'                      → présence d'une alerte de ce code
 *   - 'response:data_personal=yes'            → response[key] == valeur
 *   - 'type:LLM_GEN'                          → ai_usage.type == valeur
 *   - 'type:LLM_GEN+provider_us'              → type LLM_GEN ET fournisseur US
 *
 * La résolution des prédicats vit dans ReportContentBuilder.
 */

return [

    // =========================================================================
    // 1. Introduction (texte unique, ~400 mots)
    // =========================================================================
    'introduction' => "L'intégration de l'intelligence artificielle au sein des processus opérationnels constitue un levier de compétitivité décisif pour les entreprises. Néanmoins, cette transition technologique expose simultanément les organisations à des responsabilités réglementaires inédites. Le présent rapport détaille les conclusions de l'audit de conformité réalisé le {date_audit} pour le compte de l'entité {nom_pme}. Ce document a pour vocation d'offrir à la direction générale une cartographie lisible et objective des déploiements algorithmiques actuels de l'entreprise, évalués à travers un double prisme normatif.\n\nLa méthodologie sous-tendant cette analyse repose sur un diagnostic purement déclaratif. L'outil a traité et classifié les informations fournies concernant les {nb_usages_declares} cas d'usage recensés lors du parcours d'évaluation. Ces données ont été confrontées à une matrice d'exigences croisées pour identifier les écarts de conformité et générer une feuille de route hiérarchisée.\n\nLe cadre d'analyse englobe les deux piliers indissociables de la régulation technologique européenne. D'une part, le Règlement Général sur la Protection des Données (RGPD), qui garantit la protection des droits des personnes dès lors que leurs données sont ingérées ou produites par un algorithme. D'autre part, le Règlement européen sur l'Intelligence Artificielle (AI Act, Règlement UE 2024/1689), qui bascule d'une logique de protection des données vers une logique de sécurité des produits. Ce texte impose de nouvelles responsabilités structurelles aux entités agissant en qualité de « déployeurs » (utilisateurs professionnels), avec des échéances critiques, dont la plus imminente est fixée au 2 août 2026 pour les systèmes d'IA à haut risque et les obligations de transparence.\n\nIl est impératif d'énoncer clairement les limites inhérentes à cette restitution. Ce rapport constitue une photographie opérationnelle à un instant précis, basée sur la règle du « garbage in, garbage out » : la fiabilité de ces conclusions dépend intrinsèquement de l'exhaustivité des déclarations initiales. Ce document constitue un outil d'aide à la décision stratégique et ne saurait en aucun cas se substituer à une consultation juridique formelle. La réglementation technologique étant soumise à de fréquentes évolutions, l'intervention d'un avocat spécialisé ou d'un Délégué à la Protection des Données (DPO) demeure fortement recommandée pour valider les implémentations complexes.\n\nPour une lecture optimale, il est conseillé de parcourir en priorité la synthèse exécutive, qui consolide le niveau de risque global, avant de s'approprier le plan d'action séquentiel à 30, 60 et 90 jours. Ce dernier constitue l'outil central permettant à {nom_pme} de sécuriser ses processus tout en maintenant son agilité technologique.",

    'methodologie_short' => [
        'Évaluation déclarative sur la base du questionnaire AI Act renseigné par le déployeur.',
        'Classification automatique selon la matrice de décision v1.1 (8 règles INACCEPTABLE, 8 HAUT_RISQUE, 6 RISQUE_LIMITE).',
        'Croisement avec les obligations RGPD pertinentes (Art. 9, 13-14, 22, 35, Chapitre V).',
    ],

    'cadre_reglementaire' => [
        'Règlement (UE) 2016/679 — Règlement général sur la protection des données (RGPD).',
        'Règlement (UE) 2024/1689 — Règlement sur l\'intelligence artificielle (AI Act).',
        'Délibérations CNIL pertinentes (intervention humaine, profilage, AIPD).',
    ],

    // =========================================================================
    // 2. Synthèse exécutive
    // =========================================================================
    'synthese_executive' => [
        'header' => "L'audit de conformité technologique réalisé pour l'organisation {nom_pme} a permis de cartographier et de qualifier réglementairement {nb_usages_declares} cas d'usage impliquant des systèmes d'intelligence artificielle au sein de ses activités. L'application des grilles d'analyse issues du RGPD et de l'AI Act permet de déterminer avec précision le niveau d'exposition légale, financière et réputationnelle de l'entité.",

        'repartition' => "La répartition des usages déclarés selon la taxonomie des risques établie par la Commission européenne s'établit comme suit : l'entreprise exploite actuellement {nb_usages_inacceptable} système(s) qualifié(s) de risque inacceptable, {nb_usages_haut_risque} système(s) à haut risque, {nb_usages_limite} système(s) à risque limité, et {nb_usages_minimal} usage(s) à risque minimal. L'agrégation de ces métriques positionne le niveau d'alerte global de {nom_pme} au statut : {niveau_risque_global}. Cette classification exige une structuration immédiate de la gouvernance interne afin de prévenir toute violation des dispositions en vigueur.",

        'sanctions' => "Le déploiement de solutions d'intelligence artificielle conçues par des tiers ne transfère pas l'intégralité de la responsabilité juridique sur l'éditeur du logiciel. En sa qualité de déployeur professionnel, {nom_pme} est tenue d'appliquer des processus de contrôle, de fournir une information claire aux parties prenantes et de s'assurer de la licéité des données traitées. Les manquements à ces dispositions exposent les Petites et Moyennes Entreprises à des sanctions administratives lourdes. Toutefois, le législateur a prévu un mécanisme de proportionnalité (Art. 99 §6) : les amendes pour les PME sont plafonnées au montant le plus faible entre un pourcentage du chiffre d'affaires mondial (allant de 1 % à 7 %) et une somme forfaitaire (pouvant atteindre de 7,5 à 35 millions d'euros selon l'infraction). Cette clémence relative ne doit pas occulter la menace que représente une telle sanction pour la viabilité économique d'une structure de cette taille.",

        'priorites_intro' => "Afin de sécuriser durablement l'activité et de restaurer un cadre de confiance pour les clients et les collaborateurs, la stratégie de mise en conformité de {nom_pme} doit s'articuler sans délai autour de trois priorités d'action fondamentales :",
    ],

    // Niveau d'alerte global selon la composition du portefeuille d'usages.
    // ReportContentBuilder choisit la première condition vérifiée.
    'niveau_risque_global' => [
        'INACCEPTABLE' => 'ALERTE MAXIMALE — Pratiques prohibées détectées (Art. 5)',
        'HAUT_RISQUE' => 'CRITIQUE — Présence de systèmes haut risque (Art. 6 + Annexe III)',
        'RISQUE_LIMITE' => 'MODÉRÉ — Obligations de transparence à mettre en œuvre (Art. 50)',
        'RISQUE_MINIMAL' => 'STANDARD — Usages à risque minimal uniquement',
        'NON_EVALUE' => 'INDÉTERMINÉ — Aucun usage évalué',
    ],

    // Templates des 3 priorités, paramétrés selon le niveau d'alerte le plus
    // sévère présent dans le portefeuille. ReportContentBuilder remplit
    // {usages_inacceptable_list}, {usages_haut_risque_list}, etc.
    'priorites_templates' => [
        // Si présence ≥1 INACCEPTABLE
        'priorite_inacceptable' => "Cesser immédiatement l'usage interdit déclaré ({usages_inacceptable_list}) — pratique prohibée par l'Article 5 de l'AI Act, applicable depuis le 2 février 2025.",
        // Si présence ≥1 HAUT_RISQUE (qu'il y ait ou non un INACCEPTABLE en plus)
        'priorite_haut_risque' => 'Mettre en place une supervision humaine significative et une AIPD documentée pour chaque système haut risque ({usages_haut_risque_list}), conformément aux Articles 26 §2 et 35 RGPD.',
        // Si présence ≥1 RISQUE_LIMITE
        'priorite_risque_limite' => 'Déployer les mentions de transparence Article 50 (chatbot, contenus IA) sur les systèmes concernés ({usages_risque_limite_list}) avant le 2 août 2026.',
        // Toujours présent en bas de pile (P3 fallback)
        'priorite_charte_ia' => "Formaliser et diffuser une Charte d'usage interne de l'IA pour encadrer les pratiques des collaborateurs et neutraliser le « Shadow AI ».",
        // Fallback : présence d'usages mais aucun risque significatif
        'priorite_registre' => 'Maintenir un registre exhaustif des usages IA et la traçabilité RGPD des traitements de données personnelles.',
    ],

    // =========================================================================
    // 3. Paragraphes types par niveau de risque (~200 mots chacun)
    // =========================================================================
    'paragraphes_par_niveau' => [
        'INACCEPTABLE' => "L'audit a formellement identifié l'utilisation d'un système d'intelligence artificielle relevant de la catégorie des pratiques interdites, telles que définies par l'article 5 du Règlement européen sur l'IA. Ce périmètre englobe les technologies qui constituent une menace manifeste pour les droits fondamentaux, à l'instar de la notation sociale, des techniques subliminales manipulatrices, ou de l'inférence des émotions sur le lieu de travail. Ces dispositifs sont illégaux sur l'ensemble du territoire européen depuis le 2 février 2025. Le maintien d'un tel système en production n'est assujetti à aucune dérogation ni période de grâce. Il expose l'entreprise à la strate de sanctions la plus sévère prévue par le législateur, à savoir une amende administrative pouvant s'élever jusqu'à 35 millions d'euros ou 7 % du chiffre d'affaires annuel mondial (le montant le plus bas étant retenu en application du régime protecteur des PME). L'action corrective préconisée est radicale : la direction doit procéder à la désinstallation immédiate du système concerné, cesser toute collecte de données y afférant, et détruire les bases de données constituées illégalement.",

        'HAUT_RISQUE' => "L'analyse a mis en exergue le déploiement d'un ou plusieurs systèmes classifiés à « haut risque » en vertu de l'Annexe III de l'AI Act. Ces systèmes concernent des domaines critiques pour les droits des individus, tels que le recrutement algorithmique, l'évaluation des employés, la biométrie ou l'accès aux services financiers. En tant qu'utilisateur de ces outils, l'entreprise endosse le statut de « déployeur » et se voit imposer des obligations strictes listées à l'article 26 du règlement. Bien que la certification technique initiale incombe au fournisseur, le déployeur doit garantir que le système est utilisé conformément aux notices d'instruction, vérifier la pertinence des données d'entrée, et surtout, mettre en place une supervision humaine ininterrompue par du personnel spécifiquement formé. L'ensemble de ces obligations entre en vigueur le 2 août 2026. Toute défaillance de gouvernance sur ce périmètre est passible d'une amende pouvant atteindre 15 millions d'euros ou 3 % du chiffre d'affaires (plafonnée au montant le plus faible pour les PME). L'entreprise doit impérativement documenter ces processus et initier une surveillance continue de l'outil.",

        'RISQUE_LIMITE' => "Les activités de l'entreprise intègrent des systèmes d'intelligence artificielle catégorisés à risque limité. Cette classification vise principalement les technologies d'interaction directe avec des personnes physiques, telles que les agents conversationnels (chatbots), ainsi que les outils génératifs produisant des contenus synthétiques (textes, images, audios, incluant les deepfakes). L'article 50 de l'AI Act impose à ces déploiements des obligations d'hygiène numérique axées sur la transparence. L'entreprise a l'obligation légale d'avertir de manière claire et non équivoque les utilisateurs finaux qu'ils interagissent avec une machine ou qu'ils consultent un contenu artificiel. Cette information doit être délivrée au plus tard lors de la première interaction. En l'absence de ces marqueurs de transparence, l'entreprise s'expose à une perte de confiance critique de la part de ses parties prenantes et à des amendes administratives. La mise en conformité nécessite l'ajout de bannières d'information sur les interfaces concernées et l'adoption de standards de marquage technique lisibles par machine pour les médias générés.",

        'RISQUE_MINIMAL' => "L'évaluation a identifié l'usage de systèmes d'intelligence artificielle présentant un risque minimal. Il s'agit généralement d'outils d'optimisation bureautique, de filtres anti-spam ou de logiciels d'aide à la décision n'impactant pas directement les droits fondamentaux des individus. Par conception, l'AI Act ne prescrit aucune obligation supplémentaire pour le déploiement de ces technologies, cherchant à ne pas entraver l'innovation du marché libre. Néanmoins, il est essentiel de souligner que cette absence de contraintes au titre de l'AI Act n'exonère en rien l'entreprise de ses obligations relatives au RGPD. Dès lors que ces outils ingèrent ou traitent des données à caractère personnel (fichiers clients, bases de prospection), les principes de licéité, de sécurité et de minimisation des données s'appliquent pleinement. Il est par ailleurs vivement recommandé d'embrasser l'article 4 de l'AI Act, qui incite à promouvoir l'alphabétisation en matière d'IA (littératie numérique) auprès des collaborateurs, afin de garantir une utilisation éthique et sécurisée de ces technologies au quotidien.",

        'NON_EVALUE' => "L'usage déclaré n'a pas pu être évalué faute de réponses au questionnaire. Aucune classification n'est opposable. Il appartient au déployeur de compléter le questionnaire avant toute mise en production pour obtenir un diagnostic exploitable.",
    ],

    // =========================================================================
    // 4. Encadrés explicatifs des obligations (10 modules pédagogiques)
    // =========================================================================
    'encadres_obligations' => [

        'aipd' => [
            'titre' => 'AIPD — Analyse d\'Impact relative à la Protection des Données (Art. 35 RGPD)',
            'contenu' => "L'AIPD n'est pas une simple formalité administrative, mais un processus obligatoire de gestion des risques dicté par l'article 35 du RGPD. Elle s'impose dès lors qu'un traitement de données, notamment via l'usage de nouvelles technologies, est susceptible d'engendrer un risque élevé pour les droits et libertés des personnes. Dans le contexte de l'IA, le croisement de bases de données, le profilage systématique ou le traitement à grande échelle requièrent cet exercice. L'AIPD oblige le dirigeant à documenter de manière exhaustive la nature du traitement, à évaluer sa stricte nécessité, à cartographier les risques d'atteinte à la vie privée (fuites, biais discriminatoires) et à détailler les mesures techniques de remédiation prévues. Sans cette documentation, le déploiement d'un algorithme à haut risque est réputé illégal.",
            'declenche_si' => ['niveau:HAUT_RISQUE', 'alerte:rgpd_art9'],
        ],

        'information_personnes' => [
            'titre' => 'Information des personnes (Art. 13-14 RGPD)',
            'contenu' => "La transparence est la clé de voûte de la confiance numérique. Les articles 13 et 14 du RGPD imposent à l'entreprise de fournir aux individus (clients, prospects, collaborateurs) une information concise, transparente et facilement accessible concernant le sort de leurs données. Dès lors qu'une intelligence artificielle est introduite dans un processus métier, la politique de confidentialité de l'entité doit être impérativement mise à jour. Elle doit préciser non seulement l'identité du responsable de traitement, mais surtout la finalité de l'usage de l'algorithme, la base légale invoquée, et l'éventuelle existence de transferts de données vers des éditeurs tiers. L'information doit être délivrée avant la collecte, sous peine de rendre le traitement illicite.",
            'declenche_si' => ['response:data_personal=yes'],
        ],

        'article_22_human_washing' => [
            'titre' => 'Décision automatisée et « Human-washing » (Art. 22 RGPD)',
            'contenu' => "L'article 22 du RGPD protège les individus contre l'arbitraire des machines. Il pose le principe selon lequel aucune personne ne doit faire l'objet d'une décision produisant des effets juridiques ou l'affectant significativement (comme le refus d'un crédit ou l'élimination d'un CV) si cette décision est fondée exclusivement sur un traitement automatisé. Si le recours à l'algorithme est indispensable, la loi exige une intervention humaine. Toutefois, les autorités de contrôle rejettent fermement la pratique du « human-washing », qui consiste à placer un humain pour valider aveuglément les recommandations de la machine. L'intervention doit être significative : le superviseur humain doit analyser la situation, disposer de l'autorité requise pour contester l'IA, et avoir la capacité effective de modifier la décision finale.",
            'declenche_si' => ['alerte:rgpd_art22', 'niveau:HAUT_RISQUE'],
        ],

        'controle_humain' => [
            'titre' => 'Contrôle humain effectif (Art. 26 §2 AI Act)',
            'contenu' => "L'intelligence artificielle n'est pas infaillible, c'est pourquoi l'AI Act impose un garde-fou organisationnel par le biais de son article 26. Tout déploiement d'un système à haut risque doit s'accompagner de la désignation d'une ou plusieurs personnes physiques chargées de sa supervision (« human-in-the-loop »). Le dirigeant a l'obligation de s'assurer que ces superviseurs possèdent une compétence technique avérée, qu'ils ont reçu une formation spécifique sur l'outil, et qu'ils bénéficient de l'autorité hiérarchique indispensable pour exercer leur mandat. Leur rôle consiste à surveiller les anomalies de fonctionnement, repérer l'émergence de biais discriminatoires et, si la situation l'exige, disposer du pouvoir d'interrompre instantanément le système.",
            'declenche_si' => ['niveau:HAUT_RISQUE'],
        ],

        'information_travailleurs' => [
            'titre' => 'Information des travailleurs (Art. 26 §7 AI Act)',
            'contenu' => "La transformation technologique de l'espace de travail est encadrée par un impératif de dialogue social. L'article 26 paragraphe 7 de l'AI Act stipule que l'introduction d'un système d'IA à haut risque (logiciels de recrutement automatisé, outils d'allocation des tâches ou de surveillance des performances) ne peut se faire en catimini. Avant toute mise en service effective au sein de l'organisation, l'employeur est tenu d'informer formellement les représentants des travailleurs (le Comité Social et Économique — CSE) ainsi que les employés directement affectés. Cette mesure de transparence vise à instaurer un climat de confiance, à démystifier les modalités de l'évaluation algorithmique et à respecter les prérogatives des instances représentatives.",
            'declenche_si' => ['niveau:HAUT_RISQUE+pub:EMPLOYES'],
        ],

        'transparence_chatbot' => [
            'titre' => 'Transparence chatbot (Art. 50 §1 AI Act)',
            'contenu' => "L'essor des agents conversationnels (chatbots) impose des règles de clarté dans l'interaction homme-machine. L'article 50 paragraphe 1 de l'AI Act exige que l'entreprise conceptrice ou utilisatrice d'un tel système interactif informe l'utilisateur final qu'il dialogue avec une intelligence artificielle et non avec un conseiller humain. Cette information doit être délivrée de manière lisible et distincte, au plus tard au moment de la toute première interaction. Une exception n'est tolérée que si la nature artificielle du système relève de l'évidence absolue pour une personne raisonnablement avisée, compte tenu du contexte spécifique de l'utilisation. Le défaut d'affichage expose l'entreprise à des sanctions pour pratique trompeuse.",
            'declenche_si' => ['regle:R-L-01'],
        ],

        'marquage_contenus_ia' => [
            'titre' => 'Marquage des contenus IA (Art. 50 §2-4 AI Act)',
            'contenu' => "Pour lutter contre la désinformation et préserver l'intégrité de l'écosystème numérique, la diffusion de contenus synthétiques est strictement encadrée. Selon les paragraphes 2 à 4 de l'article 50 de l'AI Act, toute entreprise déployant un système d'IA pour générer ou manipuler des textes d'intérêt public, des images, des fichiers audio ou des vidéos (incluant les deepfakes) doit intégrer une mention explicite révélant l'origine artificielle du contenu. Au-delà de l'étiquetage visible, le législateur impose la mise en œuvre de solutions techniques permettant un marquage lisible par machine (filigranes numériques ou métadonnées robustes) afin de garantir la traçabilité de l'information tout au long de la chaîne de valeur.",
            'declenche_si' => ['regle_in:R-L-02,R-L-03,R-L-06'],
        ],

        'conservation_logs' => [
            'titre' => 'Conservation des logs (Art. 26 §6 AI Act)',
            'contenu' => "La traçabilité est la condition sine qua non de la responsabilité algorithmique. L'article 26 paragraphe 6 de l'AI Act impose aux déployeurs de systèmes à haut risque une obligation stricte de conservation des journaux d'événements (logs) générés automatiquement par l'IA. L'entreprise doit configurer son infrastructure pour archiver ces données de fonctionnement de manière inaltérable et sécurisée, pour une période minimale de six mois. Ces registres constituent la preuve technique incontournable permettant aux autorités de reconstituer la logique de l'IA en cas d'incident grave, de dérive discriminatoire ou de réclamation d'un utilisateur. Un défaut de journalisation empêche toute démonstration de conformité.",
            'declenche_si' => ['niveau:HAUT_RISQUE'],
        ],

        'transferts_hors_ue_dpf' => [
            'titre' => 'Transferts hors UE et Data Privacy Framework (Chapitre V RGPD)',
            'contenu' => "L'utilisation de solutions d'IA hébergées par des géants technologiques étrangers implique mécaniquement des transferts de données personnelles hors de l'Espace Économique Européen, une pratique strictement régulée par le Chapitre V du RGPD. Pour les prestataires situés aux États-Unis, le transfert n'est licite que si l'entreprise américaine est certifiée au titre du Data Privacy Framework (DPF). Toutefois, ce cadre juridique d'adéquation, validé par le Tribunal de l'Union européenne le 3 septembre 2025, demeure contesté devant la Cour de Justice de l'Union Européenne (risque persistant d'un arrêt « Schrems III »). La direction doit donc impérativement vérifier la localisation physique des serveurs de ses fournisseurs et s'assurer de la solidité des Clauses Contractuelles Types (CCT) signées.",
            'declenche_si' => ['type:LLM_GEN+provider_us', 'type:IA_GEN'],
        ],

        'charte_usage_ia' => [
            'titre' => 'Charte d\'usage interne de l\'IA',
            'contenu' => "La démocratisation de l'IA générative a engendré le phénomène du « Shadow AI » : l'utilisation non encadrée d'outils par les collaborateurs (copier-coller de documents sensibles, saisie de données clients). Ce comportement expose gravement l'entreprise à des violations de confidentialité. Pour pallier ce risque, la CNIL recommande la rédaction et la diffusion immédiate d'une Charte d'utilisation de l'IA. Ce document, intégré au règlement intérieur, a pour fonction de définir clairement les outils logiciels approuvés, d'édicter l'interdiction stricte de soumettre des données à caractère personnel à des modèles publics, et de rappeler aux équipes que la responsabilité juridique et éditoriale du résultat produit incombe systématiquement à l'employé qui le valide.",
            'declenche_si' => ['type:LLM_GEN', 'type:IA_GEN'],
        ],
    ],

    // =========================================================================
    // 5. Plan d'action 1 mois / 6 mois / 1 an
    // =========================================================================
    'plan_action' => [
        'header' => "L'accumulation des exigences issues du RGPD et de l'AI Act requiert une gestion de projet méthodique. Il est illusoire d'envisager une mise en conformité instantanée. La stratégie recommandée pour {nom_pme} s'articule autour d'un plan d'action séquencé en trois phases, permettant de traiter les urgences vitales avant de consolider les processus de gouvernance sur le long terme. Cette feuille de route nécessite l'implication conjointe de la Direction, de la Direction des Systèmes d'Information (DSI) et des Ressources Humaines (RH).\n\n⚠️ **Avertissement réglementaire** : Cette planification interne ne reporte pas les échéances impératives fixées par le règlement. Les obligations de l'Article 26 (systèmes à haut risque) et de l'Article 50 (transparence) s'appliquent au plus tard le 2 août 2026, indépendamment du rythme de déploiement choisi par l'entreprise.",

        'tableau' => [
            ['echeance' => '1 mois', 'urgence' => 'P0 (Urgent & Bloquant)', 'objectif' => "Neutraliser les risques d'amendes majeures et les fuites de données immédiates.", 'acteurs' => 'Direction Générale, DSI'],
            ['echeance' => '6 mois', 'urgence' => 'P1 (Important & Structurant)', 'objectif' => 'Établir la documentation légale (RGPD) et le dialogue social (AI Act).', 'acteurs' => 'DSI, RH, Référent DPO'],
            ['echeance' => '1 an', 'urgence' => 'P2 (Consolidation)', 'objectif' => 'Déployer la traçabilité technique et la formation continue des équipes.', 'acteurs' => 'DSI, RH, Managers'],
        ],

        // Actions templates par phase. Chaque action déclare ses conditions
        // d'apparition (mêmes prédicats que les encadrés, évalués à l'échelle
        // du rapport entier). Les templates {usages_*_list} sont substitués
        // par ReportContentBuilder.
        'phase_1m' => [
            'intro' => "Ce premier mois est consacré au « triage ». Il s'agit de purger l'organisation de toute pratique prohibée et de sécuriser le périmètre contractuel.",
            'actions' => [
                [
                    'titre' => 'Cessation des pratiques inacceptables',
                    'contenu' => "La Direction Générale doit exiger l'arrêt immédiat et sans condition des systèmes prohibés ({usages_inacceptable_list}) au titre de l'Article 5 de l'AI Act. Aucune exception n'est tolérée par le législateur.",
                    'effort' => 'fort',
                    'responsable' => 'Direction Générale',
                    'declenche_si' => ['report:has_inacceptable'],
                ],
                [
                    'titre' => 'Verrouillage des prestataires (DPA)',
                    'contenu' => "La DSI doit auditer l'ensemble des licences de logiciels d'IA en cours et imposer aux fournisseurs la signature d'un avenant de sous-traitance (Data Processing Agreement) interdisant formellement l'ingestion des données de {nom_pme} pour l'entraînement de leurs modèles algorithmiques.",
                    'effort' => 'moyen',
                    'responsable' => 'DSI',
                    'declenche_si' => ['report:always'],
                ],
                [
                    'titre' => 'Diffusion de la Charte IA interne',
                    'contenu' => "La Direction doit diffuser une politique interne de tolérance zéro concernant le « Shadow AI ». Cette note doit lister les générateurs d'IA autorisés et proscrire la saisie de données confidentielles ou nominatives sur des plateformes grand public.",
                    'effort' => 'faible',
                    'responsable' => 'Direction',
                    'declenche_si' => ['report:always'],
                ],
            ],
        ],

        'phase_6m' => [
            'intro' => 'Le premier semestre vise à produire la documentation opposable aux autorités de contrôle et à structurer l\'information aux parties prenantes.',
            'actions' => [
                [
                    'titre' => 'Saisine et information du CSE',
                    'contenu' => "Conformément à l'Article 26 §7 de l'AI Act, les RH doivent inscrire à l'ordre du jour du CSE l'introduction des systèmes d'IA à haut risque touchant au personnel ({usages_haut_risque_list}), afin d'informer officiellement les représentants des travailleurs.",
                    'effort' => 'moyen',
                    'responsable' => 'RH',
                    'declenche_si' => ['report:has_haut_risque_employes'],
                ],
                [
                    'titre' => 'Conduite des Analyses d\'Impact (AIPD)',
                    'contenu' => "Pour les traitements à haut risque ou impliquant une décision automatisée, le référent conformité (ou DPO externe) doit finaliser la documentation des AIPD, en détaillant l'évaluation de la nécessité, des risques pour la vie privée et des garanties de supervision.",
                    'effort' => 'fort',
                    'responsable' => 'Référent DPO',
                    'declenche_si' => ['report:has_haut_risque', 'report:has_alerte_rgpd_art9'],
                ],
                [
                    'titre' => 'Mise à jour de la transparence externe',
                    'contenu' => "La DSI doit actualiser les bannières web et les politiques de confidentialité pour rendre évidente l'utilisation de chatbots et intégrer les étiquettes signalant la nature synthétique des contenus générés.",
                    'effort' => 'faible',
                    'responsable' => 'DSI',
                    'declenche_si' => ['report:has_risque_limite'],
                ],
            ],
        ],

        'phase_1y' => [
            'intro' => "La première année s'achève en inscrivant la conformité technologique dans le fonctionnement routinier de l'entreprise.",
            'actions' => [
                [
                    'titre' => 'Infrastructure de Journalisation (Logs)',
                    'contenu' => "La DSI doit paramétrer les systèmes d'information pour capturer de manière inaltérable les journaux d'événements des IA à haut risque, en automatisant leur conservation sécurisée pour une durée de six mois glissants minimum (Art. 26 §6).",
                    'effort' => 'fort',
                    'responsable' => 'DSI',
                    'declenche_si' => ['report:has_haut_risque'],
                ],
                [
                    'titre' => 'Formation au Contrôle Humain Significatif',
                    'contenu' => "Les RH doivent former les managers chargés de valider les décisions algorithmiques. L'objectif est d'éradiquer le « human-washing » en octroyant aux équipes les compétences techniques et l'autorité managériale pour infirmer les suggestions de la machine.",
                    'effort' => 'moyen',
                    'responsable' => 'RH',
                    'declenche_si' => ['report:has_haut_risque', 'report:has_alerte_rgpd_art22'],
                ],
                [
                    'titre' => 'Gouvernance récurrente',
                    'contenu' => "La Direction doit instaurer un point d'étape trimestriel au sein du comité de direction pour actualiser le registre des traitements et réévaluer la classification des risques des outils mis à jour par les fournisseurs.",
                    'effort' => 'faible',
                    'responsable' => 'Direction',
                    'declenche_si' => ['report:always'],
                ],
            ],
        ],
    ],

    // =========================================================================
    // 6. Checklist finale (10 points)
    // =========================================================================
    'checklist_finale' => [
        ['point' => 'Registre exhaustif', 'description' => "Tous les outils d'IA utilisés (approuvés ou non) sont recensés et intégrés au registre des traitements RGPD de l'entreprise."],
        ['point' => 'Classification établie', 'description' => "Chaque algorithme déployé a été catégorisé selon les niveaux de risque de l'AI Act (Inacceptable, Haut Risque, Limité, Minimal)."],
        ['point' => 'Licéité démontrée', 'description' => 'Chaque traitement de données propulsé par une IA repose sur une base juridique solide (consentement, exécution d\'un contrat, intérêt légitime).'],
        ['point' => 'Sous-traitance verrouillée', 'description' => "Les contrats avec les fournisseurs d'IA (DPA) interdisent explicitement l'exploitation des données de l'entreprise pour réentraîner leurs modèles."],
        ['point' => 'Charte interne déployée', 'description' => "Une politique d'utilisation de l'IA encadre les pratiques des collaborateurs et prévient la fuite de données vers des outils non sécurisés."],
        ['point' => 'Transparence effective', 'description' => 'Les interfaces des chatbots et les médias générés artificiellement affichent des mentions claires informant l\'utilisateur de leur nature algorithmique.'],
        ['point' => 'AIPD documentée', 'description' => 'Les systèmes produisant des effets juridiques ou traitant des données sensibles à grande échelle ont fait l\'objet d\'une Analyse d\'Impact validée.'],
        ['point' => 'Zéro Human-Washing', 'description' => 'Les décisions automatisées affectant le personnel ou les clients sont soumises à un véritable contrôle humain, par du personnel formé et indépendant.'],
        ['point' => 'Dialogue social respecté', 'description' => "Les instances représentatives du personnel (CSE) ont été dûment informées de l'introduction d'IA affectant les conditions de travail."],
        ['point' => 'Traçabilité technique', 'description' => "Les journaux de bord (logs) des opérations d'IA à haut risque sont sauvegardés automatiquement et conservés pour une durée incompressible de 6 mois."],
    ],

    // =========================================================================
    // 7. Encadré « Zones grises » (avec Digital Omnibus + DPF — version 2026)
    // =========================================================================
    'zones_grises' => [
        'intro' => 'Si la réglementation européenne fixe un cap ambitieux, la direction de {nom_pme} doit avoir conscience que le droit des nouvelles technologies navigue actuellement dans des « zones grises » nécessitant une veille stratégique attentive.',

        'digital_omnibus' => "Premièrement, le calendrier de l'AI Act a failli être bouleversé. Le projet « Digital Omnibus », porté par la Commission Européenne, proposait de reporter la deadline d'application des systèmes d'IA à haut risque (Annexe III) du 2 août 2026 à décembre 2027, afin d'alléger la pression de mise en conformité sur les PME. Les négociations en trilogue ont échoué le 28 avril 2026 sans accord politique entre le Conseil et le Parlement européen. À la date de ce rapport, la date butoir du 2 août 2026 reste donc l'horizon réglementaire opposable. Espérer un report constitue un risque de gestion majeur.",

        'human_washing' => "Deuxièmement, la définition de « l'intervention humaine significative » (Art. 22 RGPD) souffre d'un manque de standardisation technique. Les autorités de contrôle exigent un contrôle réel pour éviter le human-washing, mais l'évaluation de l'indépendance d'esprit d'un collaborateur face à la puissance d'une recommandation machine demeure une zone d'interprétation jurisprudentielle. L'entreprise doit documenter au mieux l'autonomie laissée à ses équipes.",

        'dpf' => "Enfin, les transferts de données vers les prestataires technologiques américains reposent massivement sur le Data Privacy Framework (DPF). Ce cadre, validé par le Tribunal de l'Union européenne le 3 septembre 2025, demeure néanmoins contesté devant la Cour de Justice de l'Union Européenne (CJUE) avec le risque persistant d'une invalidation à moyen terme (spectre d'un arrêt « Schrems III »). En cas de chute du DPF, les entreprises devraient migrer en urgence vers des hébergeurs européens ou s'appuyer sur des Clauses Contractuelles Types complexes. Une approche souveraine, privilégiant la localisation européenne des données dès la conception du projet, demeure la meilleure protection contre cette instabilité.",
    ],

    // =========================================================================
    // 8. Disclaimer juridique (3 sections)
    // =========================================================================
    'disclaimer' => [
        'exclusion_responsabilite' => [
            'titre' => 'Exclusion de responsabilité et nature de l\'analyse',
            'contenu' => "Le présent rapport de conformité a été produit par un processus d'évaluation automatisé, configuré selon l'état de l'art du cadre normatif européen et français applicable à la date du {date_audit}. L'ensemble des diagnostics, des recommandations stratégiques et des mesures correctives exposés dans ce document découle exclusivement des informations, déclarations et paramétrages renseignés par le représentant de l'entité auditée lors du parcours en ligne.\n\nL'outil algorithmique fonctionne selon le principe fondamental du « garbage in, garbage out » : il analyse mécaniquement les intrants fournis. Par conséquent, toute omission volontaire ou involontaire, toute erreur d'appréciation quant à la nature des données traitées, ou toute dissimulation d'un usage interne de l'intelligence artificielle (Shadow AI) invalide structurellement la portée et l'exactitude de ces conclusions. L'éditeur de la solution logicielle décline toute responsabilité expresse ou tacite concernant l'adéquation des résultats avec la réalité non déclarée de l'infrastructure informatique de l'entreprise.\n\nCe rapport est un instrument d'aide à la décision managériale et d'acculturation réglementaire. Il n'a pas vocation à se substituer, ni en droit ni en fait, à l'analyse critique et personnalisée dispensée par un avocat inscrit au barreau ou par un Délégué à la Protection des Données (DPO) certifié. Le présent document ne constitue pas une attestation de conformité opposable aux autorités de contrôle (CNIL, ou autorités de surveillance du marché au titre de l'AI Act), et ne saurait prémunir l'entité contre le déclenchement de procédures de sanction administratives ou de recours juridictionnels.",
        ],
        'peremption_normative' => [
            'titre' => 'Péremption normative et veille réglementaire',
            'contenu' => "L'ingénierie juridique entourant les algorithmes est caractérisée par une obsolescence exceptionnellement rapide. L'articulation entre le Règlement (UE) 2016/679 (RGPD) et le Règlement (UE) 2024/1689 (AI Act) fait l'objet d'une construction doctrinale continue. Les avis du Comité Européen de la Protection des Données (CEPD), les publications des codes de bonne conduite de l'article 50, l'adoption de nouvelles normes harmonisées ou la parution d'arrêts de la Cour de Justice de l'Union Européenne (CJUE) sont susceptibles de modifier fondamentalement l'interprétation des seuils de risque exposés dans ce rapport.\n\nUne implémentation technologique jugée conforme aujourd'hui peut faire l'objet d'une requalification contraignante à brève échéance. La validité de ce diagnostic est donc strictement ponctuelle.",
        ],
        'recommandation_assistance' => [
            'titre' => 'Recommandation d\'assistance spécialisée',
            'contenu' => "Face à la complexité des enjeux liés à la qualification des systèmes à « haut risque » (Annexe III) et à l'implémentation des garanties contre les décisions exclusivement automatisées (Article 22 du RGPD), la direction générale de l'entité est vivement incitée à considérer ce rapport comme l'amorçage d'une démarche globale de conformité. Pour toute refonte des Contrats de Sous-Traitance (DPA), pour la réalisation des Analyses d'Impact (AIPD) ou pour la structuration de la supervision humaine des processus décisionnels, la consultation formelle d'un professionnel du droit des nouvelles technologies constitue l'unique moyen d'assurer une sécurité juridique pérenne.",
        ],
    ],

    // =========================================================================
    // Constantes utilitaires
    // =========================================================================
    'niveau_labels' => [
        'INACCEPTABLE' => 'Risque inacceptable',
        'HAUT_RISQUE' => 'Haut risque',
        'RISQUE_LIMITE' => 'Risque limité',
        'RISQUE_MINIMAL' => 'Risque minimal',
        'NON_EVALUE' => 'Non évalué',
    ],

    // Fournisseurs LLM dont les serveurs principaux sont aux États-Unis —
    // utilisé par le prédicat `provider_us` pour activer l'encadré DPF.
    'llm_providers_us' => ['openai', 'anthropic', 'google', 'meta'],
];
