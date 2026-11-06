<?php
require_once __DIR__ . '/includes/functions.php';

http_response_code(404);
$pageTitle  = 'Page Not Found';
$activePage = '';

include __DIR__ . '/includes/header.php';
?>

<section class="container text-center py-5">
    <h1 class="display-3">404</h1>
    <p class="lead">Sorry, we could not find that page. Maybe the cake was eaten already!</p>
    <a href="<?= url('index.php') ?>" class="btn btn-pink me-2">Go to home</a>
    <a href="<?= url('products.php') ?>" class="btn btn-outline-brand">See products</a>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
