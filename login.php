<?php
require_once 'config/db.php';
require_once 'includes/cart-functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            mergeSessionCartToDb();

            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
            }
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="auth-page">
    <div class="auth-box">
        <h2>Login</h2>
        <p class="auth-sub">Welcome back to AapkiDukaan</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="auth-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>