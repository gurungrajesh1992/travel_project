import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.chart);
        new Chart(canvas, config);
    });
});
