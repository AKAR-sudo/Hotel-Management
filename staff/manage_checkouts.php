<?php
session_start();
require_once('../includes/config.php');

// Fetch all checked-in bookings with related information
$stmt = $pdo->prepare("
    SELECT 
        b.*, 
        h.hotel_name,
        r.room_type,
        r.price,
        u.username as guest_name,
        u.phone
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.id
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.checked_in = 1
    ORDER BY b.check_out_date ASC
");
$stmt->execute();
$bookings = $stmt->fetchAll();

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Update booking status and checked_in status
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'completed', checked_in = 0 WHERE id = ?");
    $stmt->execute([$booking_id]);
    
    // Update room status to available
    $stmt = $pdo->prepare("
        UPDATE rooms r 
        JOIN bookings b ON r.id = b.room_id 
        SET r.status = 'available' 
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    
    header('Location: manage_checkouts.php?success=checkout');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی چێک ئاوت</title>
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
        
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .checkouts-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .checkouts-table th,
        .checkouts-table td {
            padding: 18px 20px;
            text-align: right;
        }
        
        .checkouts-table th {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        .checkouts-table th:first-child {
            border-top-right-radius: 15px;
        }
        
        .checkouts-table th:last-child {
            border-top-left-radius: 15px;
        }
        
        .checkouts-table tr {
            border-bottom: 1px solid #F0F0F0;
            transition: all 0.3s ease;
        }
        
        .checkouts-table tr:last-child {
            border-bottom: none;
        }
        
        .checkouts-table tr:hover {
            background: rgba(240, 248, 255, 0.7);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .days-remaining {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        
        .days-warning {
            background: rgba(239, 108, 0, 0.1);
            color: #EF6C00;
            border: 1px solid rgba(239, 108, 0, 0.3);
        }
        
        .days-normal {
            background: rgba(46, 125, 50, 0.1);
            color: #2E7D32;
            border: 1px solid rgba(46, 125, 50, 0.3);
        }
        
        .checkout-btn {
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
            background: linear-gradient(135deg, #E53935, #EF5350);
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Animation for table rows */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated-row {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .checkouts-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
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
            
            .checkouts-table th, 
            .checkouts-table td {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-sign-out-alt"></i> بەڕێوەبردنی چێک ئاوت</h1>
            <div class="btn-group">
                <a href="staff_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php if ($_GET['success'] === 'checkout'): ?>
                    <i class="fas fa-check-circle"></i> میوان بە سەرکەوتوویی چێک ئاوت کرا
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table class="checkouts-table">
                <thead>
                    <tr>
                        <th>ناوی میوان</th>
                        <th>ژمارەی تەلەفۆن</th>
                        <th>هوتێل</th>
                        <th>ژووری</th>
                        <th>نرخ</th>
                        <th>بەرواری چێک ئین</th>
                        <th>بەرواری چێک ئاوت</th>
                        <th>ڕۆژی ماوە</th>
                        <th>کردار</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $index => $booking): 
                        $days_remaining = (strtotime($booking['check_out_date']) - time()) / (60 * 60 * 24);
                        $days_class = $days_remaining <= 1 ? 'days-warning' : 'days-normal';
                    ?>
                        <tr class="animated-row" style="animation-delay: <?php echo $index * 0.05; ?>s">
                            <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                            <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                            <td>$<?php echo number_format($booking['price']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></td>
                            <td>
                                <span class="days-remaining <?php echo $days_class; ?>">
                                    <?php echo max(0, ceil($days_remaining)); ?> ڕۆژ
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('دڵنیای لە چێک ئاوت کردن؟');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="checkout-btn">
                                        <i class="fas fa-sign-out-alt"></i> چێک ئاوت
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($bookings) === 0): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px;">
                                <i class="fas fa-info-circle" style="font-size: 24px; color: #457B9D; margin-bottom: 10px;"></i>
                                <p>هیچ میوانێکی چێک ئین کراو نییە</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add animation to table rows when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const rows = document.querySelectorAll('.checkouts-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = "0";
                setTimeout(() => {
                    row.classList.add('animated-row');
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>