@props(['graph'])

<div class="surface graph-surface" x-data="usageGraph(@js($graph))" x-init="init()" style="margin-bottom: 32px;">
    <div class="surface__head">
        <h3>Réseau des usages</h3>
        <div class="graph-controls">
            <span class="graph-controls__label">Connexion par</span>
            <div class="graph-segmented" role="tablist" aria-label="Critère de connexion">
                <button type="button" role="tab"
                        :class="{ 'is-active': linkBy === 'domain' }"
                        @click="setLinkBy('domain')"
                        :aria-selected="linkBy === 'domain'">Domaine</button>
                <button type="button" role="tab"
                        :class="{ 'is-active': linkBy === 'type' }"
                        @click="setLinkBy('type')"
                        :aria-selected="linkBy === 'type'">Type d'IA</button>
                <button type="button" role="tab"
                        :class="{ 'is-active': linkBy === 'niveau' }"
                        @click="setLinkBy('niveau')"
                        :aria-selected="linkBy === 'niveau'">Risque</button>
            </div>
        </div>
        <div class="graph-controls">
            <span class="graph-controls__label">Vue</span>
            <div class="graph-segmented" role="tablist" aria-label="Disposition du graphe">
                <button type="button" role="tab"
                        :class="{ 'is-active': layout === 'clusters' }"
                        @click="setLayout('clusters')"
                        :aria-selected="layout === 'clusters'"
                        title="Clusters — regroupement par attribut">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="8" cy="8" r="5"/><circle cx="8" cy="8" r="1.5" fill="currentColor"/><circle cx="6" cy="10" r="1.5" fill="currentColor"/><circle cx="16" cy="15" r="5"/><circle cx="16" cy="15" r="1.5" fill="currentColor"/><circle cx="14" cy="17" r="1.5" fill="currentColor"/></svg>
                    Clusters
                </button>
                <button type="button" role="tab"
                        :class="{ 'is-active': layout === 'orbital' }"
                        @click="setLayout('orbital')"
                        :aria-selected="layout === 'orbital'"
                        title="Orbitale — anneaux concentriques par risque">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2" fill="currentColor"/></svg>
                    Orbitale
                </button>
                <button type="button" role="tab"
                        :class="{ 'is-active': layout === 'free' }"
                        @click="setLayout('free')"
                        :aria-selected="layout === 'free'"
                        title="Libre — force dirigée organique">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="6" cy="8" r="1.5" fill="currentColor"/><circle cx="18" cy="6" r="1.5" fill="currentColor"/><circle cx="12" cy="14" r="1.5" fill="currentColor"/><circle cx="8" cy="18" r="1.5" fill="currentColor"/><circle cx="16" cy="18" r="1.5" fill="currentColor"/><path d="M6 8 12 14M18 6 12 14M12 14 8 18M12 14 16 18" opacity="0.35"/></svg>
                    Libre
                </button>
                <button type="button" role="tab"
                        :class="{ 'is-active': layout === 'columns' }"
                        @click="setLayout('columns')"
                        :aria-selected="layout === 'columns'"
                        title="Colonnes — bandes verticales par attribut">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><rect x="3" y="4" width="5" height="16" rx="1"/><rect x="9.5" y="8" width="5" height="12" rx="1"/><rect x="16" y="6" width="5" height="14" rx="1"/></svg>
                    Colonnes
                </button>
            </div>
        </div>
    </div>

    <div class="graph-body">
        <div class="graph-canvas-wrap" x-ref="canvas">
            <svg id="usage-graph-svg" preserveAspectRatio="xMidYMid meet" aria-label="Graphe réseau des usages d'IA">
                <defs>
                    <filter id="halo-blur" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="3.5"/>
                    </filter>
                    <filter id="halo-blur-strong" x="-100%" y="-100%" width="300%" height="300%">
                        <feGaussianBlur stdDeviation="5"/>
                    </filter>
                </defs>
    <g class="graph-viewport">
        <g class="graph-layout-bg"></g>
        <g class="graph-domain-hubs"></g>
        <g class="graph-links"></g>
        <g class="graph-halos" filter="url(#halo-blur)"></g>
        <g class="graph-nodes"></g>
        <g class="graph-labels"></g>
    </g>
            </svg>

            <div class="graph-zoom">
                <button type="button" class="graph-zoom__btn" @click="zoomIn()" aria-label="Zoomer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M12 5v14M5 12h14"/></svg>
                </button>
                <button type="button" class="graph-zoom__btn" @click="zoomOut()" aria-label="Dézoomer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M5 12h14"/></svg>
                </button>
                <button type="button" class="graph-zoom__btn" @click="zoomReset()" aria-label="Réinitialiser le zoom" title="Réinitialiser">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="14" height="14"><path d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5"/></svg>
                </button>
            </div>

            <div class="graph-tooltip" x-show="hoveredNode" x-cloak
                 :style="`transform: translate(${tooltipX}px, ${tooltipY}px)`"
                 x-transition.opacity.duration.150ms>
                <template x-if="hoveredNode">
                    <div>
                        <div class="graph-tooltip__name" x-text="hoveredNode.name"></div>
                        <div class="graph-tooltip__meta">
                            <span x-text="hoveredNode.domain_label"></span>
                            <span class="graph-tooltip__sep">·</span>
                            <span x-text="hoveredNode.type_label"></span>
                        </div>
                        <div class="graph-tooltip__risk">
                            <span class="graph-tooltip__dot risk-dot" :class="`risk-dot--${riskClass(hoveredNode.niveau)}`"></span>
                            <span x-text="hoveredNode.niveau_label"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="graph-legend">
            <div class="graph-legend__group">
                <span class="graph-legend__head">Niveaux</span>
                <span class="graph-legend__item"><span class="risk-dot risk-dot--inacc"></span>Inacceptable</span>
                <span class="graph-legend__item"><span class="risk-dot risk-dot--haut"></span>Haut risque</span>
                <span class="graph-legend__item"><span class="risk-dot risk-dot--lim"></span>Risque limité</span>
                <span class="graph-legend__item"><span class="risk-dot risk-dot--min"></span>Risque minimal</span>
                <span class="graph-legend__item"><span class="risk-dot risk-dot--none"></span>Non évalué</span>
            </div>
            <div class="graph-legend__hint">
                Survolez pour révéler les noms et les connexions. Glissez les nœuds. Cliquez pour ouvrir une fiche.
            </div>
        </div>
    </div>
