<?php
session_start();
require_once('../includes/config.php');

$hotel_id = $_GET['id'] ?? null;
if (!$hotel_id) {
    header('Location: search_hotels.php');
    exit();
}

// Fetch hotel details
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

// Fetch available rooms
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE  status = 'available'");
$stmt->execute();
$rooms = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $user_id = $_SESSION['user_id'];
    
   // Replace the existing INSERT statement with this:
$stmt = $pdo->prepare("INSERT INTO bookings (user_id, hotel_id, room_id, check_in_date, check_out_date, status, checked_in) 
VALUES (?, ?, ?, ?, ?, 'pending', 0)");
$stmt->execute([$user_id, $hotel_id, $room_id, $check_in, $check_out]);

    
    header('Location: my_bookings.php?success=1');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>حیجزکردنی هوتێل</title>
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
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .hotel-details {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .hotel-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .amenities-list {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 15px 0;
        }
        .amenity-tag {
            background: #e3f2fd;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            color: #1565c0;
        }
        .booking-form {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
        }
        .room-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .room-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        .room-card.selected {
            border-color: #1a237e;
            background: #e8eaf6;
        }
        .submit-btn {
            background: #1a237e;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background: #151b60;
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
</head>
<body>
    <div class="container">
        <a href="search_hotels.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە
        </a>

        <div class="hotel-details">
            <img src="<?php echo htmlspecialchars($hotel['image']); ?>" alt="Hotel Image" class="hotel-image">
            <h1><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
            
            <div class="amenities-list">
                <?php foreach(explode(',', $hotel['amenities']) as $amenity): ?>
                    <span class="amenity-tag">
                        <i class="fas fa-check"></i>
                        <?php echo htmlspecialchars($amenity); ?>
                    </span>
                <?php endforeach; ?>
            </div>
            
            <p><?php echo htmlspecialchars($hotel['description']); ?></p>
        </div>

        <div class="booking-form">
            <h2>حیجزکردن</h2>
            <form method="POST" id="bookingForm">
                <div class="form-group">
                    <label>بەرواری چێک ئین</label>
                    <input type="date" name="check_in" class="form-control" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>بەرواری چێک ئاوت</label>
                    <input type="date" name="check_out" class="form-control" required 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>

                <div class="form-group">
                    <label>هەڵبژاردنی ژوور</label>
                    <div class="room-options">
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-card" onclick="selectRoom(this, <?php echo $room['id']; ?>)">
                                <h3><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <p>نرخ: $<?php echo number_format($room['price']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="room_id" id="selectedRoom" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-calendar-check"></i>
                    حیجزکردن
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectRoom(element, roomId) {
            document.querySelectorAll('.room-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('selectedRoom').value = roomId;
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!document.getElementById('selectedRoom').value) {
                e.preventDefault();
                alert('تکایە ژوورێک هەڵبژێرە');
            }
        });
    </script>
</body>
</html>
