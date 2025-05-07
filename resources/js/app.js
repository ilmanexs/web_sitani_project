import './bootstrap';
import "flyonui/flyonui"
import "lodash"
import ApexCharts from 'apexcharts';
import { buildChart } from 'flyonui/src/js/helpers/apexcharts/index.js';
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

window.ApexCharts = ApexCharts;
window.buildChart = buildChart;
window.Fancybox = Fancybox;
