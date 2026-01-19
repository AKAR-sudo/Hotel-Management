<?php
session_start();
require_once('../includes/config.php');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$room_id = $_GET['id'] ?? null;

if (!$room_id) {
    header('Location: manage_rooms.php');
    exit();
}

// Fetch room data
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    // Handle image upload
    if (!empty($_FILES['room_image']['name'])) {
        $target_dir = "../uploads/rooms/";
        $file_extension = strtolower(pathinfo($_FILES["room_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/rooms/' . $new_filename;
        }
    }

    $sql = "UPDATE rooms SET 
            room_number = ?, 
            room_type = ?, 
            price = ?, 
            status = ?, 
            description = ?";
    $params = [$room_number, $room_type, $price, $status, $description];

    if (isset($image_path)) {
        $sql .= ", image = ?";
        $params[] = $image_path;
    }

    $sql .= " WHERE id = ?";
    $params[] = $room_id;

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        header('Location: manage_rooms.php?success=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاری ژوور</title>
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
        .edit-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
        }
        h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: #c8a165;
            margin: 10px auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #c8a165;
            outline: none;
            box-shadow: 0 0 10px rgba(200, 161, 101, 0.2);
        }
        .current-image {
            max-width: 200px;
            margin: 10px 0;
            border-radius: 8px;
        }
        button {
            background: #c8a165;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        button:hover {
            background: #b38d4d;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #c8a165;
            text-decoration: none;
        }
        .back-link:hover {
            color: #b38d4d;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>دەستکاری ژوور</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>ژمارەی ژوور</label>
                <input type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
            </div>

            <div class="form-group">
                <label>جۆری ژوور</label>
                <select name="room_type" required>
                    <option value="single" <?php echo $room['room_type'] == 'single' ? 'selected' : ''; ?>>تاک</option>
                    <option value="double" <?php echo $room['room_type'] == 'double' ? 'selected' : ''; ?>>دووانە</option>
                    <option value="suite" <?php echo $room['room_type'] == 'suite' ? 'selected' : ''; ?>>جناح</option>
                </select>
            </div>

            <div class="form-group">
                <label>نرخ</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($room['price']); ?>" required>
            </div>

            <div class="form-group">
                <label>دۆخ</label>
                <select name="status" required>
                    <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>بەردەستە</option>
                    <option value="occupied" <?php echo $room['status'] == 'occupied' ? 'selected' : ''; ?>>داگیرکراوە</option>
                    <option value="maintenance" <?php echo $room['status'] == 'maintenance' ? 'selected' : ''; ?>>چاککردنەوە</option>
                </select>
            </div>

            <div class="form-group">
                <label>وەسف</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($room['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>وێنەی ژوور</label>
                <?php if ($room['image']): ?>
                    <img src="../<?php echo htmlspecialchars($room['image']); ?>" class="current-image" alt="Room Image">
                <?php endif; ?>
                <input type="file" name="room_image" accept="image/*">
            </div>

            <button type="submit">نوێکردنەوە</button>
            <a href="manage_rooms.php" class="back-link">گەڕانەوە</a>
        </form>
    </div>
</body>
</html>
