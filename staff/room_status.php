<?php
session_start();
require_once('../includes/config.php');

// Fetch all rooms with their status
$stmt = $pdo->prepare("SELECT * FROM rooms ORDER BY room_number");
$stmt->execute();
$rooms = $stmt->fetchAll();

// Handle status updates if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id']) && isset($_POST['status'])) {
    $updateStmt = $pdo->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $updateStmt->execute([$_POST['status'], $_POST['room_id']]);
    header("Location: room_status.php?success=updated");
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دۆخی ژوورەکان</title>
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
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
        }
        
        .btn-secondary {
            background: white;
            color: #1D3557;
            border: 1px solid #1D3557;
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
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .room-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            overflow: hidden;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(29, 53, 87, 0.15);
        }
        
        .room-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .room-card:hover .room-image {
            transform: scale(1.03);
        }
        
        .room-number {
            font-size: 22px;
            font-weight: bold;
            color: #1D3557;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .room-number i {
            color: #457B9D;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .status-available {
            background: rgba(67, 160, 71, 0.1);
            color: #43A047;
            border: 1px solid rgba(67, 160, 71, 0.3);
        }
        
        .status-occupied {
            background: rgba(229, 57, 53, 0.1);
            color: #E53935;
            border: 1px solid rgba(229, 57, 53, 0.3);
        }
        
        .status-maintenance {
            background: rgba(255, 152, 0, 0.1);
            color: #FF9800;
            border: 1px solid rgba(255, 152, 0, 0.3);
        }
        
        .status-cleaning {
            background: rgba(3, 155, 229, 0.1);
            color: #039BE5;
            border: 1px solid rgba(3, 155, 229, 0.3);
        }
        
        .status-form {
            margin-top: 15px;
        }
        
        .status-select {
            width: 100%;
            padding: 12px 20px;
            border-radius: 50px;
            border: 1px solid #E0E0E0;
            background: rgba(240, 240, 240, 0.8);
            font-size: 15px;
            margin-bottom: 15px;
            color: #333;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .status-select:focus {
            outline: none;
            border-color: #457B9D;
            box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.2);
        }
        
        .update-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .update-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .update-btn:hover::before {
            left: 100%;
        }
        
        .update-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(29, 53, 87, 0.2);
        }
        
        .update-btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 8px rgba(29, 53, 87, 0.1);
        }
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 15px 20px;
            }
            
            .rooms-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
            
            .rooms-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .room-image {
                height: 150px;
            }
        }
        
        /* Animation for room cards */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated-card {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Hover effect for buttons */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-door-open"></i> دۆخی ژوورەکان</h1>
            <div class="btn-group">
                <a href="staff_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php if ($_GET['success'] === 'updated'): ?>
                    <i class="fas fa-check-circle"></i> دۆخی ژوور بە سەرکەوتوویی نوێکرایەوە
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="rooms-grid">
            <?php foreach ($rooms as $index => $room): ?>
                <div class="room-card animated-card" style="animation-delay: <?php echo $index * 0.05; ?>s">
                    <img src="../<?php echo $room['image'] ? htmlspecialchars($room['image']) : 'uploads/rooms/default.jpg'; ?>" 
                         class="room-image" alt="Room Image">
                    <div class="room-number">
                        <i class="fas fa-bed"></i> ژووری <?php echo htmlspecialchars($room['room_number']); ?>
                    </div>
                    <div class="status-badge status-<?php echo htmlspecialchars($room['status']); ?>">
                        <?php 
                        $statusText = '';
                        switch($room['status']) {
                            case 'available':
                                $statusText = 'بەردەست';
                                break;
                            case 'occupied':
                                $statusText = 'داگیرکراو';
                                break;
                            case 'maintenance':
                                $statusText = 'چاککردنەوە';
                                break;
                            case 'cleaning':
                                $statusText = 'خاوێنکردنەوە';
                                break;
                            default:
                                $statusText = $room['status'];
                        }
                        echo $statusText;
                        ?>
                    </div>
                    <form class="status-form" method="POST">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <select name="status" class="status-select">
                            <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>بەردەست</option>
                            <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>داگیرکراو</option>
                            <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>چاککردنەوە</option>
                            <option value="cleaning" <?php echo $room['status'] === 'cleaning' ? 'selected' : ''; ?>>خاوێنکردنەوە</option>
                        </select>
                        <button type="submit" class="update-btn">
                            <i class="fas fa-sync-alt"></i> نوێکردنەوە
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Add animation to room cards when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll('.room-card');
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                setTimeout(() => {
                    card.classList.add('animated-card');
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>