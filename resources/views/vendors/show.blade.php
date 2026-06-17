<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('vendors.index') }}" style="color: inherit; text-decoration: none">Qualyra / Fournisseurs IA</a> / <b>{{ $vendor->name }}</b>
    </x-slot>

    <div class="show-page">
        <div class="show-head">
            <div>
                <div class="eyebrow eyebrow--accent">Fournisseur IA</div>
                <h1>{{ $vendor->name }}</h1>
                <p class="meta">
                    {{ $vendor->type_contractuel }}
                    @if ($vendor->pays_hebergement)
                        · {{ $vendor->pays_hebergement }}
                    @endif
                    @if ($vendor->hors_ue)
                        · <span class="pill pill--warn">Hors UE</span>
                    @endif
                </p>
            </div>
            <div class="show-head-actions">
                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn--ghost">Modifier</a>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="eyebrow">Déclaration Art. 47</div>
                <div class="card__value">{{ $vendor->declaration_conformite_art47 ? '✓ Reçue' : '— Non reçue' }}</div>
                <p class="card__help">Déclaration de conformité fournisseur — obligatoire pour les systèmes haut risque mis sur le marché UE.</p>
            </div>
            <div class="card">
                <div class="eyebrow">DPA Art. 28 RGPD</div>
                <div class="card__value">{{ $vendor->dpa_art28_signe ? '✓ Signé' : '— Non signé' }}</div>
                <p class="card__help">Contrat de sous-traitance des traitements de données personnelles.</p>
            </div>
            <div class="card">
                <div class="eyebrow">CCT (transferts hors UE)</div>
                <div class="card__value">
                    @if ($vendor->cct_signees === null)
                        — N/A
                    @elseif ($vendor->cct_signees)
                        ✓ Signées
                    @else
                        ✗ Manquantes
                    @endif
                </div>
                <p class="card__help">Clauses contractuelles types (Décision UE 2021/914) — requises si le fournisseur héberge hors UE.</p>
            </div>
            <div class="card">
                <div class="eyebrow">Usages rattachés</div>
                <div class="card__value">{{ $vendor->ai_usages_count }}</div>
                <p class="card__help">Nombre d'outils IA déclarés s'appuyant sur ce fournisseur.</p>
            </div>
        </div>

        @if ($vendor->notes)
            <div class="surface">
                <div class="surface__head"><h3>Notes internes</h3></div>
                <div class="surface__body">
                    <p style="white-space: pre-line">{{ $vendor->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 48px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }
        .show-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: 32px; flex-wrap: wrap; }
        .show-head .meta { color: var(--text-muted); font-family: var(--font-mono); font-size: 12px; margin-top: 10px; letter-spacing: 0.04em; }

        .pill { display: inline-block; font-family: var(--font-mono); font-size: 10px; padding: 3px 8px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); letter-spacing: 0.04em; color: var(--text-muted); }
        .pill--warn { color: var(--risk-lim); border-color: var(--risk-lim); }

        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .card { border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 20px; background: var(--surface); }
        .card__value { font-family: var(--font-display); font-size: 22px; color: var(--text); margin-top: 8px; }
        .card__help { font-size: 12px; color: var(--text-dim); margin-top: 8px; line-height: 1.5; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; }
        .surface__body { padding: 24px; font-size: 13px; color: var(--text-muted); line-height: 1.7; }
    </style>
</x-app-layout>
