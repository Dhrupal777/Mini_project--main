<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=profile.php');
    exit();
}

 $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
 $stmt->execute([$_SESSION['user_id']]);
 $user = $stmt->fetch();

 $success = '';
 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    if ($name === '' || $email === '' || $phone === '') {
        $error = 'Name, email and phone are required';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ?, pincode = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $phone, $address, $city, $pincode, $_SESSION['user_id']])) {
            $_SESSION['user_name'] = $name;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            $success = 'Profile updated successfully';
        } else {
            $error = 'Something went wrong';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>›</span> My Profile
    </div>

    <div class="profile-page">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <img src="assets/images/user-avatar.png" alt="User">
            </div>
            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <div class="profile-menu">
                <a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a>
                <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="profile-content">
            <h2>Personal Information</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" value="<?php echo htmlspecialchars($user['pincode'] ?? ''); ?>" maxlength="6">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="max-width:200px">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>