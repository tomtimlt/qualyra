<x-app-layout>
    <x-slot name="header">
        Qualyra / <b>Mes usages IA</b>
    </x-slot>

    @if (session('status') === 'usage-created')
        <div class="status-banner status-banner--ok"><strong>Usage créé.</strong>&nbsp; Renseignez le questionnaire pour préparer la classification.</div>
    @elseif (session('status') === 'usage-updated')
        <div class="status-banner status-banner--ok"><strong>Usage mis à jour.</strong></div>
    @elseif (session('status') === 'usage-deleted')
        <div class="status-banner status-banner--warn"><strong>Usage supprimé.</strong></div>
    @endif

    <div class="page-head">
        <div>
            <div class="eyebrow eyebrow--accent">Audit · Inventaire</div>
            <h1>Mes <em>usages.</em></h1>
            <p class="page-head__sub">
                {{ $aiUsages->count() }} usage{{ $aiUsages->count() > 1 ? 's' : '' }} déclaré{{ $aiUsages->count() > 1 ? 's' : '' }}.
                Cliquez sur un usage pour ouvrir sa fiche, son questionnaire et sa classification.
            </p>
        </div>
        <a href="{{ route('usages.create') }}" class="btn btn--accent btn--uiverse">
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

    <div class="surface"
         x-data="{
            searchQuery: '',
            filterNiveau: '',
            filterDomaine: '',
            sortField: '',
            sortDir: 'asc',
            usages: @js($usageFilterData),
            domaines: @js($domainCounts),
            domainLabels: @js($domainLabels),
            get filtered() {
                let f = this.usages;
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.trim().toLowerCase();
                    f = f.filter(u => u.name.toLowerCase().includes(q) || u.type.toLowerCase().includes(q));
                }
                if (this.filterNiveau) f = f.filter(u => u.niveau === this.filterNiveau);
                if (this.filterDomaine) f = f.filter(u => u.domain_code === this.filterDomaine);
                if (this.sortField) {
                    const dir = this.sortDir === 'asc' ? 1 : -1;
                    f = [...f].sort((a, b) => {
                        let va = a[this.sortField], vb = b[this.sortField];
                        if (this.sortField === 'niveau') {
                            const order = { INACCEPTABLE: 4, HAUT_RISQUE: 3, RISQUE_LIMITE: 2, RISQUE_MINIMAL: 1 };
                            va = order[va] ?? 0; vb = order[vb] ?? 0;
                        }
                        if (va < vb) return -1 * dir;
                        if (va > vb) return 1 * dir;
                        return 0;
                    });
                }
                return f;
            },
            sortBy(field) {
                if (this.sortField === field) {
                    if (this.sortDir === 'asc') this.sortDir = 'desc';
                    else { this.sortField = ''; this.sortDir = 'asc'; }
                } else { this.sortField = field; this.sortDir = 'asc'; }
            },
            sortIndicator(field) {
                if (this.sortField !== field) return '';
                return this.sortDir === 'asc' ? ' ▲' : ' ▼';
            },
            resetF() { this.searchQuery = ''; this.filterNiveau = ''; this.filterDomaine = ''; this.sortField = ''; this.sortDir = 'asc'; },
            confirmDelete(name) {
                return confirm('Supprimer définitivement « ' + name + ' » ?');
            },
         }">
        <div class="surface__head">
            <h3>Inventaire des usages</h3>
            <div class="surface__head-right">
                <input x-model="searchQuery" type="search" placeholder="Rechercher…" class="filter-search" aria-label="Rechercher un usage">
                <select x-model="filterNiveau" class="filter-select">
                    <option value="">Niveau : Tous</option>
                    <option value="INACCEPTABLE">Inacceptable</option>
                    <option value="HAUT_RISQUE">Haut risque</option>
                    <option value="RISQUE_LIMITE">Risque limité</option>
                    <option value="RISQUE_MINIMAL">Risque minimal</option>
                </select>
                <select x-model="filterDomaine" class="filter-select">
                    <option value="">Domaine : Tous</option>
                    <template x-for="(count, code) in domaines" :key="code">
                        <option :value="code" x-text="(domainLabels[code] || code) + ' (' + count + ')'"></option>
                    </template>
                </select>
                <span class="pill" x-text="filtered.length + ' usage' + (filtered.length > 1 ? 's' : '')"></span>
                <button type="button" x-show="searchQuery || filterNiveau || filterDomaine" @click="resetF()" class="filter-reset" aria-label="Réinitialiser les filtres">×</button>
            </div>
        </div>

        @if ($aiUsages->isEmpty())
            <div class="empty-state">
                <div class="eyebrow">Aucun usage</div>
                <p>Aucun usage d'IA n'a encore été déclaré pour votre organisation. Cliquez sur « Déclarer un usage » pour commencer.</p>
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
                        <th @click="sortBy('name')" class="sortable">Usage<span x-text="sortIndicator('name')"></span></th>
                        <th @click="sortBy('type')" class="sortable">Type<span x-text="sortIndicator('type')"></span></th>
                        <th @click="sortBy('domain_code')" class="sortable">Domaine<span x-text="sortIndicator('domain_code')"></span></th>
                        <th @click="sortBy('niveau')" class="sortable">Niveau<span x-text="sortIndicator('niveau')"></span></th>
                        <th>Questionnaire</th>
                        <th style="width: 200px; text-align: right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(u, i) in filtered" :key="u.id">
                        <tr>
                            <td class="num" x-text="String(i + 1).padStart(2, '0')"></td>
                            <td>
                                <a :href="u.url_show" class="cell-link" x-text="u.name"></a>
                                <div x-show="u.description" class="num cell-meta" x-text="u.description.length > 80 ? u.description.slice(0, 80) + '…' : u.description"></div>
                            </td>
                            <td x-text="u.type"></td>
                            <td x-text="u.domain_code"></td>
                            <td>
                                <span :class="'risk risk--' + u.status_class">
                                    <span x-show="u.niveau" class="risk__dot"></span>
                                    <span x-text="u.status_label"></span>
                                </span>
                            </td>
                            <td>
                                <span x-show="u.has_questionnaire" class="num" style="color: var(--risk-min)">✓ Renseigné</span>
                                <span x-show="!u.has_questionnaire" class="num" style="color: var(--risk-lim)">À compléter</span>
                            </td>
                            <td style="text-align: right">
                                <a :href="u.url_edit" class="row-action">Modifier</a>
                                <form method="POST" :action="u.url_destroy" style="display: inline; margin: 0"
                                      @submit.prevent="if (confirmDelete(u.name)) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="row-action row-action--danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0 && usages.length > 0">
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-dim);">
                                Aucun usage ne correspond aux filtres.
                            </td>
                        </tr>
                    </template>
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
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); display: flex; justify-content: space-between; align-items: center; }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .surface__head-right { display: flex; gap: 8px; flex-wrap: wrap; }

        .pill { font-family: var(--font-mono); font-size: 10px; padding: 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); letter-spacing: 0.04em; }
        .filter-select { font-family: var(--font-mono); font-size: 10px; padding: 4px 8px 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); background: var(--surface); letter-spacing: 0.04em; cursor: pointer; appearance: none; -webkit-appearance: none; }
        .filter-select:hover { color: var(--text); border-color: var(--text); }
        .filter-search { font-family: var(--font-mono); font-size: 10px; padding: 4px 10px; border: 1px solid var(--hairline-strong); border-radius: var(--r-pill); color: var(--text-muted); background: var(--surface); letter-spacing: 0.04em; width: 160px; outline: none; }
        .filter-search:focus { color: var(--text); border-color: var(--accent); }
        .filter-search::placeholder { color: var(--text-dim); }
        .filter-reset { background: none; border: none; color: var(--text-dim); cursor: pointer; font-size: 16px; line-height: 1; padding: 0 2px; }
        .filter-reset:hover { color: var(--text); }

        .tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tbl th, .tbl td { text-align: left; padding: 14px 24px; }
        .tbl thead th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); font-weight: 500; border-bottom: 1px solid var(--hairline); }
        .sortable { cursor: pointer; user-select: none; }
        .sortable:hover { color: var(--text); }
        .tbl tbody tr { border-bottom: 1px solid var(--hairline); transition: background var(--d-fast); }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: var(--surface-2); }
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

        @media (max-width: 900px) {
            .surface__head { flex-direction: column; align-items: stretch; gap: 12px; }
            .surface__head-right { flex-wrap: wrap; }
            .filter-search { width: 100%; }
        }
    </style>
</x-app-layout>
