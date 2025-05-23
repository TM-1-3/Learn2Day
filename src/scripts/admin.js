document.addEventListener('DOMContentLoaded', function() {
    const userChartElem = document.getElementById('userDistributionChart');
    if (userChartElem) {
        const userCtx = userChartElem.getContext('2d');
        const totalTutors = window.totalTutors || 0;
        const totalStudents = window.totalStudents || 0;
        const userChart = new Chart(userCtx, {
            type: 'pie',
            data: {
                labels: ['Tutors', 'Students'],
                datasets: [{
                    data: [totalTutors, totalStudents],
                    backgroundColor: ['#03254e', '#32533D'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.raw / total) * 100);
                                label += context.raw + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${value}\n(${percentage}%)`;
                        },
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }
});