@props(['heatmap'])

<div class="surface chart-surface heatmap-surface" style="margin-bottom: 32px;">
    <div class="surface__head">
        <h3>Cartographie des risques · domaine × niveau</h3>
        <span class="pill">{{ count($heatmap['allUsages']) }} usage{{ count($heatmap['allUsages']) > 1 ? 's' : '' }}</span>
    </div>

    <div class="heatmap-body" x-data="heatmapList(@js($heatmap))">
        {{-- Canvas Chart.js matrix --}}
        <div class="heatmap-canvas-wrap">
            <canvas id="chartHeatmap" aria-label="Heatmap domaine × niveau de risque"></canvas>
        </div>

        {{-- Légende couleurs par niveau --}}
        <div class="heatmap-legend">
            <span class="heatmap-legend__chip" style="background: hsl(0, 65%, 45%);"></span>
            <span class="heatmap-legend__txt">Inacceptable</span>
            <span class="heatmap-legend__chip" style="background: hsl(25, 65%, 45%);"></span>
            <span class="heatmap-legend__txt">Haut risque</span>
            <span class="heatmap-legend__chip" style="background: hsl(75, 65%, 45%);"></span>
            <span class="heatmap-legend__txt">Risque limité</span>
            <span class="heatmap-legend__chip" style="background: hsl(130, 65%, 45%);"></span>
            <span class="heatmap-legend__txt">Risque minimal</span>
            <span class="heatmap-legend__note">Intensité = nombre d'usages (normalisé par colonne)</span>
        </div>

        {{-- Liste filtrable --}}
        <div class="heatmap-list">
            <div class="heatmap-list__head">
                <span x-text="filteredUsages.length + ' usage(s)' + (selected ? ' · ' + selected.domain + ' / ' + selected.level : '')"></span>
                <button type="button" x-show="selected" @click="reset()" class="heatmap-reset">Réinitialiser</button>
            </div>
            <ul class="heatmap-list__items">
                <template x-for="u in filteredUsages" :key="u.id">
                    <li>
                        <span class="heatmap-list__name" x-text="u.name"></span>
                        <span class="heatmap-list__meta" x-text="u.domain + ' · ' + u.niveau"></span>
                    </li>
                </template>
                <li x-show="filteredUsages.length === 0" class="heatmap-list__empty">Aucun usage pour ce filtre.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .heatmap-surface .surface__head h3 { font-size: 15px; }
    .heatmap-body { padding: 24px; }
    .heatmap-canvas-wrap { position: relative; height: 380px; margin-bottom: 12px; }
    .heatmap-canvas-wrap canvas { max-width: 100%; }

    .heatmap-legend { display: flex; align-items: center; gap: 6px; padding: 0 8px 16px; flex-wrap: wrap; }
    .heatmap-legend__chip { display: inline-block; width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0; }
    .heatmap-legend__txt { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); letter-spacing: 0.06em; text-transform: uppercase; margin-right: 14px; }
    .heatmap-legend__note { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-left: auto; font-style: italic; text-transform: none; letter-spacing: 0; }

    .heatmap-list { margin-top: 16px; padding-top: 20px; border-top: 1px solid var(--hairline); }
    .heatmap-list__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; color: var(--text-muted); font-family: var(--font-mono); }
    .heatmap-reset { background: none; border: 1px solid var(--hairline); color: var(--text-muted); padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 11px; font-family: var(--font-mono); letter-spacing: 0.04em; }
    .heatmap-reset:hover { color: var(--text); border-color: var(--text); }
    .heatmap-list__items { list-style: none; padding: 0; margin: 0; max-height: 240px; overflow-y: auto; }
    .heatmap-list__items li { display: flex; justify-content: space-between; padding: 10px 4px; border-bottom: 1px solid var(--hairline); font-size: 13px; }
    .heatmap-list__items li:last-child { border-bottom: none; }
    .heatmap-list__name { color: var(--text); }
    .heatmap-list__meta { color: var(--text-dim); font-family: var(--font-mono); font-size: 11px; }
    .heatmap-list__empty { color: var(--text-dim); text-align: center; padding: 24px 0; font-size: 13px; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heatmapList', (heatmap) => ({
            heatmap,
            selected: null,
            allUsages: heatmap.allUsages,

            init() {
                window.addEventListener('heatmap:cell-click', (e) => {
                    this.handleClick(e.detail.domain, e.detail.level);
                });
            },

            handleClick(domain, level) {
                if (this.selected?.domain === domain && this.selected?.level === level) {
                    this.selected = null;
                } else {
                    this.selected = { domain, level };
                }
            },

            reset() { this.selected = null; },

            get filteredUsages() {
                if (!this.selected) return this.allUsages;
                return this.allUsages.filter(u =>
                    u.domain === this.selected.domain && u.niveau === this.selected.level
                );
            },
        }));
    });
</script>
