<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $cartItems = [];
 $subtotal = 0;
 $mrpTotal = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.mrp, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? ORDER BY c.id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $mrpTotal += $item['mrp'] * $item['quantity'];
    }
} elseif (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
        $stmt->execute([$pid]);
        $product = $stmt->fetch();
        if ($product) {
            $cartItems[] = [
                'id' => 'sess_' . $pid,
                'product_id' => $pid,
                'quantity' => $qty,
                'name' => $product['name'],
                'price' => $product['price'],
                'mrp' => $product['mrp'],
                'image' => $product['image'],
                'stock' => $product['stock']
            ];
            $subtotal += $product['price'] * $qty;
            $mrpTotal += $product['mrp'] * $qty;
        }
    }
}

 $savings = $mrpTotal - $subtotal;
 $delivery = $subtotal >= 499 ? 0 : 40;
 $total = $subtotal + $delivery;
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> Cart
    </div>

    <?php if (count($cartItems) > 0): ?>
        <div class="cart-page">
            <div class="cart-items">
                <h2>My Cart (<?php echo array_sum(array_column($cartItems, 'quantity')); ?>)</h2>

                <?php foreach ($cartItems as $item):
                    $discount = round(($item['mrp'] - $item['price']) / $item['mrp'] * 100);
                ?>
                    <div class="cart-item">
                        <a href="product-detail.php?id=<?php echo $item['product_id']; ?>">
                            <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        </a>
                        <div class="cart-item-info">
                            <h4><?php echo $item['name']; ?></h4>
                            <span class="price">₹<?php echo number_format($item['price']); ?></span>
                            <span class="mrp">₹<?php echo number_format($item['mrp']); ?></span>
                            <span class="discount"><?php echo $discount; ?>% off</span>
                            <div class="qty-control">
                                <button onclick="updateCartQty('<?php echo $item['id']; ?>', <?php echo $item['quantity'] - 1; ?>)">−</button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button onclick="updateCartQty('<?php echo $item['id']; ?>', <?php echo $item['quantity'] + 1; ?>)">+</button>
                            </div>
                            <br>
                            <button class="remove-btn" onclick="removeCartItem('<?php echo $item['id']; ?>')"><i class="fas fa-trash-alt"></i> Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Price Details</h3>
                <div class="cart-summary-row">
                    <span>Price (<?php echo array_sum(array_column($cartItems, 'quantity')); ?> items)</span>
                    <span>₹<?php echo number_format($mrpTotal); ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>Discount</span>
                    <span class="savings">− ₹<?php echo number_format($savings); ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>Delivery Charges</span>
                    <span><?php echo $delivery === 0 ? '<span style="color:#388e3c;font-weight:600">FREE</span>' : '₹' . $delivery; ?></span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total Amount</span>
                    <span>₹<?php echo number_format($total); ?></span>
                </div>
                <p style="font-size:13px;color:#388e3c;font-weight:600;margin-top:12px">You will save ₹<?php echo number_format($savings); ?> on this order</p>

                <a href="checkout.php"><button class="btn-checkout">Place Order</button></a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p>Add items to it now.</p>
            <a href="index.php">Shop Now</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>