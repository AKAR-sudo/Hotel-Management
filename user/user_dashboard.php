<?php
session_start();
require_once('../includes/config.php');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>داشبۆردی بەکارهێنەر</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('../fonts/20_Sarchia_Banoka_1.ttf');
        }
        * {
            font-family: '20_Sarchia_Banoka_1';
        }
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: scaleIn 0.6s ease-out;
        }
        .dashboard-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .menu-item {
            padding: 25px 20px;
            background: linear-gradient(145deg, #2196F3, #1976D2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: linear-gradient(145deg, #1976D2, #2196F3);
        }
        .menu-item i {
            font-size: 2em;
            margin-bottom: 10px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 15px;
        }
        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        ?>
        <h2>بەخێربێیت <?php echo $user['username']; ?></h2>
        
        <div class="dashboard-menu">
            <a href="profile.php" class="menu-item">
                <i class="fas fa-user-circle"></i>
                پڕۆفایل
            </a>
            <a href="my_bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i>
                حیجزەکانم
            </a>
            <a href="search_hotels.php" class="menu-item">
                <i class="fas fa-hotel"></i>
                گەڕان بۆ هوتێل
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                چوونە دەرەوە
            </a>
        </div>
    </div>
</body>
</html>
