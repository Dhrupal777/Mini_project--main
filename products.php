<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

 $where = "WHERE p.status = 1";
 $params = [];

if (isset($_GET['category']) && $_GET['category'] !== '') {
    $where .= " AND p.category_id = ?";
    $params[] = $_GET['category'];
}

 $sort = $_GET['sort'] ?? 'default';
if ($sort === 'price-low') {
    $order = "ORDER BY p.price ASC";
} elseif ($sort === 'price-high') {
    $order = "ORDER BY p.price DESC";
} elseif ($sort === 'newest') {
    $order = "ORDER BY p.created_at DESC";
} elseif ($sort === 'discount') {
    $order = "ORDER BY (p.mrp - p.price) DESC";
} else {
    $order = "ORDER BY p.id DESC";
}

 $sql = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id $where $order";
 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $products = $stmt->fetchAll();

 $categories = $pdo->query("SELECT * FROM categories WHERE status = 1")->fetchAll();

 $selectedCat = $_GET['category'] ?? '';
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-banner">
    <h1>All Products</h1>
    <p>Browse our complete collection</p>
</div>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> All Products
    </div>

    <div class="filter-bar">
        <select onchange="location.href='products.php?category='+this.value+'&sort=<?php echo $sort; ?>'">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $selectedCat == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="sort-options">
        <a href="products.php?category=<?php echo $selectedCat; ?>&sort=default" class="<?php echo $sort === 'default' ? 'active' : ''; ?>">Relevance</a>
        <a href="products.php?category=<?php echo $selectedCat; ?>&sort=price-low" class="<?php echo $sort === 'price-low' ? 'active' : ''; ?>">Price: Low to High</a>
        <a href="products.php?category=<?php echo $selectedCat; ?>&sort=price-high" class="<?php echo $sort === 'price-high' ? 'active' : ''; ?>">Price: High to Low</a>
        <a href="products.php?category=<?php echo $selectedCat; ?>&sort=newest" class="<?php echo $sort === 'newest' ? 'active' : ''; ?>">Newest First</a>
        <a href="products.php?category=<?php echo $selectedCat; ?>&sort=discount" class="<?php echo $sort === 'discount' ? 'active' : ''; ?>">Best Discount</a>
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
        <div class="no-products">No products found</div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>