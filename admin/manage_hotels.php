<?php
session_start();
require_once('../includes/config.php');

// Fetch all hotels
$stmt = $pdo->prepare("SELECT * FROM hotels ORDER BY created_at DESC");
$stmt->execute();
$hotels = $stmt->fetchAll();

// Handle hotel deletion
if (isset($_POST['delete_hotel'])) {
    $hotel_id = $_POST['hotel_id'];
    $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute([$hotel_id]);
    header('Location: manage_hotels.php?success=deleted');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی هوتێلەکان</title>
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
        
        .page-header {
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
        
        .page-header h1 {
            color: #1D3557;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-header h1 i {
            color: #457B9D;
            font-size: 32px;
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
            cursor: pointer;
            border: none;
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
        
        .btn-primary {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #c62828, #ef5350);
            color: white;
        }
        
        .alert {
            padding: 18px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(76, 175, 80, 0.1));
            border-right: 5px solid #2e7d32;
            color: #2e7d32;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .hotel-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            position: relative;
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(29, 53, 87, 0.12);
        }
        
        .hotel-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .hotel-card:hover .hotel-image img {
            transform: scale(1.05);
        }
        
        .hotel-info {
            padding: 25px;
        }
        
        .hotel-info h3 {
            color: #1D3557;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .hotel-info p {
            margin: 10px 0;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .hotel-info i {
            color: #457B9D;
            min-width: 20px;
            text-align: center;
        }
        
        .hotel-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .edit-btn, .delete-btn {
            flex: 1;
            font-size: 15px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .empty-state i {
            font-size: 70px;
            color: #457B9D;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #1D3557;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .empty-state p {
            color: #555;
            margin-bottom: 25px;
        }
        
        /* Animation for hotel cards */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-cards {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 20px 15px;
            }
            
            .page-header h1 {
                justify-content: center;
            }
            
            .hotels-grid {
                grid-template-columns: 1fr;
            }
            
            .hotel-card {
                max-width: 100%;
            }
        }
        
        /* Description truncation */
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="btn btn-primary" style="margin-bottom: 20px;">
            <i class="fas fa-arrow-right"></i>
            گەڕانەوە بۆ داشبۆرد
        </a>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php if ($_GET['success'] == 'deleted'): ?>
                    <i class="fas fa-check-circle"></i> هوتێل بە سەرکەوتوویی سڕایەوە
                <?php elseif ($_GET['success'] == 'added'): ?>
                    <i class="fas fa-check-circle"></i> هوتێل بە سەرکەوتوویی زیادکرا
                <?php elseif ($_GET['success'] == 'updated'): ?>
                    <i class="fas fa-check-circle"></i> هوتێل بە سەرکەوتوویی نوێکرایەوە
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1><i class="fas fa-hotel"></i> بەڕێوەبردنی هوتێلەکان</h1>
            <a href="add_hotel.php" class="btn btn-success">
                <i class="fas fa-plus"></i>
                زیادکردنی هوتێل
            </a>
        </div>

        <?php if (count($hotels) > 0): ?>
            <div class="hotels-grid">
                <?php foreach ($hotels as $index => $hotel): ?>
                    <div class="hotel-card animate-cards" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="hotel-image">
                            <?php if (!empty($hotel['image']) && file_exists($hotel['image'])): ?>
                                <img src="<?php echo htmlspecialchars($hotel['image']); ?>" 
                                    alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                            <?php else: ?>
                                <img src="/api/placeholder/800/600" alt="هوتێل">
                            <?php endif; ?>
                        </div>
                        <div class="hotel-info">
                            <h3><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
                            <p><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                            <p class="truncate"><i class="fas fa-circle-info"></i> <?php echo htmlspecialchars($hotel['description']); ?></p>
                            <p><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($hotel['amenities']); ?></p>
                            
                            <div class="hotel-actions">
                                <a href="edit_hotel.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary edit-btn">
                                    <i class="fas fa-edit"></i>
                                    دەستکاری
                                </a>
                                <form method="POST" style="flex: 1;" onsubmit="return confirm('دڵنیای لە سڕینەوەی ئەم هوتێلە؟');">
                                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                                    <button type="submit" name="delete_hotel" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash"></i>
                                        سڕینەوە
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-hotel"></i>
                <h3>هیچ هوتێلێک نەدۆزرایەوە</h3>
                <p>تا ئێستا هیچ هوتێلێک زیاد نەکراوە. دەتوانی یەکەم هوتێل زیاد بکەیت.</p>
                <a href="add_hotel.php" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    زیادکردنی هوتێل
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Animation for hotel cards
        document.addEventListener("DOMContentLoaded", function() {
            const hotelCards = document.querySelectorAll('.hotel-card');
            
            // Function to check if element is in viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }
            
            // Initial check for visible elements
            hotelCards.forEach(card => {
                if (isInViewport(card)) {
                    card.style.animationPlayState = 'running';
                }
            });
            
            // Check on scroll
            window.addEventListener('scroll', function() {
                hotelCards.forEach(card => {
                    if (isInViewport(card) && card.style.animationPlayState !== 'running') {
                        card.style.animationPlayState = 'running';
                    }
                });
            });
        });
    </script>
</body>
</html>