<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

 $orderId = $_GET['id'] ?? 0;

 $stmt = $pdo->prepare("SELECT o.*, oi.product_id, oi.quantity, oi.price, p.name, p.image FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id WHERE o.id = ? AND o.user_id = ?");
 $stmt->execute([$orderId, $_SESSION['user_id']]);
 $orderData = $stmt->fetchAll();

if (count($orderData) === 0) {
    header('Location: index.php');
    exit();
}

 $order = $orderData[0];
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="order-success">
        <i class="fas fa-check-circle"></i>
        <h2>Order Placed Successfully!</h2>
        <p>Your order #AD<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?> has been placed. We will send you updates on your email and phone.</p>

        <div style="background:#f5f5f5;border-radius:8px;padding:16px;text-align:left;margin-bottom:24px">
            <?php foreach ($orderData as $item): ?>
                <div style="display:flex;gap:10px;align-items:center;padding:6px 0">
                    <img src="assets/images/<?php echo $item['image']; ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px">
                    <span style="font-size:13px;flex:1"><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                    <span style="font-size:13px;font-weight:600">₹<?php echo number_format($item['price'] * $item['quantity']); ?></span>
                </div>
            <?php endforeach; ?>
            <div style="border-top:1px solid #ddd;margin-top:8px;padding-top:8px;display:flex;justify-content:space-between;font-weight:700">
                <span>Total</span>
                <span>₹<?php echo number_format($order['total_amount']); ?></span>
            </div>
        </div>

        <p style="font-size:13px;color:#888;margin-bottom:16px">Delivery to: <?php echo htmlspecialchars($order['address'] . ', ' . $order['city'] . ' - ' . $order['pincode']); ?></p>

        <a href="orders.php" class="btn-secondary" style="display:inline-block;text-align:center;color:white">View My Orders</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>