<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom Scripts -->
<script>
    document.addEventListener('alpine:init', () => {
        // Initialize any Alpine.js data or components here
        Alpine.data('notifications', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            }
        }));
        
        Alpine.data('dropdown', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            }
        }));
    });
    
    // Function to initialize charts
    function initCharts() {
        // Only initialize if chart elements exist
        const chartElements = document.querySelectorAll('.chart-container');
        if (chartElements.length === 0) return;
        
        // Sample chart initialization - can be customized per chart
        chartElements.forEach(container => {
            const ctx = container.querySelector('canvas').getContext('2d');
            const chartType = container.dataset.chartType || 'line';
            const chartId = container.dataset.chartId;
            
            // Default configuration - should be customized based on data attributes
            const config = {
                type: chartType,
                data: {
                    labels: JSON.parse(container.dataset.labels || '[]'),
                    datasets: JSON.parse(container.dataset.datasets || '[]')
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                }
            };
            
            // Create the chart
            new Chart(ctx, config);
        });
    }
    
    // Initialize charts when the DOM is loaded
    document.addEventListener('DOMContentLoaded', initCharts);
</script>
