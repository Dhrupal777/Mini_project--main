<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

header('Content-Type: application/json');

 $cartId = $_POST['cart_id'] ?? '';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $_SESSION['user_id']]);
    echo json_encode(['status' => 'success']);
} else {
    if (strpos($cartId, 'sess_') === 0) {
        $pid = str_replace('sess_', '', $cartId);
        unset($_SESSION['cart'][$pid]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please login']);
    }
}