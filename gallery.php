<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Cake Designs';
$activePage      = 'gallery';
$metaDescription = 'Photos of birthday, wedding, kids and anniversary cakes made by Sweet Bite Bakery in Colombo.';

$designs = db()->query('SELECT * FROM gallery ORDER BY gallery_id DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Cake Designs</h1>
        <p>Get ideas for your next cake. Click a picture to see it bigger.</p>
    </div>
</div>

<section class="container">
    <div class="mb-4 text-center">
        <button class="btn btn-sm btn-brand active me-1 mb-1" data-filter="all">All</button>
        <?php foreach (OCCASIONS as $occasion): ?>
            <button class="btn btn-sm btn-outline-brand me-1 mb-1" data-filter="<?= e($occasion) ?>"><?= e($occasion) ?></button>
        <?php endforeach; ?>
    </div>

    <?php if (!$designs): ?>
        <p class="text-center text-muted">Pictures will be added soon.</p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        <?php foreach ($designs as $d): ?>
            <div class="col">
                <div class="gallery-item" data-occasion="<?= e($d['occasion']) ?>"
                     data-src="<?= e(image_url('gallery', $d['image'])) ?>" data-title="<?= e($d['title']) ?>">
                    <img src="<?= e(image_url('gallery', $d['image'])) ?>" alt="<?= e($d['title']) ?>" loading="lazy">
                    <div class="caption"><?= e($d['title']) ?> &middot; <?= e($d['occasion']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
        <p>Like a design? Tell us and we will make it for you.</p>
        <a href="<?= url('order.php') ?>" class="btn btn-pink btn-lg">Order a custom cake</a>
    </div>
</section>

<!-- Big picture window -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="imageModalTitle"></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalPicture" alt="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
