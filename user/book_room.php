<?php
session_start();
require_once('../includes/config.php');

// Verify user access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

// Fetch available rooms
$stmt = $pdo->prepare("
    SELECT * FROM rooms 
    WHERE status = 'available'
");
$stmt->execute();
$rooms = $stmt->fetchAll();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    $stmt = $pdo->prepare("
        INSERT INTO bookings (guest_id, room_id, check_in_date, check_out_date)
        VALUES (?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out])) {
        header('Location: user_dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجزکردنی ژوور</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf');
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 30px;
            color: #1e88e5;
        }
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .room-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .room-type {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 10px;
        }
        .room-price {
            color: #4caf50;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .booking-form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>حجزکردنی ژوور</h1>
        
        <div class="room-grid">
            <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <div class="room-type"><?= htmlspecialchars($room['type']) ?></div>
                <div class="room-price">$<?= htmlspecialchars($room['price']) ?></div>
                
                <form class="booking-form" method="POST">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    
                    <div class="form-group">
                        <label>بەرواری هاتن</label>
                        <input type="date" name="check_in" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>بەرواری ڕۆیشتن</label>
                        <input type="date" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    
                    <button type="submit" class="submit-btn">حجزکردن</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        
        <a href="user_dashboard.php" class="back-btn">گەڕانەوە بۆ داشبۆرد</a>
    </div>
</body>
</html>
