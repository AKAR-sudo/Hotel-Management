<?php
session_start();
require_once('../includes/config.php');

// Fetch user's bookings
$stmt = $pdo->prepare("SELECT b.*, h.hotel_name, h.location, r.room_type, r.price 
                       FROM bookings b 
                       JOIN hotels h ON b.hotel_id = h.id 
                       JOIN rooms r ON b.room_id = r.id 
                       WHERE b.user_id = ? 
                       ORDER BY b.check_in_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حیجزەکانم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background: linear-gradient(135deg, #F8F9FA, #E9ECEF);
            padding: 25px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .header h1 {
            color: #1D3557;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1 i {
            color: #457B9D;
            font-size: 28px;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-back {
            background: white;
            color: #1D3557;
            border: 1px solid #E0E0E0;
        }
        
        .success-message {
            background: linear-gradient(135deg, #43A047, #66BB6A);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(67, 160, 71, 0.2);
            display: flex;
            align-items: center;
            border-right: 5px solid #2E7D32;
        }
        
        .success-message i {
            font-size: 24px;
            margin-left: 15px;
        }
        
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(29, 53, 87, 0.15);
        }
        
        .booking-header {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .booking-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIyMCIgaGVpZ2h0PSI0MCIgZmlsbD0icmdiYSgyNTUsIDI1NSwgMjU1LCAwLjAzKSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
            opacity: 0.2;
        }
        
        .hotel-name {
            font-size: 1.4em;
            font-weight: bold;
            margin-bottom: 8px;
            position: relative;
        }
        
        .booking-content {
            padding: 25px;
        }
        
        .booking-details {
            display: grid;
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-item i {
            color: #457B9D;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .detail-value {
            font-weight: bold;
            color: #1D3557;
        }
        
        .detail-label {
            color: #6c757d;
            font-size: 0.9em;
            display: block;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-pending {
            background: rgba(239, 108, 0, 0.1);
            color: #EF6C00;
            border: 1px solid rgba(239, 108, 0, 0.3);
        }
        
        .status-confirmed {
            background: rgba(46, 125, 50, 0.1);
            color: #2E7D32;
            border: 1px solid rgba(46, 125, 50, 0.3);
        }
        
        .status-cancelled {
            background: rgba(211, 47, 47, 0.1);
            color: #D32F2F;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }
        
        .empty-bookings {
            grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            padding: 50px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .empty-bookings i {
            font-size: 60px;
            color: #457B9D;
            margin-bottom: 20px;
            opacity: 0.7;
        }
        
        .empty-bookings h2 {
            color: #1D3557;
            margin-bottom: 15px;
        }
        
        .empty-bookings p {
            color: #6c757d;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .booking-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .view-btn {
            background: linear-gradient(135deg, #1A237E, #303F9F);
        }
        
        .cancel-btn {
            background: linear-gradient(135deg, #E53935, #EF5350);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .bookings-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .bookings-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media print {
            .btn-back, .action-btn {
                display: none;
            }
            body {
                padding: 0;
                background: white;
            }
            .container {
                max-width: 100%;
            }
            .booking-card {
                box-shadow: none;
                border: 1px solid #ddd;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> حیجزەکانم</h1>
            <div class="btn-group">
                <a href="user_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php if ($_GET['success'] === 'cancel'): ?>
                    <i class="fas fa-check-circle"></i> حیجزەکەت بە سەرکەوتوویی هەڵوەشایەوە
                <?php elseif ($_GET['success'] === 'book'): ?>
                    <i class="fas fa-check-circle"></i> حیجزەکەت بە سەرکەوتوویی تۆمار کرا
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="bookings-grid">
            <?php if (empty($bookings)): ?>
                <div class="empty-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <h2>هیچ حیجزێکت نییە</h2>
                    <p>تا ئێستا هیچ حیجزێکت نەکردووە. بۆ حیجزکردنی ژوورێک یان خزمەتگوزارییەک، تکایە سەردانی بەشی ئوتێلەکان بکە.</p>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $index => $booking): ?>
                    <div class="booking-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="booking-header">
                            <div class="hotel-name"><?php echo htmlspecialchars($booking['hotel_name']); ?></div>
                            <div class="location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($booking['location']); ?>
                            </div>
                        </div>
                        <div class="booking-content">
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-bed"></i>
                                    <div>
                                        <span class="detail-label">جۆری ژوور</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <div>
                                        <span class="detail-label">بەرواری چێک ئین</span>
                                        <span class="detail-value"><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <div>
                                        <span class="detail-label">بەرواری چێک ئاوت</span>
                                        <span class="detail-value"><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <div>
                                        <span class="detail-label">نرخی ژوور</span>
                                        <span class="detail-value">$<?php echo number_format($booking['price']); ?></span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <span class="detail-label">دۆخی حیجز</span>
                                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                            <?php 
                                            $status = strtolower($booking['status']);
                                            $statusText = '';
                                            
                                            switch($status) {
                                                case 'pending':
                                                    $statusText = 'چاوەڕوان';
                                                    break;
                                                case 'confirmed':
                                                    $statusText = 'پەسەندکراو';
                                                    break;
                                                case 'cancelled':
                                                    $statusText = 'هەڵوەشاوەتەوە';
                                                    break;
                                                default:
                                                    $statusText = $booking['status'];
                                            }
                                            
                                            echo $statusText;
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($booking['status'] !== 'cancelled'): ?>
                                <div class="booking-actions">
                                    <a href="view_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn view-btn">
                                        <i class="fas fa-eye"></i> بینینی وردەکاری
                                    </a>
                                    <?php if (strtotime($booking['check_in_date']) > time()): ?>
                                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn cancel-btn" onclick="return confirm('دڵنیای لە هەڵوەشاندنەوەی ئەم حیجزە؟');">
                                            <i class="fas fa-times"></i> هەڵوەشاندنەوە
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add animation to booking cards when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll('.booking-card');
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                setTimeout(() => {
                    card.style.animation = `fadeIn 0.5s ease forwards ${index * 0.1}s`;
                }, 100);
            });
        });
    </script>
</body>
</html>