import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    const nutritionChartEl = document.getElementById('nutritionChart');
    if (!nutritionChartEl) return;

    fetch('/ahli-gizi/dashboard/chart-data')
        .then(response => response.json())
        .then(data => {
            const ctx = nutritionChartEl.getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Protein', 'Karbohidrat', 'Lemak'],
                    datasets: [{
                        label: 'Total Nutrisi',
                        data: [data.protein, data.karbohidrat, data.lemak],
                        backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return `${context.label}: ${value.toFixed(2)} g`;
                                }
                            }
                        }
                    }
                }
            });
        });
});