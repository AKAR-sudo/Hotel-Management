<?php
session_start();
require_once('../includes/config.php');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $image_path = '';

    // Handle image upload
    if (!empty($_FILES['room_image']['name'])) {
        $target_dir = "../uploads/rooms/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["room_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/rooms/' . $new_filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, price, status, description, image) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$room_number, $room_type, $price, $status, $description, $image_path])) {
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
    <title>زیادکردنی ژووری نوێ</title>
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
        .add-container {
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
        button {
            background: #c8a165;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }
        button:hover {
            background: #b38d4d;
            transform: translateY(-2px);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #c8a165;
            text-decoration: none;
        }
        .back-link:hover {
            color: #b38d4d;
        }
        .preview-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
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
    <div class="add-container">
    <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە
        </a>
        <h2>زیادکردنی ژووری نوێ</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>ژمارەی ژوور</label>
                <input type="text" name="room_number" required>
            </div>

            <div class="form-group">
                <label>جۆری ژوور</label>
                <select name="room_type" required>
                    <option value="single">تاک</option>
                    <option value="double">دووانە</option>
                    <option value="suite">جناح</option>
                </select>
            </div>

            <div class="form-group">
                <label>نرخ</label>
                <input type="number" name="price" required>
            </div>

            <div class="form-group">
                <label>دۆخ</label>
                <select name="status" required>
                    <option value="available">بەردەستە</option>
                    <option value="occupied">داگیرکراوە</option>
                    <option value="maintenance">چاککردنەوە</option>
                </select>
            </div>

            <div class="form-group">
                <label>وەسف</label>
                <textarea name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>وێنەی ژوور</label>
                <input type="file" name="room_image" accept="image/*" onchange="previewImage(this)">
                <img id="preview" class="preview-image">
            </div>

            <button type="submit">زیادکردن</button>
            <a href="manage_rooms.php" class="back-link">گەڕانەوە</a>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
