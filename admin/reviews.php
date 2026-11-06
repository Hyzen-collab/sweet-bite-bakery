<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Customer Reviews';
$adminActive = 'reviews';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'add') {
        $name    = trim($_POST['customer_name'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        $rating  = max(1, min(5, (int) ($_POST['rating'] ?? 5)));

        if ($name === '' || $comment === '') {
            flash('danger', 'Please enter the customer name and the comment.');
        } else {
            db()->prepare('INSERT INTO reviews (customer_name, rating, comment, is_visible) VALUES (?, ?, ?, 1)')
                ->execute([$name, $rating, $comment]);
            flash('success', 'Review added.');
        }
    } elseif ($action === 'toggle') {
        db()->prepare('UPDATE reviews SET is_visible = 1 - is_visible WHERE review_id = ?')->execute([$id]);
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM reviews WHERE review_id = ?')->execute([$id]);
        flash('success', 'Review deleted.');
    }
    redirect('admin/reviews.php');
}

$reviews = db()->query('SELECT * FROM reviews ORDER BY review_id DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <?php foreach ($reviews as $r): ?>
            <div class="admin-card mb-3">
                <div class="d-flex justify-content-between">
                    <strong><?= e($r['customer_name']) ?> <span class="text-warning"><?= str_repeat('&#9733;', (int) $r['rating']) ?></span></strong>
                    <span class="badge <?= $r['is_visible'] ? 'bg-success' : 'bg-secondary' ?>"><?= $r['is_visible'] ? 'Visible' : 'Hidden' ?></span>
                </div>
                <p class="my-2"><?= e($r['comment']) ?></p>
                <form method="post" class="d-inline">
                    <?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $r['review_id'] ?>">
                    <button class="btn btn-sm btn-outline-secondary"><?= $r['is_visible'] ? 'Hide' : 'Show' ?></button>
                </form>
                <form method="post" class="d-inline" data-confirm="Delete this review?">
                    <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $r['review_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-lg-5">
        <form method="post" class="admin-card">
            <?= csrf_field() ?><input type="hidden" name="action" value="add">
            <h2 class="h5">Add a review</h2>
            <p class="small text-muted">Only add reviews that real customers gave you.</p>
            <div class="mb-3">
                <label class="form-label" for="customer_name">Customer name</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="rating">Rating</label>
                <select id="rating" name="rating" class="form-select">
                    <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="comment">Comment</label>
                <textarea id="comment" name="comment" rows="3" maxlength="400" class="form-control" required></textarea>
            </div>
            <button class="btn btn-brand">Add review</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
