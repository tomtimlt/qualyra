<x-app-layout>
    <x-slot name="header">
        <span>Qualyra</span> / <b>Vision</b>
    </x-slot>

    @if ($organization === null)
        <div class="vision-empty">
            <div class="eyebrow">Aucune organisation</div>
            <p>Créez votre organisation et déclarez des usages d'IA pour accéder à la cartographie des risques.</p>
            <a class="btn btn--accent" href="{{ route('organization.create') }}">Créer mon organisation</a>
        </div>
    @elseif ($aiUsages->isEmpty())
        <div class="vision-empty">
            <div class="eyebrow">Aucun usage</div>
            <p>Aucun usage d'IA déclaré. La cartographie des risques sera disponible dès que vous aurez déclaré au moins un usage.</p>
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
        <div class="vision-head" x-data="{
                view: localStorage.getItem('qualyra-vision-view') || 'sankey',
                setView(v) { this.view = v; localStorage.setItem('qualyra-vision-view', v); window.dispatchEvent(new CustomEvent('vision:view-change', { detail: { view: v } })); },
            }" x-init="window.dispatchEvent(new CustomEvent('vision:view-change', { detail: { view: view } }))">
            <div>
                <div class="eyebrow eyebrow--accent">Cartographie des risques</div>
                <h1>Vision <em>globale</em></h1>
                <p class="vision-head__sub">
                    <b>{{ $organization->name }}</b> — {{ $aiUsages->count() }} usage{{ $aiUsages->count() > 1 ? 's' : '' }} déclaré{{ $aiUsages->count() > 1 ? 's' : '' }}.
                </p>
            </div>

            <div class="vision-tabs" role="tablist" aria-label="Mode de visualisation">
                <button type="button" role="tab"
                        :class="{ 'is-active': view === 'matrix' }"
                        :aria-selected="view === 'matrix'"
                        @click="setView('matrix')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Matrice
                </button>
                <button type="button" role="tab"
                        :class="{ 'is-active': view === 'sankey' }"
                        :aria-selected="view === 'sankey'"
                        @click="setView('sankey')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path d="M3 3h18v4H3zM3 10h14v4H3zM3 17h8v4H3z"/></svg>
                    Sankey
                </button>
                <button type="button" role="tab"
                        :class="{ 'is-active': view === 'heatmap' }"
                        :aria-selected="view === 'heatmap'"
                        @click="setView('heatmap')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7" opacity="0.5"/><circle cx="12" cy="12" r="10.5" opacity="0.25"/></svg>
                    Heatmap
                </button>
            </div>
        </div>

        <div class="vision-views"
             x-data="{ view: localStorage.getItem('qualyra-vision-view') || 'sankey' }"
             :class="`is-${view}`"
             @vision:view-change.window="view = $event.detail.view">
            <div class="vision-view vision-view--matrix">
                <x-graph-matrix :matrix="$matrix" />
            </div>
            <div class="vision-view vision-view--sankey">
                <x-graph-sankey :sankey="$sankey" />
            </div>
            <div class="vision-view vision-view--heatmap">
                <x-graph-heatmap :matrix="$matrix" />
            </div>
        </div>
    @endif

    <style>
        h1 { font-family: var(--font-display); font-size: 64px; line-height: 1; letter-spacing: -0.02em; margin: 0; color: var(--text); }
        h1 em { color: var(--accent); font-style: italic; }

        .vision-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; margin-bottom: 32px; flex-wrap: wrap; }
        .vision-head__sub { color: var(--text-muted); font-size: 15px; margin-top: 12px; max-width: 60ch; }
        .vision-head__sub b { color: var(--text); font-weight: 500; }

        .vision-empty { padding: 80px 0; max-width: 520px; }
        .vision-empty .eyebrow { display: block; margin-bottom: 12px; }
        .vision-empty p { color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
        .vision-empty .btn { text-decoration: none; }

        /* Tabs */
        .vision-tabs { display: inline-flex; border: 1px solid var(--hairline); border-radius: var(--r-sm); padding: 2px; gap: 2px; background: var(--surface); flex-shrink: 0; }
        .vision-tabs button {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--font-sans); font-size: 12px; font-weight: 500;
            padding: 7px 14px; border: none; border-radius: 3px;
            background: transparent; color: var(--text-muted); cursor: pointer;
            transition: all var(--d-fast) var(--ease-out);
        }
        .vision-tabs button:hover { color: var(--text); }
        .vision-tabs button.is-active { background: var(--accent); color: #fff; }
        .vision-tabs button svg { opacity: 0.95; }

        /* Vue switcher : toutes rendues, seule l'active en flux */
        .vision-views { position: relative; }
        .vision-view {
            position: absolute; top: 0; left: 0; right: 0;
            visibility: hidden; opacity: 0; pointer-events: none;
            transition: opacity var(--d-base) var(--ease-out);
        }
        .vision-views.is-matrix .vision-view--matrix,
        .vision-views.is-sankey .vision-view--sankey,
        .vision-views.is-heatmap .vision-view--heatmap {
            position: relative;
            visibility: visible; opacity: 1; pointer-events: auto;
        }
    </style>
</x-app-layout>
