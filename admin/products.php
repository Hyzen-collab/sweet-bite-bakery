<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Products';
$adminActive = 'products';

// Buttons on this page: show/hide, feature, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_available') {
        db()->prepare('UPDATE products SET is_available = 1 - is_available WHERE product_id = ?')->execute([$id]);
        flash('success', 'Product visibility changed.');
    } elseif ($action === 'toggle_featured') {
        db()->prepare('UPDATE products SET is_featured = 1 - is_featured WHERE product_id = ?')->execute([$id]);
        flash('success', 'Featured setting changed.');
    } elseif ($action === 'delete') {
        $find = db()->prepare('SELECT image FROM products WHERE product_id = ?');
        $find->execute([$id]);
        delete_upload('products', $find->fetchColumn() ?: null);
        db()->prepare('DELETE FROM products WHERE product_id = ?')->execute([$id]);
        flash('success', 'Product deleted.');
    }
    redirect('admin/products.php');
}

$products = db()->query(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     ORDER BY c.display_order, p.name'
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="mb-3"><a href="<?= url('admin/product-form.php') ?>" class="btn btn-brand">+ Add product</a></div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Photo</th><th>Name</th><th>Category</th><th>Price from</th><th>Visible</th><th>Featured</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img class="thumb" src="<?= e(image_url('products', $p['image'])) ?>" alt=""></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= money($p['price_from']) ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['product_id'] ?>">
                            <input type="hidden" name="action" value="toggle_available">
                            <button class="btn btn-sm <?= $p['is_available'] ? 'btn-success' : 'btn-secondary' ?>"><?= $p['is_available'] ? 'Shown' : 'Hidden' ?></button>
                        </form>
                    </td>
                    <td>
                        <form method="post" class="d-inline">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['product_id'] ?>">
                            <input type="hidden" name="action" value="toggle_featured">
                            <button class="btn btn-sm <?= $p['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"><?= $p['is_featured'] ? 'Yes' : 'No' ?></button>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/product-form.php?id=' . (int) $p['product_id']) ?>">Edit</a>
                        <form method="post" class="d-inline" data-confirm="Delete this product? This cannot be undone.">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['product_id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
