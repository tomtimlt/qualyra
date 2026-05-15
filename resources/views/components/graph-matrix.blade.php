@props(['matrix'])

<div class="surface chart-surface matrix-surface" style="margin-bottom: 32px;">
    <div class="surface__head">
        <h3>Matrice Domaine × Type d'IA</h3>
        <span class="pill">{{ count($matrix['cells']) }} cellules</span>
    </div>

    <div class="matrix-body" x-data="matrixList(@js($matrix))">
        <div class="matrix-canvas-wrap">
            <canvas id="chartMatrix" aria-label="Matrice domaine × type d'IA"></canvas>
        </div>

        {{-- Légende risque --}}
        <div class="matrix-legend">
            <span class="matrix-legend__head">Risque</span>
            <span class="matrix-legend__item"><span class="risk-dot risk-dot--inacc"></span>Inacceptable</span>
            <span class="matrix-legend__item"><span class="risk-dot risk-dot--haut"></span>Haut risque</span>
            <span class="matrix-legend__item"><span class="risk-dot risk-dot--lim"></span>Risque limité</span>
            <span class="matrix-legend__item"><span class="risk-dot risk-dot--min"></span>Risque minimal</span>
            <span class="matrix-legend__item"><span class="risk-dot risk-dot--none"></span>Non évalué</span>
            <span class="matrix-legend__note">Couleur = pire risque · Opacité = volume</span>
        </div>

        {{-- Liste filtrable --}}
        <div class="matrix-list">
            <div class="matrix-list__head">
                <span x-text="filteredUsages.length + ' usage(s)' + (selected ? ' · ' + selected.domain + ' / ' + selected.type : '')"></span>
                <button type="button" x-show="selected" @click="reset()" class="matrix-reset">Réinitialiser</button>
            </div>
            <ul class="matrix-list__items">
                <template x-for="u in filteredUsages" :key="u.id">
                    <li>
                        <span class="matrix-list__name" x-text="u.name"></span>
                        <span class="matrix-list__meta" x-text="u.niveau"></span>
                    </li>
                </template>
                <li x-show="filteredUsages.length === 0" class="matrix-list__empty">Aucun usage pour ce filtre.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .matrix-surface .surface__head h3 { font-size: 15px; }
    .matrix-body { padding: 24px; }
    .matrix-canvas-wrap { position: relative; height: 420px; margin-bottom: 12px; }
    .matrix-canvas-wrap canvas { max-width: 100%; }

    .matrix-legend { display: flex; align-items: center; gap: 8px; padding: 0 8px 16px; flex-wrap: wrap; }
    .matrix-legend__head { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); margin-right: 6px; }
    .matrix-legend__item { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 0.02em; margin-right: 10px; }
    .matrix-legend__note { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-left: auto; font-style: italic; text-transform: none; letter-spacing: 0; }

    .matrix-list { margin-top: 16px; padding-top: 20px; border-top: 1px solid var(--hairline); }
    .matrix-list__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; color: var(--text-muted); font-family: var(--font-mono); }
    .matrix-reset { background: none; border: 1px solid var(--hairline); color: var(--text-muted); padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 11px; font-family: var(--font-mono); letter-spacing: 0.04em; }
    .matrix-reset:hover { color: var(--text); border-color: var(--text); }
    .matrix-list__items { list-style: none; padding: 0; margin: 0; max-height: 240px; overflow-y: auto; }
    .matrix-list__items li { display: flex; justify-content: space-between; padding: 10px 4px; border-bottom: 1px solid var(--hairline); font-size: 13px; }
    .matrix-list__items li:last-child { border-bottom: none; }
    .matrix-list__name { color: var(--text); }
    .matrix-list__meta { color: var(--text-dim); font-family: var(--font-mono); font-size: 11px; }
    .matrix-list__empty { color: var(--text-dim); text-align: center; padding: 24px 0; font-size: 13px; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('matrixList', (matrix) => ({
            matrix,
            selected: null,
            allCells: matrix.cells,

            init() {
                window.addEventListener('matrix:cell-click', (e) => {
                    this.handleClick(e.detail.domain, e.detail.type);
                });
            },

            handleClick(domain, type) {
                if (this.selected?.domain === domain && this.selected?.type === type) {
                    this.selected = null;
                } else {
                    this.selected = { domain, type };
                }
            },

            reset() { this.selected = null; },

            get filteredUsages() {
                if (!this.selected) {
                    return this.allCells.reduce((acc, c) => acc.concat(c.usages), []);
                }
                const cell = this.allCells.find(c =>
                    c.y === this.selected.domain && c.x === this.selected.type
                );
                return cell ? cell.usages : [];
            },
        }));
    });

    (function initMatrix() {
        if (typeof Chart === 'undefined' || typeof Chart.registry === 'undefined' || !Chart.registry.getController('matrix')) {
            setTimeout(initMatrix, 100);
            return;
        }

        const ctx = document.getElementById('chartMatrix');
        const matrixData = @js($matrix);
        if (!ctx || !matrixData || matrixData.maxCount === 0) return;

        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        const RISK_COLORS = {
            INACCEPTABLE: '#9B2933',
            HAUT_RISQUE: '#B5532A',
            RISQUE_LIMITE: '#A07626',
            RISQUE_MINIMAL: '#3D6E54',
            NON_EVAL: '#475061',
        };

        const levelOrder = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVAL'];

        new Chart(ctx, {
            type: 'matrix',
            data: {
                datasets: [{
                    label: 'Usages par domaine × type',
                    data: matrixData.cells,
                    backgroundColor(ctx) {
                        const cell = ctx.raw;
                        if (!cell || cell.count === 0) return 'transparent';
                        const base = RISK_COLORS[cell.worstNiveau] || '#475061';
                        const alpha = 0.25 + 0.75 * (cell.count / matrixData.maxCount);
                        return hexToRgba(base, alpha);
                    },
                    borderColor(ctx) {
                        const cell = ctx.raw;
                        if (!cell || cell.count === 0) return 'transparent';
                        return RISK_COLORS[cell.worstNiveau] || '#475061';
                    },
                    borderWidth: 1,
                    width: ({ chart }) => (chart.chartArea || {}).width / matrixData.types.length - 2,
                    height: ({ chart }) => (chart.chartArea || {}).height / matrixData.domains.length - 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (event, activeElements, chart) => {
                    if (activeElements.length === 0) return;
                    const el = activeElements[0];
                    const cell = chart.data.datasets[el.datasetIndex].data[el.index];
                    window.dispatchEvent(new CustomEvent('matrix:cell-click', {
                        detail: { domain: cell.y, type: cell.x },
                    }));
                },
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
                            title: (items) => {
                                const r = items[0].raw;
                                const dl = matrixData.domainLabels[r.y] || r.y;
                                const tl = matrixData.typeLabels[r.x] || r.x;
                                return `${dl} × ${tl}`;
                            },
                            label: (item) => {
                                const r = item.raw;
                                const lines = [`${r.count} usage(s)`];
                                if (r.worstNiveau) {
                                    const nl = matrixData.niveauLabels[r.worstNiveau] || r.worstNiveau;
                                    lines.push(`Pire risque : ${nl}`);
                                }
                                const bd = r.breakdown;
                                if (bd) {
                                    levelOrder.forEach(lvl => {
                                        if (bd[lvl] > 0) {
                                            const nl = matrixData.niveauLabels[lvl] || lvl;
                                            lines.push(`  ${nl} : ${bd[lvl]}`);
                                        }
                                    });
                                }
                                return lines;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: 'category',
                        labels: matrixData.types,
                        position: 'bottom',
                        offset: true,
                        ticks: {
                            color: '#ABB3C2',
                            font: { family: 'Geist Mono', size: 10 },
                            maxRotation: 30,
                            callback(value, index) {
                                const code = matrixData.types[index];
                                return matrixData.typeLabels[code] || code;
                            },
                        },
                        grid: { display: false, drawBorder: false },
                    },
                    y: {
                        type: 'category',
                        labels: matrixData.domains,
                        offset: true,
                        reverse: true,
                        ticks: {
                            color: '#ABB3C2',
                            font: { family: 'Geist', size: 11 },
                            callback(value, index) {
                                const code = matrixData.domains[index];
                                return matrixData.domainLabels[code] || code;
                            },
                        },
                        grid: { display: false, drawBorder: false },
                    },
                },
            },
            plugins: [{
                id: 'cellCounter',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    const meta = chart.getDatasetMeta(0);
                    meta.data.forEach((element, index) => {
                        const data = chart.data.datasets[0].data[index];
                        if (!data || data.count === 0) return;
                        const { x, y, width, height } = element.getProps(['x', 'y', 'width', 'height'], false);
                        ctx.save();
                        const count = data.count;
                        const fontSize = Math.min(Math.max(width * 0.35, 10), 16);
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
                        ctx.font = `600 ${fontSize}px "Geist Mono", monospace`;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.8)';
                        ctx.shadowBlur = 3;
                        ctx.fillText(String(count), x + width / 2, y + height / 2);
                        ctx.restore();
                    });
                },
            }],
        });
    })();
</script>
