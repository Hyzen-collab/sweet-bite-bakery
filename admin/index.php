<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Dashboard';
$adminActive = 'dashboard';

// Numbers for the top cards
function count_rows(string $sql): int
{
    return (int) db()->query($sql)->fetchColumn();
}

$stats = [
    'New orders'      => count_rows("SELECT COUNT(*) FROM orders WHERE status = 'New'"),
    'All orders'      => count_rows('SELECT COUNT(*) FROM orders'),
    'Products'        => count_rows('SELECT COUNT(*) FROM products'),
    'Unread messages' => count_rows('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
];

$latest = db()->query('SELECT * FROM orders ORDER BY order_id DESC LIMIT 6')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<p class="text-muted">Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?>.</p>

<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $number): ?>
        <div class="col-6 col-lg-3">
            <div class="card stat-card"><div class="card-body">
                <div class="text-muted small"><?= e($label) ?></div>
                <div class="number"><?= $number ?></div>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <h2 class="h5">Latest orders</h2>
            <?php if (!$latest): ?>
                <p class="text-muted mb-0">No orders yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>#</th><th>Customer</th><th>Item</th><th>Needed on</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($latest as $o): ?>
                            <tr>
                                <td><a href="<?= url('admin/order-view.php?id=' . (int) $o['order_id']) ?>"><?= (int) $o['order_id'] ?></a></td>
                                <td><?= e($o['customer_name']) ?></td>
                                <td><?= e($o['item_name']) ?></td>
                                <td><?= e($o['required_date']) ?></td>
                                <td><span class="badge status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card">
            <h2 class="h5">Quick links</h2>
            <div class="d-grid gap-2">
                <a class="btn btn-brand" href="<?= url('admin/product-form.php') ?>">Add a product</a>
                <a class="btn btn-outline-secondary" href="<?= url('admin/gallery.php') ?>">Upload cake photos</a>
                <a class="btn btn-outline-secondary" href="<?= url('admin/settings.php') ?>">Change opening hours / banner</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
