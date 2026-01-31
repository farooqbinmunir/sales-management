document.addEventListener('DOMContentLoaded', function() {
    // Monthly Sales Chart
    const monthlyCtx = document.getElementById('fbmMonthlySalesChart');
	if(monthlyCtx){
		monthlyCtx.getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: fbmChartData.monthlySales.map(item => item.month),
                datasets: [{
                    label: 'Sales (PKR)',
                    data: fbmChartData.monthlySales.map(item => item.total_sales),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Product Performance Chart
    const productCtx = document.getElementById('fbmProductPerformanceChart')
    if(productCtx){
        productCtx.getContext('2d');
        new Chart(productCtx, {
            type: 'doughnut',
            data: {
                labels: fbmChartData.productPerformance.map(item => item.product),
                datasets: [{
                    data: fbmChartData.productPerformance.map(item => item.sales),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
});