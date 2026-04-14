<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';
 $cartCount = getCartCount();
 $searchQuery = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AapkiDukaan - Online Shopping</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="top-header">
    <div class="header-top-strip">
        <div class="container">
            <p><i class="fas fa-truck"></i> Free Delivery on orders above ₹499 | <i class="fas fa-undo"></i> Easy Returns within 7 days</p>
        </div>
    </div>
    <nav class="main-nav">
        <div class="container nav-flex">
            <a href="index.php" class="logo">
                <span class="logo-aapki">Aapki</span><span class="logo-dukaan">Dukaan</span>
            </a>
            <form class="search-box" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search for products, brands and more..." value="<?php echo $searchQuery; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="nav-dropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars(substr($_SESSION['user_name'], 0, 10)); ?></a>
                        <div class="dropdown-menu">
                            <a href="profile.php">My Profile</a>
                            <a href="orders.php">My Orders</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i> Login</a>
                    <a href="signup.php" class="btn-signup">Sign Up</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>
    <div class="category-nav">
        <div class="container">
            <div class="cat-nav-scroll">
                <a href="category.php?id=1"><i class="fas fa-shopping-basket"></i> Grocery</a>
                <a href="category.php?id=6"><i class="fas fa-mobile-alt"></i> Mobiles</a>
                <a href="category.php?id=2"><i class="fas fa-laptop"></i> Electronics</a>
                <a href="category.php?id=3"><i class="fas fa-tshirt"></i> Fashion</a>
                <a href="category.php?id=4"><i class="fas fa-couch"></i> Home & Kitchen</a>
                <a href="category.php?id=5"><i class="fas fa-spa"></i> Beauty</a>
                <a href="offers.php"><i class="fas fa-percentage"></i> Offers</a>
            </div>
        </div>
    </div>
</header>
<main></main>