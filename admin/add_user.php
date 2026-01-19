<?php
session_start();
require_once('../includes/config.php');

if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $email, $phone, $password, $role]);
    
    header('Location: manage_users.php?success=added');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی بەکارهێنەر</title>
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
            max-width: 800px;
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
        
        .btn-back {
            background: white;
            color: #1D3557;
            border: 1px solid #E0E0E0;
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
        
        .form-title {
            color: #1D3557;
            font-size: 22px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-title i {
            color: #457B9D;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1D3557;
            font-weight: bold;
            font-size: 16px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #457B9D;
            outline: none;
            box-shadow: 0 5px 15px rgba(69, 123, 157, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-group .input-icon {
            position: absolute;
            left: 15px;
            top: 45px;
            color: #457B9D;
            font-size: 18px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(29, 53, 87, 0.2);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(29, 53, 87, 0.3);
        }
        
        .btn-submit:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(29, 53, 87, 0.2);
        }
        
        .btn-cancel {
            background: white;
            color: #1D3557;
            padding: 12px 25px;
            border-radius: 50px;
            border: 1px solid #E0E0E0;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .btn-cancel:hover {
            transform: translateY(-3px);
            background: #F8F9FA;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .btn-cancel:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Hover effect for buttons */
        .btn::before,
        .btn-submit::before,
        .btn-cancel::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn:hover::before,
        .btn-submit:hover::before,
        .btn-cancel:hover::before {
            left: 100%;
        }
        
        /* Animation for form */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animated-card {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        /* Form grid for larger forms */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 0;
        }
        
        .form-col {
            flex: 1;
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
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn, .btn-submit, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> زیادکردنی بەکارهێنەر</h1>
            <div class="btn-group">
                <a href="manage_users.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <div class="card animated-card">
            <h2 class="form-title"><i class="fas fa-user-edit"></i> تۆمارکردنی بەکارهێنەری نوێ</h2>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label>ناوی بەکارهێنەر</label>
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="username" placeholder="ناوی بەکارهێنەر بنووسە" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>ئیمەیڵ</label>
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" placeholder="ئیمەیڵەکەت بنووسە" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label>ژمارەی مۆبایل</label>
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" name="phone" placeholder="ژمارەی مۆبایل بنووسە" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>وشەی نهێنی</label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" placeholder="وشەی نهێنی بنووسە" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>ڕۆڵ</label>
                    <i class="fas fa-user-shield input-icon"></i>
                    <select name="role" required>
                        <option value="" disabled selected>ڕۆڵی بەکارهێنەر دیاری بکە</option>
                        <option value="user">بەکارهێنەر</option>
                        <option value="admin">بەڕێوەبەر</option>
                        <option value="staff">ستاف</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <a href="manage_users.php" class="btn-cancel">
                        <i class="fas fa-times"></i> پاشگەزبوونەوە
                    </a>
                    <button type="submit" name="add_user" class="btn-submit">
                        <i class="fas fa-save"></i> زیادکردن
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add animation to form elements
        document.addEventListener("DOMContentLoaded", function() {
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = "0";
                setTimeout(() => {
                    group.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
                }, 300);
            });
            
            // Focus animation
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-5px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>