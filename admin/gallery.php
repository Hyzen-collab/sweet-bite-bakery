<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Cake Design Gallery';
$adminActive = 'gallery';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $title    = trim($_POST['title'] ?? '');
        $occasion = in_array($_POST['occasion'] ?? '', OCCASIONS, true) ? $_POST['occasion'] : OCCASIONS[0];
        [$file, $error] = save_upload($_FILES['image'] ?? [], 'gallery');

        if ($title === '') {
            flash('danger', 'Please enter a title for the picture.');
        } elseif ($error || !$file) {
            flash('danger', $error ?: 'Please choose a picture to upload.');
        } else {
            db()->prepare('INSERT INTO gallery (title, image, occasion, created_at) VALUES (?, ?, ?, ?)')
                ->execute([$title, $file, $occasion, date('Y-m-d H:i:s')]);
            flash('success', 'Picture added to the gallery.');
        }
    } elseif ($action === 'delete') {
        $id   = (int) ($_POST['id'] ?? 0);
        $find = db()->prepare('SELECT image FROM gallery WHERE gallery_id = ?');
        $find->execute([$id]);
        delete_upload('gallery', $find->fetchColumn() ?: null);
        db()->prepare('DELETE FROM gallery WHERE gallery_id = ?')->execute([$id]);
        flash('success', 'Picture deleted.');
    }
    redirect('admin/gallery.php');
}

$pictures = db()->query('SELECT * FROM gallery ORDER BY gallery_id DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<form method="post" enctype="multipart/form-data" class="admin-card mb-4">
    <?= csrf_field() ?><input type="hidden" name="action" value="upload">
    <h2 class="h5">Upload a new picture</h2>
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="occasion">Occasion</label>
            <select id="occasion" name="occasion" class="form-select">
                <?php foreach (OCCASIONS as $o): ?><option><?= e($o) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="image">Picture (max 2 MB)</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" required>
        </div>
        <div class="col-md-2"><button class="btn btn-brand w-100">Upload</button></div>
    </div>
</form>

<div class="row row-cols-2 row-cols-md-4 row-cols-xl-6 g-3">
    <?php foreach ($pictures as $pic): ?>
        <div class="col">
            <div class="admin-card p-2 h-100">
                <img class="thumb-big mb-2" src="<?= e(image_url('gallery', $pic['image'])) ?>" alt="<?= e($pic['title']) ?>">
                <div class="small fw-bold"><?= e($pic['title']) ?></div>
                <div class="small text-muted mb-2"><?= e($pic['occasion']) ?></div>
                <form method="post" data-confirm="Delete this picture?">
                    <?= csrf_field() ?><input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $pic['gallery_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
