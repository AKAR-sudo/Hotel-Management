<?php
session_start();
require_once('../includes/config.php');

// Search functionality
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';

$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role) {
    $query .= " AND role = ?";
    $params[] = $role;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header('Location: manage_users.php?success=deleted');
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ku">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی بەکارهێنەران</title>
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
        
        .btn-primary {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
        }
        
        .btn-secondary {
            background: white;
            color: #1D3557;
            border: 1px solid #1D3557;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
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
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th,
        .users-table td {
            padding: 18px 20px;
            text-align: right;
        }
        
        .users-table th {
            background: linear-gradient(135deg, #1D3557, #457B9D);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        .users-table th:first-child {
            border-top-right-radius: 15px;
        }
        
        .users-table th:last-child {
            border-top-left-radius: 15px;
        }
        
        .users-table tr {
            border-bottom: 1px solid #F0F0F0;
            transition: all 0.3s ease;
        }
        
        .users-table tr:last-child {
            border-bottom: none;
        }
        
        .users-table tr:hover {
            background: rgba(240, 248, 255, 0.7);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .edit-btn {
            background: linear-gradient(135deg, #039BE5, #29B6F6);
        }
        
        .delete-btn {
            background: linear-gradient(135deg, #E53935, #EF5350);
        }
        
        .user-role {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        
        .role-admin {
            background: rgba(25, 118, 210, 0.1);
            color: #1976D2;
            border: 1px solid rgba(25, 118, 210, 0.3);
        }
        
        .role-user {
            background: rgba(97, 97, 97, 0.1);
            color: #616161;
            border: 1px solid rgba(97, 97, 97, 0.3);
        }
        
        .role-staff {
            background: rgba(156, 39, 176, 0.1);
            color: #9C27B0;
            border: 1px solid rgba(156, 39, 176, 0.3);
        }
        
        .no-results {
            padding: 50px;
            text-align: center;
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
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .users-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .users-table th, 
            .users-table td {
                padding: 12px 15px;
            }
        }
        
        /* Animation for table rows */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated-row {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> بەڕێوەبردنی بەکارهێنەران</h1>
            <div class="btn-group">
                <a href="add_user.php" class="btn btn-add">
                    <i class="fas fa-plus"></i> زیادکردنی بەکارهێنەر
                </a>
                <a href="admin_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-right"></i> گەڕانەوە
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php if ($_GET['success'] === 'deleted'): ?>
                    <i class="fas fa-check-circle"></i> بەکارهێنەر بە سەرکەوتوویی سڕایەوە
                <?php elseif ($_GET['success'] === 'added'): ?>
                    <i class="fas fa-check-circle"></i> بەکارهێنەر بە سەرکەوتوویی زیادکرا
                <?php elseif ($_GET['success'] === 'updated'): ?>
                    <i class="fas fa-check-circle"></i> زانیاری بەکارهێنەر بە سەرکەوتوویی نوێکرایەوە
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Search Section - Added from hotel search page -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="گەڕان بە ناو، ئیمەیڵ یان ژمارەی مۆبایل" 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="form-group">
                    <select name="role" class="form-control">
                        <option value="">هەموو ڕۆڵەکان</option>
                        <option value="admin" <?php if($role === 'admin') echo 'selected'; ?>>
                            بەڕێوەبەر
                        </option>
                        <option value="staff" <?php if($role === 'staff') echo 'selected'; ?>>
                            ستاف
                        </option>
                        <option value="user" <?php if($role === 'user') echo 'selected'; ?>>
                            بەکارهێنەر
                        </option>
                    </select>
                    <i class="fas fa-user-tag search-icon"></i>
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> گەڕان
                </button>
            </form>
        </div>

        <div class="card">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ناو</th>
                        <th>ئیمەیڵ</th>
                        <th>ژمارەی مۆبایل</th>
                        <th>ڕۆڵ</th>
                        <th>بەرواری دروستکردن</th>
                        <th>کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr class="animated-row" style="animation-delay: <?php echo $index * 0.05; ?>s">
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td>
                                    <span class="user-role role-<?php echo $user['role']; ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> دەستکاری
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('دڵنیای لە سڕینەوەی ئەم بەکارهێنەرە؟');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="action-btn delete-btn">
                                            <i class="fas fa-trash"></i> سڕینەوە
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-results">
                                <i class="fas fa-info-circle"></i>
                                <p>هیچ بەکارهێنەرێک نەدۆزرایەوە. تکایە پارامەتەرەکانی گەڕان بگۆڕە.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add animation to table rows when page loads
        document.addEventListener("DOMContentLoaded", function() {
            const rows = document.querySelectorAll('.animated-row');
            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.style.opacity = "1";
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>