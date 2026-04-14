<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $categories = $pdo->query("SELECT * FROM categories WHERE status = 1")->fetchAll();
 $featuredProducts = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND p.featured = 1 ORDER BY RAND() LIMIT 12")->fetchAll();
 $deals = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND p.mrp > p.price ORDER BY (p.mrp - p.price) DESC LIMIT 8")->fetchAll();
 $newProducts = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 ORDER BY p.created_at DESC LIMIT 8")->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="banner-section">
    <div class="container">
        <div class="banner-slider">
            <div class="banner-track">
                <img src="assets/images/banner1.jpg" alt="Banner 1">
                <img src="assets/images/banner2.jpg" alt="Banner 2">
                <img src="assets/images/banner3.jpg" alt="Banner 3">
            </div>
            <button class="banner-btn prev"><i class="fas fa-chevron-left"></i></button>
            <button class="banner-btn next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="category.php?id=<?php echo $cat['id']; ?>" class="category-card">
                    <img src="assets/images/<?php echo $cat['image']; ?>" alt="<?php echo $cat['name']; ?>">
                    <p><?php echo $cat['name']; ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="section-header">
            <h2>Deals of the Day <span class="flash-timer"><i class="fas fa-bolt"></i> <span class="timer-display">01:00:00</span></span></h2>
            <a href="products.php">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="product-scroll">
            <?php foreach ($deals as $product):
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
                        <span class="delivery">Free delivery</span>
                        <button class="btn-add-cart" onclick="addToCart(this, <?php echo $product['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="section-header">
            <h2>Top Picks For You</h2>
            <a href="products.php">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="product-scroll">
            <?php foreach ($featuredProducts as $product):
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
                        <span class="delivery">Free delivery</span>
                        <button class="btn-add-cart" onclick="addToCart(this, <?php echo $product['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="section-header">
            <h2>Special Offers</h2>
            <a href="offers.php">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="offers-grid">
            <a href="category.php?id=1" class="offer-card">
                <img src="assets/images/offer1.jpg" alt="Offer 1">
            </a>
            <a href="category.php?id=6" class="offer-card">
                <img src="assets/images/offer2.jpg" alt="Offer 2">
            </a>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="section-header">
            <h2>Recently Added</h2>
            <a href="products.php">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="product-scroll">
            <?php foreach ($newProducts as $product):
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
                        <span class="delivery">Free delivery</span>
                        <button class="btn-add-cart" onclick="addToCart(this, <?php echo $product['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>