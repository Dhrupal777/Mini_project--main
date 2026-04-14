<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

 $cartItems = [];
 $subtotal = 0;

 $buyNowId = $_GET['buy_now'] ?? 0;

if ($buyNowId) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
    $stmt->execute([$buyNowId]);
    $product = $stmt->fetch();
    if ($product) {
        $cartItems[] = [
            'product_id' => $product['id'],
            'quantity' => 1,
            'name' => $product['name'],
            'price' => $product['price'],
            'mrp' => $product['mrp'],
            'image' => $product['image']
        ];
        $subtotal = $product['price'];
    }
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.mrp, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}

if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit();
}

 $delivery = $subtotal >= 499 ? 0 : 40;
 $total = $subtotal + $delivery;

 $user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
 $user->execute([$_SESSION['user_id']]);
 $userData = $user->fetch();

 $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment = $_POST['payment_method'] ?? 'COD';

    if ($address === '' || $city === '' || $pincode === '' || $phone === '') {
        $error = 'All fields are required';
    } elseif (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
        $error = 'Please enter a valid 6 digit pincode';
    } elseif (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        $error = 'Please enter a valid 10 digit phone number';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, address, city, pincode, phone, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total, $address, $city, $pincode, $phone, $payment]);
            $orderId = $pdo->lastInsertId();

            foreach ($cartItems as $item) {
                $qty = $item['quantity'] ?? 1;
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_id'], $qty, $item['price']]);

                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $item['product_id']]);
            }

            if (!$buyNowId) {
                $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
            }

            $pdo->commit();

            header('Location: place-order.php?id=' . $orderId);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> <a href="cart.php">Cart</a> <span>›</span> Checkout
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:16px"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="checkout-form">
            <h2>Delivery Address</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($userData['name']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" required><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" value="<?php echo htmlspecialchars($userData['pincode'] ?? ''); ?>" maxlength="6" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" maxlength="10" required>
                </div>

                <h2 style="margin-top:24px">Payment Method</h2>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="radio" name="payment_method" value="COD" checked> Cash on Delivery
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="radio" name="payment_method" value="UPI"> UPI Payment
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="radio" name="payment_method" value="Card"> Credit/Debit Card
                    </label>
                </div>

                <button type="submit" class="btn-checkout" style="margin-top:16px">Place Order - ₹<?php echo number_format($total); ?></button>
            </form>
        </div>

        <div class="checkout-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cartItems as $item):
                $qty = $item['quantity'] ?? 1;
            ?>
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f0f0f0">
                    <img src="assets/images/<?php echo $item['image']; ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px">
                    <div style="flex:1">
                        <p style="font-size:13px;font-weight:500"><?php echo $item['name']; ?></p>
                        <p style="font-size:12px;color:#888">Qty: <?php echo $qty; ?></p>
                        <p style="font-size:14px;font-weight:700">₹<?php echo number_format($item['price'] * $qty); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary-row" style="margin-top:12px">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($subtotal); ?></span>
            </div>
            <div class="cart-summary-row">
                <span>Delivery</span>
                <span><?php echo $delivery === 0 ? '<span style="color:#388e3c;font-weight:600">FREE</span>' : '₹' . $delivery; ?></span>
            </div>
            <div class="cart-summary-row total">
                <span>Total</span>
                <span>₹<?php echo number_format($total); ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>