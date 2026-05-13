<x-app-layout>
    <x-slot name="header">
        <span>Qualyra</span> / <b>Tableau de bord</b>
    </x-slot>

    @php
        $user = auth()->user();
        $firstName = trim(explode(' ', $user->name)[0] ?? $user->name);

        // Map des libellés et classes pour les niveaux de risque
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
        $niveauPriority = [
            'INACCEPTABLE' => 4,
            'HAUT_RISQUE' => 3,
            'RISQUE_LIMITE' => 2,
            'RISQUE_MINIMAL' => 1,
        ];

        // Calcul du niveau global et des compteurs à partir des dernières évaluations
        $countByLevel = ['INACCEPTABLE' => 0, 'HAUT_RISQUE' => 0, 'RISQUE_LIMITE' => 0, 'RISQUE_MINIMAL' => 0];
        $countNonEvalues = 0;
        $globalLevel = null;
        $globalLevelTrigger = 0;

        foreach ($aiUsages as $usage) {
            $latest = $usage->assessments()->latest('computed_at')->first();
            $usage->setRelation('latestAssessment', $latest);

            if ($latest === null) {
                $countNonEvalues++;
                continue;
            }
            if (isset($countByLevel[$latest->niveau])) {
                $countByLevel[$latest->niveau]++;
            }
            $priority = $niveauPriority[$latest->niveau] ?? 0;
            if ($priority > $globalLevelTrigger) {
                $globalLevelTrigger = $priority;
                $globalLevel = $latest->niveau;
            }
        }

        $globalLabel = $globalLevel ? $niveauLabels[$globalLevel] : ($organization && $aiUsages->count() ? 'Non évalué' : '—');
        $globalClass = $globalLevel ? $niveauRiskClass[$globalLevel] : 'none';

        // Données pour les graphiques
        $domainLabels = [
            'RH' => 'Ressources humaines', 'EDUCATION' => 'Éducation', 'CREDIT' => 'Crédit',
            'SANTE' => 'Santé', 'SECURITE' => 'Sécurité', 'MARKETING' => 'Marketing',
            'PROD_INT' => 'Productivité interne', 'DEV_LOG' => 'Développement', 'AUTRE' => 'Autre',
        ];
        $domainCounts = [];
        foreach ($aiUsages as $u) {
            $key = $u->domain;
            $domainCounts[$key] = ($domainCounts[$key] ?? 0) + 1;
        }
        arsort($domainCounts);

        $chartRiskData = [
            'labels' => ['Inacceptable', 'Haut risque', 'Risque limité', 'Risque minimal', 'Non évalué'],
            'data' => [
                $countByLevel['INACCEPTABLE'],
                $countByLevel['HAUT_RISQUE'],
                $countByLevel['RISQUE_LIMITE'],
                $countByLevel['RISQUE_MINIMAL'],
                $countNonEvalues,
            ],
            'colors' => ['#9B2933', '#B5532A', '#A07626', '#3D6E54', '#475061'],
        ];
        $chartDomainData = [
            'labels' => array_values(array_map(fn ($k) => $domainLabels[$k] ?? $k, array_keys($domainCounts))),
            'data' => array_values($domainCounts),
        ];
    @endphp

    {{-- Bandeau de statut session --}}
    @if (session('status') === 'organization-created')
        <div class="status-banner status-banner--ok">
            <strong>Organisation créée.</strong>&nbsp; Vous pouvez maintenant déclarer vos usages d'IA.
        </div>
    @endif

    @if ($organization === null)
        {{-- État initial : pas d'organisation --}}
        <div class="dashboard-onboard">
            <div class="dashboard-onboard__hero">
                <div class="eyebrow eyebrow--accent">Étape 1 · Audit AI Act + RGPD</div>
                <h1>Bienvenue, <em>{{ $firstName }}</em>.</h1>
                <p class="lead">
                    Avant de déclarer vos usages d'IA, créez la fiche de votre PME. Ces informations sont utilisées
                    uniquement pour qualifier le contexte de l'audit (taille, secteur, SIRET).
                </p>
                <div style="margin-top: 32px; display: flex; gap: 12px;">
                    <a class="btn btn--accent btn--lg btn--uiverse" href="{{ route('organization.create') }}">
                        <div class="wrapper">
                            <span>Créer mon organisation</span>
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
                    <a class="btn btn--secondary btn--lg" href="{{ route('home') }}">Voir la méthode</a>
                </div>
            </div>
        </div>
    @else
        {{-- Page head : salutation + actions principales --}}
        <div class="dashboard-head">
            <div>
                <div class="eyebrow eyebrow--accent">Audit en cours · {{ now()->translatedFormat('d F Y') }}</div>
                <h1>Bonjour, <em>{{ $firstName }}</em>.</h1>
                <p class="dashboard-head__sub">
                    @if ($aiUsages->isEmpty())
                        Votre organisation <b>{{ $organization->name }}</b> est prête à recevoir ses premiers usages d'IA déclarés.
                    @else
                        {{ $aiUsages->count() }} usage{{ $aiUsages->count() > 1 ? 's' : '' }} d'IA déclaré{{ $aiUsages->count() > 1 ? 's' : '' }} pour <b>{{ $organization->name }}</b>{{ $countNonEvalues > 0 ? ", dont {$countNonEvalues} en attente d'évaluation." : '.' }}
                    @endif
                </p>
            </div>
            <div class="dashboard-head__actions">
                <a class="btn btn--secondary" href="{{ route('reports.index') }}">Mes rapports</a>
                <a class="btn btn--accent btn--uiverse" href="{{ route('usages.create') }}">
                    <div class="wrapper">
                        <span>+ Déclarer un usage</span>
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

        {{-- Bande de stats (modèle kits/dashboard.html) --}}
        <div class="band">
            <div class="band__cell band__cell--hero">
                <div class="eyebrow eyebrow--accent">Niveau de risque global</div>
                <div class="band__hero-num band__hero-num--{{ $globalClass }}">{{ $globalLabel }}</div>
                <div class="band__delta">
                    @if ($globalLevel)
                        DÉTERMINÉ PAR <b>{{ $countByLevel[$globalLevel] ?? 0 }} USAGE(S)</b>
                    @elseif ($aiUsages->count() > 0)
                        QUESTIONNAIRE À COMPLÉTER POUR ÉVALUER
                    @else
                        DÉCLAREZ AU MOINS UN USAGE POUR LANCER L'AUDIT
                    @endif
                </div>
            </div>
            <div class="band__cell">
                <div class="band__num">{{ str_pad((string) $aiUsages->count(), 2, '0', STR_PAD_LEFT) }}</div>
                <div class="band__delta">{{ $countNonEvalues }} en attente</div>
                <div class="band__label">Usages déclarés</div>
            </div>
            <div class="band__cell">
                <div class="band__num band__num--haut">{{ str_pad((string) ($countByLevel['HAUT_RISQUE'] + $countByLevel['INACCEPTABLE']), 2, '0', STR_PAD_LEFT) }}</div>
                <div class="band__delta">{{ $countByLevel['INACCEPTABLE'] > 0 ? 'dont '.$countByLevel['INACCEPTABLE'].' inacceptable' : '—' }}</div>
                <div class="band__label">Haut / Inacceptable</div>
            </div>
            <div class="band__cell">
                <div class="band__num band__num--lim">{{ str_pad((string) $countByLevel['RISQUE_LIMITE'], 2, '0', STR_PAD_LEFT) }}</div>
                <div class="band__delta">art. 50</div>
                <div class="band__label">Risque limité</div>
            </div>
            <div class="band__cell">
                <div class="band__num band__num--min">{{ str_pad((string) $countByLevel['RISQUE_MINIMAL'], 2, '0', STR_PAD_LEFT) }}</div>
                <div class="band__delta">fallback</div>
                <div class="band__label">Risque minimal</div>
            </div>
        </div>

        {{-- Graphiques --}}
        @if ($aiUsages->count() > 0)
            <div class="charts-row">
                <div class="surface chart-surface">
                    <div class="surface__head">
                        <h3>Répartition par niveau de risque</h3>
                        <span class="pill">{{ $aiUsages->count() }} usage{{ $aiUsages->count() > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="chart-surface__body">
                        <div class="chart-canvas-wrap"><canvas id="chartRisk" aria-label="Répartition des usages par niveau de risque"></canvas></div>
                        <ul class="chart-legend" id="legendRisk"></ul>
                    </div>
                </div>

                <div class="surface chart-surface">
                    <div class="surface__head">
                        <h3>Usages par domaine</h3>
                        <span class="pill">{{ count($domainCounts) }} domaine{{ count($domainCounts) > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="chart-surface__body">
                        <div class="chart-canvas-wrap chart-canvas-wrap--bar"><canvas id="chartDomain" aria-label="Nombre d'usages par domaine"></canvas></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Deux colonnes : table d'usages + sidebar organisation --}}
        <div class="dashboard-cols">
            <div class="surface">
                <div class="surface__head">
                    <h3>Usages d'IA déclarés</h3>
                    <div class="surface__head-right">
                        <span class="pill">Triés · récents</span>
                    </div>
                </div>

                @if ($aiUsages->isEmpty())
                    <div class="empty-state">
                        <div class="eyebrow">Aucun usage</div>
                        <p>
                            Aucun usage d'IA n'est encore déclaré pour <b>{{ $organization->name }}</b>.
                            Cliquez sur « Déclarer un usage » pour ajouter votre premier outil.
                        </p>
                        <a class="btn btn--accent btn--uiverse" href="{{ route('usages.create') }}">
                            <div class="wrapper">
                                <span>+ Déclarer un usage</span>
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
                @else
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th style="width: 36px"></th>
                                <th>Usage</th>
                                <th>Domaine</th>
                                <th>Niveau</th>
                                <th style="width: 120px; text-align: right">Audité</th>
                                <th style="width: 36px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aiUsages as $i => $usage)
                                @php $a = $usage->latestAssessment; @endphp
                                <tr onclick="window.location='{{ route('usages.show', $usage) }}'" style="cursor: pointer">
                                    <td class="num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="cell-name">{{ $usage->name }}</div>
                                        <div class="num cell-meta">{{ $usage->type }} · {{ $usage->domain }}</div>
                                    </td>
                                    <td>{{ $usage->domain }}</td>
                                    <td>
                                        @if ($a)
                                            <span class="risk risk--{{ $niveauRiskClass[$a->niveau] }}"><span class="risk__dot"></span>{{ $niveauLabels[$a->niveau] }}</span>
                                        @elseif ($usage->responses()->exists())
                                            <span class="risk risk--none">À évaluer</span>
                                        @else
                                            <span class="risk risk--none">Questionnaire</span>
                                        @endif
                                    </td>
                                    <td class="num" style="text-align: right">
                                        {{ $a ? $a->computed_at->translatedFormat('d M') : '—' }}
                                    </td>
                                    <td class="arrow">›</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="dashboard-aside">
                <div class="surface">
                    <div class="surface__head">
                        <h3>Organisation</h3>
                    </div>
                    <div class="org-block">
                        <div class="org-block__name">{{ $organization->name }}</div>
                        <dl class="kv">
                            @if ($organization->siret)
                                <div><dt>SIRET</dt><dd>{{ $organization->siret }}</dd></div>
                            @endif
                            <div><dt>Effectif</dt><dd>{{ $organization->size }} salariés</dd></div>
                            @if ($organization->sector)
                                <div><dt>Secteur</dt><dd>{{ $organization->sector }}</dd></div>
                            @endif
                            <div><dt>Inscrit</dt><dd>{{ $organization->created_at->translatedFormat('d F Y') }}</dd></div>
                        </dl>
                    </div>
                </div>

                @if ($globalLevel === 'HAUT_RISQUE' || $globalLevel === 'INACCEPTABLE')
                    <div class="surface cta-surface">
                        <div class="eyebrow eyebrow--accent">Action recommandée</div>
                        <div class="cta-surface__title">
                            @if ($globalLevel === 'INACCEPTABLE')
                                Un usage est <em>interdit</em> par l'AI Act.
                            @else
                                Au moins un usage est classé <em>haut risque</em>.
                            @endif
                        </div>
                        <div class="cta-surface__sub">
                            @if ($globalLevel === 'INACCEPTABLE')
                                L'Article 5 impose un arrêt immédiat. Générez le rapport pour disposer du détail réglementaire et du plan de remédiation.
                            @else
                                Une analyse d'impact (AIPD) est probablement requise. Le rapport contient le détail des obligations et un plan d'action 1 mois / 6 mois / 1 an.
                            @endif
                        </div>
                        <a class="btn btn--accent btn--uiverse" href="{{ route('reports.index') }}" style="margin-top: 12px">
                            <div class="wrapper">
                                <span>Générer le rapport</span>
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
                @endif
            </div>
        </div>
    @endif

    <style>
        h1 { font-family: var(--font-display); font-size: 64px; line-height: 1; letter-spacing: -0.02em; margin: 0; color: var(--text); }
        h1 em { color: var(--accent); font-style: italic; }

        /* Onboarding state */
        .dashboard-onboard { padding: 48px 0; }
        .dashboard-onboard__hero { max-width: 720px; }
        .dashboard-onboard__hero .lead { margin-top: 24px; max-width: 56ch; }

        /* Page head */
        .dashboard-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: 32px; flex-wrap: wrap; }
        .dashboard-head__sub { color: var(--text-muted); font-size: 15px; margin-top: 12px; max-width: 60ch; }
        .dashboard-head__sub b { color: var(--text); font-weight: 500; }
        .dashboard-head__actions { display: flex; gap: 10px; }
        .dashboard-head__actions .btn { text-decoration: none; }

        /* BAND */
        .band { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr 1fr; border: 1px solid var(--hairline); border-radius: var(--r-md); overflow: hidden; background: var(--ink-950); margin-bottom: 32px; }
        .band__cell { padding: 24px 28px; border-right: 1px solid var(--hairline); display: flex; flex-direction: column; gap: 6px; min-height: 132px; }
        .band__cell:last-child { border-right: none; }
        .band__cell--hero { background: linear-gradient(135deg, var(--ink-900) 0%, var(--ink-1000) 100%); }
        .band__num { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; color: var(--text); }
        .band__num--haut  { color: var(--risk-haut); }
        .band__num--lim   { color: var(--risk-lim); }
        .band__num--min   { color: var(--risk-min); }
        .band__hero-num { font-family: var(--font-display); font-size: 38px; line-height: 1.05; letter-spacing: -0.015em; margin-top: 8px; }
        .band__hero-num--inacc { color: var(--risk-inacc); }
        .band__hero-num--haut  { color: var(--risk-haut); }
        .band__hero-num--lim   { color: var(--risk-lim); }
        .band__hero-num--min   { color: var(--risk-min); }
        .band__hero-num--none  { color: var(--text-dim); }
        .band__label { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-dim); margin-top: auto; }
        .band__delta { font-family: var(--font-mono); font-size: 11px; color: var(--text-muted); letter-spacing: 0.04em; }
        .band__delta b { color: var(--text); font-weight: 500; }

        /* Two columns */
        .dashboard-cols { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        .dashboard-aside { display: flex; flex-direction: column; gap: 24px; }

        /* Surface (cards) */
        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); overflow: hidden; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); display: flex; justify-content: space-between; align-items: center; }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .surface__head-right { display: flex; gap: 8px; }
        .pill { font-family: var(--font-mono); font-size: 10px; padding: 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); letter-spacing: 0.04em; }

        /* Table */
        .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tbl th, .tbl td { text-align: left; padding: 14px 24px; }
        .tbl thead th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); font-weight: 500; }
        .tbl tbody tr { border-top: 1px solid var(--hairline); transition: background var(--d-fast); }
        .tbl tbody tr:hover { background: var(--ink-900); }
        .cell-name { font-weight: 500; color: var(--text); }
        .cell-meta { margin-top: 2px; }
        .tbl .num { font-family: var(--font-mono); color: var(--text-dim); font-size: 12px; }
        .tbl .arrow { color: var(--text-dim); font-size: 16px; }

        /* Empty state inside surface */
        .empty-state { padding: 48px 32px; text-align: left; max-width: 520px; }
        .empty-state .eyebrow { display: block; margin-bottom: 12px; }
        .empty-state p { color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
        .empty-state b { color: var(--text); font-weight: 500; }
        .empty-state .btn { text-decoration: none; }

        /* Organization block */
        .org-block { padding: 24px; }
        .org-block__name { font-family: var(--font-display); font-size: 22px; line-height: 1.1; letter-spacing: -0.01em; color: var(--text); margin-bottom: 16px; }
        .kv { display: flex; flex-direction: column; gap: 10px; margin: 0; }
        .kv > div { display: flex; justify-content: space-between; gap: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--hairline); font-size: 12px; }
        .kv > div:last-child { border-bottom: none; padding-bottom: 0; }
        .kv dt { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); margin: 0; }
        .kv dd { margin: 0; color: var(--text); font-weight: 500; }

        /* CTA surface */
        .cta-surface { padding: 24px; background: radial-gradient(ellipse at 90% 20%, rgba(46, 95, 160, 0.08) 0%, transparent 50%), var(--ink-950); }
        .cta-surface__title { font-family: var(--font-display); font-size: 22px; line-height: 1.2; letter-spacing: -0.01em; color: var(--text); margin-top: 12px; margin-bottom: 8px; }
        .cta-surface__title em { color: var(--accent); font-style: italic; }
        .cta-surface__sub { color: var(--text-muted); font-size: 13px; line-height: 1.55; }
        .cta-surface .btn { text-decoration: none; }

        /* Charts */
        .charts-row { display: grid; grid-template-columns: 1fr 1.4fr; gap: 24px; margin-bottom: 32px; }
        .chart-surface { display: flex; flex-direction: column; }
        .chart-surface__body { padding: 24px; flex: 1; display: flex; gap: 24px; align-items: stretch; min-height: 280px; }
        .chart-canvas-wrap { flex: 1; min-width: 0; position: relative; max-width: 240px; }
        .chart-canvas-wrap--bar { max-width: none; }
        .chart-legend { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; min-width: 180px; align-self: center; }
        .chart-legend li { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--ink-200); }
        .chart-legend .swatch { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
        .chart-legend .label { flex: 1; }
        .chart-legend .count { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.04em; }
        .chart-legend .count b { color: var(--text); font-weight: 500; }

        @media (max-width: 1100px) {
            .band { grid-template-columns: 1fr 1fr; }
            .band__cell--hero { grid-column: span 2; }
            .band__cell { border-right: none; border-bottom: 1px solid var(--hairline); }
            .dashboard-cols { grid-template-columns: 1fr; }
            .charts-row { grid-template-columns: 1fr; }
            .chart-surface__body { flex-direction: column; align-items: center; }
            .chart-canvas-wrap { max-width: 280px; }
            h1 { font-size: 48px; }
        }
    </style>

    @if ($aiUsages->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const riskData = @json($chartRiskData);
                const domainData = @json($chartDomainData);
                const total = riskData.data.reduce((a, b) => a + b, 0);

                // ---- Donut: répartition des risques ----
                const ctxRisk = document.getElementById('chartRisk');
                if (ctxRisk && total > 0) {
                    new Chart(ctxRisk, {
                        type: 'doughnut',
                        data: {
                            labels: riskData.labels,
                            datasets: [{
                                data: riskData.data,
                                backgroundColor: riskData.colors,
                                borderColor: '#11161E',
                                borderWidth: 2,
                                hoverOffset: 8,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '62%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0B0F14',
                                    borderColor: '#303845',
                                    borderWidth: 1,
                                    titleColor: '#E8EBF0',
                                    bodyColor: '#ABB3C2',
                                    padding: 12,
                                    titleFont: { family: 'Geist', size: 12, weight: '500' },
                                    bodyFont: { family: 'Geist Mono', size: 11 },
                                    callbacks: {
                                        label: (ctx) => {
                                            const v = ctx.parsed;
                                            const pct = total > 0 ? Math.round((v / total) * 100) : 0;
                                            return `${v} usage${v > 1 ? 's' : ''} · ${pct}%`;
                                        },
                                    },
                                },
                            },
                        },
                    });

                    // Légende custom (lisible et alignée au design)
                    const legend = document.getElementById('legendRisk');
                    if (legend) {
                        legend.innerHTML = riskData.labels.map((label, i) => {
                            const v = riskData.data[i];
                            const pct = total > 0 ? Math.round((v / total) * 100) : 0;
                            return `<li>
                                <span class="swatch" style="background:${riskData.colors[i]}"></span>
                                <span class="label">${label}</span>
                                <span class="count"><b>${v}</b> · ${pct}%</span>
                            </li>`;
                        }).join('');
                    }
                }

                // ---- Bar horizontale: usages par domaine ----
                const ctxDomain = document.getElementById('chartDomain');
                if (ctxDomain && domainData.data.length > 0) {
                    new Chart(ctxDomain, {
                        type: 'bar',
                        data: {
                            labels: domainData.labels,
                            datasets: [{
                                data: domainData.data,
                                backgroundColor: '#2E5FA0',
                                hoverBackgroundColor: '#6E92C7',
                                borderRadius: 2,
                                barThickness: 18,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0B0F14',
                                    borderColor: '#303845',
                                    borderWidth: 1,
                                    titleColor: '#E8EBF0',
                                    bodyColor: '#ABB3C2',
                                    padding: 12,
                                    titleFont: { family: 'Geist', size: 12, weight: '500' },
                                    bodyFont: { family: 'Geist Mono', size: 11 },
                                    callbacks: {
                                        label: (ctx) => `${ctx.parsed.x} usage${ctx.parsed.x > 1 ? 's' : ''}`,
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { color: '#5B6478', font: { family: 'Geist Mono', size: 10 }, stepSize: 1 },
                                    grid: { color: '#232A35', drawBorder: false },
                                },
                                y: {
                                    ticks: { color: '#ABB3C2', font: { family: 'Geist', size: 12 } },
                                    grid: { display: false, drawBorder: false },
                                },
                            },
                        },
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
