<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'About Us';
$activePage      = 'about';
$metaDescription = 'The story of Sweet Bite Bakery, a small family bakery in Colombo, Sri Lanka.';

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>About Sweet Bite Bakery</h1>
        <p>A small bakery with a big love for cakes</p>
    </div>
</div>

<section class="container">
    <div class="row g-5 align-items-center">
        <div class="col-md-6">
            <img src="<?= asset('images/about/shop.jpg') ?>" alt="Sweet Bite Bakery shop front" class="img-fluid rounded-4 shadow">
        </div>
        <div class="col-md-6">
            <h2>Our story</h2>
            <p>Sweet Bite Bakery started as a small home kitchen in Colombo. Friends and family kept asking for our birthday cakes, and soon neighbours, office teams and wedding couples were asking too.</p>
            <p>Today we are a small bakery shop that still bakes the same way: fresh every morning, with real butter and eggs, and with every cake decorated by hand.</p>
            <p>We sell birthday cakes, wedding cakes, cupcakes, pastries and fresh bread, and we love making a custom cake that matches your idea.</p>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-md-4">
            <div class="feature-box">
                <div class="icon">&#9829;</div>
                <h3 class="h5">Fresh ingredients</h3>
                <p class="text-muted mb-0">Butter, fresh eggs and quality flour. Nothing is baked until it is needed.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <div class="icon">&#10003;</div>
                <h3 class="h5">Clean and hygienic</h3>
                <p class="text-muted mb-0">Our kitchen is cleaned daily and our staff follow food safety rules at every step.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <div class="icon">&#9733;</div>
                <h3 class="h5">Made for you</h3>
                <p class="text-muted mb-0">Tell us your flavour, size and design. We will help you choose the right cake.</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?= url('order.php') ?>" class="btn btn-pink btn-lg">Order your cake</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
