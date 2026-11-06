<?php
require_once __DIR__ . '/includes/functions.php';

// Find the product from the address: product.php?id=3
$id   = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     WHERE p.product_id = ? AND p.is_available = 1'
);
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// More from the same category
$related = db()->prepare(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     WHERE p.category_id = ? AND p.product_id <> ? AND p.is_available = 1
     ORDER BY p.product_id LIMIT 3'
);
$related->execute([$product['category_id'], $id]);
$relatedProducts = $related->fetchAll();

$pageTitle       = $product['name'];
$activePage      = 'products';
$metaDescription = $product['description'];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= url('products.php') ?>">Products</a></li>
                <li class="breadcrumb-item"><a href="<?= url('products.php?cat=' . (int) $product['category_id']) ?>"><?= e($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
            </ol>
        </nav>
        <h1><?= e($product['name']) ?></h1>
    </div>
</div>

<section class="container">
    <div class="row g-5">
        <div class="col-md-6">
            <img src="<?= e(image_url('products', $product['image'])) ?>" alt="<?= e($product['name']) ?>"
                 class="img-fluid rounded-4 shadow">
        </div>
        <div class="col-md-6">
            <span class="badge badge-soft mb-2"><?= e($product['category_name']) ?></span>
            <p class="fs-3 price mb-1">From <?= money($product['price_from']) ?>
                <small class="text-muted fs-6"><?= e($product['price_unit']) ?></small></p>
            <p><?= nl2br(e($product['description'])) ?></p>

            <table class="table table-borderless">
                <?php if ($product['sizes']): ?>
                    <tr><th class="w-25">Sizes</th><td><?= e($product['sizes']) ?></td></tr>
                <?php endif; ?>
                <?php if ($product['flavours']): ?>
                    <tr><th>Flavours</th><td><?= e($product['flavours']) ?></td></tr>
                <?php endif; ?>
                <?php if ($product['lead_time']): ?>
                    <tr><th>Lead time</th><td><?= e($product['lead_time']) ?></td></tr>
                <?php endif; ?>
            </table>
            <p class="small text-muted">The final price depends on size and decoration. We will confirm the price when you order.</p>

            <a href="<?= url('order.php?product=' . (int) $product['product_id']) ?>" class="btn btn-pink btn-lg me-2 mb-2">Enquire / Order</a>
            <a href="<?= e(whatsapp_link('Hi, I would like to ask about the ' . $product['name'] . '.')) ?>"
               target="_blank" rel="noopener" class="btn btn-outline-brand btn-lg mb-2">Ask on WhatsApp</a>
        </div>
    </div>

    <?php if ($relatedProducts): ?>
        <h2 class="h4 mt-5 mb-3">You may also like</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php foreach ($relatedProducts as $item) { render_product_card($item); } ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
