@props(['sankey'])

<div class="surface chart-surface sankey-surface" style="margin-bottom: 32px;">
    <div class="surface__head">
        <h3>Parcours Domaine → Type d'IA → Risque</h3>
        <span class="pill">{{ count($sankey['links']) }} flux</span>
    </div>

    <div class="sankey-body" x-data="sankeyDiagram(@js($sankey))">
        <div class="sankey-canvas-wrap" x-ref="wrap" id="sankey-container">
            <svg id="chartSankey" preserveAspectRatio="xMidYMid meet" aria-label="Diagramme Sankey domaine → type → risque"></svg>
        </div>

        <div class="sankey-legend">
            <div class="sankey-legend__group">
                <span class="sankey-legend__head">Couleur des flux</span>
                <span class="sankey-legend__item"><span class="risk-dot risk-dot--inacc"></span>Inacceptable</span>
                <span class="sankey-legend__item"><span class="risk-dot risk-dot--haut"></span>Haut risque</span>
                <span class="sankey-legend__item"><span class="risk-dot risk-dot--lim"></span>Risque limité</span>
                <span class="sankey-legend__item"><span class="risk-dot risk-dot--min"></span>Risque minimal</span>
                <span class="sankey-legend__item"><span class="risk-dot risk-dot--none"></span>Non évalué</span>
            </div>
            <div class="sankey-legend__hint">
                Survolez un nœud pour voir ses flux. Survolez un flux pour voir le détail.
            </div>
        </div>
    </div>
</div>

<style>
    .sankey-surface .surface__head h3 { font-size: 15px; }
    .sankey-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
    .sankey-canvas-wrap {
        position: relative; height: 520px;
        border: 1px solid var(--hairline); border-radius: var(--r-sm);
        background:
            radial-gradient(ellipse at 50% 40%, color-mix(in oklab, var(--accent) 4%, transparent), transparent 60%),
            var(--bg);
        overflow: hidden;
    }
    .sankey-canvas-wrap svg { width: 100%; height: 100%; display: block; }

    .sankey-legend { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; padding-top: 8px; }
    .sankey-legend__group { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .sankey-legend__head { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-dim); }
    .sankey-legend__item { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 0.02em; }
    .sankey-legend__hint { font-size: 11px; color: var(--text-dim); font-style: italic; margin-left: auto; max-width: 360px; line-height: 1.5; }
</style>

