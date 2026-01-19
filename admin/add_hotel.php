<?php
session_start();
require_once('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_name = $_POST['hotel_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $amenities = implode(',', $_POST['amenities'] ?? []);
    
    // Handle image upload
    $image = $_FILES['hotel_image'];
    $image_path = '';
    if ($image['error'] === 0) {
        $target_dir = "../uploads/hotels/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . time() . '_' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
    }
    
    $stmt = $pdo->prepare("INSERT INTO hotels (hotel_name, location, description, amenities, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$hotel_name, $location, $description, $amenities, $image_path]);
    
    header('Location: manage_hotels.php?success=added');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <title>زیادکردنی هوتێل</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
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
        textarea.form-control {
            height: 150px;
            resize: vertical;
        }
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .submit-btn {
            background: #1a237e;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
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
        <a href="manage_hotels.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە
        </a>

        <h1>زیادکردنی هوتێل</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>ناوی هوتێل</label>
                <input type="text" name="hotel_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>شوێن</label>
                <input type="text" name="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label>وەسف</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>خزمەتگوزارییەکان</label>
                <div class="amenities-grid">
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="wifi">
                        <label>وایفای</label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="parking">
                        <label>پارکینگ</label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="pool">
                        <label>مەلەوانگە</label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="restaurant">
                        <label>چێشتخانە</label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="gym">
                        <label>هۆڵی وەرزش</label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="spa">
                        <label>سپا</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>وێنەی هوتێل</label>
                <input type="file" name="hotel_image" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-plus"></i>
                زیادکردنی هوتێل
            </button>
        </form>
    </div>
</body>
</html>
