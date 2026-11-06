<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Prices';
$activePage      = 'prices';
$metaDescription = 'Starting prices for cakes, cupcakes, pastries and bread at Sweet Bite Bakery, Colombo.';

$categories = db()->query('SELECT * FROM categories ORDER BY display_order')->fetchAll();
$itemStmt   = db()->prepare('SELECT * FROM products WHERE category_id = ? AND is_available = 1 ORDER BY name');

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Price List</h1>
        <p>Starting prices in Sri Lankan Rupees (Rs.)</p>
    </div>
</div>

<section class="container">
    <?php foreach ($categories as $cat):
        $itemStmt->execute([$cat['category_id']]);
        $items = $itemStmt->fetchAll();
        if (!$items) { continue; } ?>
        <h2 class="h4 mt-4"><?= e($cat['category_name']) ?></h2>
        <div class="table-responsive">
            <table class="table table-striped price-table align-middle">
                <thead>
                    <tr><th>Item</th><th>Sizes</th><th class="text-end">Price from</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><a href="<?= url('product.php?id=' . (int) $item['product_id']) ?>"><?= e($item['name']) ?></a></td>
                            <td><?= e($item['sizes']) ?></td>
                            <td class="text-end price"><?= money($item['price_from']) ?>
                                <small class="text-muted"><?= e($item['price_unit']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <div class="alert alert-light border mt-4">
        <strong>Please note:</strong> these are starting prices. The final price depends on the size, flavour and
        decoration of your cake. Delivery charges depend on the distance. Contact us for a quotation.
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
