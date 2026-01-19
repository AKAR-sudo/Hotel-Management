<?php
session_start();
require_once('../includes/config.php');

// Fetch today's statistics
$today = date('Y-m-d');


$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE checked_in = 1");
$stmt->execute();
$todays_checkins = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE checked_in = 0");
$stmt->execute();
$todays_checkouts = $stmt->fetchColumn();


$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$stmt->execute();
$pending_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE status = 'available'");
$stmt->execute();
$available_rooms = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>داشبۆردی ستاف</title>
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
        
        .dashboard-header {
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
        
        .dashboard-header h1 {
            color: #1D3557;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .dashboard-header h1 i {
            color: #457B9D;
            font-size: 32px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .user-name {
            font-weight: bold;
            color: #1D3557;
        }
        
        .user-role {
            color: #457B9D;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1D3557, #457B9D);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.12);
        }
        
        .stat-icon {
            font-size: 40px;
            color: #457B9D;
            margin-bottom: 15px;
            background: rgba(69, 123, 157, 0.1);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1);
            color: #1D3557;
            background: rgba(29, 53, 87, 0.1);
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #1D3557;
            margin: 10px 0;
            position: relative;
            display: inline-block;
        }
        
        .stat-label {
            color: #457B9D;
            font-size: 16px;
            font-weight: 500;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .menu-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 25px;
            text-decoration: none;
            color: #1D3557;
            text-align: center;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .menu-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, transparent 50%, rgba(69, 123, 157, 0.1) 50%);
            border-radius: 0 0 20px 0;
            transition: all 0.3s ease;
        }
        
        .menu-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(29, 53, 87, 0.15);
        }
        
        .menu-item:hover::after {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 30%, rgba(69, 123, 157, 0.1) 30%);
        }
        
        .menu-icon {
            font-size: 40px;
            margin-bottom: 20px;
            color: #457B9D;
            transition: all 0.3s ease;
            background: rgba(69, 123, 157, 0.1);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .menu-item:hover .menu-icon {
            transform: scale(1.1);
            color: #1D3557;
            background: rgba(29, 53, 87, 0.1);
        }
        
        .menu-item h3 {
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
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
        
        .logout-btn {
            background: linear-gradient(135deg, #E53935, #EF5350);
            color: white;
            margin-top: 15px;
        }
        
        /* Animation for stats */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-stats {
            opacity: 0;
            animation: countUp 0.8s ease forwards;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .dashboard-header h1 {
                justify-content: center;
            }
            
            .user-info {
                flex-direction: column;
            }
            
            .stats-grid, .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card, .menu-item {
                padding: 20px;
            }
        }
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
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #1a237e;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 1.1em;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .menu-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            color: #333;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        .menu-icon {
            font-size: 2em;
            margin-bottom: 10px;
            color: #1a237e;
        }
        .logout-btn {
            background: #f44336;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
        }
     
    </style>
</head>
<body>
    <div class="container">
        <h1>داشبۆردی ستاف</h1>
        <br><br>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-sign-in-alt menu-icon"></i>
                <div class="stat-value"><?php echo $todays_checkins; ?></div>
                <div class="stat-label">چێک ئینی ئەمڕۆ</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-sign-out-alt menu-icon"></i>
                <div class="stat-value"><?php echo $todays_checkouts; ?></div>
                <div class="stat-label">چێک ئاوتی ئەمڕۆ</div>
            </div>
            
            
            
            <div class="stat-card">
                <i class="fas fa-door-open menu-icon"></i>
                <div class="stat-value"><?php echo $available_rooms; ?></div>
                <div class="stat-label">ژوورە بەردەستەکان</div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="manage_checkins.php" class="menu-item">
                <i class="fas fa-sign-in-alt menu-icon"></i>
                <h3>بەڕێوەبردنی چێک ئین</h3>
            </a>
            
            <a href="manage_checkouts.php" class="menu-item">
                <i class="fas fa-sign-out-alt menu-icon"></i>
                <h3>بەڕێوەبردنی چێک ئاوت</h3>
            </a>
            
            <a href="room_status.php" class="menu-item">
                <i class="fas fa-door-closed menu-icon"></i>
                <h3>دۆخی ژوورەکان</h3>
            </a>
            
            
        </div>

        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            چوونە دەرەوە
        </a>
    </div>
    <script>
        // Animation for counting up stats
        document.addEventListener("DOMContentLoaded", function() {
            const statsElements = document.querySelectorAll('.animate-stats');
            
            setTimeout(() => {
                statsElements.forEach((element, index) => {
                    const target = parseInt(element.getAttribute('data-count'));
                    const duration = 1500; // milliseconds to complete animation
                    const startTime = Date.now();
                    
                    element.style.animationDelay = `${index * 100}ms`;
                    
                    const updateCounter = () => {
                        const currentTime = Date.now();
                        const progress = Math.min((currentTime - startTime) / duration, 1);
                        const currentCount = Math.floor(progress * target);
                        
                        element.textContent = currentCount;
                        
                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        } else {
                            element.textContent = target;
                        }
                    };
                    
                    updateCounter();
                });
            }, 300);
            
            // Add hover effect to menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 100}ms`;
            });
        });
    </script>
</body>
</html>
