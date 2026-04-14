<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit();
}

 $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
 $stmt->execute([$_SESSION['user_id']]);
 $orders = $stmt->fetchAll();

 $ordersWithData = [];
foreach ($orders as $order) {
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order['id']]);
    $items = $stmt->fetchAll();
    $ordersWithData[] = [
        'order' => $order,
        'items' => $items
    ];
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-banner">
    <h1>My Orders</h1>
    <p>Track and manage your orders</p>
</div>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> My Orders
    </div>

    <?php if (count($ordersWithData) > 0): ?>
        <div class="orders-list">
            <?php foreach ($ordersWithData as $od):
                $order = $od['order'];
                $items = $od['items'];
            ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <span class="order-id">Order #AD<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        <span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                    </div>
                    <div class="order-card-items">
                        <?php foreach ($items as $item): ?>
                            <div class="order-card-item">
                                <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <span><?php echo htmlspecialchars(substr($item['name'], 0, 30)); ?> x <?php echo $item['quantity']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-card-footer">
                        <span><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></span>
                        <span class="total">₹<?php echo number_format($order['total_amount']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No orders yet</h3>
            <p>Looks like you haven't placed any order</p>
            <a href="index.php">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>