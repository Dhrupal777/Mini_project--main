<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $productId = $_GET['id'] ?? 0;
 $product = null;

if ($productId) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
}

if (!$product) {
    header('Location: products.php');
    exit();
}

 $discount = round(($product['mrp'] - $product['price']) / $product['mrp'] * 100);

 $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 1 ORDER BY RAND() LIMIT 6");
 $stmt->execute([$product['category_id'], $productId]);
 $relatedProducts = $stmt->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> <a href="category.php?id=<?php echo $product['category_id']; ?>"><?php echo $product['cat_name']; ?></a> <span>›</span> <?php echo $product['name']; ?>
    </div>

    <div class="product-detail-wrapper">
        <div>
            <img class="product-detail-img" src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        </div>
        <div class="product-detail-info">
            <p class="pd-cat"><?php echo $product['cat_name']; ?></p>
            <h1><?php echo $product['name']; ?></h1>
            <div class="pd-price-row">
                <span class="pd-price">₹<?php echo number_format($product['price']); ?></span>
                <span class="pd-mrp">₹<?php echo number_format($product['mrp']); ?></span>
                <span class="pd-discount"><?php echo $discount; ?>% off</span>
            </div>
            <p class="pd-desc"><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available for this product.')); ?></p>

            <div class="pd-highlights">
                <h3>Key Features</h3>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Brand Assured Product</li>
                    <li><i class="fas fa-check-circle"></i> Free Delivery on this item</li>
                    <li><i class="fas fa-check-circle"></i> Easy 7 Day Returns</li>
                    <li><i class="fas fa-check-circle"></i> Cash on Delivery Available</li>
                    <?php if ($product['stock'] > 0): ?>
                        <li><i class="fas fa-check-circle"></i> In Stock - <?php echo $product['stock']; ?> units left</li>
                    <?php else: ?>
                        <li style="color:#e74c3c"><i class="fas fa-times-circle" style="color:#e74c3c"></i> Out of Stock</li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if ($product['stock'] > 0): ?>
                <div class="pd-actions">
                    <button class="btn-cart-lg" onclick="addToCart(this, <?php echo $product['id']; ?>)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    <a href="checkout.php?buy_now=<?php echo $product['id']; ?>" class="btn-buy-lg"><i class="fas fa-bolt"></i> Buy Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($relatedProducts) > 0): ?>
        <div class="section">
            <div class="section-header">
                <h2>Similar Products</h2>
                <a href="category.php?id=<?php echo $product['category_id']; ?>">View All <i class="fas fa-chevron-right"></i></a>
            </div>
            <div class="product-scroll">
                <?php foreach ($relatedProducts as $rp):
                    $rpDiscount = round(($rp['mrp'] - $rp['price']) / $rp['mrp'] * 100);
                ?>
                    <div class="product-card">
                        <a href="product-detail.php?id=<?php echo $rp['id']; ?>">
                            <img class="product-card-img" src="assets/images/<?php echo $rp['image']; ?>" alt="<?php echo $rp['name']; ?>">
                        </a>
                        <div class="product-card-body">
                            <h4><?php echo $rp['name']; ?></h4>
                            <div class="price-row">
                                <span class="price">₹<?php echo number_format($rp['price']); ?></span>
                                <span class="mrp">₹<?php echo number_format($rp['mrp']); ?></span>
                                <span class="discount"><?php echo $rpDiscount; ?>% off</span>
                            </div>
                            <button class="btn-add-cart" onclick="addToCart(this, <?php echo $rp['id']; ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>