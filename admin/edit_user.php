<?php
session_start();
require_once('../includes/config.php');

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header('Location: manage_users.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $email, $phone, $role, $user_id]);

    header('Location: manage_users.php?success=updated');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاریکردنی بەکارهێنەر</title>
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
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(29, 53, 87, 0.15);
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

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 45px; /* fixed: added left padding for icon */
            border: 1px solid rgba(69, 123, 157, 0.2);
            border-radius: 50px;
            font-size: 16px;
            background: rgba(240, 240, 240, 0.5);
            transition: all 0.3s ease;
            color: #333;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            border-color: #457B9D;
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(69, 123, 157, 0.25);
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 47px;
            color: #457B9D;
            font-size: 18px;
        }

        .submit-btn {
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
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animated-card {
            animation: fadeIn 0.5s ease forwards;
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

            .header h1 {
                font-size: 20px;
            }

            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }

            .card {
                padding: 20px;
            }

            .form-control {
                padding: 12px 15px 12px 45px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-user-edit"></i> دەستکاریکردنی بەکارهێنەر</h1>
        <a href="manage_users.php" class="btn btn-back">
            <i class="fas fa-arrow-right"></i> گەڕانەوە
        </a>
    </div>

    <div class="card animated-card">
        <form method="POST">
            <div class="form-group">
                <label for="username">ناوی بەکارهێنەر</label>
                <i class="fas fa-user input-icon"></i>
                <input type="text" id="username" name="username" class="form-control"
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">ئیمەیڵ</label>
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">ژمارەی مۆبایل</label>
                <i class="fas fa-mobile-alt input-icon"></i>
                <input type="tel" id="phone" name="phone" class="form-control"
                       value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">ڕۆڵ</label>
                <i class="fas fa-user-tag input-icon"></i>
                <select id="role" name="role" class="form-control" required>
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>بەکارهێنەر</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>بەڕێوەبەر</option>
                    <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>ستاف</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> پاشەکەوتکردنی گۆڕانکارییەکان
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const card = document.querySelector('.card');
        card.style.opacity = "0";
        setTimeout(() => {
            card.classList.add('animated-card');
        }, 100);
    });

    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.querySelector('.input-icon').style.color = '#1D3557';
        });

        input.addEventListener('blur', () => {
            input.parentElement.querySelector('.input-icon').style.color = '#457B9D';
        });
    });
</script>
</body>
</html>
