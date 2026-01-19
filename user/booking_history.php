<?php
session_start();
require_once('../includes/config.php');

// Fetch user's booking history
$stmt = $pdo->prepare("
    SELECT b.*, r.room_number, r.price, r.type
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مێژووی حیجزکردن</title>
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
            background: #f5f5f5;
            padding: 20px;
        }
        .history-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .booking-list {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            color: white;
        }
        .status-completed { background: #4CAF50; }
        .status-active { background: #2196F3; }
        .status-cancelled { background: #f44336; }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .detail-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-primary { background: #2196F3; color: white; }
        .btn-danger { background: #f44336; color: white; }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="history-container">
        <h1>مێژووی حیجزکردن</h1>
        
        <div class="booking-list">
            <?php foreach($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <h3>ژووری ژمارە <?php echo $booking['room_number']; ?></h3>
                        <?php
                        $status_class = '';
                        $status_text = '';
                        if(strtotime($booking['check_out']) < time()) {
                            $status_class = 'status-completed';
                            $status_text = 'تەواوبووە';
                        } elseif(strtotime($booking['check_in']) <= time()) {
                            $status_class = 'status-active';
                            $status_text = 'چالاک';
                        } else {
                            $status_class = 'status-pending';
                            $status_text = 'چاوەڕوان';
                        }
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-item">
                            <strong>بەرواری هاتن:</strong>
                            <div><?php echo date('Y-m-d', strtotime($booking['check_in'])); ?></div>
                        </div>
                        <div class="detail-item">
                            <strong>بەرواری دەرچوون:</strong>
                            <div><?php echo date('Y-m-d', strtotime($booking['check_out'])); ?></div>
                        </div>
                        <div class="detail-item">
                            <strong>نرخی گشتی:</strong>
                            <div><?php echo number_format($booking['total_price']); ?> دینار</div>
                        </div>
                        <div class="detail-item">
                            <strong>جۆری ژوور:</strong>
                            <div><?php echo $booking['type']; ?></div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="viewDetails(<?php echo $booking['id']; ?>)">
                            وردەکارییەکان
                        </button>
                        <?php if(strtotime($booking['check_in']) > time()): ?>
                            <button class="btn btn-danger" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                هەڵوەشاندنەوە
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function viewDetails(bookingId) {
            window.location.href = `booking_details.php?id=${bookingId}`;
        }

        function cancelBooking(bookingId) {
            if(confirm('دڵنیای لە هەڵوەشاندنەوەی حیجزەکە؟')) {
                fetch('cancel_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ booking_id: bookingId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('هەڵەیەک ڕوویدا');
                    }
                });
            }
        }
    </script>
</body>
</html>
