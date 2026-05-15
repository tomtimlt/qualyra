<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('reports.index') }}" style="color: inherit; text-decoration: none">Qualyra / Rapports</a> / <b>#{{ $report->id }}</b>
    </x-slot>

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
    @endphp

    <div class="report-paper">

        {{-- Header bar at top of report --}}
        <div class="report-paper__topbar">
            <div class="report-paper__topbar-left">
                <span class="meta">RAPPORT · <b>#{{ $report->id }}</b></span>
            </div>
            <a href="{{ route('reports.download', $report) }}" class="btn btn--accent btn--sm btn--uiverse">
                <div class="wrapper">
                    <span>Télécharger le PDF</span>
                    <div class="circle circle-12"></div>
                    <div class="circle circle-11"></div>
                    <div class="circle circle-10"></div>
                    <div class="circle circle-9"></div>
                    <div class="circle circle-8"></div>
                    <div class="circle circle-7"></div>
                    <div class="circle circle-6"></div>
                    <div class="circle circle-5"></div>
                    <div class="circle circle-4"></div>
                    <div class="circle circle-3"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-1"></div>
                </div>
            </a>
        </div>

        <div class="report-paper__sheet">
            {{-- Header brand --}}
            <div class="report__head">
                <a href="{{ route('dashboard') }}" class="report__brand">
                    <img src="{{ asset('qualyra/brand/qualyra-mark-original.png') }}" alt="">
                    <div class="report__brand-word">Qualyra<span class="dot">.</span></div>
                </a>
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
                <div><span>PME</span><b>{{ $meta['nom_pme'] }}</b></div>
                @if (! empty($meta['size']))
                    <div><span>EFFECTIF</span><b>{{ $meta['size'] }} salariés</b></div>
                @endif
                @if (! empty($meta['sector']))
                    <div><span>SECTEUR</span><b>{{ $meta['sector'] }}</b></div>
                @endif
                <div><span>AUDITÉ PAR</span><b>Qualyra · v0.1</b></div>
            </div>

            {{-- Section 1 — Niveau de risque global --}}
            <div class="section">
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
            <div class="section">
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

                <h3 style="margin-top: 32px;">Trois priorités</h3>
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
            <div class="section">
                <div class="eyebrow-l">03 · Analyse détaillée</div>
                <h2>Cas par cas.</h2>

                @forelse ($content['usages'] as $i => $usage)
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
                @empty
                    <div class="empty-state">Aucun usage déclaré au moment de la génération du rapport.</div>
                @endforelse
            </div>

            {{-- Section 4 — Plan d'action 1m/6m/1an --}}
            <div class="section">
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
            <div class="section">
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
            <div class="section">
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

            {{-- Section 7 — Disclaimer --}}
            <div class="section">
                <div class="eyebrow-l">07 · Avertissement</div>
                <h2>Limites de l'audit.</h2>
                @foreach (['exclusion_responsabilite', 'peremption_normative', 'recommandation_assistance'] as $key)
                    <h3 style="margin-top: 24px">{{ $content['disclaimer'][$key]['titre'] }}</h3>
                    <div class="body-prose">
                        @foreach (preg_split("/\n\n/", $content['disclaimer'][$key]['contenu']) as $paragraph)
                            @if (trim($paragraph) !== '')
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="report__foot">
                <span>QUALYRA · v0.1 · GÉNÉRÉ LE {{ $report->created_at->translatedFormat('d M Y') }}</span>
                <span>RAPPORT #{{ $report->id }} · CONFIDENTIEL · {{ \Illuminate\Support\Str::upper($meta['nom_pme']) }}</span>
            </div>
        </div>
    </div>

    <style>
        /* The whole report sheet sits on bone parchment within the dark app shell */
        .report-paper { display: flex; flex-direction: column; gap: 16px; }
        .report-paper__topbar { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); }
        .report-paper__topbar-left { display: flex; align-items: center; gap: 16px; }
        .report-paper__topbar .meta { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.06em; }
        .report-paper__topbar .meta b { color: var(--text); font-weight: 500; }
        .report-paper__topbar .btn { text-decoration: none; }

        .report-paper__sheet { background: var(--bone-100); color: var(--ink-950); border-radius: var(--r-md); padding: 80px 80px 96px; box-shadow: 0 0 80px -20px rgba(0,0,0,0.5); max-width: 920px; margin: 0 auto; width: 100%; }

        .report__head { display: flex; justify-content: space-between; align-items: flex-end; padding-bottom: 32px; border-bottom: 1px solid var(--bone-300); margin-bottom: 48px; }
        .report__brand { display: flex; align-items: center; gap: 14px; text-decoration: none; transition: opacity var(--d-fast); }
        .report__brand:hover { opacity: 0.8; }
        .report__brand img { height: 36px; }
        .report__brand-word { font-family: var(--font-display); font-size: 32px; line-height: 1; letter-spacing: -0.01em; color: var(--ink-950); font-weight: 600; }
        .report__brand-word .dot { color: var(--stag-700); }
        .report__head-meta { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.04em; color: var(--bone-500); text-align: right; line-height: 1.7; }
        .report__head-meta b { color: var(--ink-950); font-weight: 500; }

        h1.cover { font-family: var(--font-display); font-size: 72px; line-height: 0.98; letter-spacing: -0.02em; color: var(--ink-950); margin: 0 0 16px; font-weight: 600; }
        h1.cover em { color: var(--stag-700); font-style: italic; }
        .cover-sub { font-size: 16px; line-height: 1.55; color: var(--bone-700); max-width: 60ch; margin: 0; }
        .cover-meta { display: flex; flex-wrap: wrap; gap: 32px; margin-top: 40px; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.04em; color: var(--bone-500); }
        .cover-meta div { display: flex; flex-direction: column; gap: 4px; }
        .cover-meta b { color: var(--ink-950); font-weight: 500; font-size: 13px; font-family: var(--font-sans); letter-spacing: 0; text-transform: none; }

        .eyebrow-l { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--bone-500); font-weight: 500; }
        .eyebrow-l.accent { color: var(--stag-700); }

        .section { margin-top: 64px; }
        .section h2 { font-family: var(--font-display); font-size: 38px; line-height: 1.05; letter-spacing: -0.02em; margin: 12px 0 24px; color: var(--ink-950); font-weight: 600; }
        .section h3 { font-family: var(--font-display); font-size: 22px; line-height: 1.15; letter-spacing: -0.01em; color: var(--ink-950); margin: 0 0 8px; font-weight: 500; }

        /* Risk band */
        .risk-band { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr 1fr 1fr; border: 1px solid var(--bone-300); border-radius: var(--r-md); overflow: hidden; }
        .rb__cell { padding: 22px 24px; border-right: 1px solid var(--bone-300); display: flex; flex-direction: column; gap: 4px; }
        .rb__cell:last-child { border-right: none; }
        .rb__cell--hero { background: var(--bone-200); }
        .rb__num { font-family: var(--font-display); font-size: 48px; line-height: 1; letter-spacing: -0.02em; color: var(--ink-950); font-weight: 600; }
        .rb__num--global { font-size: 26px; color: #8C4810; line-height: 1.1; margin-top: 4px; }
        .rb__num--inacc { color: #9B2933; }
        .rb__num--haut { color: #8C4810; }
        .rb__num--lim { color: #8B6620; }
        .rb__num--min { color: #2F6B53; }
        .rb__label { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--bone-500); margin-top: 12px; }

        /* Lede + body prose */
        .lede { font-family: var(--font-display); font-size: 22px; font-style: italic; line-height: 1.4; color: var(--ink-950); border-left: 2px solid var(--stag-700); padding-left: 20px; margin: 24px 0; }
        .body-prose { font-size: 14px; line-height: 1.7; color: var(--bone-700); max-width: 64ch; }
        .body-prose p { margin: 0 0 12px; }
        .muted-prose { font-size: 13px; line-height: 1.6; color: var(--bone-500); margin: 0 0 16px; max-width: 64ch; }

        /* Callout */
        .callout { border-left: 2px solid var(--bone-400); padding: 12px 0 12px 18px; margin: 16px 0; }
        .callout--inacc { border-color: #9B2933; }
        .callout--lim { border-color: #8B6620; }
        .callout__t { font-family: var(--font-mono); font-size: 11px; font-weight: 500; letter-spacing: 0.06em; color: var(--ink-950); text-transform: uppercase; }
        .callout__c { font-size: 13px; line-height: 1.6; color: var(--bone-700); margin-top: 6px; max-width: 64ch; }

        /* Priorities */
        .priorities { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
        .prio { display: flex; gap: 16px; align-items: center; padding: 16px 20px; border: 1px solid var(--bone-300); border-radius: var(--r-md); background: var(--bone-50); }
        .prio__num { font-family: var(--font-display); font-size: 24px; line-height: 1; color: var(--stag-700); font-weight: 600; min-width: 40px; }
        .prio__t { font-size: 14px; color: var(--ink-950); line-height: 1.5; }

        /* Usage */
        .usage { border: 1px solid var(--bone-300); border-radius: var(--r-md); padding: 24px 28px; margin-top: 16px; background: var(--bone-50); }
        .usage__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 12px; flex-wrap: wrap; }
        .usage__num { font-family: var(--font-display); font-size: 16px; color: var(--bone-500); margin-right: 10px; font-weight: 600; }
        .usage__title { font-family: var(--font-display); font-size: 24px; line-height: 1.15; color: var(--ink-950); font-weight: 600; }
        .usage__meta { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.06em; color: var(--bone-500); margin-top: 4px; }
        .obligation { border-left: 2px solid var(--stag-700); padding: 4px 0 4px 16px; margin-top: 14px; }
        .obligation__t { font-size: 11px; font-weight: 600; letter-spacing: 0.06em; color: var(--ink-950); text-transform: uppercase; font-family: var(--font-mono); }
        .obligation__c { font-size: 13px; line-height: 1.6; color: var(--bone-700); margin-top: 4px; max-width: 64ch; }

        .report-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; font-family: var(--font-mono); font-size: 10px; font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; border-radius: var(--r-xs); border: 1px solid; flex-shrink: 0; }
        .report-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .report-badge--inacc { color: #9B2933; background: #F8DDDF; border-color: #9B2933; }
        .report-badge--haut { color: #8C4810; background: #FBE9D8; border-color: #C4631C; }
        .report-badge--lim  { color: #8B6620; background: #F5E9C8; border-color: #B98A2E; }
        .report-badge--min  { color: #2F6B53; background: #DDEAE0; border-color: #2F6B53; }
        .report-badge--none { color: var(--bone-500); background: transparent; border-color: var(--bone-300); }

        /* Plan */
        .plan { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 16px; }
        .phase { border: 1px solid var(--bone-300); border-radius: var(--r-md); padding: 22px; background: var(--bone-50); }
        .phase__h { font-family: var(--font-display); font-size: 30px; color: var(--ink-950); line-height: 1; font-weight: 600; }
        .phase__h em { color: var(--stag-700); font-style: italic; font-size: 16px; font-weight: 400; }
        .phase__sub { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--bone-500); margin-top: 6px; }
        .phase__intro { font-size: 12px; color: var(--bone-500); margin: 12px 0; line-height: 1.5; font-style: italic; }
        .phase__list { margin: 16px 0 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 14px; }
        .phase__list li { padding-left: 14px; position: relative; }
        .phase__list li::before { content: ''; position: absolute; left: 0; top: 8px; width: 6px; height: 1px; background: var(--stag-700); }
        .phase__action-t { font-size: 13px; font-weight: 500; color: var(--ink-950); line-height: 1.4; }
        .phase__action-d { font-size: 12px; color: var(--bone-700); margin-top: 4px; line-height: 1.5; }
        .phase__action-meta { font-family: var(--font-mono); font-size: 10px; color: var(--bone-500); letter-spacing: 0.04em; margin-top: 4px; }
        .phase__empty { padding-left: 0; font-size: 12px; color: var(--bone-500); font-style: italic; }
        .phase__empty::before { display: none; }

        /* Checklist */
        .checklist { padding: 0; margin: 16px 0 0; list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .checklist li { display: flex; gap: 12px; align-items: flex-start; padding: 12px 16px; border-bottom: 1px solid var(--bone-300); }
        .checklist__box { width: 14px; height: 14px; border: 1.5px solid var(--ink-950); flex-shrink: 0; margin-top: 2px; border-radius: 2px; }
        .checklist__t { font-size: 13px; font-weight: 500; color: var(--ink-950); }
        .checklist__d { font-size: 12px; color: var(--bone-700); margin-top: 3px; line-height: 1.5; }

        /* Grey/Zone block */
        .grey { border-left: 2px solid var(--bone-400); padding: 10px 0 10px 18px; margin: 16px 0; }
        .grey__t { font-family: var(--font-mono); font-size: 11px; font-weight: 600; letter-spacing: 0.06em; color: var(--ink-950); text-transform: uppercase; }
        .grey__c { font-size: 13px; line-height: 1.6; color: var(--bone-700); margin-top: 6px; max-width: 64ch; }

        .empty-state { padding: 48px 24px; text-align: center; color: var(--bone-500); font-size: 13px; }

        .report__foot { margin-top: 80px; padding-top: 24px; border-top: 1px solid var(--bone-300); display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px; font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.06em; color: var(--bone-500); text-transform: uppercase; }

        @media (max-width: 960px) {
            .report-paper__sheet { padding: 48px 32px; }
            h1.cover { font-size: 48px; }
            .section h2 { font-size: 30px; }
            .risk-band { grid-template-columns: 1fr 1fr; }
            .rb__cell--hero { grid-column: span 2; }
            .rb__cell { border-right: none; border-bottom: 1px solid var(--bone-300); }
            .plan { grid-template-columns: 1fr; }
        }
    </style>
</x-app-layout>
