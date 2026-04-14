<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

 $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
 $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
 $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
 $totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

 $recentOrders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AapkiDukaan Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:#f5f6fa">
    <div class="admin-layout">
        <div class="admin-sidebar">
            <div class="admin-brand"><span>Aapki</span><span>Dukaan</span></div>
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-products.php"><i class="fas fa-box"></i> Products</a>
            <a href="add-product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
            <a href="../index.php"><i class="fas fa-globe"></i> View Website</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <span style="font-size:14px;color:#888">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-box"></i></div>
                    <h3><?php echo $totalProducts; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-users"></i></div>
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-shopping-bag"></i></div>
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-rupee-sign"></i></div>
                    <h3>₹<?php echo number_format($totalRevenue); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>

            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong>#AD<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><strong>₹<?php echo number_format($order['total_amount']); ?></strong></td>
                                <td><?php echo $order['payment_method']; ?></td>
                                <td><span class="order-status status-<?php echo $order['status']; ?>" style="font-size:11px"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($recentOrders) === 0): ?>
                            <tr><td colspan="6" style="text-align:center;color:#888;padding:40px">No orders yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>