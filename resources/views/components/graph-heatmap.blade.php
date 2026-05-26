@props(['matrix'])

<div class="surface chart-surface hmd-surface" style="margin-bottom: 32px;">
    <div class="surface__head">
        <h3>Heatmap densité · domaine × type d'IA</h3>
        <span class="pill">{{ $matrix['maxCount'] }} max / cellule</span>
    </div>

    <div class="hmd-body" x-data="heatmapDensityList(@js($matrix))">
        <div class="hmd-canvas-wrap">
            <canvas id="chartHeatmapDensity" aria-label="Heatmap densité domaine × type d'IA"></canvas>
            <div class="hmd-tooltip" x-ref="tip" style="display:none;"></div>
        </div>

        {{-- Légende : barre de gradient densité --}}
        <div class="hmd-legend">
            <span class="hmd-legend__lbl">Faible</span>
            <span class="hmd-legend__bar"></span>
            <span class="hmd-legend__lbl">Élevé</span>
            <span class="hmd-legend__note">Intensité = volume d'usages (scoring matrice)</span>
        </div>

        {{-- Liste filtrable (clic sur une zone chaude) --}}
        <div class="hmd-list">
            <div class="hmd-list__head">
                <span x-text="filteredUsages.length + ' usage(s)' + (selected ? ' · ' + (matrix.domainLabels[selected.domain] || selected.domain) + ' / ' + (matrix.typeLabels[selected.type] || selected.type) : '')"></span>
                <button type="button" x-show="selected" @click="reset()" class="hmd-reset">Réinitialiser</button>
            </div>
            <ul class="hmd-list__items">
                <template x-for="u in filteredUsages" :key="u.id">
                    <li>
                        <span class="hmd-list__name" x-text="u.name"></span>
                        <span class="hmd-list__meta" x-text="u.niveau"></span>
                    </li>
                </template>
                <li x-show="filteredUsages.length === 0" class="hmd-list__empty">Aucun usage pour ce filtre.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .hmd-surface .surface__head h3 { font-size: 15px; }
    .hmd-body { padding: 24px; }
    .hmd-canvas-wrap { position: relative; height: 460px; margin-bottom: 12px; }
    .hmd-canvas-wrap canvas { width: 100%; height: 100%; display: block; cursor: crosshair; }

    .hmd-tooltip {
        position: absolute; pointer-events: none; z-index: 5;
        background: var(--surface); border: 1px solid var(--hairline-strong); border-radius: 6px;
        padding: 10px 12px; color: var(--text);
        font-family: var(--font-mono); font-size: 11px; line-height: 1.5;
        max-width: 240px; transform: translate(-50%, -100%); white-space: pre-line;
    }
    .hmd-tooltip b { font-family: var(--font-sans); font-weight: 500; color: var(--text); }

    .hmd-legend { display: flex; align-items: center; gap: 8px; padding: 0 8px 16px; flex-wrap: wrap; }
    .hmd-legend__bar {
        display: inline-block; width: 180px; height: 12px; border-radius: 3px;
        background: linear-gradient(90deg, rgba(0,0,255,0.35) 0%, #00ffff 25%, #00ff00 50%, #ffff00 75%, #ff0000 100%);
    }
    .hmd-legend__lbl { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); letter-spacing: 0.06em; text-transform: uppercase; }
    .hmd-legend__note { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-left: auto; font-style: italic; }

    .hmd-list { margin-top: 16px; padding-top: 20px; border-top: 1px solid var(--hairline); }
    .hmd-list__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; color: var(--text-muted); font-family: var(--font-mono); }
    .hmd-reset { background: none; border: 1px solid var(--hairline); color: var(--text-muted); padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 11px; font-family: var(--font-mono); letter-spacing: 0.04em; }
    .hmd-reset:hover { color: var(--text); border-color: var(--text); }
    .hmd-list__items { list-style: none; padding: 0; margin: 0; max-height: 240px; overflow-y: auto; }
    .hmd-list__items li { display: flex; justify-content: space-between; padding: 10px 4px; border-bottom: 1px solid var(--hairline); font-size: 13px; }
    .hmd-list__items li:last-child { border-bottom: none; }
    .hmd-list__name { color: var(--text); }
    .hmd-list__meta { color: var(--text-dim); font-family: var(--font-mono); font-size: 11px; }
    .hmd-list__empty { color: var(--text-dim); text-align: center; padding: 24px 0; font-size: 13px; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heatmapDensityList', (matrix) => ({
            matrix,
            selected: null,
            allCells: matrix.cells,

            init() {
                window.addEventListener('heatmap:cell-click', (e) => {
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

    function cssVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    }

    (function initHeatmapDensity() {
        const cv = document.getElementById('chartHeatmapDensity');
        const data = @js($matrix);
        if (!cv || !data) return;

        const niveauLabels = data.niveauLabels || {};
        const levelOrder = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVAL'];
        const max = Math.max(data.maxCount || 0, 1);

        // Rampe de couleurs 256 px : transparent → bleu → cyan → vert → jaune → rouge
        const gradData = (() => {
            const g = document.createElement('canvas');
            g.width = 1; g.height = 256;
            const gx = g.getContext('2d');
            const grad = gx.createLinearGradient(0, 0, 0, 256);
            grad.addColorStop(0.0, '#0000ff');
            grad.addColorStop(0.25, '#00ffff');
            grad.addColorStop(0.5, '#00ff00');
            grad.addColorStop(0.75, '#ffff00');
            grad.addColorStop(1.0, '#ff0000');
            gx.fillStyle = grad;
            gx.fillRect(0, 0, 1, 256);
            return gx.getImageData(0, 0, 1, 256).data;
        })();

        // Pinceau radial pré-rendu (technique simpleheat : flou via shadow)
        function makeBrush(r, blur) {
            const b = document.createElement('canvas');
            const bx = b.getContext('2d');
            const r2 = r + blur;
            b.width = b.height = r2 * 2;
            bx.shadowOffsetX = bx.shadowOffsetY = r2 * 2;
            bx.shadowBlur = blur;
            bx.shadowColor = '#000';
            bx.beginPath();
            bx.arc(-r2, -r2, r, 0, Math.PI * 2, true);
            bx.closePath();
            bx.fill();
            return { canvas: b, r2 };
        }

        let geom = null; // { padL, padT, plotW, plotH, cellW, cellH, cols, rows }

        function cellAt(mx, my) {
            if (!geom) return null;
            const cx = mx - geom.padL, cy = my - geom.padT;
            if (cx < 0 || cy < 0 || cx > geom.plotW || cy > geom.plotH) return null;
            const col = Math.floor(cx / geom.cellW);
            const row = Math.floor(cy / geom.cellH);
            const type = data.types[col];
            const domain = data.domains[row];
            if (!type || !domain) return null;
            const cell = data.cells.find(c => c.x === type && c.y === domain);
            return cell || { x: type, y: domain, count: 0, worstNiveau: 'NON_EVAL', breakdown: {} };
        }

        let heatCanvas = null;
        let viewW = 0, viewH = 0, devPR = 1;
        let plotCx = 0, plotCy = 0;
        let rafId = null, running = false;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Construit (une fois par layout) le calque de densité colorisé hors-écran.
        function buildLayer() {
            const W = cv.clientWidth, H = cv.clientHeight;
            if (W === 0 || H === 0) return false;
            const dpr = window.devicePixelRatio || 1;
            viewW = W; viewH = H; devPR = dpr;
            cv.width = W * dpr;
            cv.height = H * dpr;

            const padL = 150, padR = 24, padT = 14, padB = 48;
            const cols = data.types.length, rows = data.domains.length;
            const plotW = W - padL - padR;
            const plotH = H - padT - padB;
            const cellW = plotW / cols;
            const cellH = plotH / rows;
            geom = { padL, padT, plotW, plotH, cellW, cellH, cols, rows };
            plotCx = padL + plotW / 2;
            plotCy = padT + plotH / 2;

            const heat = document.createElement('canvas');
            heat.width = W; heat.height = H;
            const hx = heat.getContext('2d');
            const r = Math.min(cellW, cellH) * 0.72;
            const brush = makeBrush(r, r * 0.95);

            data.cells.forEach((c) => {
                if (!c.count) return;
                const col = data.types.indexOf(c.x);
                const row = data.domains.indexOf(c.y);
                if (col < 0 || row < 0) return;
                const px = padL + (col + 0.5) * cellW;
                const py = padT + (row + 0.5) * cellH;
                // Intensité = scoring de la matrice (0.25 + 0.75·volume), rendu en dégradé heatmap
                hx.globalAlpha = 0.25 + 0.75 * (c.count / max);
                hx.drawImage(brush.canvas, px - brush.r2, py - brush.r2);
            });

            const img = hx.getImageData(0, 0, W, H);
            const pix = img.data;
            for (let i = 0, n = pix.length; i < n; i += 4) {
                const a = pix[i + 3] * 4;
                if (a) {
                    pix[i] = gradData[a];
                    pix[i + 1] = gradData[a + 1];
                    pix[i + 2] = gradData[a + 2];
                }
            }
            hx.putImageData(img, 0, 0);
            heatCanvas = heat;
            return true;
        }

        // Composite peu coûteux appelé à chaque frame : respiration + libellés.
        function paint(now) {
            if (!heatCanvas || !geom) return;
            const ctx = cv.getContext('2d');
            ctx.setTransform(devPR, 0, 0, devPR, 0, 0);
            ctx.clearRect(0, 0, viewW, viewH);

            // État normal en permanence ; bref pic d'illumination puis retour normal.
            let glow = 0;
            if (!reduceMotion) {
                const t = ((now || 0) % 4500) / 4500;            // cycle ~4,5 s
                glow = Math.exp(-Math.pow((t - 0.5) / 0.13, 2));  // flash centré, repos = 0
            }
            ctx.save();
            ctx.globalAlpha = 0.9;
            ctx.drawImage(heatCanvas, 0, 0);                       // luminosité normale
            if (glow > 0.01) {
                ctx.globalCompositeOperation = 'lighter';          // surbrillance additive
                ctx.globalAlpha = 0.85 * glow;
                ctx.drawImage(heatCanvas, 0, 0);
                ctx.globalAlpha = 0.55 * glow;                     // 2e passe : pic plus intense
                ctx.drawImage(heatCanvas, 0, 0);
            }
            ctx.restore();

            const { padL, padT, plotH, cellW, cellH } = geom;
            ctx.globalAlpha = 1;
            ctx.fillStyle = cssVar('--text');
            ctx.textBaseline = 'middle';
            ctx.textAlign = 'right';
            ctx.font = '11px "Geist", system-ui, sans-serif';
            data.domains.forEach((dom, row) => {
                ctx.fillText(data.domainLabels[dom] || dom, padL - 14, padT + (row + 0.5) * cellH);
            });
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.font = '10px "Geist Mono", ui-monospace, monospace';
            data.types.forEach((type, col) => {
                ctx.fillText(data.typeLabels[type] || type, padL + (col + 0.5) * cellW, padT + plotH + 14);
            });
        }

        function loop(now) {
            paint(now);
            rafId = requestAnimationFrame(loop);
        }
        function startLoop() {
            if (reduceMotion) { paint(0); return; }
            if (running) return;
            running = true;
            rafId = requestAnimationFrame(loop);
        }
        function stopLoop() {
            running = false;
            if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
        }
        function render() {
            if (buildLayer()) { stopLoop(); startLoop(); }
        }

        // Tooltip + clic → filtre la liste (event écouté par Alpine heatmapDensityList)
        const wrap = cv.parentElement;
        const tip = wrap ? wrap.querySelector('.hmd-tooltip') : null;

        cv.addEventListener('mousemove', (e) => {
            if (!tip) return;
            const rect = cv.getBoundingClientRect();
            const mx = e.clientX - rect.left, my = e.clientY - rect.top;
            const cell = cellAt(mx, my);
            if (!cell || !cell.count) { tip.style.display = 'none'; return; }
            const dl = data.domainLabels[cell.y] || cell.y;
            const tl = data.typeLabels[cell.x] || cell.x;
            let html = `<b>${dl} × ${tl}</b>\n${cell.count} usage(s)`;
            if (cell.worstNiveau) {
                html += `\nPire risque : ${niveauLabels[cell.worstNiveau] || cell.worstNiveau}`;
            }
            if (cell.breakdown) {
                levelOrder.forEach((lvl) => {
                    if (cell.breakdown[lvl] > 0) {
                        html += `\n  ${niveauLabels[lvl] || lvl} : ${cell.breakdown[lvl]}`;
                    }
                });
            }
            tip.innerHTML = html;
            tip.style.left = mx + 'px';
            tip.style.top = (my - 12) + 'px';
            tip.style.display = 'block';
        });
        cv.addEventListener('mouseleave', () => { if (tip) tip.style.display = 'none'; });

        cv.addEventListener('click', (e) => {
            const rect = cv.getBoundingClientRect();
            const cell = cellAt(e.clientX - rect.left, e.clientY - rect.top);
            if (!cell) return;
            window.dispatchEvent(new CustomEvent('heatmap:cell-click', {
                detail: { domain: cell.y, type: cell.x },
            }));
        });

        // Déclenchement : chargement, resize, activation de l'onglet, visibilité
        const boot = () => buildLayer();
        if (document.readyState === 'complete') boot();
        else window.addEventListener('load', boot);

        let rt;
        window.addEventListener('resize', () => {
            clearTimeout(rt);
            rt = setTimeout(() => { if (buildLayer() && !running) paint(performance.now()); }, 150);
        });
        window.addEventListener('vision:view-change', (e) => {
            if (e.detail && e.detail.view === 'heatmap') render();
            else stopLoop();
        });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stopLoop();
        });
    })();
</script>
