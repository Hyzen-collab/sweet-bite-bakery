<?php
require_once __DIR__ . '/../includes/functions.php';

// Already logged in? Go to the dashboard.
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Slow down guessing: 5 wrong tries = wait 5 minutes
    $locked = ($_SESSION['login_fails'] ?? 0) >= 5 && time() - ($_SESSION['login_last_fail'] ?? 0) < 300;

    if ($locked) {
        $error = 'Too many wrong tries. Please wait 5 minutes and try again.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']      = (int) $admin['admin_id'];
            $_SESSION['admin_name']    = $admin['full_name'];
            $_SESSION['last_activity'] = time();
            unset($_SESSION['login_fails'], $_SESSION['login_last_fail']);

            db()->prepare('UPDATE admins SET last_login = ? WHERE admin_id = ?')
                ->execute([date('Y-m-d H:i:s'), $admin['admin_id']]);

            redirect('admin/index.php');
        }

        $_SESSION['login_fails']     = ($_SESSION['login_fails'] ?? 0) + 1;
        $_SESSION['login_last_fail'] = time();
        $error = 'Wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | Sweet Bite Bakery</title>
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>
<div class="login-wrap">
    <form method="post" class="login-card">
        <?= csrf_field() ?>
        <div class="text-center mb-3">
            <img src="<?= asset('images/logo.png') ?>" alt="Sweet Bite Bakery" width="90" height="90">
            <h1 class="h4 mt-2">Admin Login</h1>
        </div>

        <?php show_flash(); ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-brand w-100">Log in</button>
        <p class="text-center small mt-3 mb-0"><a href="<?= url('index.php') ?>">&larr; Back to website</a></p>
    </form>
</div>
</body>
</html>
