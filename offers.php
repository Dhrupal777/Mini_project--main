<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $offerProducts = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND p.mrp > p.price ORDER BY (p.mrp - p.price) / p.mrp DESC LIMIT 12")->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-banner" style="background:linear-gradient(135deg,#ff6b6b,#ee5a24)">
    <h1><i class="fas fa-percentage"></i> Offers & Deals</h1>
    <p>Best deals across all categories</p>
</div>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> Offers
    </div>

    <div class="offers-grid" style="margin-bottom:24px">
        <a href="category.php?id=1" class="offer-card">
            <img src="assets/images/offer1.jpg" alt="Grocery Offers">
        </a>
        <a href="category.php?id=6" class="offer-card">
            <img src="assets/images/offer2.jpg" alt="Mobile Offers">
        </a>
    </div>

    <div class="section-header">
        <h2>Best Discounts For You</h2>
    </div>

    <div class="product-grid">
        <?php foreach ($offerProducts as $product):
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
</div>

<?php require_once 'includes/footer.php'; ?>