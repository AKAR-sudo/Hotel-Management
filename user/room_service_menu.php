<?php
session_start();
require_once('../includes/config.php');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>مێنیوی خزمەتگوزاری ژوور</title>
    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding: 30px;
        }
        .menu-item {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .menu-item:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="menu-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM room_service_menu");
        while($item = $stmt->fetch()) {
            echo "<div class='menu-item' onclick='orderItem({$item['id']})'>
                    <img src='{$item['image']}' alt='{$item['name']}'>
                    <h3>{$item['name']}</h3>
                    <p>{$item['price']} دینار</p>
                  </div>";
        }
        ?>
    </div>
</body>
</html>
