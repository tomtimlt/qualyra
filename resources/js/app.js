import './bootstrap';
import './brain';
import './custom-scrollbar';
import './heatmap';

import * as d3 from 'd3';
import { sankey, sankeyLeft, sankeyLinkHorizontal } from 'd3-sankey';

const d3Extended = { ...d3, sankey, sankeyLeft, sankeyLinkHorizontal };

import Chart from 'chart.js/auto';
import { MatrixController, MatrixElement } from 'chartjs-chart-matrix';
Chart.register(MatrixController, MatrixElement);

import Alpine from 'alpinejs';

window.d3 = d3Extended;
window.Chart = Chart;
window.Alpine = Alpine;

Alpine.start();
