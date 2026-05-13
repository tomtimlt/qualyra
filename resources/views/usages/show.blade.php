<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('usages.index') }}" style="color: inherit; text-decoration: none">Qualyra / Mes usages IA</a> / <b>{{ \Illuminate\Support\Str::limit($aiUsage->name, 40) }}</b>
    </x-slot>

    @if (session('status') === 'questionnaire-saved')
        <div class="status-banner status-banner--ok"><strong>Réponses enregistrées.</strong>&nbsp; Vous pouvez maintenant calculer la classification AI Act.</div>
    @elseif (session('status') === 'assessment-computed')
        <div class="status-banner status-banner--ok"><strong>Classification calculée.</strong>&nbsp; Voir le détail ci-dessous.</div>
    @endif

    @php
        $assessment = $aiUsage->assessments()->latest('computed_at')->first();
        $niveauLabels = [
            'INACCEPTABLE' => 'Inacceptable',
            'HAUT_RISQUE' => 'Haut risque',
            'RISQUE_LIMITE' => 'Risque limité',
            'RISQUE_MINIMAL' => 'Risque minimal',
        ];
        $niveauRiskClass = [
            'INACCEPTABLE' => 'inacc',
            'HAUT_RISQUE' => 'haut',
            'RISQUE_LIMITE' => 'lim',
            'RISQUE_MINIMAL' => 'min',
        ];
        $hasResponses = $aiUsage->responses()->exists();
    @endphp

    <div class="usage-page">
        <div class="usage-page__head">
            <div>
                <div class="eyebrow eyebrow--accent">Usage IA · {{ $aiUsage->type }}</div>
                <h1>{{ $aiUsage->name }}</h1>
                <div class="usage-page__meta">{{ $aiUsage->type }} · {{ $aiUsage->domain }}</div>
            </div>
            <div class="usage-page__actions">
                <a href="{{ route('usages.edit', $aiUsage) }}" class="btn btn--secondary">Modifier</a>
                <form method="POST" action="{{ route('usages.destroy', $aiUsage) }}" style="display: inline; margin: 0"
                      onsubmit="return confirm('Supprimer définitivement « {{ $aiUsage->name }} » ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--ghost">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="usage-grid">

            {{-- Détail de l'usage --}}
            <div class="surface">
                <div class="surface__head"><h3>Détail</h3></div>
                <div class="surface__body">
                    <dl class="kv-grid">
                        <div><dt>Type d'IA</dt><dd>{{ $aiUsage->type }}</dd></div>
                        <div><dt>Domaine</dt><dd>{{ $aiUsage->domain }}</dd></div>
                        <div class="kv-wide">
                            <dt>Description</dt>
                            <dd class="kv-prose">{{ $aiUsage->description ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Questionnaire --}}
            <div class="surface">
                <div class="surface__head">
                    <h3>Questionnaire AI Act</h3>
                    <span class="pill">{{ $hasResponses ? 'Renseigné' : 'À compléter' }}</span>
                </div>
                <div class="surface__body">
                    <p class="muted">
                        @if ($hasResponses)
                            Le questionnaire a été renseigné. Vous pouvez relire vos réponses ou les modifier
                            avant de relancer la classification.
                        @else
                            Pas encore renseigné. Répondez aux questions pour permettre le calcul du niveau de risque.
                        @endif
                    </p>
                    <a href="{{ route('usages.questionnaire.show', $aiUsage) }}" class="btn btn--accent btn--uiverse">
                        <div class="wrapper">
                            <span>{{ $hasResponses ? 'Modifier mes réponses' : 'Répondre au questionnaire' }}</span>
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
            </div>

            {{-- Classification --}}
            <div class="surface usage-grid__wide">
                <div class="surface__head">
                    <h3>Classification AI Act</h3>
                    @if ($hasResponses)
                        <form method="POST" action="{{ route('usages.assessment.store', $aiUsage) }}" style="margin: 0">
                            @csrf
                            <button type="submit" class="btn btn--accent btn--sm btn--uiverse">
                                <div class="wrapper">
                                    <span>{{ $assessment ? 'Recalculer' : 'Évaluer' }}</span>
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
                            </button>
                        </form>
                    @endif
                </div>
                <div class="surface__body">
                    @if (! $assessment)
                        <p class="muted">
                            Pas encore évalué. {{ $hasResponses ? 'Cliquez sur « Évaluer » pour lancer la classification automatique.' : 'Renseignez d\'abord le questionnaire.' }}
                        </p>
                    @else
                        <div class="assessment-card assessment-card--{{ $niveauRiskClass[$assessment->niveau] }}">
                            <div class="assessment-card__bar"></div>
                            <div class="assessment-card__main">
                                <div class="assessment-card__head">
                                    <div class="assessment-card__num">{{ ['INACCEPTABLE'=>'04','HAUT_RISQUE'=>'03','RISQUE_LIMITE'=>'02','RISQUE_MINIMAL'=>'01'][$assessment->niveau] ?? '—' }}</div>
                                    <div>
                                        <div class="assessment-card__title">{{ $niveauLabels[$assessment->niveau] }}</div>
                                        <div class="assessment-card__article">{{ $assessment->article }} · règle {{ $assessment->regle_id }}</div>
                                    </div>
                                    <span class="risk risk--{{ $niveauRiskClass[$assessment->niveau] }}" style="margin-left: auto"><span class="risk__dot"></span>{{ $niveauLabels[$assessment->niveau] }}</span>
                                </div>
                                <p class="assessment-card__reason">{{ $assessment->raison }}</p>
                                <div class="assessment-card__foot">
                                    Calculée le {{ $assessment->computed_at->translatedFormat('d M Y · H:i') }}
                                </div>
                            </div>
                        </div>

                        @if (! empty($assessment->alertes))
                            <div class="alertes">
                                <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Alertes complémentaires</div>
                                <div class="alertes__list">
                                    @foreach ($assessment->alertes as $alerte)
                                        <div class="alerte">
                                            <span class="alerte__code">{{ $alerte['code'] ?? 'alerte' }}</span>
                                            <span class="alerte__msg">{{ $alerte['message'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }

        .usage-page__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; padding-bottom: 32px; border-bottom: 1px solid var(--hairline); margin-bottom: 32px; flex-wrap: wrap; }
        .usage-page__meta { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; color: var(--text-dim); margin-top: 8px; text-transform: uppercase; }
        .usage-page__actions { display: flex; gap: 8px; }

        .usage-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .usage-grid__wide { grid-column: span 2; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); overflow: hidden; display: flex; flex-direction: column; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); display: flex; justify-content: space-between; align-items: center; }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .surface__body { padding: 24px; flex: 1; display: flex; flex-direction: column; gap: 16px; }
        .surface__body .btn { text-decoration: none; align-self: flex-start; }
        .surface__body .muted { color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0; }
        .pill { font-family: var(--font-mono); font-size: 10px; padding: 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); letter-spacing: 0.04em; }

        /* Detail KV grid */
        .kv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 0; }
        .kv-grid > div { padding-bottom: 16px; border-bottom: 1px solid var(--hairline); }
        .kv-wide { grid-column: span 2; border-bottom: none !important; padding-bottom: 0; }
        .kv-grid dt { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); margin: 0 0 6px; }
        .kv-grid dd { margin: 0; color: var(--text); font-size: 14px; }
        .kv-prose { white-space: pre-line; line-height: 1.6; color: var(--ink-200); }

        /* Assessment card */
        .assessment-card { display: flex; gap: 0; border: 1px solid; border-radius: var(--r-md); overflow: hidden; }
        .assessment-card__bar { width: 4px; flex-shrink: 0; }
        .assessment-card__main { flex: 1; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
        .assessment-card__head { display: flex; align-items: flex-start; gap: 24px; }
        .assessment-card__num { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; }
        .assessment-card__title { font-family: var(--font-display); font-size: 22px; line-height: 1; letter-spacing: -0.015em; color: var(--text); }
        .assessment-card__article { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; color: var(--text-dim); margin-top: 6px; text-transform: uppercase; }
        .assessment-card__reason { font-size: 14px; line-height: 1.6; color: var(--ink-200); margin: 0; }
        .assessment-card__foot { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.06em; color: var(--text-dim); }

        .assessment-card--inacc { border-color: var(--risk-inacc); background: var(--risk-inacc-bg); }
        .assessment-card--inacc .assessment-card__bar { background: var(--risk-inacc); }
        .assessment-card--inacc .assessment-card__num { color: var(--risk-inacc); }
        .assessment-card--haut { border-color: var(--risk-haut); background: var(--risk-haut-bg); }
        .assessment-card--haut .assessment-card__bar { background: var(--risk-haut); }
        .assessment-card--haut .assessment-card__num { color: var(--risk-haut); }
        .assessment-card--lim { border-color: var(--risk-lim); background: var(--risk-lim-bg); }
        .assessment-card--lim .assessment-card__bar { background: var(--risk-lim); }
        .assessment-card--lim .assessment-card__num { color: var(--risk-lim); }
        .assessment-card--min { border-color: var(--risk-min); background: var(--risk-min-bg); }
        .assessment-card--min .assessment-card__bar { background: var(--risk-min); }
        .assessment-card--min .assessment-card__num { color: var(--risk-min); }

        .alertes__list { display: flex; flex-direction: column; gap: 8px; }
        .alerte { padding: 12px 16px; border: 1px solid var(--hairline); border-left: 3px solid var(--risk-lim); border-radius: var(--r-sm); background: var(--ink-1000); display: flex; gap: 12px; align-items: flex-start; }
        .alerte__code { font-family: var(--font-mono); font-size: 11px; color: var(--risk-lim); letter-spacing: 0.06em; text-transform: uppercase; flex-shrink: 0; min-width: 120px; }
        .alerte__msg { font-size: 13px; color: var(--ink-200); line-height: 1.55; }

        @media (max-width: 960px) {
            .usage-grid { grid-template-columns: 1fr; }
            .usage-grid__wide { grid-column: span 1; }
            .kv-grid { grid-template-columns: 1fr; }
            .kv-wide { grid-column: span 1; }
            h1 { font-size: 36px; }
        }
    </style>
</x-app-layout>
