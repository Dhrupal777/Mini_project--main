<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $catId = $_GET['id'] ?? 0;
 $category = null;
 $products = [];

if ($catId) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND status = 1");
    $stmt->execute([$catId]);
    $category = $stmt->fetch();

    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND status = 1 ORDER BY id DESC");
        $stmt->execute([$catId]);
        $products = $stmt->fetchAll();
    }
}

 $categories = $pdo->query("SELECT * FROM categories WHERE status = 1")->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<?php if ($category): ?>
    <div class="page-banner">
        <h1><?php echo $category['name']; ?></h1>
        <p>Explore our <?php echo $category['name']; ?> collection</p>
    </div>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>›</span> <?php echo $category['name']; ?>
        </div>

        <div class="filter-bar">
            <select onchange="location.href='category.php?id='+this.value">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $catId == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (count($products) > 0): ?>
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
                            <span class="delivery">Free delivery</span>
                            <button class="btn-add-cart" onclick="addToCart(this, <?php echo $product['id']; ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-products">No products found in this category</div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="container">
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Category Not Found</h3>
            <p>The category you are looking for does not exist</p>
            <a href="index.php">Go to Home</a>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>