<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = 'Products';
$activePage = 'products';

$categories = db()->query('SELECT * FROM categories ORDER BY display_order')->fetchAll();

// Filters from the address bar:  products.php?cat=2&q=chocolate
$selectedCat = (int) ($_GET['cat'] ?? 0);
$search      = trim($_GET['q'] ?? '');

$sql = 'SELECT p.*, c.category_name FROM products p
        JOIN categories c ON c.category_id = p.category_id
        WHERE p.is_available = 1';
$params = [];

if ($selectedCat > 0) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $selectedCat;
}
if ($search !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$sql .= ' ORDER BY c.display_order, p.name';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Our Products</h1>
        <p>Cakes, cupcakes, pastries and bread. Prices are starting prices in Sri Lankan Rupees.</p>
    </div>
</div>

<section class="container">
    <div class="row g-3 align-items-center mb-4">
        <div class="col-lg-8">
            <a href="<?= url('products.php') ?>" class="btn btn-sm <?= $selectedCat === 0 ? 'btn-brand' : 'btn-outline-brand' ?> me-1 mb-1">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('products.php?cat=' . (int) $cat['category_id']) ?>"
                   class="btn btn-sm <?= $selectedCat === (int) $cat['category_id'] ? 'btn-brand' : 'btn-outline-brand' ?> me-1 mb-1">
                    <?= e($cat['category_name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
            <form method="get" class="d-flex gap-2">
                <?php if ($selectedCat > 0): ?><input type="hidden" name="cat" value="<?= $selectedCat ?>"><?php endif; ?>
                <input type="search" name="q" class="form-control" placeholder="Search cakes..." value="<?= e($search) ?>">
                <button class="btn btn-pink" type="submit">Search</button>
            </form>
        </div>
    </div>

    <?php if (!$products): ?>
        <div class="alert alert-warning">No products found. Try another word or <a href="<?= url('products.php') ?>">see all products</a>.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php foreach ($products as $product) { render_product_card($product); } ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-light border mt-5 text-center">
        Can't find your design? <a href="<?= url('order.php') ?>">Send us a custom cake request</a>.
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
