<x-app-layout>
    <x-slot name="header">
        Cervus / <b>Organisation</b>
    </x-slot>

    @if (session('status') === 'organization-updated')
        <div class="status-banner status-banner--ok"><strong>Organisation mise à jour.</strong></div>
    @endif

    <div class="org-page">
        <div class="org-page__head">
            <div>
                <div class="eyebrow eyebrow--accent">Compte · Organisation</div>
                <h1>{{ $organization->name }}</h1>
                <p class="org-page__sub">
                    Ces informations apparaissent en couverture de chaque rapport généré.
                    Toute modification s'applique aux <b>nouveaux rapports</b> uniquement.
                </p>
            </div>
            <a href="{{ route('organization.edit') }}" class="btn btn--primary">Modifier</a>
        </div>

        <div class="surface">
            <div class="surface__head">
                <h3>Détail</h3>
            </div>
            <div class="surface__body">
                <dl class="kv-grid">
                    <div>
                        <dt>Nom</dt>
                        <dd>{{ $organization->name }}</dd>
                    </div>
                    <div>
                        <dt>SIRET</dt>
                        <dd class="kv-mono">{{ $organization->siret ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt>Effectif</dt>
                        <dd>{{ $organization->size }} salariés</dd>
                    </div>
                    <div>
                        <dt>Secteur</dt>
                        <dd>{{ $organization->sector ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt>Inscrite le</dt>
                        <dd>{{ $organization->created_at->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt>Dernière modification</dt>
                        <dd>{{ $organization->updated_at->translatedFormat('d F Y · H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="surface" style="margin-top: 16px">
            <div class="surface__head">
                <h3>Activité</h3>
            </div>
            <div class="surface__body">
                <dl class="kv-grid">
                    <div>
                        <dt>Usages déclarés</dt>
                        <dd>{{ $organization->aiUsages()->count() }}</dd>
                    </div>
                    <div>
                        <dt>Rapports générés</dt>
                        <dd>{{ $organization->reports()->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }

        .org-page { max-width: 760px; }
        .org-page__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--hairline); margin-bottom: 24px; flex-wrap: wrap; }
        .org-page__sub { color: var(--text-muted); font-size: 14px; margin-top: 12px; max-width: 60ch; line-height: 1.55; }
        .org-page__sub b { color: var(--text); font-weight: 500; }
        .org-page__head .btn { text-decoration: none; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); overflow: hidden; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .surface__body { padding: 28px; }

        .kv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 0; }
        .kv-grid > div { padding-bottom: 16px; border-bottom: 1px solid var(--hairline); }
        .kv-grid dt { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); margin: 0 0 6px; }
        .kv-grid dd { margin: 0; color: var(--text); font-size: 15px; line-height: 1.4; }
        .kv-mono { font-family: var(--font-mono); font-size: 13px; letter-spacing: 0.04em; }

        @media (max-width: 720px) {
            h1 { font-size: 40px; }
            .kv-grid { grid-template-columns: 1fr; }
        }
    </style>
</x-app-layout>
