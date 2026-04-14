<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

header('Content-Type: application/json');

 $cartId = $_POST['cart_id'] ?? '';
 $quantity = intval($_POST['quantity'] ?? 1);

if ($quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
    exit();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cartId, $_SESSION['user_id']]);
    $cartItem = $stmt->fetch();

    if (!$cartItem) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found']);
        exit();
    }

    if ($quantity > $cartItem['stock']) {
        echo json_encode(['status' => 'error', 'message' => 'Only ' . $cartItem['stock'] . ' items available']);
        exit();
    }

    $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $upd->execute([$quantity, $cartId]);
    echo json_encode(['status' => 'success']);
} else {
    if (strpos($cartId, 'sess_') === 0) {
        $pid = str_replace('sess_', '', $cartId);
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $product = $stmt->fetch();

        if ($product && $quantity <= $product['stock']) {
            $_SESSION['cart'][$pid] = $quantity;
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Stock limit reached']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please login']);
    }
}