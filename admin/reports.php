<?php
session_start();
require_once('../includes/config.php');

// Fetch statistics
$stats = [
    'total_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'confirmed_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
    'pending_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
    'cancelled_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status = 'confirmed'")->fetchColumn()
];

// Monthly bookings chart data
$monthly_bookings = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM bookings 
    GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
    ORDER BY month DESC 
    LIMIT 12
")->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>ڕاپۆرتەکان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('../fonts/20_Sarchia_Banoka_1.ttf');
        }
        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #1a237e;
        }
        .stat-value {
            font-size: 1.8em;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .back-btn {
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .report-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .filter-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .export-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە
        </a>

        <h1>ڕاپۆرتەکان</h1>

        <div class="report-filters">
            <select class="filter-select">
                <option value="all">هەموو کات</option>
                <option value="month">ئەم مانگە</option>
                <option value="year">ئەم ساڵ</option>
            </select>
            <a href="export_report.php" class="export-btn">
                <i class="fas fa-download"></i>
                داگرتنی ڕاپۆرت
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar-check stat-icon"></i>
                <div class="stat-value"><?php echo number_format($stats['total_bookings']); ?></div>
                <div class="stat-label">کۆی حیجزەکان</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-value"><?php echo number_format($stats['confirmed_bookings']); ?></div>
                <div class="stat-label">حیجزە پەسەندکراوەکان</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-value"><?php echo number_format($stats['pending_bookings']); ?></div>
                <div class="stat-label">حیجزە چاوەڕوانەکان</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-money-bill-wave stat-icon"></i>
                <div class="stat-value">$<?php echo number_format($stats['total_revenue']); ?></div>
                <div class="stat-label">کۆی داهات</div>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>

    <script>
        // Monthly bookings chart
        const ctx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column(array_reverse($monthly_bookings), 'month')); ?>,
                datasets: [{
                    label: 'حیجزەکان بەپێی مانگ',
                    data: <?php echo json_encode(array_column(array_reverse($monthly_bookings), 'count')); ?>,
                    borderColor: '#1a237e',
                    tension: 0.1
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
    </script>
</body>
</html>
