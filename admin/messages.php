<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Contact Messages';
$adminActive = 'messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'read') {
        db()->prepare('UPDATE messages SET is_read = 1 WHERE message_id = ?')->execute([$id]);
    } elseif (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM messages WHERE message_id = ?')->execute([$id]);
        flash('success', 'Message deleted.');
    }
    redirect('admin/messages.php');
}

$messages = db()->query('SELECT * FROM messages ORDER BY message_id DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<?php if (!$messages): ?>
    <div class="admin-card text-muted">No messages yet.</div>
<?php endif; ?>

<?php foreach ($messages as $m): ?>
    <div class="admin-card mb-3 <?= $m['is_read'] ? '' : 'border-start border-4 border-danger' ?>">
        <div class="d-flex justify-content-between flex-wrap gap-2">
            <div>
                <strong><?= e($m['name']) ?></strong>
                <span class="text-muted small">
                    <?= $m['phone'] ? ' &middot; ' . e($m['phone']) : '' ?>
                    <?= $m['email'] ? ' &middot; ' . e($m['email']) : '' ?>
                    &middot; <?= e(substr($m['created_at'], 0, 16)) ?>
                </span>
            </div>
            <div class="d-flex gap-2">
                <?php if (!$m['is_read']): ?>
                    <form method="post">
                        <?= csrf_field() ?><input type="hidden" name="action" value="read"><input type="hidden" name="id" value="<?= (int) $m['message_id'] ?>">
                        <button class="btn btn-sm btn-outline-success">Mark as read</button>
                    </form>
                <?php endif; ?>
                <form method="post" data-confirm="Delete this message?">
                    <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $m['message_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
        <p class="mt-2 mb-0"><?= nl2br(e($m['message'])) ?></p>
    </div>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
