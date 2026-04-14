<?php
function getCartCount() {
    if (!isset($_SESSION['user_id'])) {
        if (isset($_SESSION['cart'])) {
            return array_sum($_SESSION['cart']);
        }
        return 0;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['total'] ? $result['total'] : 0;
}

function getCartTotal() {
    if (!isset($_SESSION['user_id'])) {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            global $pdo;
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$pid]);
                $product = $stmt->fetch();
                if ($product) {
                    $total += $product['price'] * $qty;
                }
            }
        }
        return $total;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['total'] ? $result['total'] : 0;
}

function mergeSessionCartToDb() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart'])) {
        return;
    }
    global $pdo;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $pid]);
        $existing = $stmt->fetch();
        if ($existing) {
            $newQty = $existing['quantity'] + $qty;
            $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $upd->execute([$newQty, $existing['id']]);
        } else {
            $ins = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $ins->execute([$_SESSION['user_id'], $pid, $qty]);
        }
    }
    unset($_SESSION['cart']);
}
?>