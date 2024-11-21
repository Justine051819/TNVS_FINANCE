<?php
// Fetch monthly sales data from the database
// Example static data for testing
$salesData = [50000, 60000, 55000, 70000, 65000, 80000, 75000, 85000, 70000, 95000, 90000, 100000];

// Convert PHP array to JSON format for JavaScript
$salesDataJson = json_encode($salesData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Sales Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3faff;
        }

        .container {
            max-width: 100%;
            margin: 20px 0 0 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Hide the sections */
        .hidden {
            display: none;
        }

        .chart-container {
            width: 100%;
            margin: 20px auto;
        }

        canvas {
            max-height: 400px;
        }

        h1{
            font-weight: bold;
            font-size: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Monthly Sales Report</h1>
        <div class="chart-container">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>



    <script>
        // Monthly sales data from PHP
        const salesData = <?php echo $salesDataJson; ?>;

        // Month labels
        const labels = [
            'January', 'February', 'March', 'April', 'May',
            'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Chart configuration
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales (₱)',
                    data: salesData,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: '#388e3c',
                    pointBorderColor: '#4caf50',
                    fill: true,
                    tension: 0.4, // Smooth curve
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `₱${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
