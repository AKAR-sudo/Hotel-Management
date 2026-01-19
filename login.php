<?php
session_start();
require_once('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header('Location: admin/admin_dashboard.php');
        } else if ($user['role'] == 'user') {
            header('Location: user/user_dashboard.php');
        } else {
            header('Location: staff/staff_dashboard.php');
        }
        exit();
    } else {
        $error = 'ناوی بەکارهێنەر یان وشەی نهێنی هەڵەیە';
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە بۆ هوتێل</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf');
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: '20_Sarchia_Banoka_1';
        }

        body {
            min-height: 100vh;
            background: url('https://api/placeholder/1200/800') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(29, 53, 87, 0.9), rgba(69, 123, 157, 0.8));
            z-index: -1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 450px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .hotel-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 70px;
            color: #1D3557;
            margin-bottom: 10px;
            display: inline-block;
            transition: all 0.5s ease;
        }

        .logo-icon:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .hotel-name {
            font-size: 28px;
            color: #1D3557;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .hotel-tagline {
            font-size: 14px;
            color: #457B9D;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group select,
        .input-group input {
            width: 100%;
            padding: 15px 55px 15px 20px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(240, 240, 240, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), inset 0 1px 3px rgba(0, 0, 0, 0.05);
            color: #333;
        }

        .input-group i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #457B9D;
            font-size: 18px;
        }

        .input-group select:focus,
        .input-group input:focus {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(69, 123, 157, 0.25);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.2);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(1px);
        }

        .error-message {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #d32f2f;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-right: 4px solid #d32f2f;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #457B9D;
            font-size: 14px;
        }

        .footer a {
            color: #1D3557;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: #A8DADC;
        }

        /* Animation for logo */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Responsive design */
        @media (max-width: 500px) {
            .login-container {
                width: 90%;
                padding: 30px 20px;
            }
            
            .hotel-name {
                font-size: 24px;
            }
            
            .input-group select,
            .input-group input {
                padding: 14px 50px 14px 15px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="hotel-logo">
            <div class="logo-icon floating-animation">
                <i class="fas fa-hotel"></i>
            </div>
            <h1 class="hotel-name">هوتێلی کوردستان</h1>
            <p class="hotel-tagline">بەخێربێن بۆ سیستەمی بەڕێوەبەرایەتی هوتێل</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <select name="username" required>
                    <option value="" disabled selected>ناوی بەکارهێنەر هەڵبژێرە</option>
                    <?php
                    $stmt = $pdo->prepare("SELECT username FROM users");
                    $stmt->execute();
                    while($row = $stmt->fetch()) {
                        echo "<option value='" . htmlspecialchars($row['username']) . "'>" . 
                             htmlspecialchars($row['username']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="وشەی نهێنی" required>
            </div>

            <button type="submit" class="login-btn">
                چوونەژوورەوە <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>
        
        <div class="footer">
            <p>&copy; 2025 هوتێلی کوردستان - هەموو مافەکان پارێزراوە</p>
        </div>
    </div>

    <script>
        const logoIcon = document.querySelector('.logo-icon');
        const usernameSelect = document.querySelector('select[name="username"]');
        const passwordInput = document.querySelector('input[type="password"]');
        
        usernameSelect.addEventListener('focus', () => {
            logoIcon.innerHTML = '<i class="fas fa-user-circle"></i>';
            logoIcon.classList.remove('floating-animation');
        });
        
        passwordInput.addEventListener('focus', () => {
            logoIcon.innerHTML = '<i class="fas fa-key"></i>';
            logoIcon.classList.remove('floating-animation');
        });
        
        const resetLogo = () => {
            setTimeout(() => {
                logoIcon.innerHTML = '<i class="fas fa-hotel"></i>';
                logoIcon.classList.add('floating-animation');
            }, 200);
        };
        
        usernameSelect.addEventListener('blur', resetLogo);
        passwordInput.addEventListener('blur', resetLogo);
        
    </script>
     
</body>
</html>
