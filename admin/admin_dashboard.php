<?php
session_start();
require_once('../includes/config.php');

// Fetch statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM hotels");
$stmt->execute();
$total_hotels = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms");
$stmt->execute();
$total_rooms = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$stmt->execute();
$pending_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$total_users = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی بەڕێوەبەر</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> داشبۆردی بەڕێوەبەر</h1>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="user-name"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'بەڕێوەبەر'; ?></div>
                    <div class="user-role">بەڕێوەبەری سیستەم</div>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <div class="stat-value animate-stats" data-count="<?php echo $total_hotels; ?>">0</div>
                <div class="stat-label">کۆی هوتێلەکان</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-door-closed"></i>
                </div>
                <div class="stat-value animate-stats" data-count="<?php echo $total_rooms; ?>">0</div>
                <div class="stat-label">کۆی ژوورەکان</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value animate-stats" data-count="<?php echo $pending_bookings; ?>">0</div>
                <div class="stat-label">حیجزە چاوەڕوانەکان</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value animate-stats" data-count="<?php echo $total_users; ?>">0</div>
                <div class="stat-label">کۆی بەکارهێنەران</div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="manage_hotels.php" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <h3>بەڕێوەبردنی هوتێلەکان</h3>
            </a>
            
            <a href="manage_rooms.php" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-door-closed"></i>
                </div>
                <h3>بەڕێوەبردنی ژوورەکان</h3>
            </a>
            
            <a href="manage_bookings.php" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>بەڕێوەبردنی حیجزەکان</h3>
            </a>
            
            <a href="manage_users.php" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>بەڕێوەبردنی بەکارهێنەران</h3>
            </a>
        </div>

        <div style="text-align: center;">
            <a href="logout.php" class="btn logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                چوونە دەرەوە
            </a>
        </div>
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