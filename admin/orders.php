<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Orders and Enquiries';
$adminActive = 'orders';

// Filters
$status = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql    = 'SELECT * FROM orders WHERE 1 = 1';
$params = [];

if (in_array($status, ORDER_STATUSES, true)) {
    $sql .= ' AND status = ?';
    $params[] = $status;
}
if ($search !== '') {
    $sql .= ' AND (customer_name LIKE ? OR phone LIKE ? OR item_name LIKE ?)';
    array_push($params, "%$search%", "%$search%", "%$search%");
}
$sql .= ' ORDER BY order_id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<form method="get" class="admin-card mb-3">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <?php foreach (ORDER_STATUSES as $s): ?>
                    <option <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <input type="search" name="q" class="form-control" placeholder="Search name, phone or item" value="<?= e($search) ?>">
        </div>
        <div class="col-md-3"><button class="btn btn-brand w-100">Filter</button></div>
    </div>
</form>

<div class="admin-card">
    <?php if (!$orders): ?>
        <p class="mb-0 text-muted">No orders found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>#</th><th>Received</th><th>Customer</th><th>Phone</th><th>Item</th><th>Needed on</th><th>Type</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= (int) $o['order_id'] ?></td>
                        <td class="text-nowrap"><?= e(substr($o['created_at'], 0, 16)) ?></td>
                        <td><?= e($o['customer_name']) ?></td>
                        <td><?= e($o['phone']) ?></td>
                        <td><?= e($o['item_name']) ?></td>
                        <td class="text-nowrap"><?= e($o['required_date']) ?></td>
                        <td><?= e(ucfirst($o['delivery_type'])) ?></td>
                        <td><span class="badge status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= url('admin/order-view.php?id=' . (int) $o['order_id']) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
