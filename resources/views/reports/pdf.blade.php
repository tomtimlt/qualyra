<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport AI Act + RGPD — {{ $report->snapshot['organization']['name'] ?? 'Rapport' }}</title>
@include('reports.pdf-fonts')
<style>
@page { size: A4; margin: 0; }

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: "Geist", system-ui, -apple-system, sans-serif;
    font-size: 12px;
    color: #2A2820;
    line-height: 1.6;
    background: #F3F1EB;
}

.report-page {
    width: 210mm;
    min-height: 297mm;
    padding: 20mm 18mm;
    background: #F3F1EB;
    page-break-after: always;
}

.report-page:last-child {
    page-break-after: auto;
}

.report__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-bottom: 28px;
    border-bottom: 1px solid #C9C3B2;
    margin-bottom: 42px;
}

.report__brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.report__brand img { height: 32px; }

.report__brand-word {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 28px;
    line-height: 1;
    letter-spacing: -0.01em;
    color: #0B0F14;
    font-weight: 600;
}

.report__brand-word .dot { color: #1B3A6F; }

.report__head-meta {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 10px;
    letter-spacing: 0.04em;
    color: #5A5746;
    text-align: right;
    line-height: 1.7;
}

.report__head-meta b { color: #0B0F14; font-weight: 500; }

.eyebrow-l {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #5A5746;
    font-weight: 500;
}

.eyebrow-l.accent { color: #1B3A6F; }

h1.cover {
    font-family: "Instrument Serif", Cambria, Georgia, serif;
    font-size: 64px;
    line-height: 0.98;
    letter-spacing: -0.02em;
    color: #0B0F14;
    margin: 0 0 14px;
    font-weight: 400;
}

h1.cover em { color: #1B3A6F; font-style: italic; }

.cover-sub {
    font-size: 15px;
    line-height: 1.55;
    color: #5A5746;
    max-width: 60ch;
    margin: 0;
}

.cover-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 28px;
    margin-top: 36px;
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 10px;
    letter-spacing: 0.04em;
    color: #5A5746;
}

.cover-meta > div {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.cover-meta b {
    color: #0B0F14;
    font-weight: 500;
    font-size: 12px;
    font-family: "Geist", system-ui, sans-serif;
    letter-spacing: 0;
    text-transform: none;
}

.section { margin-top: 48px; }

.section h2 {
    font-family: "Instrument Serif", Cambria, Georgia, serif;
    font-size: 36px;
    line-height: 1.02;
    letter-spacing: -0.018em;
    margin: 10px 0 20px;
    color: #0B0F14;
    font-weight: 400;
}

.section h3 {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 20px;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: #0B0F14;
    margin: 0 0 8px;
    font-weight: 500;
}

.risk-band {
    display: flex;
    border: 1px solid #C9C3B2;
    border-radius: 6px;
    overflow: hidden;
}

.rb__cell {
    flex: 1;
    padding: 18px 20px;
    border-right: 1px solid #C9C3B2;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.rb__cell:last-child { border-right: none; }

.rb__cell--hero {
    flex: 1.6;
    background: #E4E0D5;
}

.rb__num {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 40px;
    line-height: 1;
    letter-spacing: -0.02em;
    color: #0B0F14;
    font-weight: 600;
}

.rb__num--global { font-size: 22px; color: #8C4810; line-height: 1.1; margin-top: 4px; }
.rb__num--inacc { color: #9B2933; }
.rb__num--haut { color: #8C4810; }
.rb__num--lim { color: #8B6620; }
.rb__num--min { color: #2F6B53; }

.rb__label {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #5A5746;
    margin-top: 10px;
}

.lede {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 20px;
    font-style: italic;
    line-height: 1.4;
    color: #0B0F14;
    border-left: 2px solid #1B3A6F;
    padding-left: 18px;
    margin: 20px 0;
}

.body-prose { font-size: 12px; line-height: 1.7; color: #5A5746; max-width: 64ch; }
.body-prose p { margin: 0 0 10px; }
.muted-prose { font-size: 12px; line-height: 1.6; color: #5A5746; margin: 0 0 14px; max-width: 64ch; }

.callout {
    border-left: 2px solid #C9C3B2;
    padding: 10px 0 10px 16px;
    margin: 14px 0;
}

.callout--inacc { border-color: #9B2933; }
.callout--lim { border-color: #8B6620; }

.callout__t {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.06em;
    color: #0B0F14;
    text-transform: uppercase;
}

.callout__c {
    font-size: 12px;
    line-height: 1.6;
    color: #5A5746;
    margin-top: 4px;
    max-width: 64ch;
}

.priorities {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 14px;
}

.prio {
    display: flex;
    gap: 14px;
    align-items: center;
    padding: 14px 18px;
    border: 1px solid #C9C3B2;
    border-radius: 6px;
    background: #FAF9F6;
}

.prio__num {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 22px;
    line-height: 1;
    color: #1B3A6F;
    font-weight: 600;
    min-width: 36px;
}

.prio__t { font-size: 13px; color: #0B0F14; line-height: 1.5; }

.usage {
    border: 1px solid #C9C3B2;
    border-radius: 6px;
    padding: 20px 24px;
    margin-top: 14px;
    background: #FAF9F6;
}

.usage__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.usage__num {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 14px;
    color: #5A5746;
    margin-right: 8px;
    font-weight: 600;
}

.usage__title {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 22px;
    line-height: 1.15;
    color: #0B0F14;
    font-weight: 600;
}

.usage__meta {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.06em;
    color: #5A5746;
    margin-top: 4px;
}

.obligation {
    border-left: 2px solid #1B3A6F;
    padding: 4px 0 4px 14px;
    margin-top: 12px;
}

.obligation__t {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.06em;
    color: #0B0F14;
    text-transform: uppercase;
    font-family: "Geist Mono", ui-monospace, monospace;
}

.obligation__c {
    font-size: 12px;
    line-height: 1.6;
    color: #5A5746;
    margin-top: 4px;
    max-width: 64ch;
}

.report-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 9px;
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border-radius: 4px;
    border: 1px solid;
    flex-shrink: 0;
}

.report-badge .dot { width: 4px; height: 4px; border-radius: 50%; background: currentColor; }
.report-badge--inacc { color: #9B2933; background: #F8DDDF; border-color: #9B2933; }
.report-badge--haut { color: #8C4810; background: #FBE9D8; border-color: #C4631C; }
.report-badge--lim  { color: #8B6620; background: #F5E9C8; border-color: #B98A2E; }
.report-badge--min  { color: #2F6B53; background: #DDEAE0; border-color: #2F6B53; }
.report-badge--none { color: #5A5746; background: transparent; border-color: #C9C3B2; }

.plan {
    display: flex;
    gap: 14px;
    margin-top: 14px;
}

.phase {
    flex: 1;
    border: 1px solid #C9C3B2;
    border-radius: 6px;
    padding: 18px;
    background: #FAF9F6;
}

.phase__h {
    font-family: "Geist", system-ui, sans-serif;
    font-size: 26px;
    color: #0B0F14;
    line-height: 1;
    font-weight: 600;
}

.phase__h em { color: #1B3A6F; font-style: italic; font-size: 14px; font-weight: 400; }

.phase__sub {
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #5A5746;
    margin-top: 6px;
}

.phase__intro { font-size: 11px; color: #5A5746; margin: 10px 0; line-height: 1.5; font-style: italic; }

.phase__list { margin: 14px 0 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 12px; }
.phase__list li { padding-left: 12px; position: relative; }
.phase__list li::before { content: ''; position: absolute; left: 0; top: 7px; width: 5px; height: 1px; background: #1B3A6F; }

.phase__action-t { font-size: 12px; font-weight: 500; color: #0B0F14; line-height: 1.4; }
.phase__action-d { font-size: 11px; color: #5A5746; margin-top: 4px; line-height: 1.5; }
.phase__action-meta { font-family: "Geist Mono", ui-monospace, monospace; font-size: 9px; color: #5A5746; letter-spacing: 0.04em; margin-top: 4px; }

.phase__empty { padding-left: 0; font-size: 11px; color: #5A5746; font-style: italic; }
.phase__empty::before { display: none; }

.checklist { padding: 0; margin: 14px 0 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
.checklist li { display: flex; gap: 10px; align-items: flex-start; padding: 10px 14px; border-bottom: 1px solid #C9C3B2; }
.checklist__box { width: 13px; height: 13px; border: 1.5px solid #0B0F14; flex-shrink: 0; margin-top: 2px; border-radius: 2px; }
.checklist__t { font-size: 12px; font-weight: 500; color: #0B0F14; }
.checklist__d { font-size: 11px; color: #5A5746; margin-top: 2px; line-height: 1.5; }

.grey { border-left: 2px solid #C9C3B2; padding: 8px 0 8px 16px; margin: 14px 0; }
.grey__t { font-family: "Geist Mono", ui-monospace, monospace; font-size: 10px; font-weight: 600; letter-spacing: 0.06em; color: #0B0F14; text-transform: uppercase; }
.grey__c { font-size: 12px; line-height: 1.6; color: #5A5746; margin-top: 4px; max-width: 64ch; }

.timeline-table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 12px; }
.timeline-table thead th { text-align: left; padding: 8px 10px; border-bottom: 1px solid #C9C3B2; font-family: "Geist Mono", ui-monospace, monospace; font-size: 9px; letter-spacing: 0.08em; color: #8A8674; text-transform: uppercase; font-weight: 500; }
.timeline-table tbody td { padding: 10px; border-bottom: 1px solid #E5E0D0; color: #0B0F14; }
.timeline-table tbody tr:last-child td { border-bottom: none; }
.timeline-transitions { padding-left: 16px; margin-top: 6px; font-size: 12px; color: #5A5746; line-height: 1.7; }
.timeline-transitions li { margin-bottom: 4px; }
.timeline-transitions b { color: #0B0F14; font-weight: 500; }

.empty-state { padding: 40px 24px; text-align: center; color: #5A5746; font-size: 12px; }

.report__foot {
    margin-top: 60px;
    padding-top: 20px;
    border-top: 1px solid #C9C3B2;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    font-family: "Geist Mono", ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.06em;
    color: #5A5746;
    text-transform: uppercase;
}

.page-break { page-break-before: always; }
</style>
</head>
<body>

@php
    $snapshot = $report->snapshot;
    $content = $snapshot['content'] ?? app(\App\Services\ReportContentBuilder::class)->build($snapshot);
    $meta = $content['meta'];
    $compteurs = $content['compteurs_par_niveau'];
    $labels = $content['niveau_labels'];
    $niveauClass = [
        'INACCEPTABLE' => 'inacc',
        'HAUT_RISQUE' => 'haut',
        'RISQUE_LIMITE' => 'lim',
        'RISQUE_MINIMAL' => 'min',
        'NON_EVALUE' => 'none',
    ];
    $boxClass = [
        'FLAG_ZONE_GRISE' => 'lim',
        'AGGRAVATION' => 'inacc',
    ];
    $brandPath = public_path('qualyra/brand/qualyra-mark-original.png');
    $brandSrc = file_exists($brandPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($brandPath)) : '';
@endphp

<div class="report-page">
    {{-- Header brand --}}
    <div class="report__head">
        <div class="report__brand">
            @if ($brandSrc)
                <img src="{{ $brandSrc }}" alt="">
            @endif
            <div class="report__brand-word">Qualyra<span class="dot">.</span></div>
        </div>
        <div class="report__head-meta">
            RAPPORT · <b>#{{ $report->id }}</b><br>
            {{ \Illuminate\Support\Str::upper($meta['nom_pme']) }}<br>
            @if (! empty($meta['siret']))
                SIRET {{ $meta['siret'] }}
            @endif
        </div>
    </div>

    {{-- Cover --}}
    <div class="eyebrow-l accent">AUDIT DE CONFORMITÉ · AI ACT + RGPD</div>
    <h1 class="cover">Audit de conformité,<br><em>au {{ \Illuminate\Support\Str::lower($meta['date_audit']) }}.</em></h1>
    <p class="cover-sub">{{ $content['synthese_executive']['header'] ?? '' }}</p>
    <div class="cover-meta">
        <div><span>ORGANISATION</span><b>{{ $meta['nom_pme'] }}</b></div>
        @if (! empty($meta['size']))
            <div><span>EFFECTIF</span><b>{{ $meta['size'] }} salariés</b></div>
        @endif
        @if (! empty($meta['sector']))
            <div><span>SECTEUR</span><b>{{ $meta['sector'] }}</b></div>
        @endif
        <div><span>AUDITÉ PAR</span><b>Qualyra · v0.1</b></div>
    </div>
</div>

{{-- Section 1 — Niveau de risque global --}}
<div class="report-page page-break">
    <div class="eyebrow-l">01 · Niveau de risque</div>
    <h2>Quatre niveaux, {{ $meta['nb_usages_declares'] }} usage{{ $meta['nb_usages_declares'] > 1 ? 's' : '' }}.</h2>
    <div class="risk-band">
        <div class="rb__cell rb__cell--hero">
            <div class="eyebrow-l accent">Niveau global</div>
            <div class="rb__num rb__num--global">{{ $content['niveau_risque_global'] ?? '—' }}</div>
        </div>
        <div class="rb__cell"><div class="rb__num">{{ $meta['nb_usages_declares'] }}</div><div class="rb__label">Usages déclarés</div></div>
        @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL'] as $niveau)
            <div class="rb__cell">
                <div class="rb__num rb__num--{{ $niveauClass[$niveau] }}">{{ str_pad((string) ($compteurs[$niveau] ?? 0), 2, '0', STR_PAD_LEFT) }}</div>
                <div class="rb__label">{{ $labels[$niveau] }}</div>
            </div>
        @endforeach
    </div>
</div>

{{-- Section 2 — Synthèse exécutive --}}
<div class="report-page page-break">
    <div class="eyebrow-l">02 · Synthèse exécutive</div>
    <h2>Ce que dit la matrice.</h2>
    <p class="lede">{{ $content['synthese_executive']['repartition'] }}</p>

    <div class="body-prose">
        @foreach (preg_split("/\n\n/", $content['introduction'] ?? '') as $paragraph)
            @if (trim($paragraph) !== '')
                <p>{{ $paragraph }}</p>
            @endif
        @endforeach
    </div>

    <div class="callout">
        <div class="callout__t">Plafond PME · Article 99 §6</div>
        <div class="callout__c">{{ $content['synthese_executive']['sanctions'] }}</div>
    </div>

    <h3 style="margin-top: 28px;">Trois priorités</h3>
    <p class="muted-prose">{{ $content['synthese_executive']['priorites_intro'] ?? '' }}</p>
    <div class="priorities">
        @foreach ($content['priorites'] as $i => $priorite)
            <div class="prio">
                <div class="prio__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                <div class="prio__t">{{ $priorite }}</div>
            </div>
        @endforeach
    </div>
</div>

{{-- Section 3 — Détail par usage --}}
@foreach ($content['usages'] as $i => $usage)
    <div class="report-page page-break">
        <div class="eyebrow-l">03.{{ $i + 1 }} · Cas d'usage</div>
        <h2>{{ $usage['name'] }}.</h2>

        <div class="usage">
            <div class="usage__head">
                <div>
                    <div class="usage__title">
                        <span class="usage__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        {{ $usage['name'] }}
                    </div>
                    <div class="usage__meta">{{ \Illuminate\Support\Str::upper($usage['type']) }} · {{ \Illuminate\Support\Str::upper($usage['domain']) }}</div>
                </div>
                @php $cls = $niveauClass[$usage['niveau']] ?? 'none'; @endphp
                <span class="report-badge report-badge--{{ $cls }}">
                    <span class="dot"></span>{{ $usage['niveau_label'] }}
                    @if (! empty($usage['article']))
                        · {{ $usage['article'] }}
                    @endif
                </span>
            </div>

            @if ($usage['raison'])
                <div class="body-prose"><p>{{ $usage['raison'] }}</p></div>
            @endif

            <div class="body-prose"><p>{{ $usage['paragraphe_niveau'] }}</p></div>

            @if (! empty($usage['encadres']))
                @foreach ($usage['encadres'] as $encadre)
                    <div class="obligation">
                        <div class="obligation__t">{{ $encadre['titre'] }}</div>
                        <div class="obligation__c">{{ $encadre['contenu'] }}</div>
                    </div>
                @endforeach
            @endif

            @if (! empty($usage['alertes']))
                @foreach ($usage['alertes'] as $alerte)
                    @php $aCls = $boxClass[$alerte['type'] ?? ''] ?? 'none'; @endphp
                    <div class="callout callout--{{ $aCls }}">
                        <div class="callout__t">
                            {{ $alerte['code'] ?? 'alerte' }}
                            @if (! empty($alerte['type']))
                                · {{ $alerte['type'] }}
                            @endif
                        </div>
                        <div class="callout__c">{{ $alerte['message'] ?? '' }}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endforeach

{{-- Section 4 — Plan d'action 1m/6m/1an --}}
<div class="report-page page-break">
    <div class="eyebrow-l accent">04 · Plan d'action</div>
    <h2>Un mois, six mois, un an.</h2>
    <p class="muted-prose">{{ $content['plan_action']['header'] }}</p>

    <div class="plan">
        @foreach (['phase_1m' => '1 mois', 'phase_6m' => '6 mois', 'phase_1y' => '1 an'] as $key => $label)
            @php
                $sub = match ($key) {
                    'phase_1m' => 'P0 · obligatoire',
                    'phase_6m' => 'P1 · prioritaire',
                    default => 'P2 · structurant',
                };
            @endphp
            <div class="phase">
                <div class="phase__h">{{ $label }}</div>
                <div class="phase__sub">{{ $sub }}</div>
                <p class="phase__intro">{{ $content['plan_action'][$key]['intro'] }}</p>
                <ul class="phase__list">
                    @forelse ($content['plan_action'][$key]['actions'] as $action)
                        <li>
                            <div class="phase__action-t">{{ $action['titre'] }}</div>
                            <div class="phase__action-d">{{ $action['contenu'] }}</div>
                            <div class="phase__action-meta">{{ $action['responsable'] }} · effort {{ $action['effort'] }}</div>
                        </li>
                    @empty
                        <li class="phase__empty">Aucune action déclenchée pour cette phase.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
</div>

{{-- Section 5 — Checklist --}}
<div class="report-page page-break">
    <div class="eyebrow-l">05 · Checklist opérationnelle</div>
    <h2>Dix points à valider.</h2>
    <ol class="checklist">
        @foreach ($content['checklist'] as $i => $item)
            <li>
                <span class="checklist__box"></span>
                <div>
                    <div class="checklist__t">{{ $i + 1 }}. {{ $item['point'] }}</div>
                    <div class="checklist__d">{{ $item['description'] }}</div>
                </div>
            </li>
        @endforeach
    </ol>
</div>

{{-- Section 6 — Zones grises --}}
<div class="report-page page-break">
    <div class="eyebrow-l">06 · Zones grises juridiques</div>
    <h2>Points de veille.</h2>
    <p class="muted-prose">{{ $content['zones_grises']['intro'] }}</p>

    <div class="grey">
        <div class="grey__t">Calendrier AI Act et « Digital Omnibus »</div>
        <div class="grey__c">{{ $content['zones_grises']['digital_omnibus'] }}</div>
    </div>
    <div class="grey">
        <div class="grey__t">Intervention humaine significative · Art. 22 RGPD</div>
        <div class="grey__c">{{ $content['zones_grises']['human_washing'] }}</div>
    </div>
    <div class="grey">
        <div class="grey__t">Data Privacy Framework · risque Schrems III</div>
        <div class="grey__c">{{ $content['zones_grises']['dpf'] }}</div>
    </div>
</div>

{{-- Section 7 — Chaîne d'approvisionnement --}}
@php
    $vendorsByUsage = collect($snapshot['usages'] ?? [])
        ->filter(fn ($u) => ! empty($u['vendor']))
        ->groupBy(fn ($u) => $u['vendor']['id']);
    $vendorStatusLabels = [
        'complet' => 'Conforme',
        'partiel' => 'Partiel',
        'manquant' => 'Manquant',
    ];
    $vendorStatusClass = [
        'complet' => 'min',
        'partiel' => 'lim',
        'manquant' => 'inacc',
    ];
@endphp
@if ($vendorsByUsage->isNotEmpty())
<div class="report-page page-break">
    <div class="eyebrow-l">07 · Chaîne d'approvisionnement</div>
    <h2>Vos fournisseurs IA.</h2>
    <p class="muted-prose">
        Pour chaque fournisseur IA rattaché à vos usages, nous vérifions trois engagements contractuels : déclaration de conformité Art. 47 AI Act, contrat de sous-traitance Art. 28 RGPD, et clauses contractuelles types si l'hébergement est hors UE.
    </p>

    @foreach ($vendorsByUsage as $vendorId => $vendorUsages)
        @php
            $v = $vendorUsages->first()['vendor'];
            $compliance = $v['compliance'];
            $cls = $vendorStatusClass[$compliance['status']] ?? 'none';
        @endphp
        <div class="usage" style="margin-top: 14px">
            <div class="usage__head">
                <div>
                    <div class="usage__title">{{ $v['name'] }}</div>
                    <div class="usage__meta">{{ $v['type_contractuel'] }}{{ $v['pays_hebergement'] ? ' · '.$v['pays_hebergement'] : '' }}{{ $v['hors_ue'] ? ' · HORS UE' : '' }} · {{ $vendorUsages->count() }} usage{{ $vendorUsages->count() > 1 ? 's' : '' }}</div>
                </div>
                <span class="report-badge report-badge--{{ $cls }}"><span class="dot"></span>{{ $vendorStatusLabels[$compliance['status']] ?? $compliance['status'] }}</span>
            </div>
            @if (! empty($compliance['gaps']))
                <div class="obligation">
                    <div class="obligation__t">Points à régulariser</div>
                    <ul class="obligation__c" style="padding-left: 16px; margin: 4px 0 0">
                        @foreach ($compliance['gaps'] as $gap)
                            <li>{{ $gap }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- Section 8 — Évolution dans le temps --}}
@php
    $timeline = $snapshot['timeline_prospective'] ?? [];
    $horizonLabels = [
        'now' => "Aujourd'hui",
        'plus_1y' => 'Dans 1 an',
        'plus_2y' => 'Dans 2 ans',
    ];
@endphp
@if (! empty($timeline))
<div class="report-page page-break">
    <div class="eyebrow-l">08 · Évolution de la conformité</div>
    <h2>Calendrier d'application.</h2>
    <p class="muted-prose">
        Le Règlement UE 2024/1689 entre en vigueur par paliers. Le tableau ci-dessous projette le niveau de risque de vos usages déclarés à trois horizons, en filtrant les règles non encore opposables à chaque date.
    </p>

    <table class="timeline-table">
        <thead>
            <tr>
                <th>Horizon</th>
                <th>Date</th>
                <th>Inacceptable</th>
                <th>Haut risque</th>
                <th>Risque limité</th>
                <th>Risque minimal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeline as $snap)
                <tr>
                    <td>{{ $horizonLabels[$snap['label']] ?? $snap['label'] }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($snap['date'])->translatedFormat('d M Y') }}</td>
                    <td>{{ $snap['counts']['INACCEPTABLE'] ?? 0 }}</td>
                    <td>{{ $snap['counts']['HAUT_RISQUE'] ?? 0 }}</td>
                    <td>{{ $snap['counts']['RISQUE_LIMITE'] ?? 0 }}</td>
                    <td>{{ $snap['counts']['RISQUE_MINIMAL'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $allTransitions = collect($timeline)
            ->flatMap(fn ($snap) => collect($snap['transitions'] ?? [])->map(
                fn ($t) => array_merge($t, ['horizon' => $horizonLabels[$snap['label']] ?? $snap['label']])
            ));
    @endphp
    @if ($allTransitions->isNotEmpty())
        <h3 style="margin-top: 20px">Usages basculant à un niveau supérieur</h3>
        <ul class="timeline-transitions">
            @foreach ($allTransitions as $t)
                <li><b>{{ $t['name'] }}</b> — {{ $t['horizon'] }} : {{ $labels[$t['from']] ?? $t['from'] }} → {{ $labels[$t['to']] ?? $t['to'] }} ({{ $t['regle_id'] }})</li>
            @endforeach
        </ul>
    @endif
</div>
@endif

{{-- Section 9 — Disclaimer --}}
<div class="report-page page-break">
    <div class="eyebrow-l">09 · Avertissement</div>
    <h2>Limites de l'audit.</h2>
    @foreach (['exclusion_responsabilite', 'peremption_normative', 'recommandation_assistance'] as $key)
        <h3 style="margin-top: 20px">{{ $content['disclaimer'][$key]['titre'] }}</h3>
        <div class="body-prose">
            @foreach (preg_split("/\n\n/", $content['disclaimer'][$key]['contenu']) as $paragraph)
                @if (trim($paragraph) !== '')
                    <p>{{ $paragraph }}</p>
                @endif
            @endforeach
        </div>
    @endforeach

    <div class="report__foot">
        <span>QUALYRA · v0.1 · GÉNÉRÉ LE {{ $report->created_at->translatedFormat('d M Y') }}</span>
        <span>RAPPORT #{{ $report->id }} · CONFIDENTIEL · {{ \Illuminate\Support\Str::upper($meta['nom_pme']) }}</span>
    </div>
</div>

</body>
</html>
