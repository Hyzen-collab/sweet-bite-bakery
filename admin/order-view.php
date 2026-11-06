<?php
require_once __DIR__ . '/includes/auth.php';

$adminActive = 'orders';
$id = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM orders WHERE order_id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    flash('danger', 'Order not found.');
    redirect('admin/orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (($_POST['action'] ?? '') === 'delete') {
        delete_upload('orders', $order['reference_image']);
        db()->prepare('DELETE FROM orders WHERE order_id = ?')->execute([$id]);
        flash('success', 'Order deleted.');
        redirect('admin/orders.php');
    }

    $newStatus = $_POST['status'] ?? '';
    if (in_array($newStatus, ORDER_STATUSES, true)) {
        db()->prepare('UPDATE orders SET status = ?, admin_note = ? WHERE order_id = ?')
            ->execute([$newStatus, trim($_POST['admin_note'] ?? ''), $id]);
        flash('success', 'Order updated.');
    }
    redirect('admin/order-view.php?id=' . $id);
}

$adminTitle = 'Order #' . $order['order_id'];
$message    = 'Hello ' . $order['customer_name'] . ', this is Sweet Bite Bakery about your order #' . $order['order_id'] . ' (' . $order['item_name'] . ').';

include __DIR__ . '/includes/header.php';
?>

<a href="<?= url('admin/orders.php') ?>" class="btn btn-sm btn-outline-secondary mb-3">&larr; All orders</a>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <table class="table table-borderless mb-0">
                <tr><th class="w-25">Customer</th><td><?= e($order['customer_name']) ?></td></tr>
                <tr><th>Phone</th><td><?= e($order['phone']) ?>
                    <a class="btn btn-sm btn-success ms-2" target="_blank" rel="noopener"
                       href="<?= e(whatsapp_link($message, $order['phone'])) ?>">WhatsApp</a></td></tr>
                <tr><th>Email</th><td><?= e($order['email'] ?: '-') ?></td></tr>
                <tr><th>Item</th><td><?= e($order['item_name']) ?></td></tr>
                <tr><th>Size / Flavour</th><td><?= e($order['size'] ?: '-') ?> / <?= e($order['flavour'] ?: '-') ?></td></tr>
                <tr><th>Message on cake</th><td><?= e($order['cake_message'] ?: '-') ?></td></tr>
                <tr><th>Design idea</th><td><?= nl2br(e($order['design_notes'] ?: '-')) ?></td></tr>
                <tr><th>Needed on</th><td><?= e($order['required_date']) ?> <?= e($order['required_time']) ?></td></tr>
                <tr><th>Pickup / Delivery</th><td><?= e(ucfirst($order['delivery_type'])) ?></td></tr>
                <?php if ($order['delivery_type'] === 'delivery'): ?>
                    <tr><th>Address</th><td><?= e($order['address']) ?></td></tr>
                <?php endif; ?>
                <tr><th>Received</th><td><?= e($order['created_at']) ?></td></tr>
            </table>
            <?php if ($order['reference_image']): ?>
                <hr>
                <p class="fw-bold mb-2">Reference photo</p>
                <img class="thumb-big" style="max-height:320px" src="<?= e(image_url('orders', $order['reference_image'])) ?>" alt="Reference photo">
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <form method="post" class="admin-card mb-3">
            <?= csrf_field() ?>
            <h2 class="h5">Status and notes</h2>
            <div class="mb-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <?php foreach (ORDER_STATUSES as $s): ?>
                        <option <?= $order['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="admin_note">Your note (customers cannot see this)</label>
                <textarea id="admin_note" name="admin_note" rows="4" class="form-control"><?= e($order['admin_note']) ?></textarea>
            </div>
            <button class="btn btn-brand">Save</button>
        </form>

        <form method="post" data-confirm="Delete this order for good?">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete">
            <button class="btn btn-outline-danger btn-sm">Delete order</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
