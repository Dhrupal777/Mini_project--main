<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

 $productId = $_GET['id'] ?? 0;
 $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
 $stmt->execute([$productId]);
 $product = $stmt->fetch();

if (!$product) {
    header('Location: manage-products.php');
    exit();
}

 $categories = $pdo->query("SELECT * FROM categories WHERE status = 1")->fetchAll();
 $error = '';
 $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $mrp = floatval($_POST['mrp'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;
    $image = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = 'p' . time() . '.' . $ext;
            $uploadPath = '../assets/images/' . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $newName;
            }
        }
    }

    if ($name === '' || $category_id === '' || $price <= 0 || $mrp <= 0) {
        $error = 'Name, category, price and MRP are required';
    } elseif ($price > $mrp) {
        $error = 'Selling price cannot be more than MRP';
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, mrp=?, image=?, stock=?, featured=?, status=? WHERE id=?");
        if ($stmt->execute([$name, $category_id, $description, $price, $mrp, $image, $stock, $featured, $status, $productId])) {
            $success = 'Product updated successfully';
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
        } else {
            $error = 'Failed to update product';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - AapkiDukaan Admin</title>
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
            <a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="manage-users.php"><i class="fas fa-users"></i> Users</a>
            <a href="../index.php"><i class="fas fa-globe"></i> View Website</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Edit Product</h1>
                <a href="manage-products.php" style="color:#2874f0;font-size:14px;text-decoration:none"><i class="fas fa-arrow-left"></i> Back to Products</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" style="max-width:700px"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" style="max-width:700px"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div style="display:flex;gap:16px;align-items:center;margin-bottom:20px">
                    <img src="../assets/images/<?php echo $product['image']; ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e0e0e0">
                    <div class="form-group" style="margin-bottom:0;flex:1">
                        <label>Change Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Selling Price (₹)</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>MRP (₹)</label>
                        <input type="number" name="mrp" step="0.01" value="<?php echo $product['mrp']; ?>" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?>> Featured Product
                        </label>
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="status" value="1" <?php echo $product['status'] ? 'checked' : ''; ?>> Active
                        </label>
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:8px">
                    <button type="submit" class="btn-admin-primary">Update Product</button>
                    <a href="manage-products.php"><button type="button" class="btn-admin-cancel">Cancel</button></a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>