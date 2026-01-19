<?php
session_start();
require_once('../includes/config.php');

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);
    
    header("Location: profile.php?success=updated");
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پڕۆفایل</title>
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
            max-width: 900px;
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
        
        .success-message {
            background: linear-gradient(135deg, #43A047, #66BB6A);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(67, 160, 71, 0.2);
            display: flex;
            align-items: center;
            border-right: 5px solid #2E7D32;
        }
        
        .success-message i {
            font-size: 24px;
            margin-left: 15px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(5px);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #457B9D, #1D3557);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 5px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .profile-avatar i {
            font-size: 50px;
            color: white;
        }
        
        .profile-header h2 {
            color: #1D3557;
            font-size: 26px;
            margin-top: 15px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #1D3557;
            font-weight: bold;
            font-size: 16px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(69, 123, 157, 0.2);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        
        .form-group input:focus {
            border-color: #457B9D;
            outline: none;
            background: white;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 47px;
            color: #457B9D;
            font-size: 18px;
        }
        
        .form-group input:focus + i {
            color: #1D3557;
        }
        
        .save-btn {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 15px rgba(29, 53, 87, 0.2);
        }
        
        .save-btn:hover {
            background: linear-gradient(135deg, #162B48, #3D6990);
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(29, 53, 87, 0.3);
        }
        
        .save-btn:active {
            transform: translateY(1px);
            box-shadow: 0 5px 10px rgba(29, 53, 87, 0.2);
        }
        
        /* Animation for form */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 15px;
            }
            
            .card {
                padding: 20px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-avatar i {
                font-size: 40px;
            }
            
            .form-group input {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-circle"></i> پڕۆفایلی من</h1>
            <div class="btn-group">
                <a href="user_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> پڕۆفایلەکەت بە سەرکەوتوویی نوێکرایەوە
            </div>
        <?php endif; ?>

        <div class="card animated">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            </div>

            <form method="POST" action="" id="profileForm">
                <div class="form-group" style="animation-delay: 0.1s;" class="animated">
                    <label for="username">ناوی بەکارهێنەر</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <i class="fas fa-user"></i>
                </div>

                <div class="form-group" style="animation-delay: 0.2s;" class="animated">
                    <label for="email">ئیمەیڵ</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="form-group" style="animation-delay: 0.3s;" class="animated">
                    <label for="phone">ژمارەی مۆبایل</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    <i class="fas fa-phone"></i>
                </div>

                <button type="submit" class="save-btn" style="animation-delay: 0.4s;" class="animated">
                    <i class="fas fa-save"></i> پاشەکەوتکردن
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add animation to form elements when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const formElements = document.querySelectorAll('.form-group, .save-btn');
            formElements.forEach((element, index) => {
                element.style.opacity = "0";
                setTimeout(() => {
                    element.classList.add('animated');
                }, 100 + (index * 100));
            });
            
            // Form validation
            const profileForm = document.getElementById('profileForm');
            profileForm.addEventListener('submit', function(event) {
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                
                // Simple email validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    alert('تکایە ئیمەیڵێکی دروست بنووسە');
                    event.preventDefault();
                    return;
                }
                
                // Simple phone validation (optional)
                if (phone && !/^[0-9\+\-\s]{10,15}$/.test(phone)) {
                    alert('تکایە ژمارەی مۆبایلی دروست بنووسە');
                    event.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>