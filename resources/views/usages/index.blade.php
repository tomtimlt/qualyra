<x-app-layout>
    <x-slot name="header">
        Cervus / <b>Mes usages IA</b>
    </x-slot>

    @if (session('status') === 'usage-created')
        <div class="status-banner status-banner--ok"><strong>Usage créé.</strong>&nbsp; Renseignez le questionnaire pour préparer la classification.</div>
    @elseif (session('status') === 'usage-updated')
        <div class="status-banner status-banner--ok"><strong>Usage mis à jour.</strong></div>
    @elseif (session('status') === 'usage-deleted')
        <div class="status-banner status-banner--warn"><strong>Usage supprimé.</strong></div>
    @endif

    @php
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
    @endphp

    <div class="page-head">
        <div>
            <div class="eyebrow eyebrow--accent">Audit · Inventaire</div>
            <h1>Mes <em>usages.</em></h1>
            <p class="page-head__sub">
                {{ $aiUsages->count() }} usage{{ $aiUsages->count() > 1 ? 's' : '' }} déclaré{{ $aiUsages->count() > 1 ? 's' : '' }}.
                Cliquez sur un usage pour ouvrir sa fiche, son questionnaire et sa classification.
            </p>
        </div>
        <a href="{{ route('usages.create') }}" class="btn btn--primary">+ Déclarer un usage</a>
    </div>

    <div class="surface">
        @if ($aiUsages->isEmpty())
            <div class="empty-state">
                <div class="eyebrow">Aucun usage</div>
                <p>Aucun usage d'IA n'a encore été déclaré pour votre organisation. Cliquez sur « Déclarer un usage » pour commencer.</p>
                <a class="btn btn--primary" href="{{ route('usages.create') }}">+ Déclarer un usage</a>
            </div>
        @else
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 36px"></th>
                        <th>Usage</th>
                        <th>Type</th>
                        <th>Domaine</th>
                        <th>Niveau</th>
                        <th>Questionnaire</th>
                        <th style="width: 200px; text-align: right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aiUsages as $i => $usage)
                        @php $a = $usage->assessments()->latest('computed_at')->first(); @endphp
                        <tr>
                            <td class="num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('usages.show', $usage) }}" class="cell-link">{{ $usage->name }}</a>
                                @if ($usage->description)
                                    <div class="num cell-meta">{{ \Illuminate\Support\Str::limit($usage->description, 80) }}</div>
                                @endif
                            </td>
                            <td>{{ $usage->type }}</td>
                            <td>{{ $usage->domain }}</td>
                            <td>
                                @if ($a)
                                    <span class="risk risk--{{ $niveauRiskClass[$a->niveau] }}"><span class="risk__dot"></span>{{ $niveauLabels[$a->niveau] }}</span>
                                @else
                                    <span class="risk risk--none">Non évalué</span>
                                @endif
                            </td>
                            <td>
                                @if ($usage->responses()->exists())
                                    <span class="num" style="color: var(--risk-min)">✓ Renseigné</span>
                                @else
                                    <span class="num" style="color: var(--risk-lim)">À compléter</span>
                                @endif
                            </td>
                            <td style="text-align: right">
                                <a href="{{ route('usages.edit', $usage) }}" class="row-action">Modifier</a>
                                <form method="POST" action="{{ route('usages.destroy', $usage) }}" style="display: inline; margin: 0"
                                      onsubmit="return confirm('Supprimer définitivement « {{ $usage->name }} » ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-action row-action--danger">Supprimer</button>
                                </form>
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

        .page-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: 32px; flex-wrap: wrap; }
        .page-head__sub { color: var(--text-muted); font-size: 14px; margin-top: 12px; max-width: 60ch; }
        .page-head .btn { text-decoration: none; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); overflow: hidden; }

        .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tbl th, .tbl td { text-align: left; padding: 14px 24px; }
        .tbl thead th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); font-weight: 500; border-bottom: 1px solid var(--hairline); }
        .tbl tbody tr { border-bottom: 1px solid var(--hairline); transition: background var(--d-fast); }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: var(--ink-900); }
        .cell-link { color: var(--text); font-weight: 500; text-decoration: none; }
        .cell-link:hover { color: var(--accent-soft); }
        .cell-meta { margin-top: 2px; }
        .tbl .num { font-family: var(--font-mono); color: var(--text-dim); font-size: 12px; }

        .row-action { font-size: 12px; color: var(--text-muted); text-decoration: none; padding: 4px 8px; background: none; border: none; cursor: pointer; font-family: inherit; transition: color var(--d-fast); margin-left: 4px; }
        .row-action:hover { color: var(--text); }
        .row-action--danger:hover { color: var(--risk-inacc); }

        .empty-state { padding: 64px 32px; max-width: 520px; }
        .empty-state .eyebrow { display: block; margin-bottom: 12px; }
        .empty-state p { color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
        .empty-state .btn { text-decoration: none; }
    </style>
</x-app-layout>
