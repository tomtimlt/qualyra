<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport AI Act + RGPD — {{ $report->snapshot['organization']['name'] ?? 'Rapport' }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.55; }

        h1, h2, h3, h4 { color: #111827; margin-top: 0; }
        h1 { font-size: 24px; margin-bottom: 4px; }
        h2 { font-size: 16px; margin: 0 0 14px; padding-bottom: 4px; border-bottom: 2px solid #1f2937; }
        h3 { font-size: 13px; margin: 16px 0 6px; color: #1f2937; }
        h4 { font-size: 11.5px; margin: 12px 0 4px; }

        p { margin: 0 0 9px; text-align: justify; }
        ul { margin: 6px 0 10px 18px; padding: 0; }
        ul li { margin-bottom: 4px; }

        .page-break { page-break-before: always; }

        /* Couverture */
        .cover { padding-top: 20%; text-align: center; }
        .cover .logo-placeholder {
            border: 1px dashed #9ca3af; padding: 14px; margin: 0 auto 32px;
            width: 220px; color: #9ca3af; font-size: 10px; text-transform: uppercase; letter-spacing: 2px;
        }
        .cover h1 { font-size: 30px; margin-bottom: 18px; }
        .cover .subtitle { font-size: 14px; color: #4b5563; margin-bottom: 60px; }
        .cover .org-name { font-size: 22px; font-weight: 700; margin: 30px 0 8px; }
        .cover .org-meta { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .cover .confidential {
            position: absolute; bottom: 4cm; left: 0; right: 0;
            text-align: center; font-size: 10px; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 4px;
        }

        /* Sommaire */
        .toc { margin-top: 12px; }
        .toc-row { display: table; width: 100%; padding: 5px 0; border-bottom: 1px dotted #d1d5db; }
        .toc-title { display: table-cell; font-size: 11.5px; }
        .toc-page { display: table-cell; text-align: right; color: #6b7280; font-size: 10px; }

        /* Tableaux */
        table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        th, td { text-align: left; padding: 7px 9px; border: 1px solid #e5e7eb; vertical-align: top; font-size: 10px; }
        th { background: #f3f4f6; font-weight: 700; text-transform: uppercase; font-size: 9.5px; color: #374151; }
        table.no-borders th, table.no-borders td { border: none; }

        /* Synthèse — grille des compteurs */
        .summary-grid { width: 100%; margin: 6px 0 14px; }
        .summary-grid td { width: 20%; text-align: center; border: 1px solid #d1d5db; padding: 14px 4px; }
        .summary-grid .count { font-size: 26px; font-weight: 700; color: #111827; display: block; margin-bottom: 4px; }
        .summary-grid .label { font-size: 9px; color: #4b5563; text-transform: uppercase; }

        /* Bandeau coloré niveau de risque */
        .level-banner { padding: 10px 14px; margin: 10px 0; color: white; font-weight: 700; font-size: 13px; }
        .level-INACCEPTABLE { background: #991b1b; }
        .level-HAUT_RISQUE { background: #c2410c; }
        .level-RISQUE_LIMITE { background: #b45309; }
        .level-RISQUE_MINIMAL { background: #15803d; }
        .level-NON_EVALUE { background: #6b7280; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: uppercase; color: white; }
        .badge-INACCEPTABLE { background: #991b1b; }
        .badge-HAUT_RISQUE { background: #c2410c; }
        .badge-RISQUE_LIMITE { background: #b45309; }
        .badge-RISQUE_MINIMAL { background: #15803d; }
        .badge-NON_EVALUE { background: #6b7280; }

        /* Encadrés (boîtes pédagogiques) */
        .box { border-left: 3px solid #6b7280; background: #f9fafb; padding: 10px 12px; margin: 8px 0; }
        .box .box-title { font-weight: 700; font-size: 11px; color: #111827; margin-bottom: 4px; }
        .box.box-info { border-color: #2563eb; }
        .box.box-warn { border-color: #d97706; background: #fffbeb; }
        .box.box-danger { border-color: #b91c1c; background: #fef2f2; }
        .box.box-grey { border-color: #6b7280; background: #f3f4f6; }
        .box.box-action { border-color: #15803d; background: #f0fdf4; }

        /* Plan d'action */
        .action-list { padding-left: 0; list-style: none; }
        .action-item { border-left: 4px solid #2563eb; padding: 8px 12px; margin: 8px 0; background: #f9fafb; }
        .action-item .action-meta { font-size: 9.5px; color: #4b5563; margin-top: 4px; }
        .effort { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; color: white; }
        .effort-faible { background: #15803d; }
        .effort-moyen { background: #b45309; }
        .effort-fort { background: #b91c1c; }

        /* Checklist */
        .checklist { padding-left: 0; list-style: none; }
        .checklist li { padding: 6px 0; border-bottom: 1px solid #e5e7eb; }
        .checklist .check-box { display: inline-block; width: 12px; height: 12px; border: 1.5px solid #1f2937; margin-right: 10px; vertical-align: -2px; }

        /* Footer / pagination */
        footer { position: fixed; bottom: -1cm; left: 0; right: 0; text-align: center; font-size: 9px; color: #9ca3af; }
        .page-num:after { content: counter(page); }

        /* Misc */
        .meta-line { color: #6b7280; font-size: 10px; margin-bottom: 14px; }
        .alert-line { background: #fef3c7; border-left: 3px solid #d97706; padding: 6px 10px; margin: 4px 0; font-size: 10px; }
        .small { font-size: 9.5px; color: #6b7280; }
        .strong { font-weight: 700; }
    </style>
</head>
<body>

@php
    $snapshot = $report->snapshot;
    // Lecture du contenu rédactionnel figé. Si la clé `content` est absente
    // (snapshot ancien, antérieur à l'enrichissement), on dégrade en utilisant
    // ReportContentBuilder live — comportement de transition seulement.
    $content = $snapshot['content'] ?? app(\App\Services\ReportContentBuilder::class)->build($snapshot);
    $meta = $content['meta'];
    $compteurs = $content['compteurs_par_niveau'];
    $labels = $content['niveau_labels'];
@endphp

<footer>
    {{ $meta['nom_pme'] }} — Rapport #{{ $report->id }} — Page <span class="page-num"></span>
</footer>

{{-- ========================================================================
     PAGE 1 — COUVERTURE
     ====================================================================== --}}
<section class="cover">
    <div class="logo-placeholder">[ LOGO ]</div>
    <div class="subtitle">Document confidentiel</div>
    <h1>Rapport d'audit de conformité<br>AI Act + RGPD</h1>
    <div class="subtitle">Règlements (UE) 2016/679 et 2024/1689</div>

    <div class="org-name">{{ $meta['nom_pme'] }}</div>
    @if (! empty($meta['siret']))
        <div class="org-meta">SIRET : {{ $meta['siret'] }}</div>
    @endif
    @if (! empty($meta['size']))
        <div class="org-meta">Taille : {{ $meta['size'] }} salariés</div>
    @endif
    @if (! empty($meta['sector']))
        <div class="org-meta">Secteur : {{ $meta['sector'] }}</div>
    @endif
    <div class="org-meta">Date d'audit : {{ $meta['date_audit'] }}</div>
    <div class="org-meta">Identifiant rapport : #{{ $report->id }}</div>

    <div class="confidential">— Document confidentiel — Diffusion restreinte —</div>
</section>

{{-- ========================================================================
     PAGE 2 — SOMMAIRE
     ====================================================================== --}}
<section class="page-break">
    <h2>Sommaire</h2>
    <div class="toc">
        <div class="toc-row"><span class="toc-title">1. Introduction et méthodologie</span><span class="toc-page">3</span></div>
        <div class="toc-row"><span class="toc-title">2. Synthèse exécutive et niveau d'alerte global</span><span class="toc-page">4</span></div>
        <div class="toc-row"><span class="toc-title">3. Analyse détaillée par cas d'usage ({{ $meta['nb_usages_declares'] }} usages)</span><span class="toc-page">5</span></div>
        <div class="toc-row"><span class="toc-title">4. Plan d'action stratégique 30 / 60 / 90 jours</span><span class="toc-page">—</span></div>
        <div class="toc-row"><span class="toc-title">5. Checklist finale opérationnelle (10 points)</span><span class="toc-page">—</span></div>
        <div class="toc-row"><span class="toc-title">6. Zones grises juridiques en veille</span><span class="toc-page">—</span></div>
        <div class="toc-row"><span class="toc-title">7. Avertissement légal et limites de l'audit</span><span class="toc-page">—</span></div>
    </div>
    <p class="small" style="margin-top:24px;">
        La numérotation des sections 4 à 7 est conditionnée par le nombre d'usages déclarés
        et le volume des encadrés réglementaires applicables. Se référer aux titres dans
        la suite du document.
    </p>
</section>

{{-- ========================================================================
     PAGE 3 — INTRODUCTION
     ====================================================================== --}}
<section class="page-break">
    <h2>1. Introduction et méthodologie</h2>
    @foreach (preg_split("/\n\n/", $content['introduction']) as $paragraph)
        <p>{{ $paragraph }}</p>
    @endforeach

    <h3>Méthodologie</h3>
    <ul>
        @foreach ($content['methodologie_short'] as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <h3>Cadre réglementaire couvert</h3>
    <ul>
        @foreach ($content['cadre_reglementaire'] as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</section>

{{-- ========================================================================
     PAGE 4 — SYNTHÈSE EXÉCUTIVE
     ====================================================================== --}}
<section class="page-break">
    <h2>2. Synthèse exécutive</h2>

    <p>{{ $content['synthese_executive']['header'] }}</p>
    <p>{{ $content['synthese_executive']['repartition'] }}</p>

    <table class="summary-grid">
        <tr>
            @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVALUE'] as $niveau)
                <td>
                    <span class="count">{{ $compteurs[$niveau] ?? 0 }}</span>
                    <span class="label">{{ $labels[$niveau] }}</span>
                </td>
            @endforeach
        </tr>
    </table>

    <table>
        <tr>
            <th>Catégorie de risque (AI Act)</th>
            <th>Nombre d'usages</th>
            <th>Impact sur la conformité</th>
        </tr>
        <tr><td>Inacceptable (Art. 5)</td><td>{{ $compteurs['INACCEPTABLE'] }}</td><td>Interdiction stricte — arrêt immédiat requis.</td></tr>
        <tr><td>Haut risque (Annexe III)</td><td>{{ $compteurs['HAUT_RISQUE'] }}</td><td>Conformité documentaire et supervision humaine obligatoires.</td></tr>
        <tr><td>Risque limité (Art. 50)</td><td>{{ $compteurs['RISQUE_LIMITE'] }}</td><td>Obligations de transparence et d'information.</td></tr>
        <tr><td>Risque minimal</td><td>{{ $compteurs['RISQUE_MINIMAL'] }}</td><td>Application exclusive du RGPD si données personnelles.</td></tr>
    </table>

    <div class="box box-warn">
        <div class="box-title">Plafond de sanctions PME — Article 99 §6 AI Act</div>
        <p>{{ $content['synthese_executive']['sanctions'] }}</p>
    </div>

    <h3>Trois priorités d'action immédiates</h3>
    <p>{{ $content['synthese_executive']['priorites_intro'] }}</p>
    <ol>
        @foreach ($content['priorites'] as $priorite)
            <li><strong>{{ $priorite }}</strong></li>
        @endforeach
    </ol>
</section>

{{-- ========================================================================
     PAGES 5+ — DÉTAIL PAR USAGE (1 par page)
     ====================================================================== --}}
@foreach ($content['usages'] as $index => $usage)
    <section class="page-break">
        <h2>3.{{ $index + 1 }} — {{ $usage['name'] }}</h2>

        <table>
            <tr>
                <th width="20%">Type d'IA</th><td>{{ $usage['type'] }}</td>
                <th width="20%">Domaine</th><td>{{ $usage['domain'] }}</td>
            </tr>
            @if (! empty($usage['description']))
                <tr><th>Description</th><td colspan="3">{{ $usage['description'] }}</td></tr>
            @endif
            @if ($usage['regle_id'])
                <tr>
                    <th>Règle déclenchée</th><td>{{ $usage['regle_id'] }}</td>
                    <th>Article</th><td>{{ $usage['article'] }}</td>
                </tr>
            @endif
        </table>

        <div class="level-banner level-{{ $usage['niveau'] }}">
            Niveau : {{ $usage['niveau_label'] }}
            @if ($usage['article']) — {{ $usage['article'] }} @endif
        </div>

        @if (! empty($usage['raison']))
            <h4>Justification de la classification</h4>
            <p>{{ $usage['raison'] }}</p>
        @endif

        <h4>Analyse réglementaire</h4>
        <p>{{ $usage['paragraphe_niveau'] }}</p>

        @if (! empty($usage['encadres']))
            <h4>Vos obligations de conformité pour cet outil</h4>
            @foreach ($usage['encadres'] as $encadre)
                <div class="box box-info">
                    <div class="box-title">{{ $encadre['titre'] }}</div>
                    <p>{{ $encadre['contenu'] }}</p>
                </div>
            @endforeach
        @endif

        @if (! empty($usage['alertes']))
            <h4>Alertes complémentaires</h4>
            @foreach ($usage['alertes'] as $alerte)
                @php
                    $boxClass = match ($alerte['type'] ?? null) {
                        'FLAG_ZONE_GRISE' => 'box-warn',
                        'AGGRAVATION' => 'box-danger',
                        default => 'box-grey',
                    };
                @endphp
                <div class="box {{ $boxClass }}">
                    <div class="box-title">
                        {{ $alerte['code'] ?? 'alerte' }}
                        @if (! empty($alerte['type']))
                            — <span class="small">{{ $alerte['type'] }}</span>
                        @endif
                    </div>
                    <p>{{ $alerte['message'] ?? '' }}</p>
                    @if (! empty($alerte['article']))
                        <p class="small">Article : {{ $alerte['article'] }}</p>
                    @endif
                </div>
            @endforeach
        @endif
    </section>
@endforeach

{{-- ========================================================================
     SECTION 4 — PLAN D'ACTION 30/60/90
     ====================================================================== --}}
<section class="page-break">
    <h2>4. Plan d'action stratégique 30 / 60 / 90 jours</h2>
    <p>{{ $content['plan_action']['header'] }}</p>

    <table>
        <tr>
            <th>Échéance</th>
            <th>Niveau d'urgence</th>
            <th>Objectif principal</th>
            <th>Acteurs impliqués</th>
        </tr>
        @foreach ($content['plan_action']['tableau'] as $row)
            <tr>
                <td><strong>{{ $row['echeance'] }}</strong></td>
                <td>{{ $row['urgence'] }}</td>
                <td>{{ $row['objectif'] }}</td>
                <td>{{ $row['acteurs'] }}</td>
            </tr>
        @endforeach
    </table>

    @foreach (['phase_30j' => 'Phase 1 — Actions à 30 jours (P0 Urgentes & Bloquantes)',
              'phase_60j' => 'Phase 2 — Actions à 60 jours (P1 Importantes & Structurantes)',
              'phase_90j' => 'Phase 3 — Actions à 90 jours (P2 Consolidation & Processus)']
              as $key => $titre)
        <h3>{{ $titre }}</h3>
        <p>{{ $content['plan_action'][$key]['intro'] }}</p>

        @forelse ($content['plan_action'][$key]['actions'] as $action)
            <div class="action-item">
                <div class="strong">{{ $action['titre'] }}</div>
                <p style="margin-top:6px;">{{ $action['contenu'] }}</p>
                <div class="action-meta">
                    Responsable : <strong>{{ $action['responsable'] }}</strong> ·
                    Effort : <span class="effort effort-{{ $action['effort'] }}">{{ $action['effort'] }}</span>
                </div>
            </div>
        @empty
            <p class="small"><em>Aucune action déclenchée pour cette phase compte tenu du portefeuille audité.</em></p>
        @endforelse
    @endforeach
</section>

{{-- ========================================================================
     SECTION 5 — CHECKLIST 10 POINTS
     ====================================================================== --}}
<section class="page-break">
    <h2>5. Checklist finale opérationnelle</h2>
    <p>La mise en conformité est un processus continu. Avant tout nouveau déploiement
       ou pour valider l'existant, la direction de {{ $meta['nom_pme'] }} doit s'assurer
       de pouvoir cocher l'intégralité des points suivants.</p>

    <ol class="checklist">
        @foreach ($content['checklist'] as $i => $item)
            <li>
                <span class="check-box"></span>
                <strong>{{ $i + 1 }}. {{ $item['point'] }}</strong> — {{ $item['description'] }}
            </li>
        @endforeach
    </ol>
</section>

{{-- ========================================================================
     SECTION 6 — ZONES GRISES
     ====================================================================== --}}
<section class="page-break">
    <h2>6. Zones grises juridiques — points de veille</h2>
    <p>{{ $content['zones_grises']['intro'] }}</p>

    <div class="box box-grey">
        <div class="box-title">Calendrier AI Act et projet « Digital Omnibus »</div>
        <p>{{ $content['zones_grises']['digital_omnibus'] }}</p>
    </div>

    <div class="box box-grey">
        <div class="box-title">Intervention humaine significative — Art. 22 RGPD</div>
        <p>{{ $content['zones_grises']['human_washing'] }}</p>
    </div>

    <div class="box box-grey">
        <div class="box-title">Data Privacy Framework et risque Schrems III</div>
        <p>{{ $content['zones_grises']['dpf'] }}</p>
    </div>
</section>

{{-- ========================================================================
     SECTION 7 — DISCLAIMER
     ====================================================================== --}}
<section class="page-break">
    <h2>7. Avertissement légal et limites de l'audit</h2>

    @foreach (['exclusion_responsabilite', 'peremption_normative', 'recommandation_assistance'] as $key)
        <h3>{{ $content['disclaimer'][$key]['titre'] }}</h3>
        @foreach (preg_split("/\n\n/", $content['disclaimer'][$key]['contenu']) as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    @endforeach

    <p class="small" style="margin-top:30px; text-align:center;">
        — Fin du rapport d'audit — Document confidentiel — Diffusion restreinte —
    </p>
</section>

</body>
</html>
