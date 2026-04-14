<?php
require_once '../config/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid admin credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AapkiDukaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#1a1a2e; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .admin-login-box { background:white; padding:40px; border-radius:12px; width:100%; max-width:400px; box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .admin-login-box h2 { font-size:22px; color:#1a1a2e; margin-bottom:4px; }
        .admin-login-box p { font-size:13px; color:#888; margin-bottom:24px; }
        .admin-login-box .brand { text-align:center; margin-bottom:24px; }
        .admin-login-box .brand span:first-child { font-size:24px; font-weight:800; color:#ffdd00; }
        .admin-login-box .brand span:last-child { font-size:24px; font-weight:800; color:#1a1a2e; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:13px; font-weight:500; color:#555; margin-bottom:6px; }
        .form-group input { width:100%; padding:10px 14px; border:1.5px solid #e0e0e0; border-radius:6px; font-size:14px; font-family:'Poppins',sans-serif; outline:none; }
        .form-group input:focus { border-color:#2874f0; }
        .btn-admin { width:100%; padding:12px; background:#2874f0; color:white; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif; }
        .btn-admin:hover { background:#1a5dc8; }
        .alert { padding:12px; border-radius:6px; font-size:13px; margin-bottom:16px; }
        .alert-error { background:#ffeaea; color:#e74c3c; border:1px solid #ffcccc; }
        .back-link { text-align:center; margin-top:16px; }
        .back-link a { color:#2874f0; font-size:13px; text-decoration:none; }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <div class="brand">
            <span>Aapki</span><span>Dukaan</span>
        </div>
        <h2>Admin Login</h2>
        <p>Enter your admin credentials</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-admin">Login</button>
        </form>
        <div class="back-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>
</body>
</html>