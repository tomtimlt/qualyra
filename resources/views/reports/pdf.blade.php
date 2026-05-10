<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport AI Act + RGPD — {{ $report->snapshot['organization']['name'] ?? 'Rapport' }}</title>
    <style>
        /* dompdf-safe CSS: no CSS variables, no flexbox, no grid. Tables for layout. */
        @page { margin: 2.2cm 2cm 2.5cm; }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10.5px;
            color: #0B0F14;
            line-height: 1.65;
            background: #F3F1EB;
        }

        p { margin: 0 0 10px; text-align: justify; }
        ul { margin: 6px 0 12px 18px; padding: 0; }
        ul li { margin-bottom: 4px; }
        em { font-style: italic; color: #1B3A6F; }
        strong, b { font-weight: bold; }
        .page-break { page-break-before: always; }

        /* Display (heavy sans, matches the Geist look on the website) */
        h1, h2, h3, h4 {
            color: #0B0F14;
            margin-top: 0;
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        h1 { font-size: 34px; margin-bottom: 6px; line-height: 1.05; }
        h2 { font-size: 22px; margin: 28px 0 14px; line-height: 1.1; }
        h3 { font-size: 14px; margin: 18px 0 8px; line-height: 1.2; letter-spacing: -0.3px; }
        h4 { font-size: 12px; margin: 12px 0 4px; letter-spacing: -0.2px; }

        /* Helpers */
        .mono { font-family: 'Courier', monospace; }
        .small { font-size: 9.5px; color: #5A5746; }
        .muted { color: #5A5746; }
        .accent { color: #1B3A6F; }

        /* Eyebrow */
        .eyebrow { font-family: 'Courier', monospace; font-size: 9px; font-weight: bold; letter-spacing: 1.6px; text-transform: uppercase; color: #5A5746; }
        .eyebrow.is-accent { color: #1B3A6F; }

        /* ============ HEADER (brand + meta) ============ */
        .head { width: 100%; border-collapse: collapse; padding-bottom: 28px; border-bottom: 1px solid #C9C3B2; margin-bottom: 36px; }
        .head td { border: none; padding: 0; vertical-align: middle; }
        .head .brand-cell { width: 60%; }
        .head .meta-cell { width: 40%; text-align: right; }
        .head .brand-mark { width: 36px; height: 36px; vertical-align: middle; margin-right: 12px; }
        .head .brand-word { display: inline-block; vertical-align: middle; font-family: 'Helvetica', sans-serif; font-size: 26px; font-weight: bold; color: #0B0F14; letter-spacing: -0.5px; }
        .head .brand-word .dot { color: #1B3A6F; }
        .head .meta-line { font-family: 'Courier', monospace; font-size: 10px; color: #5A5746; line-height: 1.7; letter-spacing: 0.5px; text-transform: uppercase; }
        .head .meta-line b { color: #0B0F14; font-weight: bold; }

        /* ============ COVER ============ */
        .cover h1 em { color: #1B3A6F; font-style: italic; font-weight: bold; }
        .cover .subtitle { font-size: 14px; color: #5A5746; line-height: 1.55; margin: 16px 0 28px; max-width: 540px; }

        /* Cover meta — horizontal columns */
        .cover-meta { width: 100%; border-collapse: collapse; margin-top: 28px; padding-top: 20px; border-top: 1px solid #C9C3B2; }
        .cover-meta td { vertical-align: top; padding: 0 16px 0 0; border: none; width: 25%; }
        .cover-meta .label { font-family: 'Courier', monospace; font-size: 9px; color: #5A5746; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 6px; }
        .cover-meta .value { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #0B0F14; font-weight: bold; line-height: 1.3; }

        .cover .confidential {
            position: absolute; bottom: 1.5cm; left: 0; right: 0;
            text-align: center; font-family: 'Courier', monospace; font-size: 9px; color: #8E876E;
            text-transform: uppercase; letter-spacing: 3px;
        }

        /* ============ TOC ============ */
        .toc-row { width: 100%; padding: 6px 0; border-bottom: 1px dotted #C9C3B2; }
        .toc-row td:first-child { font-size: 11px; color: #0B0F14; padding: 7px 0; }
        .toc-row td:last-child { text-align: right; color: #5A5746; font-size: 10px; font-family: 'Courier', monospace; padding: 7px 0; }

        /* ============ RISK BAND (mirror website) ============ */
        .section-eyebrow { margin-top: 0; }

        .risk-band { width: 100%; border-collapse: collapse; margin: 14px 0 24px; border: 1px solid #C9C3B2; }
        .risk-band td { vertical-align: top; padding: 18px 16px; border-right: 1px solid #C9C3B2; background: #FAF9F6; }
        .risk-band td:last-child { border-right: none; }
        .risk-band .hero-cell { background: #E4E0D5; width: 28%; }
        .risk-band .stat-cell { width: 14.4%; text-align: left; }

        .rb-eyebrow { font-family: 'Courier', monospace; font-size: 9px; font-weight: bold; letter-spacing: 1.4px; text-transform: uppercase; color: #5A5746; }
        .rb-eyebrow.is-accent { color: #1B3A6F; }

        .rb-hero-name { font-family: 'Helvetica', sans-serif; font-size: 22px; font-weight: bold; line-height: 1.1; margin-top: 6px; letter-spacing: -0.4px; }
        .rb-hero-name.haut  { color: #8C4810; }
        .rb-hero-name.lim   { color: #8B6620; }
        .rb-hero-name.min   { color: #2F6B53; }
        .rb-hero-name.inacc { color: #9B2933; }
        .rb-hero-name.none  { color: #5A5746; }

        .rb-stat-num { font-family: 'Helvetica', sans-serif; font-size: 36px; font-weight: bold; line-height: 1; color: #0B0F14; letter-spacing: -1px; }
        .rb-stat-num.haut  { color: #8C4810; }
        .rb-stat-num.lim   { color: #8B6620; }
        .rb-stat-num.min   { color: #2F6B53; }
        .rb-stat-num.inacc { color: #9B2933; }
        .rb-stat-label { font-family: 'Courier', monospace; font-size: 9px; color: #5A5746; letter-spacing: 1px; text-transform: uppercase; margin-top: 14px; line-height: 1.3; }

        /* ============ Tables ============ */
        table.data { width: 100%; border-collapse: collapse; margin: 12px 0 16px; }
        table.data th, table.data td { text-align: left; padding: 9px 11px; border: 1px solid #C9C3B2; vertical-align: top; font-size: 10.5px; }
        table.data th { background: #E4E0D5; font-weight: bold; text-transform: uppercase; font-size: 9.5px; color: #2A2820; letter-spacing: 0.6px; }

        /* Niveau bandeau (par usage, intra-section) */
        .level-banner { padding: 10px 14px; margin: 12px 0; font-weight: bold; font-size: 12px; border-left: 4px solid #C9C3B2; background: #FAF9F6; }
        .level-banner.inacc { border-color: #9B2933; color: #9B2933; }
        .level-banner.haut  { border-color: #8C4810; color: #8C4810; }
        .level-banner.lim   { border-color: #8B6620; color: #8B6620; }
        .level-banner.min   { border-color: #2F6B53; color: #2F6B53; }
        .level-banner.none  { border-color: #5A5746; color: #5A5746; }

        /* Risk badge inline (pour usage-head) */
        .badge { display: inline-block; padding: 3px 9px; font-family: 'Courier', monospace; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border: 1px solid; }
        .badge.inacc { color: #9B2933; background: #F8DDDF; border-color: #9B2933; }
        .badge.haut  { color: #8C4810; background: #FBE9D8; border-color: #C4631C; }
        .badge.lim   { color: #8B6620; background: #F5E9C8; border-color: #B98A2E; }
        .badge.min   { color: #2F6B53; background: #DDEAE0; border-color: #2F6B53; }
        .badge.none  { color: #5A5746; background: transparent; border-color: #C9C3B2; }

        /* Lede / italic */
        .lede { font-family: 'Helvetica', sans-serif; font-style: italic; font-size: 16px; line-height: 1.45; color: #0B0F14; padding: 6px 0 6px 18px; border-left: 2px solid #1B3A6F; margin: 14px 0; font-weight: bold; }

        /* Encadrés */
        .box { border-left: 3px solid #5A5746; background: #FAF9F6; padding: 10px 14px; margin: 10px 0; }
        .box .box-t { font-family: 'Courier', monospace; font-weight: bold; font-size: 10px; color: #0B0F14; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; }
        .box .box-c { font-size: 11px; color: #2A2820; line-height: 1.55; }
        .box.is-accent  { border-color: #1B3A6F; background: #EFF3F8; }
        .box.is-warn    { border-color: #8B6620; background: #FBF4DC; }
        .box.is-danger  { border-color: #9B2933; background: #FCE6E8; }
        .box.is-action  { border-color: #2F6B53; background: #E6F0E9; }

        /* Usage / Cas par cas */
        .usage { border: 1px solid #C9C3B2; background: #FAF9F6; padding: 18px 22px; margin: 14px 0; }
        .usage .usage-head { width: 100%; border-collapse: collapse; }
        .usage .usage-head td { vertical-align: top; padding: 0; border: none; }
        .usage .usage-num { font-family: 'Helvetica', sans-serif; font-size: 14px; color: #5A5746; font-weight: bold; }
        .usage .usage-title { font-family: 'Helvetica', sans-serif; font-size: 18px; font-weight: bold; color: #0B0F14; margin-top: 2px; line-height: 1.2; letter-spacing: -0.3px; }
        .usage .usage-meta { font-family: 'Courier', monospace; font-size: 9px; color: #5A5746; letter-spacing: 0.5px; margin-top: 6px; }
        .usage .obligation { border-left: 2px solid #1B3A6F; padding: 4px 0 4px 14px; margin: 12px 0 0; }
        .usage .obligation .t { font-family: 'Courier', monospace; font-size: 10px; font-weight: bold; color: #0B0F14; text-transform: uppercase; letter-spacing: 0.6px; }
        .usage .obligation .c { font-size: 11px; color: #2A2820; line-height: 1.6; margin-top: 4px; }

        /* Plan d'action */
        .action-item { border-left: 4px solid #1B3A6F; padding: 10px 14px; margin: 10px 0; background: #FAF9F6; }
        .action-item .t { font-weight: bold; font-size: 11.5px; color: #0B0F14; }
        .action-item .c { font-size: 11px; color: #2A2820; margin-top: 6px; line-height: 1.55; }
        .action-item .meta { font-family: 'Courier', monospace; font-size: 9.5px; color: #5A5746; margin-top: 6px; letter-spacing: 0.5px; }
        .effort { display: inline-block; padding: 1px 6px; font-size: 8.5px; font-weight: bold; text-transform: uppercase; color: #FFFFFF; letter-spacing: 0.8px; }
        .effort.faible { background: #2F6B53; }
        .effort.moyen  { background: #8B6620; }
        .effort.fort   { background: #9B2933; }

        /* Checklist */
        .checklist { padding: 0; margin: 0; list-style: none; }
        .checklist li { padding: 8px 0; border-bottom: 1px solid #C9C3B2; font-size: 11px; line-height: 1.55; }
        .checklist .box-mark { display: inline-block; width: 11px; height: 11px; border: 1.5px solid #0B0F14; margin-right: 10px; vertical-align: -1px; }

        /* Footer (pagination) */
        footer { position: fixed; bottom: -1.5cm; left: 0; right: 0; text-align: center; font-family: 'Courier', monospace; font-size: 9px; color: #8E876E; letter-spacing: 0.6px; text-transform: uppercase; }
        .page-num:after { content: counter(page); }
    </style>
</head>
<body>

@php
    $snapshot = $report->snapshot;
    $content = $snapshot['content'] ?? app(\App\Services\ReportContentBuilder::class)->build($snapshot);
    $meta = $content['meta'];
    $compteurs = $content['compteurs_par_niveau'];
    $labels = $content['niveau_labels'];

    $niveauSlug = [
        'INACCEPTABLE' => 'inacc',
        'HAUT_RISQUE' => 'haut',
        'RISQUE_LIMITE' => 'lim',
        'RISQUE_MINIMAL' => 'min',
        'NON_EVALUE' => 'none',
    ];

    $priority = ['INACCEPTABLE'=>4,'HAUT_RISQUE'=>3,'RISQUE_LIMITE'=>2,'RISQUE_MINIMAL'=>1];
    $globalLevel = null;
    foreach (['INACCEPTABLE','HAUT_RISQUE','RISQUE_LIMITE','RISQUE_MINIMAL'] as $n) {
        if (($compteurs[$n] ?? 0) > 0) { $globalLevel = $n; break; }
    }

    // Image embarquée via file path (dompdf charge depuis le filesystem)
    $brandImagePath = public_path('cervus/brand/cervus-mark-original.png');
@endphp

<footer>
    {{ \Illuminate\Support\Str::upper($meta['nom_pme']) }} · Cervus · Rapport #{{ $report->id }} · Page <span class="page-num"></span>
</footer>

{{-- ===== HEADER (deer mark + ORG meta) ===== --}}
<table class="head">
    <tr>
        <td class="brand-cell">
            @if (file_exists($brandImagePath))
                <img src="{{ $brandImagePath }}" class="brand-mark" alt="">
            @endif
            <span class="brand-word">Cervus<span class="dot">.</span></span>
        </td>
        <td class="meta-cell">
            <div class="meta-line">
                Rapport · <b>#{{ $report->id }}</b><br>
                <b>{{ \Illuminate\Support\Str::upper($meta['nom_pme']) }}</b>
                @if (! empty($meta['siret']))
                    <br>SIRET {{ $meta['siret'] }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ===== COVER ===== --}}
<section class="cover">
    <div class="eyebrow is-accent">Audit de conformité · AI Act + RGPD</div>
    <h1>Audit de conformité,<br><em>au {{ \Illuminate\Support\Str::lower($meta['date_audit']) }}.</em></h1>
    <div class="subtitle">{{ $content['synthese_executive']['header'] ?? '' }}</div>

    <table class="cover-meta">
        <tr>
            <td>
                <div class="label">PME</div>
                <div class="value">{{ $meta['nom_pme'] }}</div>
            </td>
            @if (! empty($meta['size']))
                <td>
                    <div class="label">Effectif</div>
                    <div class="value">{{ $meta['size'] }} salariés</div>
                </td>
            @endif
            @if (! empty($meta['sector']))
                <td>
                    <div class="label">Secteur</div>
                    <div class="value">{{ $meta['sector'] }}</div>
                </td>
            @endif
            <td>
                <div class="label">Audité par</div>
                <div class="value">Cervus · v0.1</div>
            </td>
        </tr>
    </table>
</section>

{{-- ===== SECTION 1 — NIVEAU DE RISQUE ===== --}}
<section style="margin-top: 56px;">
    <div class="eyebrow">01 · Niveau de risque</div>
    <h2>Quatre niveaux, {{ $meta['nb_usages_declares'] }} usage{{ $meta['nb_usages_declares'] > 1 ? 's' : '' }}.</h2>

    {{-- Risk band 6 colonnes (1 hero + 5 stats) — mirror website --}}
    <table class="risk-band">
        <tr>
            <td class="hero-cell">
                <div class="rb-eyebrow is-accent">Niveau global</div>
                <div class="rb-hero-name {{ $niveauSlug[$globalLevel] ?? 'none' }}">{{ $content['niveau_risque_global'] ?? '—' }}</div>
            </td>
            <td class="stat-cell">
                <div class="rb-stat-num">{{ $meta['nb_usages_declares'] }}</div>
                <div class="rb-stat-label">Usages déclarés</div>
            </td>
            @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL'] as $niveau)
                <td class="stat-cell">
                    <div class="rb-stat-num {{ $niveauSlug[$niveau] }}">{{ str_pad((string) ($compteurs[$niveau] ?? 0), 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="rb-stat-label">{{ $labels[$niveau] }}</div>
                </td>
            @endforeach
        </tr>
    </table>
</section>

{{-- ===== SECTION 2 — SYNTHESE ===== --}}
<section class="page-break">
    <div class="eyebrow">02 · Synthèse</div>
    <h2>Synthèse exécutive.</h2>

    <p class="lede">{{ $content['synthese_executive']['repartition'] }}</p>

    <p>{{ $content['synthese_executive']['header'] }}</p>

    <table class="data">
        <tr>
            <th>Catégorie de risque (AI Act)</th>
            <th width="14%">Usages</th>
            <th>Impact sur la conformité</th>
        </tr>
        <tr><td><strong>Inacceptable</strong> · Article 5</td><td>{{ $compteurs['INACCEPTABLE'] }}</td><td>Interdiction stricte — arrêt immédiat requis.</td></tr>
        <tr><td><strong>Haut risque</strong> · Annexe III</td><td>{{ $compteurs['HAUT_RISQUE'] }}</td><td>Conformité documentaire et supervision humaine obligatoires.</td></tr>
        <tr><td><strong>Risque limité</strong> · Article 50</td><td>{{ $compteurs['RISQUE_LIMITE'] }}</td><td>Obligations de transparence et d'information.</td></tr>
        <tr><td><strong>Risque minimal</strong></td><td>{{ $compteurs['RISQUE_MINIMAL'] }}</td><td>Application exclusive du RGPD si données personnelles.</td></tr>
    </table>

    <div class="box is-warn">
        <div class="box-t">Plafond de sanctions PME · Article 99 §6</div>
        <div class="box-c">{{ $content['synthese_executive']['sanctions'] }}</div>
    </div>

    <h3>Trois priorités d'action immédiates</h3>
    <p>{{ $content['synthese_executive']['priorites_intro'] }}</p>
    <ol>
        @foreach ($content['priorites'] as $priorite)
            <li><strong>{{ $priorite }}</strong></li>
        @endforeach
    </ol>
</section>

{{-- ===== SECTION 3 — INTRODUCTION ===== --}}
<section class="page-break">
    <div class="eyebrow">03 · Méthode</div>
    <h2>Introduction et méthodologie.</h2>
    @foreach (preg_split("/\n\n/", $content['introduction']) as $paragraph)
        @if (trim($paragraph) !== '')
            <p>{{ $paragraph }}</p>
        @endif
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

{{-- ===== SECTION 4+ — DETAIL PAR USAGE (1 par page) ===== --}}
@foreach ($content['usages'] as $index => $usage)
    <section class="page-break">
        <div class="eyebrow">04.{{ $index + 1 }} · Cas d'usage</div>
        <h2>{{ $usage['name'] }}.</h2>

        <div class="usage">
            <table class="usage-head">
                <tr>
                    <td>
                        <div class="usage-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="usage-title">{{ $usage['name'] }}</div>
                        <div class="usage-meta">{{ \Illuminate\Support\Str::upper($usage['type']) }} · {{ \Illuminate\Support\Str::upper($usage['domain']) }}</div>
                    </td>
                    <td style="text-align: right; width: 200px">
                        @php $cls = $niveauSlug[$usage['niveau']] ?? 'none'; @endphp
                        <span class="badge {{ $cls }}">{{ $usage['niveau_label'] }}@if (! empty($usage['article'])) · {{ $usage['article'] }}@endif</span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="data">
            @if (! empty($usage['description']))
                <tr><th width="20%">Description</th><td>{{ $usage['description'] }}</td></tr>
            @endif
            @if ($usage['regle_id'])
                <tr><th>Règle déclenchée</th><td>{{ $usage['regle_id'] }} · {{ $usage['article'] }}</td></tr>
            @endif
        </table>

        <div class="level-banner {{ $niveauSlug[$usage['niveau']] ?? 'none' }}">
            Niveau : {{ $usage['niveau_label'] }}
            @if ($usage['article']) — {{ $usage['article'] }} @endif
        </div>

        @if (! empty($usage['raison']))
            <h4>Justification</h4>
            <p>{{ $usage['raison'] }}</p>
        @endif

        <h4>Analyse réglementaire</h4>
        <p>{{ $usage['paragraphe_niveau'] }}</p>

        @if (! empty($usage['encadres']))
            <h4>Vos obligations de conformité</h4>
            @foreach ($usage['encadres'] as $encadre)
                <div class="box is-accent">
                    <div class="box-t">{{ $encadre['titre'] }}</div>
                    <div class="box-c">{{ $encadre['contenu'] }}</div>
                </div>
            @endforeach
        @endif

        @if (! empty($usage['alertes']))
            <h4>Alertes complémentaires</h4>
            @foreach ($usage['alertes'] as $alerte)
                @php
                    $boxKind = match ($alerte['type'] ?? null) {
                        'FLAG_ZONE_GRISE' => 'is-warn',
                        'AGGRAVATION' => 'is-danger',
                        default => '',
                    };
                @endphp
                <div class="box {{ $boxKind }}">
                    <div class="box-t">
                        {{ $alerte['code'] ?? 'alerte' }}
                        @if (! empty($alerte['type'])) — {{ $alerte['type'] }} @endif
                    </div>
                    <div class="box-c">
                        {{ $alerte['message'] ?? '' }}
                        @if (! empty($alerte['article']))
                            <br><span class="small">Article : {{ $alerte['article'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </section>
@endforeach

{{-- ===== PLAN D'ACTION ===== --}}
<section class="page-break">
    <div class="eyebrow is-accent">05 · Plan d'action</div>
    <h2>Trente, soixante, quatre-vingt-dix.</h2>
    <p>{{ $content['plan_action']['header'] }}</p>

    <table class="data">
        <tr>
            <th width="14%">Échéance</th>
            <th width="18%">Niveau d'urgence</th>
            <th>Objectif principal</th>
            <th width="22%">Acteurs impliqués</th>
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

    @foreach (['phase_30j' => '30 jours · P0 — Urgentes & Bloquantes',
              'phase_60j' => '60 jours · P1 — Importantes & Structurantes',
              'phase_90j' => '90 jours · P2 — Consolidation & Processus']
              as $key => $titre)
        <h3 style="margin-top: 28px">{{ $titre }}</h3>
        <p class="muted small">{{ $content['plan_action'][$key]['intro'] }}</p>

        @forelse ($content['plan_action'][$key]['actions'] as $action)
            <div class="action-item">
                <div class="t">{{ $action['titre'] }}</div>
                <div class="c">{{ $action['contenu'] }}</div>
                <div class="meta">
                    Responsable : <strong>{{ $action['responsable'] }}</strong> ·
                    Effort : <span class="effort {{ $action['effort'] }}">{{ $action['effort'] }}</span>
                </div>
            </div>
        @empty
            <p class="small"><em>Aucune action déclenchée pour cette phase compte tenu du portefeuille audité.</em></p>
        @endforelse
    @endforeach
</section>

{{-- ===== CHECKLIST ===== --}}
<section class="page-break">
    <div class="eyebrow">06 · Checklist</div>
    <h2>Checklist finale opérationnelle.</h2>
    <p>La mise en conformité est un processus continu. Avant tout nouveau déploiement ou pour valider l'existant,
       la direction de <strong>{{ $meta['nom_pme'] }}</strong> doit s'assurer de pouvoir cocher chacun des dix points suivants.</p>

    <ol class="checklist">
        @foreach ($content['checklist'] as $i => $item)
            <li>
                <span class="box-mark"></span>
                <strong>{{ $i + 1 }}. {{ $item['point'] }}</strong>
                <span class="muted"> — {{ $item['description'] }}</span>
            </li>
        @endforeach
    </ol>
</section>

{{-- ===== ZONES GRISES ===== --}}
<section class="page-break">
    <div class="eyebrow">07 · Zones grises</div>
    <h2>Zones grises juridiques.</h2>
    <p>{{ $content['zones_grises']['intro'] }}</p>

    <div class="box">
        <div class="box-t">Calendrier AI Act et projet « Digital Omnibus »</div>
        <div class="box-c">{{ $content['zones_grises']['digital_omnibus'] }}</div>
    </div>

    <div class="box">
        <div class="box-t">Intervention humaine significative · Art. 22 RGPD</div>
        <div class="box-c">{{ $content['zones_grises']['human_washing'] }}</div>
    </div>

    <div class="box">
        <div class="box-t">Data Privacy Framework et risque Schrems III</div>
        <div class="box-c">{{ $content['zones_grises']['dpf'] }}</div>
    </div>
</section>

{{-- ===== DISCLAIMER ===== --}}
<section class="page-break">
    <div class="eyebrow">08 · Avertissement</div>
    <h2>Limites de l'audit.</h2>

    @foreach (['exclusion_responsabilite', 'peremption_normative', 'recommandation_assistance'] as $key)
        <h3 style="margin-top: 24px">{{ $content['disclaimer'][$key]['titre'] }}</h3>
        @foreach (preg_split("/\n\n/", $content['disclaimer'][$key]['contenu']) as $paragraph)
            @if (trim($paragraph) !== '')
                <p>{{ $paragraph }}</p>
            @endif
        @endforeach
    @endforeach

    <p class="small" style="margin-top: 32px; text-align: center;">
        — Fin du rapport d'audit — Document confidentiel — Diffusion restreinte —
    </p>
</section>

</body>
</html>
