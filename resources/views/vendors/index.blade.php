<x-app-layout>
    <x-slot name="header">
        Qualyra / <b>Fournisseurs IA</b>
    </x-slot>

    @if (session('status') === 'vendor-created')
        <div class="status-banner status-banner--ok"><strong>Fournisseur créé.</strong></div>
    @elseif (session('status') === 'vendor-updated')
        <div class="status-banner status-banner--ok"><strong>Fournisseur mis à jour.</strong></div>
    @elseif (session('status') === 'vendor-deleted')
        <div class="status-banner status-banner--warn"><strong>Fournisseur supprimé.</strong></div>
    @endif

    <div class="page-head">
        <div>
            <div class="eyebrow eyebrow--accent">Audit · Chaîne d'approvisionnement</div>
            <h1>Mes <em>fournisseurs.</em></h1>
            <p class="page-head__sub">
                {{ $vendors->count() }} fournisseur{{ $vendors->count() > 1 ? 's' : '' }} déclaré{{ $vendors->count() > 1 ? 's' : '' }}.
                Cette liste alimente l'analyse de transfert hors UE, de DPA Art. 28 et de déclaration Art. 47.
            </p>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn--accent">+ Déclarer un fournisseur</a>
    </div>

    <div class="surface">
        <div class="surface__head">
            <h3>Liste des fournisseurs</h3>
        </div>

        @if ($vendors->isEmpty())
            <div class="empty-state">
                <div class="eyebrow">Aucun fournisseur</div>
                <p>Aucun fournisseur IA n'a encore été déclaré. Déclarez vos prestataires (OpenAI, Anthropic, solution interne, etc.) pour activer l'analyse de chaîne d'approvisionnement dans vos rapports.</p>
                <a class="btn btn--accent" href="{{ route('vendors.create') }}">+ Déclarer un fournisseur</a>
            </div>
        @else
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 36px"></th>
                        <th>Fournisseur</th>
                        <th>Type</th>
                        <th>Pays</th>
                        <th>Art. 47</th>
                        <th>DPA</th>
                        <th>CCT</th>
                        <th>Usages</th>
                        <th style="width: 200px; text-align: right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $i => $v)
                        <tr>
                            <td class="num">{{ str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('vendors.show', $v) }}" class="cell-link">{{ $v->name }}</a>
                                @if ($v->hors_ue)
                                    <span class="pill pill--warn" title="Hébergement hors UE">Hors UE</span>
                                @endif
                            </td>
                            <td><span class="num">{{ $v->type_contractuel }}</span></td>
                            <td><span class="num">{{ $v->pays_hebergement ?? '—' }}</span></td>
                            <td>{{ $v->declaration_conformite_art47 ? '✓' : '—' }}</td>
                            <td>{{ $v->dpa_art28_signe ? '✓' : '—' }}</td>
                            <td>{{ $v->cct_signees === null ? '—' : ($v->cct_signees ? '✓' : '✗') }}</td>
                            <td><span class="num">{{ $v->ai_usages_count }}</span></td>
                            <td style="text-align: right">
                                <a href="{{ route('vendors.edit', $v) }}" class="row-action">Modifier</a>
                                <form method="POST" action="{{ route('vendors.destroy', $v) }}" style="display: inline; margin: 0"
                                      onsubmit="return confirm('Supprimer définitivement « {{ $v->name }} » ? Les usages rattachés ne seront pas supprimés mais détachés.')">
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

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); overflow: hidden; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }

        .pill { display: inline-block; font-family: var(--font-mono); font-size: 10px; padding: 3px 8px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); margin-left: 8px; letter-spacing: 0.04em; color: var(--text-muted); }
        .pill--warn { color: var(--risk-lim); border-color: var(--risk-lim); }

        .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tbl th, .tbl td { text-align: left; padding: 14px 24px; }
        .tbl thead th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); font-weight: 500; border-bottom: 1px solid var(--hairline); }
        .tbl tbody tr { border-bottom: 1px solid var(--hairline); transition: background var(--d-fast); }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: var(--surface-2); }
        .cell-link { color: var(--text); font-weight: 500; text-decoration: none; }
        .cell-link:hover { color: var(--accent-soft); }
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
