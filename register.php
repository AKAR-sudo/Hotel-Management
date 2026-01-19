<?php
session_start();
require_once('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, role) VALUES (?, ?, ?, ?, 'user')");
    if($stmt->execute([$username, $hashed_password, $email, $phone])) {
        header('Location: login.php');
        exit();
    } else {
        $error = 'هەڵەیەک ڕوویدا تکایە دووبارە هەوڵبدەوە';
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تۆمارکردنی ئەندامی نوێ - هوتێل</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf');
        }
        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #1c1c1c 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('images/hotel-bg.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            position: relative;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h2 {
            color: #1a1a1a;
            margin-bottom: 25px;
            font-size: 28px;
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
        .error-message {
            background: rgba(198, 40, 40, 0.1);
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(198, 40, 40, 0.3);
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 15px;
            margin: 8px 0;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        input:focus {
            border-color: #c8a165;
            outline: none;
            box-shadow: 0 0 10px rgba(200, 161, 101, 0.2);
        }
        button {
            width: 100%;
            padding: 15px;
            background: #c8a165;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        button:hover {
            background: #b38d4d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(200, 161, 101, 0.3);
        }
        p {
            margin-top: 20px;
            text-align: center;
            color: #666;
        }
        a {
            color: #c8a165;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #b38d4d;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-container {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>تۆمارکردنی ئەندامی نوێ</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" placeholder="ناوی بەکارهێنەر" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="ئیمەیڵ" required>
            </div>
            <div class="form-group">
                <input type="tel" name="phone" placeholder="ژمارەی مۆبایل" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="وشەی نهێنی" required>
            </div>
            <button type="submit">تۆمارکردن</button>
        </form>
        <p>هەژمارت هەیە؟ <a href="login.php">چوونە ژوورەوە</a></p>
    </div>
</body>
</html>
