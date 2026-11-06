<?php
/**
 * Top part of every admin page.
 * Before including, set:  $adminTitle, $adminActive
 */
$adminTitle  = $adminTitle ?? 'Admin';
$adminActive = $adminActive ?? '';

$newOrders = (int) db()->query("SELECT COUNT(*) FROM orders WHERE status = 'New'")->fetchColumn();
$newMessages = (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();

$adminMenu = [
    'dashboard'  => ['Dashboard', 'admin/index.php', 0],
    'orders'     => ['Orders', 'admin/orders.php', $newOrders],
    'products'   => ['Products', 'admin/products.php', 0],
    'categories' => ['Categories', 'admin/categories.php', 0],
    'gallery'    => ['Gallery', 'admin/gallery.php', 0],
    'messages'   => ['Messages', 'admin/messages.php', $newMessages],
    'reviews'    => ['Reviews', 'admin/reviews.php', 0],
    'settings'   => ['Settings', 'admin/settings.php', 0],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($adminTitle) ?> | Sweet Bite Admin</title>
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg admin-nav">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('admin/index.php') ?>">
            <img src="<?= asset('images/logo.png') ?>" alt="" width="36" height="36">
            <span>Sweet Bite Admin</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-label="Open menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminMenu">
            <ul class="navbar-nav me-auto">
                <?php foreach ($adminMenu as $key => [$label, $link, $count]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $adminActive === $key ? ' active' : '' ?>" href="<?= url($link) ?>">
                            <?= e($label) ?>
                            <?php if ($count > 0): ?><span class="badge rounded-pill bg-danger"><?= $count ?></span><?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-sm btn-outline-light me-2" href="<?= url('index.php') ?>" target="_blank">View website</a>
            <a class="btn btn-sm btn-light" href="<?= url('admin/logout.php') ?>">Log out</a>
        </div>
    </div>
</nav>

<main class="container-fluid py-4 px-lg-4">
    <h1 class="h3 mb-3"><?= e($adminTitle) ?></h1>
    <?php show_flash(); ?>
