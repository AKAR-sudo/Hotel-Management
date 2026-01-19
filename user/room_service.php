<?php
session_start();
require_once('../includes/config.php');

// Fetch active bookings for the user
$stmt = $pdo->prepare("
    SELECT b.*, r.room_number 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? AND b.check_out >= CURRENT_DATE
    ORDER BY b.check_in ASC
");
$stmt->execute([$_SESSION['user_id']]);
$active_bookings = $stmt->fetchAll();

// Fetch service menu items
$menu_items = $pdo->query("SELECT * FROM room_service_menu WHERE available = 1")->fetchAll();

// Handle service request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $menu_item_id = $_POST['menu_item_id'];
    $quantity = $_POST['quantity'];
    
    $stmt = $pdo->prepare("
        INSERT INTO room_service_orders (booking_id, menu_item_id, quantity)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$booking_id, $menu_item_id, $quantity]);
    
    header('Location: room_service.php?success=1');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خزمەتگوزاری ژوور</title>
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
        .service-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .menu-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            animation: fadeIn 0.5s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        .menu-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .order-form {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        select, input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #45a049;
        }
        .success-message {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="service-container">
        <h1>خزمەتگوزاری ژوور</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                داواکارییەکەت بە سەرکەوتوویی نێردرا
            </div>
        <?php endif; ?>

        <?php if (empty($active_bookings)): ?>
            <div class="alert">
                هیچ حیجزێکی چالاکت نییە
            </div>
        <?php else: ?>
            <div class="menu-grid">
                <?php foreach($menu_items as $item): ?>
                    <div class="menu-item">
                        <img src="../images/menu/<?php echo $item['image_url']; ?>" 
                             alt="<?php echo $item['name']; ?>" 
                             class="menu-image">
                        <h3><?php echo $item['name']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                        <div class="price"><?php echo number_format($item['price']); ?> دینار</div>
                        
                        <form method="POST" class="order-form">
                            <select name="booking_id" required>
                                <option value="">ژوورەکەت هەڵبژێرە</option>
                                <?php foreach($active_bookings as $booking): ?>
                                    <option value="<?php echo $booking['id']; ?>">
                                        ژووری <?php echo $booking['room_number']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" min="1" value="1" required>
                            
                            <button type="submit">داواکردن</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);

        // Quantity validation
        document.querySelectorAll('input[name="quantity"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
            });
        });
    </script>
</body>
</html>
