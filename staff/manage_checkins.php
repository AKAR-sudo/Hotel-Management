<?php
session_start();
require_once('../includes/config.php');

// Fetch all bookings with related information
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
    ORDER BY b.check_in_date DESC
");

$stmt->execute();
$bookings = $stmt->fetchAll();

// Handle check-in status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $checked_in = $_POST['checked_in'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET checked_in = ? WHERE id = ?");
    $stmt->execute([$checked_in, $booking_id]);
    
    header('Location: manage_checkins.php?success=checkin');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی چێک ئین</title>
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
        
        .checkins-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .checkins-table th,
        .checkins-table td {
            padding: 18px 20px;
            text-align: right;
        }
        
        .checkins-table th {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        .checkins-table th:first-child {
            border-top-right-radius: 15px;
        }
        
        .checkins-table th:last-child {
            border-top-left-radius: 15px;
        }
        
        .checkins-table tr {
            border-bottom: 1px solid #F0F0F0;
            transition: all 0.3s ease;
        }
        
        .checkins-table tr:last-child {
            border-bottom: none;
        }
        
        .checkins-table tr:hover {
            background: rgba(240, 248, 255, 0.7);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-checked {
            background: rgba(46, 125, 50, 0.1);
            color: #2E7D32;
            border: 1px solid rgba(46, 125, 50, 0.3);
        }
        
        .status-pending {
            background: rgba(239, 108, 0, 0.1);
            color: #EF6C00;
            border: 1px solid rgba(239, 108, 0, 0.3);
        }
        
        .checkin-btn {
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
            background: linear-gradient(135deg, #1A237E, #303F9F);
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
        
        .print-btn {
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
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            margin-right: 8px;
        }
        
        .checkin-btn:hover, .checkout-btn:hover, .print-btn:hover {
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
            
            .checkins-table {
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
            
            .checkins-table th, 
            .checkins-table td {
                padding: 12px 15px;
            }
        }
        
        @media print {
            .print-btn,
            .checkin-btn,
            .checkout-btn,
            .btn-back {
                display: none;
            }
            body {
                padding: 0;
                background: white;
            }
            .container {
                max-width: 100%;
            }
            .card {
                box-shadow: none;
                border: none;
            }
            .header {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-check-circle"></i> بەڕێوەبردنی چێک ئین</h1>
            <div class="btn-group">
                <a href="staff_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php if ($_GET['success'] === 'checkin'): ?>
                    <i class="fas fa-check-circle"></i> دۆخی چێک ئین بە سەرکەوتوویی نوێکرایەوە
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table class="checkins-table">
                <thead>
                    <tr>
                        <th>ناوی میوان</th>
                        <th>ژمارەی تەلەفۆن</th>
                        <th>هوتێل</th>
                        <th>ژووری</th>
                        <th>نرخ</th>
                        <th>بەرواری چێک ئین</th>
                        <th>بەرواری چێک ئاوت</th>
                        <th>دۆخی چێک ئین</th>
                        <th>کردار</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $index => $booking): ?>
                        <tr class="animated-row" style="animation-delay: <?php echo $index * 0.05; ?>s" data-booking-id="<?php echo $booking['id']; ?>">
                            <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                            <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                            <td>$<?php echo number_format($booking['price']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $booking['checked_in'] ? 'status-checked' : 'status-pending'; ?>">
                                    <?php echo $booking['checked_in'] ? 'چێک ئین کراوە' : 'چاوەڕوانی چێک ئین'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="print-btn" onclick="printBooking(<?php echo $booking['id']; ?>)">
                                    <i class="fas fa-print"></i> چاپ
                                </button>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('دڵنیای لە گۆڕینی دۆخی چێک ئین؟');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <?php if (!$booking['checked_in']): ?>
                                        <input type="hidden" name="checked_in" value="1">
                                        <button type="submit" class="checkin-btn">
                                            <i class="fas fa-check-circle"></i> چێک ئین
                                        </button>
                                    <?php else: ?>
                                        <input type="hidden" name="checked_in" value="0">
                                        <button type="submit" class="checkout-btn">
                                            <i class="fas fa-sign-out-alt"></i> هەڵوەشاندنەوە
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($bookings) === 0): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px;">
                                <i class="fas fa-info-circle" style="font-size: 24px; color: #457B9D; margin-bottom: 10px;"></i>
                                <p>هیچ داواکارییەکی داواکراو نییە</p>
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
            const rows = document.querySelectorAll('.checkins-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = "0";
                setTimeout(() => {
                    row.classList.add('animated-row');
                }, 100 + (index * 50));
            });
        });

        function printBooking(bookingId) {
            const row = document.querySelector(`[data-booking-id="${bookingId}"]`);
            const printWindow = window.open('', '_blank');
            
            if (!printWindow) {
                alert('پێویستە ڕێگە بدەیت بە کردنەوەی پەنجەرەی نوێ بۆ چاپکردنی داواکارییەکە');
                return;
            }
            
            const cells = row.querySelectorAll('td');
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl" lang="ku">
                <head>
                    <meta charset="UTF-8">
                    <title>چێک ئین</title>
                    <style>
                        @font-face {
                            font-family: '20_Sarchia_Banoka_1';
                            src: url('../fonts/20_Sarchia_Banoka_1.ttf');
                        }
                        * {
                            font-family: '20_Sarchia_Banoka_1';
                        }
                        body {
                            padding: 20px;
                        }
                        .receipt {
                            max-width: 800px;
                            margin: 0 auto;
                            border: 1px solid #ddd;
                            border-radius: 10px;
                            padding: 30px;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 2px solid #eee;
                            padding-bottom: 20px;
                        }
                        .booking-info {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 15px;
                        }
                        .info-item {
                            margin-bottom: 15px;
                        }
                        .info-label {
                            font-weight: bold;
                            margin-bottom: 5px;
                            color: #457B9D;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 14px;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <div class="receipt">
                        <div class="header">
                            <h1>داواکاری چێک ئین</h1>
                            <p>بەرواری چاپ: ${new Date().toLocaleDateString()}</p>
                        </div>
                        
                        <div class="booking-info">
                            <div class="info-item">
                                <div class="info-label">ناوی میوان:</div>
                                <div>${cells[0].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">ژمارەی تەلەفۆن:</div>
                                <div>${cells[1].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">هوتێل:</div>
                                <div>${cells[2].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">ژووری:</div>
                                <div>${cells[3].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">نرخ:</div>
                                <div>${cells[4].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">بەرواری چێک ئین:</div>
                                <div>${cells[5].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">بەرواری چێک ئاوت:</div>
                                <div>${cells[6].textContent}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">دۆخی چێک ئین:</div>
                                <div>${cells[7].textContent}</div>
                            </div>
                        </div>
                        
                        <div class="footer">
                            <p>سپاس بۆ سەردانت. ھیوادارین کات بەسەربردنێکی خۆشت ھەبێت!</p>
                        </div>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        };
                    </script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        }
    </script>
</body>
</html>