<?php
/**
 * Top part of every public page.
 * Before including this file, set:  $pageTitle, $activePage  (and optionally $metaDescription)
 */
$pageTitle       = $pageTitle ?? 'Sweet Bite Bakery';
$activePage      = $activePage ?? '';
$metaDescription = $metaDescription ?? 'Sweet Bite Bakery in Colombo, Sri Lanka. Birthday cakes, wedding cakes, cupcakes, pastries and fresh bread.';

$menu = [
    'home'     => ['Home', 'index.php'],
    'about'    => ['About', 'about.php'],
    'products' => ['Products', 'products.php'],
    'gallery'  => ['Cake Designs', 'gallery.php'],
    'prices'   => ['Prices', 'prices.php'],
    'contact'  => ['Contact', 'contact.php'],
    'faq'      => ['FAQ', 'faq.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(setting('shop_name', 'Sweet Bite Bakery')) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<?php if (setting('banner_on') === '1' && setting('banner_text') !== ''): ?>
    <div class="announcement text-center py-2 px-3"><?= e(setting('banner_text')) ?></div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg site-nav sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
            <img src="<?= asset('images/logo.png') ?>" alt="Sweet Bite Bakery logo" width="46" height="46">
            <span>Sweet Bite</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu"
                aria-controls="mainMenu" aria-expanded="false" aria-label="Open menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <?php foreach ($menu as $key => [$label, $link]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $activePage === $key ? ' active' : '' ?>"
                           href="<?= url($link) ?>"><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a class="btn btn-pink" href="<?= url('order.php') ?>">Order Now</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main>