</div>

<style>
    .graph-surface .surface__head { flex-wrap: wrap; gap: 16px; }
    .graph-controls { display: flex; align-items: center; gap: 12px; }
    .graph-controls__label { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); }
    .graph-segmented { display: inline-flex; border: 1px solid var(--hairline); border-radius: var(--r-sm); padding: 2px; gap: 2px; background: var(--bg); }
    .graph-segmented button {
        font-family: var(--font-sans); font-size: 12px; font-weight: 500;
        padding: 5px 12px; border: none; border-radius: 3px;
        background: transparent; color: var(--text-muted); cursor: pointer;
        transition: all var(--d-fast) var(--ease-out);
    }
    .graph-segmented button:hover { color: var(--text); }
    .graph-segmented button.is-active { background: var(--accent); color: #fff; }

    .graph-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
    .graph-canvas-wrap {
        position: relative; height: 560px;
        border: 1px solid var(--hairline); border-radius: var(--r-sm);
        background:
            radial-gradient(ellipse at 50% 40%, color-mix(in oklab, var(--accent) 6%, transparent), transparent 60%),
            var(--bg);
        overflow: hidden;
    }
    .graph-canvas-wrap svg { width: 100%; height: 100%; display: block; cursor: grab; }
    .graph-canvas-wrap svg:active { cursor: grabbing; }

    /* Zoom controls — overlay top-right */
    .graph-zoom { position: absolute; top: 12px; right: 12px; display: flex; flex-direction: column; gap: 4px; z-index: 4; }
    .graph-zoom__btn {
        width: 28px; height: 28px;
        display: inline-flex; align-items: center; justify-content: center;
        background: var(--surface);
        border: 1px solid var(--hairline);
        border-radius: var(--r-sm);
        color: var(--text-muted);
        cursor: pointer;
        transition: all var(--d-fast) var(--ease-out);
    }
    .graph-zoom__btn:hover { color: var(--text); border-color: var(--hairline-strong); background: var(--surface-2); }
    .graph-zoom__btn:active { transform: scale(0.95); }

    /* Edges — quiet by default, flow only when highlighted */
    .graph-links line {
        stroke: var(--hairline-strong);
        stroke-opacity: 0.32;
        stroke-width: 0.8;
        transition: stroke-opacity var(--d-base) var(--ease-out), stroke-width var(--d-base);
    }
    .graph-links line.is-dimmed { stroke-opacity: 0.06; }
    .graph-links line.is-highlighted {
        stroke: var(--accent);
        stroke-opacity: 0.85;
        stroke-width: 1.4;
        stroke-dasharray: 3 7;
        animation: graph-flow 1.4s linear infinite;
    }
    @keyframes graph-flow {
        from { stroke-dashoffset: 0; }
        to   { stroke-dashoffset: -20; }
    }

    /* Halo layer is blurred globally via SVG filter — each halo = simple circle */
    .graph-halos circle {
        opacity: 0.55;
        transition: opacity var(--d-base) var(--ease-out);
    }
    .graph-halos circle.is-dimmed { opacity: 0.08; }
    .graph-halos circle.is-highlighted { opacity: 0.95; }

    /* Sharp core circles */
    .graph-nodes g.node { cursor: pointer; }
    .graph-nodes circle.node-core {
        transition: opacity var(--d-base) var(--ease-out);
        stroke: var(--bg);
        stroke-width: 1.5;
    }
    .graph-nodes circle.node-core.is-dimmed { opacity: 0.25; }

    /* Pulse rings — inacceptable + haut risque only, gentle */
    .graph-nodes circle.node-pulse {
        pointer-events: none;
        fill: none;
        stroke-width: 1.5;
        opacity: 0;
        transform-origin: center;
        transform-box: fill-box;
        animation: graph-pulse 2.6s var(--ease-out) infinite;
    }
    @keyframes graph-pulse {
        0%   { opacity: 0.55; transform: scale(0.4); }
        70%  { opacity: 0; }
        100% { opacity: 0; transform: scale(3); }
    }

    /* Risk-tied fills + halo strokes (use risk vars from qualyra.css) */
    .node--inacc { fill: var(--risk-inacc); }
    .node--haut  { fill: var(--risk-haut); }
    .node--lim   { fill: var(--risk-lim); }
    .node--min   { fill: var(--risk-min); }
    .node--none  { fill: var(--risk-none); }
    .pulse--inacc { stroke: var(--risk-inacc); }
    .pulse--haut  { stroke: var(--risk-haut); }

    /* Labels — hidden by default, revealed on hover */
    .graph-labels text {
        font-family: var(--font-mono);
        font-size: 9px;
        fill: var(--text);
        pointer-events: none;
        text-anchor: middle;
        opacity: 0;
        transition: opacity var(--d-base) var(--ease-out);
        paint-order: stroke;
        stroke: var(--bg);
        stroke-width: 3;
        stroke-linejoin: round;
    }
    .graph-labels text.is-highlighted { opacity: 1; }

    /* Tooltip */
    .graph-tooltip {
        position: absolute; top: 0; left: 0;
        background: var(--surface-2);
        border: 1px solid var(--hairline-strong);
        border-radius: var(--r-sm);
        padding: 10px 14px;
        box-shadow: var(--shadow-3);
        font-size: 12px;
        pointer-events: none;
        max-width: 260px;
        z-index: 5;
    }
    .graph-tooltip__name { font-weight: 500; color: var(--text); margin-bottom: 4px; }
    .graph-tooltip__meta { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); letter-spacing: 0.04em; margin-bottom: 6px; }
    .graph-tooltip__sep { margin: 0 6px; opacity: 0.5; }
    .graph-tooltip__risk { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); }

    /* Domain hubs — fixed anchors for cluster layout */
    .graph-domain-hubs g.domain-hub { pointer-events: none; }
    .domain-hub__glow { fill: var(--accent); opacity: 0.06; }
    .domain-hub__core {
        fill: var(--surface);
        stroke: var(--hairline-strong);
        stroke-width: 1.5;
    }
    .domain-hub__label {
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 600;
        fill: var(--text);
        text-anchor: middle;
        pointer-events: none;
        paint-order: stroke;
        stroke: var(--bg);
        stroke-width: 3;
        stroke-linejoin: round;
    }

    /* Legend */
    .graph-legend { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; padding-top: 8px; }
    .graph-legend__group { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .graph-legend__head { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); }
    .graph-legend__item { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 0.02em; }
    .graph-legend__hint { font-size: 11px; color: var(--text-dim); font-style: italic; margin-left: auto; max-width: 360px; line-height: 1.5; }

    .risk-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; display: inline-block; }
    .risk-dot--inacc { background: var(--risk-inacc); }
    .risk-dot--haut  { background: var(--risk-haut); }
    .risk-dot--lim   { background: var(--risk-lim); }
    .risk-dot--min   { background: var(--risk-min); }
    .risk-dot--none  { background: var(--risk-none); }

    @media (max-width: 720px) {
        .graph-canvas-wrap { height: 420px; }
        .graph-legend__hint { display: none; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/d3@7.9.0/dist/d3.min.js" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('usageGraph', (graph) => ({
            usages: graph.usages,
            linkBy: 'domain',
            layout: 'clusters',
            simulation: null,
            hoveredNode: null,
            tooltipX: 0,
            tooltipY: 0,
            _initialised: false,
            _width: 800,
            _height: 560,
            _padding: 26,
            _zoom: null,
            _domainHubs: {},

            init() {
                const startWhenReady = () => {
                    if (typeof d3 === 'undefined') {
                        setTimeout(startWhenReady, 80);
                        return;
                    }
                    this.renderGraph();
                    this._initialised = true;
                };
                startWhenReady();
            },

            setLinkBy(mode) {
                if (this.linkBy === mode) return;
                this.linkBy = mode;
                if (this._initialised) {
                    this.updateLinks();
                    this.applyLayout();
                }
            },

            setLayout(mode) {
                if (this.layout === mode) return;
                this.layout = mode;
                if (this._initialised) this.applyLayout();
            },

            applyLayout() {
                if (!this.simulation) return;
                this.simulation.force('x', null).force('y', null).force('radial', null);
                this.updateBgVisual();

                // Clear domain hubs (only used in clusters mode)
                this._svg.select('.graph-domain-hubs').selectAll('*').remove();
                this._domainHubs = {};

                const cx = this._width / 2, cy = this._height / 2;
                switch (this.layout) {
                    case 'free':
                        this.simulation
                            .force('center', d3.forceCenter(cx, cy).strength(0.04))
                            .force('x', d3.forceX(cx).strength(0.05))
                            .force('y', d3.forceY(cy).strength(0.05))
                            .force('charge', d3.forceManyBody().strength(-95).distanceMax(260));
                        break;
                    case 'clusters': {
                        const positions = this._computeClusterPositions();
                        this._domainHubs = positions;
                        this.bindDomainHubs();

                        this.simulation
                            .force('center', null)
                            .force('x', d3.forceX(d => positions[d[this.linkBy]].x).strength(0.8))
                            .force('y', d3.forceY(d => positions[d[this.linkBy]].y).strength(0.8))
                            .force('charge', d3.forceManyBody().strength(-30).distanceMax(100))
                            .force('collide', d3.forceCollide().radius(d => this.collideRadius(d)).strength(1).iterations(3));
                        break;
                    }
                    case 'orbital': {
                        const radii = { INACCEPTABLE: 40, HAUT_RISQUE: 85, RISQUE_LIMITE: 130, RISQUE_MINIMAL: 175, NON_EVAL: 220 };
                        this.simulation
                            .force('center', d3.forceCenter(cx, cy).strength(0.01))
                            .force('radial', d3.forceRadial(d => radii[d.niveau] || 220, cx, cy).strength(0.35))
                            .force('charge', d3.forceManyBody().strength(-80).distanceMax(260));
                        break;
                    }
                    case 'columns': {
                        const groups = this._getGroups();
                        const keys = Object.keys(groups);
                        const bandWidth = this._width / keys.length;
                        this._columnCenters = {};
                        keys.forEach((key, i) => {
                            this._columnCenters[key] = bandWidth * (i + 0.5);
                        });
                        this.simulation
                            .force('center', d3.forceCenter(cx, cy).strength(0.01))
                            .force('x', d3.forceX(d => this._columnCenters[d[this.linkBy]]).strength(0.25))
                            .force('y', d3.forceY(cy).strength(0.02))
                            .force('charge', d3.forceManyBody().strength(-60).distanceMax(260));
                        break;
                    }
                }
                this.simulation.alpha(0.55).restart();
            },

            _getGroups() {
                const groups = {};
                this._nodes.forEach(n => {
                    const key = n[this.linkBy];
                    if (!key) return;
                    (groups[key] = groups[key] || []).push(n);
                });
                return groups;
            },

            _computeClusterPositions() {
                const groups = this._getGroups();
                const keys = Object.keys(groups);
                const n = keys.length;
                if (n === 0) return {};

                const cols = Math.ceil(Math.sqrt(n));
                const rows = Math.ceil(n / cols);
                const margin = 80;
                const cellW = (this._width - 2 * margin) / cols;
                const cellH = (this._height - 2 * margin) / rows;

                const positions = {};
                keys.forEach((key, i) => {
                    const col = i % cols;
                    const row = Math.floor(i / cols);
                    positions[key] = {
                        x: margin + col * cellW + cellW / 2,
                        y: margin + row * cellH + cellH / 2,
                        radius: Math.min(cellW, cellH) * 0.35,
                    };
                });
                return positions;
            },

            bindDomainHubs() {
                const hubs = this._svg.select('.graph-domain-hubs');
                hubs.selectAll('*').remove();

                Object.entries(this._domainHubs).forEach(([key, pos]) => {
                    const g = hubs.append('g')
                        .attr('class', 'domain-hub')
                        .attr('transform', `translate(${pos.x},${pos.y})`);

                    g.append('circle')
                        .attr('class', 'domain-hub__glow')
                        .attr('r', 28);

                    g.append('circle')
                        .attr('class', 'domain-hub__core')
                        .attr('r', 20);

                    g.append('text')
                        .attr('class', 'domain-hub__label')
                        .attr('text-anchor', 'middle')
                        .attr('dy', '0.35em')
                        .text(key);
                });
            },

            updateBgVisual() {
                const bg = this._svg.select('.graph-layout-bg');
                bg.selectAll('*').remove();
                switch (this.layout) {
                    case 'clusters': this._bgClusters(bg); break;
                    case 'orbital': this._bgOrbital(bg); break;
                    case 'columns': this._bgColumns(bg); break;
                }
            },

            _bgClusters(bg) {
                if (!this._domainHubs) return;
                Object.entries(this._domainHubs).forEach(([key, pos]) => {
                    const r = (pos.radius || 60) * 1.15;
                    bg.append('circle')
                        .attr('cx', pos.x).attr('cy', pos.y)
                        .attr('r', r)
                        .attr('fill', 'var(--accent)')
                        .attr('opacity', 0.03)
                        .attr('stroke', 'var(--hairline)')
                        .attr('stroke-width', 1)
                        .attr('stroke-opacity', 0.2);
                });
            },

            _bgOrbital(bg) {
                const cx = this._width / 2, cy = this._height / 2;
                const levels = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVAL'];
                const radii = [40, 85, 130, 175, 220];
                levels.forEach((lvl, i) => {
                    bg.append('circle')
                        .attr('cx', cx).attr('cy', cy)
                        .attr('r', radii[i])
                        .attr('fill', 'none')
                        .attr('stroke', 'var(--hairline)')
                        .attr('stroke-width', 1)
                        .attr('opacity', 0.25);
                });
            },

            _bgColumns(bg) {
                if (!this._columnCenters) return;
                const keys = Object.keys(this._columnCenters);
                const bandWidth = this._width / keys.length;
                keys.forEach((key, i) => {
                    const x = i * bandWidth;
                    if (i % 2 === 0) {
                        bg.append('rect')
                            .attr('x', x).attr('y', 0)
                            .attr('width', bandWidth).attr('height', this._height)
                            .attr('fill', 'var(--accent)')
                            .attr('opacity', 0.03);
                    }
                    bg.append('text')
                        .attr('x', x + bandWidth / 2).attr('y', 18)
                        .attr('text-anchor', 'middle')
                        .attr('fill', 'var(--text-dim)')
                        .attr('font-size', 10)
                        .attr('font-family', "'Geist Mono', monospace")
                        .attr('font-weight', '500')
                        .text(key);
                });
            },

            riskClass(niveau) {
                return ({
                    INACCEPTABLE: 'inacc',
                    HAUT_RISQUE: 'haut',
                    RISQUE_LIMITE: 'lim',
                    RISQUE_MINIMAL: 'min',
                })[niveau] ?? 'none';
            },

            coreRadius(d) {
                return d.niveau === 'INACCEPTABLE' ? 7 : (d.niveau === 'HAUT_RISQUE' ? 6 : 5);
            },

            haloRadius(d) {
                return this.coreRadius(d) + 8;
            },

            collideRadius(d) {
                // min distance between nodes (centers): 2 * collideRadius
                return this.coreRadius(d) + 32;
            },

            computeLinks() {
                const groups = {};
                this.usages.forEach(n => {
                    const key = n[this.linkBy];
                    if (!key) return;
                    (groups[key] = groups[key] || []).push(n);
                });
                const links = [];
                Object.values(groups).forEach(group => {
                    for (let i = 0; i < group.length; i++) {
                        for (let j = i + 1; j < group.length; j++) {
                            links.push({ source: group[i].id, target: group[j].id });
                        }
                    }
                });
                return links;
            },

            renderGraph() {
                const wrap = this.$refs.canvas;
                const svg = d3.select('#usage-graph-svg');
                this._width = Math.max(wrap.clientWidth, 320);
                this._height = Math.max(wrap.clientHeight, 320);
                svg.attr('viewBox', `0 0 ${this._width} ${this._height}`);

                // seed initial positions near center to avoid edge-bias on first ticks
                const cx = this._width / 2, cy = this._height / 2;
                this._nodes = this.usages.map((u, i) => ({
                    ...u,
                    x: cx + Math.cos(i / this.usages.length * Math.PI * 2) * 60,
                    y: cy + Math.sin(i / this.usages.length * Math.PI * 2) * 60,
                }));
                this._links = this.computeLinks();
                this._svg = svg;

                this.bindHalos();
                this.bindNodes();
                this.bindLinks();

                this.simulation = d3.forceSimulation(this._nodes)
                    .force('link', d3.forceLink(this._links).id(d => d.id).distance(72).strength(0.55))
                    .force('charge', d3.forceManyBody().strength(-95).distanceMax(260))
                    .force('collide', d3.forceCollide().radius(d => this.collideRadius(d)).strength(0.95).iterations(2))
                    .velocityDecay(0.45)
                    .alphaDecay(0.03)
                    .on('tick', () => this.tick());

                this.applyLayout();

                this._onResize = () => {
                    if (!wrap) return;
                    this._width = Math.max(wrap.clientWidth, 320);
                    this._height = Math.max(wrap.clientHeight, 320);
                    svg.attr('viewBox', `0 0 ${this._width} ${this._height}`);
                    this.applyLayout();
                };
                window.addEventListener('resize', this._onResize);

                // ─── Zoom & pan ───────────────────────────────────────
                this._zoom = d3.zoom()
                    .scaleExtent([0.4, 3])
                    .filter((event) => {
                        if (event.type === 'wheel') return true;
                        // pan only when mousedown is on empty background, not on a node
                        return !event.target.closest('g.node');
                    })
                    .on('zoom', (event) => {
                        svg.select('.graph-viewport').attr('transform', event.transform);
                    });

                svg.call(this._zoom).on('dblclick.zoom', null);
            },

            zoomIn() {
                if (!this._zoom) return;
                this._svg.transition().duration(220).call(this._zoom.scaleBy, 1.4);
            },
            zoomOut() {
                if (!this._zoom) return;
                this._svg.transition().duration(220).call(this._zoom.scaleBy, 1 / 1.4);
            },
            zoomReset() {
                if (!this._zoom) return;
                this._svg.transition().duration(300).call(this._zoom.transform, d3.zoomIdentity);
            },

            tick() {
                // Clamp positions inside canvas bounds
                const pad = this._padding;
                const w = this._width;
                const h = this._height;
                this._nodes.forEach(n => {
                    const r = this.coreRadius(n);
                    n.x = Math.max(pad + r, Math.min(w - pad - r, n.x));
                    n.y = Math.max(pad + r, Math.min(h - pad - r, n.y));
                });

                const svg = this._svg;
                svg.select('.graph-links').selectAll('line')
                    .attr('x1', d => d.source.x)
                    .attr('y1', d => d.source.y)
                    .attr('x2', d => d.target.x)
                    .attr('y2', d => d.target.y);
                svg.select('.graph-halos').selectAll('circle')
                    .attr('cx', d => d.x)
                    .attr('cy', d => d.y);
                svg.select('.graph-nodes').selectAll('g.node')
                    .attr('transform', d => `translate(${d.x},${d.y})`);
                svg.select('.graph-labels').selectAll('text')
                    .attr('x', d => d.x)
                    .attr('y', d => d.y);
            },

            bindHalos() {
                const self = this;
                this._svg.select('.graph-halos').selectAll('circle')
                    .data(this._nodes, d => d.id)
                    .join('circle')
                    .attr('class', d => `node-halo node--${self.riskClass(d.niveau)}`)
                    .attr('r', d => self.haloRadius(d));
            },

            bindNodes() {
                const self = this;
                this._svg.select('.graph-nodes').selectAll('g.node')
                    .data(this._nodes, d => d.id)
                    .join(enter => {
                        const g = enter.append('g').attr('class', 'node');
                        g.append('circle')
                            .attr('class', d => `node-pulse pulse--${self.riskClass(d.niveau)}`)
                            .attr('r', d => self.coreRadius(d) + 2)
                            .style('display', d => (d.niveau === 'INACCEPTABLE' || d.niveau === 'HAUT_RISQUE') ? 'block' : 'none');
                        g.append('circle')
                            .attr('class', d => `node-core node--${self.riskClass(d.niveau)}`)
                            .attr('r', d => self.coreRadius(d));
                        return g;
                    })
                    .on('mouseenter', (event, d) => self.onHover(d, event))
                    .on('mousemove', (event) => self.onMove(event))
                    .on('mouseleave', () => self.onLeave())
                    .on('click', (event, d) => { window.location.href = d.url; })
                    .call(d3.drag()
                        .on('start', (event, d) => {
                            if (!event.active) self.simulation.alphaTarget(0.3).restart();
                            d.fx = d.x; d.fy = d.y;
                        })
                        .on('drag', (event, d) => {
                            const t = d3.zoomTransform(self._svg.node());
                            const x = t.invertX(event.x);
                            const y = t.invertY(event.y);
                            const pad = self._padding;
                            const r = self.coreRadius(d);
                            d.fx = Math.max(pad + r, Math.min(self._width - pad - r, x));
                            d.fy = Math.max(pad + r, Math.min(self._height - pad - r, y));
                        })
                        .on('end', (event, d) => {
                            if (!event.active) self.simulation.alphaTarget(0);
                            d.fx = null; d.fy = null;
                        }));

                this._svg.select('.graph-labels').selectAll('text')
                    .data(this._nodes, d => d.id)
                    .join('text')
                    .attr('dy', d => self.coreRadius(d) + 14)
                    .text(d => d.name.length > 24 ? d.name.slice(0, 22) + '…' : d.name);
            },

            bindLinks() {
                this._svg.select('.graph-links').selectAll('line')
                    .data(this._links, d => {
                        const s = typeof d.source === 'object' ? d.source.id : d.source;
                        const t = typeof d.target === 'object' ? d.target.id : d.target;
                        return `${s}-${t}`;
                    })
                    .join('line');
            },

            updateLinks() {
                if (!this.simulation) return;
                this._links = this.computeLinks();
                this.bindLinks();
                this.simulation.force('link').links(this._links);
                this.simulation.alpha(0.55).restart();
            },

            onHover(node, event) {
                this.hoveredNode = node;
                this.onMove(event);
                const svg = this._svg;

                const connectedIds = new Set([node.id]);
                svg.select('.graph-links').selectAll('line').each(function (d) {
                    if (d.source.id === node.id) connectedIds.add(d.target.id);
                    if (d.target.id === node.id) connectedIds.add(d.source.id);
                });

                svg.select('.graph-links').selectAll('line')
                    .classed('is-highlighted', d => d.source.id === node.id || d.target.id === node.id)
                    .classed('is-dimmed', d => d.source.id !== node.id && d.target.id !== node.id);

                svg.select('.graph-halos').selectAll('circle')
                    .classed('is-dimmed', d => !connectedIds.has(d.id))
                    .classed('is-highlighted', d => d.id === node.id);

                svg.select('.graph-nodes').selectAll('circle.node-core')
                    .classed('is-dimmed', d => !connectedIds.has(d.id));

                svg.select('.graph-labels').selectAll('text')
                    .classed('is-highlighted', d => connectedIds.has(d.id));
            },

            onMove(event) {
                const rect = this.$refs.canvas.getBoundingClientRect();
                const x = event.clientX - rect.left + 14;
                const y = event.clientY - rect.top + 14;
                // clamp tooltip inside wrap
                this.tooltipX = Math.min(x, rect.width - 240);
                this.tooltipY = Math.min(y, rect.height - 80);
            },

            onLeave() {
                this.hoveredNode = null;
                const svg = this._svg;
                svg.select('.graph-links').selectAll('line')
                    .classed('is-highlighted', false)
                    .classed('is-dimmed', false);
                svg.select('.graph-halos').selectAll('circle')
                    .classed('is-dimmed', false)
                    .classed('is-highlighted', false);
                svg.select('.graph-nodes').selectAll('circle.node-core')
                    .classed('is-dimmed', false);
                svg.select('.graph-labels').selectAll('text')
                    .classed('is-highlighted', false);
            },
        }));
    });
</script>
