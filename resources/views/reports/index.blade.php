<x-app-layout>
    <x-slot name="header">
        Qualyra / <b>Rapports de conformité</b>
    </x-slot>

    @php
        $statusMessages = [
            'reports-need-usages' => ['type' => 'warn', 'msg' => 'Déclarez au moins un usage IA avant de générer un rapport.'],
        ];
        $status = session('status');
        $banner = $statusMessages[$status] ?? null;
    @endphp

    @if ($banner)
        <div class="status-banner status-banner--{{ $banner['type'] }}">{{ $banner['msg'] }}</div>
    @endif

    <div class="page-head">
        <div>
            <div class="eyebrow eyebrow--accent">Audit · Livrable</div>
            <h1>Rapports <em>PDF.</em></h1>
            <p class="page-head__sub">
                Chaque rapport est un PDF figé à un instant T : modifier un usage par la suite n'altère
                jamais un rapport déjà généré (snapshot conservé en base).
            </p>
        </div>
    </div>

    {{-- Génération d'un nouveau rapport --}}
    <div class="surface surface--cta">
        <div>
            <div class="eyebrow eyebrow--accent">Nouveau rapport</div>
            <div class="cta-title">Générer un audit pour <em>l'état actuel</em>.</div>
            <div class="cta-sub">Synthèse exécutive · classification AI Act par usage · plan d'action 1 mois / 6 mois / 1 an · checklist opérationnelle.</div>
        </div>
        <form method="POST" action="{{ route('checkout.create') }}" style="margin: 0">
            @csrf
            <button type="submit" class="btn btn--accent btn--lg btn--uiverse">
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
            </button>
        </form>
    </div>

    {{-- Historique --}}
    <div class="surface" style="margin-top: 24px">
        <div class="surface__head">
            <h3>Historique</h3>
            <span class="pill">{{ $reports->count() }} rapport{{ $reports->count() > 1 ? 's' : '' }}</span>
        </div>

        @if ($reports->isEmpty())
            <div class="empty-state">
                <div class="eyebrow">Aucun rapport</div>
                <p>Aucun rapport n'a encore été généré pour votre organisation. Cliquez sur « Générer » ci-dessus pour produire votre premier audit PDF.</p>
            </div>
        @else
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 48px"></th>
                        <th>Référence</th>
                        <th>Généré</th>
                        <th style="width: 200px; text-align: right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $i => $report)
                        <tr>
                            <td class="num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('reports.show', $report) }}" class="cell-link">
                                    Rapport #{{ $report->id }}
                                </a>
                            </td>
                            <td class="num">{{ $report->created_at->translatedFormat('d M Y · H:i') }}</td>
                            <td style="text-align: right">
                                <a href="{{ route('reports.show', $report) }}" class="row-action">Consulter</a>
                                <a href="{{ route('reports.download', $report) }}" class="row-action row-action--accent">Télécharger PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }
        h1 em { color: var(--accent); font-style: italic; }
        .page-head { margin-bottom: 32px; }
        .page-head__sub { color: var(--text-muted); font-size: 14px; margin-top: 12px; max-width: 64ch; }
        .page-head__sub b { color: var(--text); font-weight: 500; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); overflow: hidden; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); display: flex; justify-content: space-between; align-items: center; }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .pill { font-family: var(--font-mono); font-size: 10px; padding: 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); letter-spacing: 0.04em; }

        .surface--cta { padding: 32px; display: flex; align-items: center; justify-content: space-between; gap: 32px; background: radial-gradient(ellipse at 90% 20%, color-mix(in oklab, var(--accent) 8%, transparent) 0%, transparent 50%), var(--surface); }
        .cta-title { font-family: var(--font-display); font-size: 28px; line-height: 1.1; letter-spacing: -0.015em; color: var(--text); margin: 8px 0; max-width: 28ch; }
        .cta-title em { color: var(--accent); font-style: italic; }
        .cta-sub { color: var(--text-muted); font-size: 13px; max-width: 60ch; line-height: 1.55; }

        .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tbl th, .tbl td { text-align: left; padding: 14px 24px; }
        .tbl thead th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); font-weight: 500; border-bottom: 1px solid var(--hairline); }
        .tbl tbody tr { border-bottom: 1px solid var(--hairline); transition: background var(--d-fast); }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: var(--surface-2); }
        .cell-link { color: var(--text); font-weight: 500; text-decoration: none; }
        .cell-link:hover { color: var(--accent-soft); }
        .tbl .num { font-family: var(--font-mono); color: var(--text-dim); font-size: 12px; }

        .row-action { font-size: 12px; color: var(--text-muted); text-decoration: none; padding: 4px 8px; transition: color var(--d-fast); margin-left: 4px; }
        .row-action:hover { color: var(--text); }
        .row-action--accent { color: var(--accent-soft); font-weight: 500; }
        .row-action--accent:hover { color: var(--accent); }

        .empty-state { padding: 64px 32px; max-width: 520px; }
        .empty-state .eyebrow { display: block; margin-bottom: 12px; }
        .empty-state p { color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0; }

        @media (max-width: 720px) {
            .surface--cta { flex-direction: column; align-items: flex-start; }
            h1 { font-size: 40px; }
            .cta-title { font-size: 22px; }
        }
    </style>
</x-app-layout>
