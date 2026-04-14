<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

 $productId = $_POST['product_id'] ?? 0;

if (!$productId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit();
}

 $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
 $stmt->execute([$productId]);
 $product = $stmt->fetch();

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty = $existing['quantity'] + 1;
        if ($newQty > $product['stock']) {
            echo json_encode(['status' => 'error', 'message' => 'Only ' . $product['stock'] . ' items available']);
            exit();
        }
        $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $upd->execute([$newQty, $existing['id']]);
    } else {
        if ($product['stock'] < 1) {
            echo json_encode(['status' => 'error', 'message' => 'Product out of stock']);
            exit();
        }
        $ins = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $ins->execute([$_SESSION['user_id'], $productId]);
    }
} else {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        if ($_SESSION['cart'][$productId] >= $product['stock']) {
            echo json_encode(['status' => 'error', 'message' => 'Only ' . $product['stock'] . ' items available']);
            exit();
        }
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
}

 $cartCount = getCartCount();
echo json_encode(['status' => 'success', 'cart_count' => $cartCount]);