<?php
session_start();
require_once('../includes/config.php');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Delete room if requested
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: manage_rooms.php?deleted=1');
    exit();
}

// Fetch all rooms
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY room_number");
$rooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی ژوورەکان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            background: #f4f6f9;
            min-height: 100vh;
            padding: 20px;
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
        }
        .add-btn {
            background: #c8a165;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .add-btn:hover {
            background: #b38d4d;
            transform: translateY(-2px);
        }
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .room-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .room-details {
            padding: 20px;
        }
        .room-number {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .room-info {
            margin-bottom: 5px;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            margin-top: 10px;
        }
        .available { background: #e8f5e9; color: #2e7d32; }
        .occupied { background: #fbe9e7; color: #c62828; }
        .maintenance { background: #fff3e0; color: #ef6c00; }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .edit-btn, .delete-btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            flex: 1;
            text-align: center;
            transition: all 0.3s;
        }
        .edit-btn {
            background: #2196f3;
            color: white;
        }
        .delete-btn {
            background: #f44336;
            color: white;
        }
        .edit-btn:hover { background: #1976d2; }
        .delete-btn:hover { background: #d32f2f; }
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
    </style>
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
        
        .page-header {
            padding: 25px 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .page-header h1 {
            color: #1D3557;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-header h1 i {
            color: #457B9D;
            font-size: 32px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
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
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn:active {
            transform: translateY(1px);
        }
        
        .back-btn {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            margin-bottom: 20px;
        }
        
        .add-btn {
            background: linear-gradient(135deg, #43A047, #66BB6A);
            color: white;
        }
        
        .notification {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.5s ease-out forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .success-message {
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
            border-right: 5px solid #43A047;
            color: #2E7D32;
        }
        
        .error-message {
            background: linear-gradient(135deg, #FFEBEE, #FFCDD2);
            border-right: 5px solid #E53935;
            color: #C62828;
        }
        
        .notification i {
            font-size: 24px;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .room-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }
        
        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(29, 53, 87, 0.15);
        }
        
        .room-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .room-card:hover .room-image {
            transform: scale(1.05);
        }
        
        .room-details {
            padding: 25px;
            position: relative;
        }
        
        .room-number {
            font-size: 22px;
            font-weight: bold;
            color: #1D3557;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .room-number i {
            color: #457B9D;
        }
        
        .room-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #455A64;
            font-size: 16px;
        }
        
        .room-info-label {
            color: #1D3557;
            font-weight: bold;
        }
        
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
        }
        
        .available { 
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9); 
            color: #2E7D32; 
        }
        
        .occupied { 
            background: linear-gradient(135deg, #FFEBEE, #FFCDD2); 
            color: #C62828; 
        }
        
        .maintenance { 
            background: linear-gradient(135deg, #FFF3E0, #FFE0B2); 
            color: #EF6C00; 
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .edit-btn, .delete-btn {
            padding: 12px 20px;
            border-radius: 15px;
            text-decoration: none;
            flex: 1;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
        }
        
        .edit-btn {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
        }
        
        .delete-btn {
            background: linear-gradient(135deg, #E53935, #EF5350);
            color: white;
        }
        
        .edit-btn:hover, .delete-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        /* Animation for cards */
        .rooms-grid .room-card:nth-child(1) { animation-delay: 0.1s; }
        .rooms-grid .room-card:nth-child(2) { animation-delay: 0.2s; }
        .rooms-grid .room-card:nth-child(3) { animation-delay: 0.3s; }
        .rooms-grid .room-card:nth-child(4) { animation-delay: 0.4s; }
        .rooms-grid .room-card:nth-child(5) { animation-delay: 0.5s; }
        .rooms-grid .room-card:nth-child(6) { animation-delay: 0.6s; }

        /* Responsive design */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .page-header h1 {
                justify-content: center;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .room-card {
                max-width: 450px;
                margin: 0 auto;
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            margin: 30px auto;
            max-width: 600px;
        }
        
        .empty-icon {
            font-size: 80px;
            color: #B0BEC5;
            margin-bottom: 20px;
        }
        
        .empty-text {
            color: #607D8B;
            font-size: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
    <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە
        </a>
        <div class="header">
            <h2>بەڕێوەبردنی ژوورەکان</h2>
            <a href="add_room.php" class="add-btn">
                <i class="fas fa-plus"></i> زیادکردنی ژووری نوێ
            </a>
        </div>
          <?php if (isset($_GET['success'])): ?>
              <div class="success-message">ژوور بە سەرکەوتوویی نوێکرایەوە</div>
          <?php endif; ?>

          <?php if (isset($_GET['deleted'])): ?>
              <div class="success-message">ژوور بە سەرکەوتوویی سڕایەوە</div>
          <?php endif; ?>

          <?php if (isset($_GET['error']) && $_GET['error'] == 'has_bookings'): ?>
              <div class="error-message">ناتوانرێت ئەم ژوورە بسڕدرێتەوە چونکە حجزی هەیە</div>
          <?php endif; ?>
        <div class="rooms-grid">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <img src="../<?php echo $room['image'] ? htmlspecialchars($room['image']) : 'uploads/rooms/default.jpg'; ?>" 
                         class="room-image" alt="Room Image">
                    <div class="room-details">
                        <div class="room-number">ژووری <?php echo htmlspecialchars($room['room_number']); ?></div>
                        <div class="room-info">
                            <strong>جۆر:</strong> 
                            <?php 
                                $types = [
                                    'single' => 'تاک',
                                    'double' => 'دووانە',
                                    'suite' => 'جناح'
                                ];
                                echo $types[$room['room_type']] ?? $room['room_type'];
                            ?>
                        </div>
                        <div class="room-info">
                            <strong>نرخ:</strong> $<?php echo htmlspecialchars($room['price']); ?>
                        </div>
                        <div class="status <?php echo $room['status']; ?>">
                            <?php 
                                $statuses = [
                                    'available' => 'بەردەستە',
                                    'occupied' => 'داگیرکراوە',
                                    'maintenance' => 'چاککردنەوە'
                                ];
                                echo $statuses[$room['status']] ?? $room['status'];
                            ?>
                        </div>
                        <div class="actions">
                            <a href="edit_room.php?id=<?php echo $room['id']; ?>" class="edit-btn">
                                <i class="fas fa-edit"></i> دەستکاری
                            </a>
                            <a href="?delete=<?php echo $room['id']; ?>" class="delete-btn" 
                               onclick="return confirm('دڵنیای لە سڕینەوەی ئەم ژوورە؟');">
                                <i class="fas fa-trash"></i> سڕینەوە
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
