<?php
session_start();
require_once('config.php');

function generateReceipt($booking_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT b.*, r.room_number, r.price, u.username
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html dir="rtl" lang="ku">
    <head>
        <style>
            @font-face {
                font-family: '20_Sarchia_Banoka_1';
                src: url('../fonts/20_Sarchia_Banoka_1.ttf');
            }
            
            * {
                font-family: '20_Sarchia_Banoka_1';
            }
            
            .receipt {
                width: 80mm;
                padding: 10mm;
                margin: auto;
                border: 1px solid #000;
            }
        </style>
    </head>
    <body>
        <div class="receipt">
            <h2>پسوولەی حیجزکردن</h2>
            <p>ژمارەی ژوور: <?php echo $booking['room_number']; ?></p>
            <p>بەکارهێنەر: <?php echo $booking['username']; ?></p>
            <p>بەرواری هاتن: <?php echo $booking['check_in']; ?></p>
            <p>بەرواری دەرچوون: <?php echo $booking['check_out']; ?></p>
            <p>کۆی گشتی: <?php echo $booking['total_price']; ?></p>
        </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();
    return $html;
}
?>
