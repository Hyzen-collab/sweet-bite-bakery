<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Categories';
$adminActive = 'categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['category_name'] ?? '');
        if ($name === '') {
            flash('danger', 'Please enter a category name.');
        } else {
            $next = (int) db()->query('SELECT COALESCE(MAX(display_order), 0) + 1 FROM categories')->fetchColumn();
            db()->prepare('INSERT INTO categories (category_name, description, display_order) VALUES (?, ?, ?)')
                ->execute([$name, trim($_POST['description'] ?? ''), $next]);
            flash('success', 'Category added.');
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $used = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $used->execute([$id]);

        if ($used->fetchColumn() > 0) {
            flash('danger', 'This category still has products. Move or delete them first.');
        } else {
            db()->prepare('DELETE FROM categories WHERE category_id = ?')->execute([$id]);
            flash('success', 'Category deleted.');
        }
    }
    redirect('admin/categories.php');
}

$categories = db()->query(
    'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id) AS total
     FROM categories c ORDER BY c.display_order'
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <table class="table align-middle mb-0">
                <thead><tr><th>Category</th><th>Products</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><strong><?= e($c['category_name']) ?></strong><br><small class="text-muted"><?= e($c['description']) ?></small></td>
                        <td><?= (int) $c['total'] ?></td>
                        <td class="text-end">
                            <form method="post" data-confirm="Delete this category?">
                                <?= csrf_field() ?><input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $c['category_id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <form method="post" class="admin-card">
            <?= csrf_field() ?><input type="hidden" name="action" value="add">
            <h2 class="h5">Add a category</h2>
            <div class="mb-3">
                <label class="form-label" for="category_name">Name</label>
                <input type="text" id="category_name" name="category_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Short description</label>
                <input type="text" id="description" name="description" class="form-control">
            </div>
            <button class="btn btn-brand">Add category</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
