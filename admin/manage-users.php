<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

 $users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - AapkiDukaan Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:#f5f6fa">
    <div class="admin-layout">
        <div class="admin-sidebar">
            <div class="admin-brand"><span>Aapki</span><span>Dukaan</span></div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-products.php"><i class="fas fa-box"></i> Products</a>
            <a href="add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="manage-users.php" class="active"><i class="fas fa-users"></i> Users</a>
            <a href="../index.php"><i class="fas fa-globe"></i> View Website</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Registered Users (<?php echo count($users); ?>)</h1>
            </div>

            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>#<?php echo $u['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo $u['phone']; ?></td>
                                <td><?php echo htmlspecialchars($u['city'] ?? '-'); ?></td>
                                <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($users) === 0): ?>
                            <tr><td colspan="6" style="text-align:center;color:#888;padding:40px">No users registered yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>