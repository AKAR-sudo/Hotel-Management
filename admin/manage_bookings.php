<?php
session_start();
require_once('../includes/config.php');

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch all bookings with related information
$stmt = $pdo->prepare("
    SELECT b.*, h.hotel_name, u.username, u.phone, r.room_type 
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.id 
    JOIN users u ON b.user_id = u.id 
    JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.created_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll();

// Handle booking status updates
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$status, $booking_id]);
    
    header('Location: manage_bookings.php?success=updated');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی حیجزەکان</title>
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
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(29, 53, 87, 0.15);
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(240, 240, 240, 0.8);
        }
        
        .booking-header h3 {
            font-size: 18px;
            color: #1D3557;
            font-weight: bold;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: rgba(255, 152, 0, 0.1);
            color: #FF9800;
            border: 1px solid rgba(255, 152, 0, 0.3);
        }
        
        .status-confirmed {
            background: rgba(67, 160, 71, 0.1);
            color: #43A047;
            border: 1px solid rgba(67, 160, 71, 0.3);
        }
        
        .status-cancelled {
            background: rgba(229, 57, 53, 0.1);
            color: #E53935;
            border: 1px solid rgba(229, 57, 53, 0.3);
        }
        
        .status-completed {
            background: rgba(3, 169, 244, 0.1);
            color: #03A9F4;
            border: 1px solid rgba(3, 169, 244, 0.3);
        }
        
        .info-group {
            margin-bottom: 15px;
            padding: 12px;
            background: rgba(240, 248, 255, 0.5);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .info-group:hover {
            background: rgba(240, 248, 255, 0.8);
            transform: translateX(-5px);
        }
        
        .info-label {
            color: #457B9D;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 16px;
            color: #1D3557;
            font-weight: bold;
        }
        
        select {
            width: 100%;
            padding: 12px 20px;
            border-radius: 50px;
            border: 2px solid #E0E0E0;
            background: white;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%231D3557" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
        }
        
        select:focus {
            outline: none;
            border-color: #1D3557;
            box-shadow: 0 0 0 3px rgba(29, 53, 87, 0.2);
        }
        
        select:hover {
            border-color: #457B9D;
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
        
        /* Animation for booking cards */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
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
            <h1><i class="fas fa-calendar-check"></i> بەڕێوەبردنی حیجزەکان</h1>
            <div class="btn-group">
                <a href="admin_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> حیجز بە سەرکەوتوویی نوێکرایەوە
            </div>
        <?php endif; ?>

        <div class="bookings-grid">
            <?php foreach ($bookings as $index => $booking): ?>
                <div class="booking-card animated-card" style="animation-delay: <?php echo $index * 0.05; ?>s">
                    <div class="booking-header">
                        <h3><?php echo htmlspecialchars($booking['hotel_name']); ?></h3>
                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                            <?php 
                                $status_text = '';
                                switch($booking['status']) {
                                    case 'pending':
                                        $status_text = 'چاوەڕوان';
                                        break;
                                    case 'confirmed':
                                        $status_text = 'پەسەندکراو';
                                        break;
                                    case 'cancelled':
                                        $status_text = 'هەڵوەشاوە';
                                        break;
                                    case 'completed':
                                        $status_text = 'تەواوبوو';
                                        break;
                                    default:
                                        $status_text = $booking['status'];
                                }
                                echo htmlspecialchars($status_text); 
                            ?>
                        </span>
                    </div>

                    <div class="info-group">
                        <div class="info-label">ناوی میوان</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['username']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">ژمارەی مۆبایل</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['phone']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">جۆری ژوور</div>
                        <div class="info-value"><?php echo htmlspecialchars($booking['room_type']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">بەرواری چێک ئین</div>
                        <div class="info-value"><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">بەرواری چێک ئاوت</div>
                        <div class="info-value"><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>چاوەڕوان</option>
                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>پەسەندکراو</option>
                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>هەڵوەشاوە</option>
                            <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>تەواوبوو</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Add animation to booking cards when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll('.booking-card');
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