<script src="https://cdn.jsdelivr.net/npm/d3@7.9.0/dist/d3.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sankeyDiagram', (sankey) => ({
            sankey,
            _svg: null,
            _tooltip: null,

            init() {
                const startWhenReady = () => {
                    if (typeof d3 === 'undefined' || typeof d3.sankey === 'undefined') {
                        setTimeout(startWhenReady, 100);
                        return;
                    }
                    this.render();
                };
                startWhenReady();
            },

            riskColors: {
                INACCEPTABLE: '#9B2933',
                HAUT_RISQUE: '#B5532A',
                RISQUE_LIMITE: '#A07626',
                RISQUE_MINIMAL: '#3D6E54',
                NON_EVAL: '#475061',
            },

            layerColors: {
                0: '#2A3148',
                1: '#3F4863',
                2: '#5B6383',
            },

            render() {
                const wrap = this.$refs.wrap;
                const svg = d3.select('#chartSankey');
                const width = Math.max(wrap.clientWidth, 600);
                const height = Math.max(wrap.clientHeight, 480);

                svg.attr('viewBox', `0 0 ${width} ${height}`);

                const data = this.sankey;
                const self = this;

                const generator = d3.sankey()
                    .nodeId(d => d.id)
                    .nodeWidth(16)
                    .nodePadding(12)
                    .nodeAlign(d3.sankeyLeft)
                    .extent([[60, 24], [width - 60, height - 24]]);

                const { nodes, links } = generator({
                    nodes: data.nodes.map(d => ({ ...d })),
                    links: data.links.map(d => ({ ...d })),
                });

                // Defs for gradients
                const defs = svg.select('defs').empty()
                    ? svg.append('defs')
                    : svg.select('defs');

                // Tooltip
                const tooltip = svg.append('g')
                    .attr('class', 'sankey-tooltip')
                    .style('display', 'none');

                const tooltipBg = tooltip.append('rect')
                    .attr('fill', 'var(--surface-2)')
                    .attr('rx', 4)
                    .attr('stroke', 'var(--hairline-strong)')
                    .attr('stroke-width', 1);

                const tooltipText = tooltip.append('text')
                    .attr('fill', 'var(--text)')
                    .attr('font-family', 'Geist Mono, monospace')
                    .attr('font-size', '11px')
                    .attr('dy', 16)
                    .attr('dx', 10);

                // Links
                const linkGroup = svg.append('g')
                    .attr('class', 'sankey-links')
                    .selectAll('path')
                    .data(links)
                    .join('path')
                    .attr('d', d3.sankeyLinkHorizontal())
                    .attr('fill', 'none')
                    .attr('stroke', d => self.riskColors[d.niveau] || '#475061')
                    .attr('stroke-opacity', 0.4)
                    .attr('stroke-width', d => Math.max(d.width, 1))
                    .on('mouseenter', function (event, d) {
                        d3.select(this).attr('stroke-opacity', 0.85);
                        // highlight connected nodes
                        svg.selectAll('.sankey-node-rect')
                            .attr('stroke-opacity', n => (n.id === d.source.id || n.id === d.target.id) ? 1 : 0.15);
                        // Show tooltip
                        const label = `${d.source.name} → ${d.target.name} · ${d.value} usage${d.value > 1 ? 's' : ''}`;
                        tooltip.style('display', null);
                        tooltipText.text(label);
                        const bbox = tooltipText.node().getBBox();
                        tooltipBg.attr('width', bbox.width + 20).attr('height', bbox.height + 12);
                        const pt = d3.pointer(event, svg.node());
                        tooltip.attr('transform', `translate(${pt[0] + 12}, ${pt[1] - 10})`);
                    })
                    .on('mousemove', function (event) {
                        const pt = d3.pointer(event, svg.node());
                        tooltip.attr('transform', `translate(${pt[0] + 12}, ${pt[1] - 10})`);
                    })
                    .on('mouseleave', function () {
                        d3.select(this).attr('stroke-opacity', 0.4);
                        svg.selectAll('.sankey-node-rect').attr('stroke-opacity', 0);
                        tooltip.style('display', 'none');
                    })
                    .transition()
                    .delay((d, i) => d.source.layer * 200 + i * 8)
                    .duration(400)
                    .attr('stroke-opacity', 0.5);

                // Nodes
                const nodeGroup = svg.append('g')
                    .attr('class', 'sankey-nodes')
                    .selectAll('g')
                    .data(nodes)
                    .join('g')
                    .attr('transform', d => `translate(${d.x0}, ${d.y0})`)
                    .style('cursor', 'pointer')
                    .on('mouseenter', function (event, d) {
                        const connected = new Set();
                        links.forEach(l => {
                            if (l.source.id === d.id) connected.add(l.target.id);
                            if (l.target.id === d.id) connected.add(l.source.id);
                        });
                        connected.add(d.id);

                        // Dim non-connected links
                        svg.selectAll('.sankey-links path')
                            .attr('stroke-opacity', l => (l.source.id === d.id || l.target.id === d.id) ? 0.85 : 0.08);
                        // Dim non-connected nodes
                        svg.selectAll('.sankey-node-rect')
                            .attr('opacity', n => connected.has(n.id) ? 1 : 0.2);
                        svg.selectAll('.sankey-node-label')
                            .attr('opacity', n => connected.has(n.id) ? 1 : 0.2);
                    })
                    .on('mouseleave', function () {
                        svg.selectAll('.sankey-links path').attr('stroke-opacity', 0.5);
                        svg.selectAll('.sankey-node-rect').attr('opacity', 1);
                        svg.selectAll('.sankey-node-label').attr('opacity', 1);
                    });

                nodeGroup.append('rect')
                    .attr('class', 'sankey-node-rect')
                    .attr('width', d => Math.max(d.x1 - d.x0, 1))
                    .attr('height', d => Math.max(d.y1 - d.y0, 1))
                    .attr('fill', d => {
                        if (d.label === 'niveau') return self.riskColors[d.code] || '#475061';
                        return self.layerColors[d.layer] || '#2A3148';
                    })
                    .attr('rx', 2)
                    .attr('stroke', 'transparent')
                    .attr('stroke-width', 1.5)
                    .attr('stroke-opacity', 0)
                    .attr('opacity', 0)
                    .transition()
                    .delay((d) => d.layer * 200 + 100)
                    .duration(300)
                    .attr('opacity', 1);

                nodeGroup.append('text')
                    .attr('class', 'sankey-node-label')
                    .attr('x', d => (d.x0 < width / 2) ? 8 : -8)
                    .attr('y', d => (d.y1 - d.y0) / 2)
                    .attr('dy', '0.35em')
                    .attr('text-anchor', d => (d.x0 < width / 2) ? 'start' : 'end')
                    .attr('fill', 'var(--text)')
                    .attr('font-family', 'Geist, system-ui, sans-serif')
                    .attr('font-size', '12px')
                    .attr('font-weight', '500')
                    .attr('opacity', 0)
                    .text(d => d.name + ' (' + (d.sourceLinks?.reduce((a, l) => a + l.value, 0) || d.targetLinks?.reduce((a, l) => a + l.value, 0) || 0) + ')')
                    .transition()
                    .delay((d) => d.layer * 200 + 200)
                    .duration(300)
                    .attr('opacity', 1);

                this._svg = svg;
            },
        }));
    });
</script>
