document.addEventListener('alpine:init', () => {
    Alpine.data('heatmapFilter', (heatmap) => ({
        heatmap,
        selected: null, // { domain, level } ou null

        levelLabel(lvl) {
            return {
                INACCEPTABLE: 'Inacceptable',
                HAUT_RISQUE: 'Haut risque',
                RISQUE_LIMITE: 'Risque limité',
                RISQUE_MINIMAL: 'Risque minimal',
            }[lvl] ?? lvl;
        },

        levelClass(lvl) {
            return {
                INACCEPTABLE: 'inacc',
                HAUT_RISQUE: 'haut',
                RISQUE_LIMITE: 'lim',
                RISQUE_MINIMAL: 'min',
            }[lvl] ?? '';
        },

        cellColor(dom, lvl) {
            const score = this.heatmap.cells[dom]?.[lvl]?.score ?? 0;
            if (this.heatmap.maxScore === 0 || score === 0) return 'transparent';
            const intensity = score / this.heatmap.maxScore;
            // Dégradé HSL : vert (120°) → rouge (0°), saturation 65%, luminosité 55→30%
            const hue = 120 - intensity * 120;
            const lightness = 55 - intensity * 25;
            return `hsl(${hue}, 65%, ${lightness}%)`;
        },

        cellTooltip(dom, lvl) {
            const cell = this.heatmap.cells[dom]?.[lvl];
            if (!cell || cell.count === 0) return `${dom} – ${this.levelLabel(lvl)} : 0 usage`;
            const recent = cell.recent.length > 0 ? `\nRécents : ${cell.recent.join(', ')}` : '';
            return `${dom} – ${this.levelLabel(lvl)} : ${cell.count} usage(s)${recent}`;
        },

        isSelected(dom, lvl) {
            return this.selected?.domain === dom && this.selected?.level === lvl;
        },

        selectCell(dom, lvl) {
            if (this.isSelected(dom, lvl)) {
                this.selected = null; // 2e clic = désélection
            } else {
                this.selected = { domain: dom, level: lvl };
            }
        },

        filteredUsages() {
            if (!this.selected) return this.heatmap.allUsages;
            return this.heatmap.allUsages.filter(u =>
                u.domain === this.selected.domain && u.niveau === this.selected.level
            );
        },
    }));
});
