<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $q = trim($_GET['q'] ?? '');

 $products = [];
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND (p.name LIKE ? OR p.description LIKE ?) ORDER BY p.id DESC");
    $stmt->execute([$like, $like]);
    $products = $stmt->fetchAll();
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-banner">
    <h1>Search Results</h1>
    <p><?php echo $q ? 'Showing results for "' . htmlspecialchars($q) . '"' : 'Please enter a search term'; ?></p>
</div>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> Search
    </div>

    <?php if (count($products) > 0): ?>
        <p style="margin-bottom:16px;font-size:14px;color:#888"><?php echo count($products); ?> products found</p>
        <div class="product-grid">
            <?php foreach ($products as $product):
                $discount = round(($product['mrp'] - $product['price']) / $product['mrp'] * 100);
            ?>
                <div class="product-card">
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                        <img class="product-card-img" src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                    <div class="product-card-body">
                        <h4><?php echo $product['name']; ?></h4>
                        <div class="price-row">
                            <span class="price">₹<?php echo number_format($product['price']); ?></span>
                            <span class="mrp">₹<?php echo number_format($product['mrp']); ?></span>
                            <span class="discount"><?php echo $discount; ?>% off</span>
                        </div>
                        <button class="btn-add-cart" onclick="addToCart(this, <?php echo $product['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($q !== ''): ?>
        <div class="no-products">No products found for "<?php echo htmlspecialchars($q); ?>"</div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>