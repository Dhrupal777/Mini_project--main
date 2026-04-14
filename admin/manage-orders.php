<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: manage-orders.php');
    exit();
}

 $orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - AapkiDukaan Admin</title>
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
            <a href="manage-orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
            <a href="../index.php"><i class="fas fa-globe"></i> View Website</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Orders (<?php echo count($orders); ?>)</h1>
            </div>

            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                            $stmt->execute([$order['id']]);
                            $itemCount = $stmt->fetchColumn();
                        ?>
                            <tr>
                                <td><strong>#AD<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><?php echo $itemCount; ?> items</td>
                                <td><strong>₹<?php echo number_format($order['total_amount']); ?></strong></td>
                                <td><?php echo $order['payment_method']; ?></td>
                                <td><span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:flex;gap:4px;align-items:center">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="admin-status" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($orders) === 0): ?>
                            <tr><td colspan="8" style="text-align:center;color:#888;padding:40px">No orders found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>