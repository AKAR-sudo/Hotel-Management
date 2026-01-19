<?php
session_start();
require_once('../includes/config.php');

// Search functionality
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';

$query = "SELECT * FROM hotels WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND hotel_name LIKE ?";
    $params[] = "%$search%";
}

if ($location) {
    $query .= " AND location LIKE ?";
    $params[] = "%$location%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hotels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گەڕان بۆ هوتێل</title>
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
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .header h1 {
            color: #1D3557;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1 i {
            color: #457B9D;
            font-size: 28px;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
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
        
        .btn-back {
            background: white;
            color: #1D3557;
            border: 1px solid #E0E0E0;
        }
        
        .search-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 20px;
            padding-right: 45px;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:focus {
            border-color: #457B9D;
            box-shadow: 0 4px 15px rgba(69, 123, 157, 0.2);
            outline: none;
        }
        
        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #457B9D;
            font-size: 18px;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(29, 53, 87, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(29, 53, 87, 0.3);
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .hotel-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.5s ease forwards;
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(29, 53, 87, 0.15);
        }
        
        .hotel-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        
        .hotel-details {
            padding: 25px;
        }
        
        .hotel-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #1D3557;
        }
        
        .hotel-location {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .hotel-location i {
            color: #457B9D;
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .amenity-tag {
            background: rgba(69, 123, 157, 0.1);
            color: #457B9D;
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(69, 123, 157, 0.3);
        }
        
        .book-btn {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(29, 53, 87, 0.2);
        }
        
        .book-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(29, 53, 87, 0.3);
        }
        
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
        }
        
        .no-results i {
            font-size: 48px;
            color: #457B9D;
            margin-bottom: 20px;
        }
        
        .no-results p {
            font-size: 18px;
            color: #666;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .hotels-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-search"></i> گەڕان بۆ هوتێل</h1>
            <div class="btn-group">
                <a href="user_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <div class="search-section">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="گەڕان بە ناوی هوتێل" 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="form-group">
                    <input type="text" name="location" class="form-control" 
                           placeholder="شوێن" 
                           value="<?php echo htmlspecialchars($location); ?>">
                    <i class="fas fa-map-marker-alt search-icon"></i>
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> گەڕان
                </button>
            </form>
        </div>

        <div class="hotels-grid">
            <?php if(count($hotels) > 0): ?>
                <?php foreach ($hotels as $index => $hotel): ?>
                    <div class="hotel-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <img src="<?php echo htmlspecialchars($hotel['image']); ?>" 
                             alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" 
                             class="hotel-image">
                        
                        <div class="hotel-details">
                            <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
                            <p class="hotel-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($hotel['location']); ?>
                            </p>
                            
                            <div class="amenities-list">
                                <?php foreach(explode(',', $hotel['amenities']) as $amenity): ?>
                                    <span class="amenity-tag">
                                        <i class="fas fa-check"></i>
                                        <?php echo htmlspecialchars(trim($amenity)); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            
                            <a href="book_hotel.php?id=<?php echo $hotel['id']; ?>" class="book-btn">
                                <i class="fas fa-calendar-check"></i>
                                حیجزکردن
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-info-circle"></i>
                    <p>هیچ هوتێلێک نەدۆزرایەوە. تکایە پارامەتەرەکانی گەڕان بگۆڕە.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hotelCards = document.querySelectorAll('.hotel-card');
            hotelCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>