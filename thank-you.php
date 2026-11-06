<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = 'Thank You';
$activePage = '';
$ref        = (int) ($_GET['ref'] ?? 0);

include __DIR__ . '/includes/header.php';
?>

<section class="container text-center py-5">
    <img src="<?= asset('images/logo.png') ?>" alt="" width="120" height="120" class="mb-3">
    <h1>Thank you!</h1>
    <p class="lead">We received your request<?= $ref ? ' (reference no. ' . $ref . ')' : '' ?>.</p>
    <p>We will contact you on the phone number you gave us to confirm the price and the details. If it is urgent, please call us on
        <strong><?= e(setting('phone')) ?></strong>.</p>
    <a href="<?= url('index.php') ?>" class="btn btn-brand me-2">Back to home</a>
    <a href="<?= url('products.php') ?>" class="btn btn-outline-brand">See more products</a>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
