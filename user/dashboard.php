<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی بەکارهێنەر</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('../fonts/20_Sarchia_Banoka_1.ttf');
        }
        
        * {
            font-family: '20_Sarchia_Banoka_1';
        }

        .booking-container {
            padding: 20px;
            animation: fadeIn 0.5s ease-in;
        }

        .room-card {
            background: #fff;
            border-radius: 8px;
            margin: 10px 0;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .available { background-color: #4CAF50; }
        .booked { background-color: #f44336; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <!-- Room Search -->
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="گەڕان بە پێی ژمارەی ژوور">
            <button type="submit">گەڕان</button>
        </form>

        <!-- Available Rooms -->
        <?php
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_number LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
        
        while ($room = $stmt->fetch()) {
            $status_class = $room['status'] == 'available' ? 'available' : 'booked';
            echo "<div class='room-card $status_class'>";
            echo "<h3>ژووری ژمارە: {$room['room_number']}</h3>";
            echo "<p>نرخ: {$room['price']}</p>";
            if ($room['status'] == 'available') {
                echo "<button onclick='bookRoom({$room['id']})'>حیجزکردن</button>";
            }
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function bookRoom(roomId) {
            // AJAX booking functionality
            fetch('book_room.php', {
                method: 'POST',
                body: JSON.stringify({ room_id: roomId }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ژوورەکە بە سەرکەوتوویی حیجز کرا');
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
