<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle  = 'Home';
$activePage = 'home';

// Categories with one picture each
$categories = db()->query('SELECT * FROM categories ORDER BY display_order')->fetchAll();
$pictureStmt = db()->prepare('SELECT image FROM products WHERE category_id = ? AND is_available = 1 ORDER BY product_id LIMIT 1');

// Featured products
$featured = db()->query(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     WHERE p.is_available = 1 AND p.is_featured = 1
     ORDER BY p.product_id LIMIT 6'
)->fetchAll();

// A few gallery pictures and the reviews
$designs = db()->query('SELECT * FROM gallery ORDER BY gallery_id DESC LIMIT 4')->fetchAll();
$reviews = db()->query('SELECT * FROM reviews WHERE is_visible = 1 ORDER BY review_id LIMIT 3')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1>Sweet moments,<br>baked fresh in Colombo</h1>
        <p class="lead my-3">Birthday cakes, wedding cakes, cupcakes, pastries and fresh bread. Made with good ingredients and a lot of love.</p>
        <a href="<?= url('products.php') ?>" class="btn btn-brand me-2 mb-2">View Cakes</a>
        <a href="<?= e(whatsapp_link('Hi, I would like to order a cake.')) ?>" target="_blank" rel="noopener"
           class="btn btn-pink mb-2">Order on WhatsApp</a>
    </div>
</section>

<!-- Categories -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>What we bake</h2>
            <p>Something sweet for every occasion</p>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php foreach ($categories as $cat):
                $pictureStmt->execute([$cat['category_id']]);
                $picture = $pictureStmt->fetchColumn(); ?>
                <div class="col">
                    <a class="category-tile" href="<?= url('products.php?cat=' . (int) $cat['category_id']) ?>">
                        <img src="<?= e(image_url('products', $picture)) ?>" alt="<?= e($cat['category_name']) ?>" loading="lazy">
                        <span><?= e($cat['category_name']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured products -->
<section class="section section-cream">
    <div class="container">
        <div class="section-title">
            <h2>Our favourites</h2>
            <p>The cakes and treats our customers order the most</p>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php foreach ($featured as $product) { render_product_card($product); } ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= url('products.php') ?>" class="btn btn-outline-brand px-4">See all products</a>
        </div>
    </div>
</section>

<!-- Why choose us -->
<section class="section">
    <div class="container">
        <div class="section-title"><h2>Why choose Sweet Bite?</h2></div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="icon">1</div>
                    <h3 class="h5">Baked fresh daily</h3>
                    <p class="mb-0 text-muted">We bake every morning with fresh butter, eggs and quality ingredients. No shortcuts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="icon">2</div>
                    <h3 class="h5">Made to your design</h3>
                    <p class="mb-0 text-muted">Show us a picture or tell us your idea. We will decorate your cake just the way you like.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="icon">3</div>
                    <h3 class="h5">Easy to order</h3>
                    <p class="mb-0 text-muted">Send the order form, call us or message on WhatsApp. We deliver around Colombo.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Opening hours and offers -->
<section class="section section-cream">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <div class="hours-box bg-white">
                    <h2 class="h4">Opening hours</h2>
                    <p class="mb-1"><?= e(setting('hours_weekdays')) ?></p>
                    <p class="mb-2"><?= e(setting('hours_sunday')) ?></p>
                    <p class="small text-muted mb-0"><?= e(setting('hours_note')) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <h2 class="h4">Festival season orders</h2>
                <p>Avurudu, Vesak, Christmas and wedding season are our busiest times. Book your cake early so we can keep your date for you.</p>
                <a href="<?= url('order.php') ?>" class="btn btn-pink">Book a custom cake</a>
            </div>
        </div>
    </div>
</section>

<!-- Cake designs -->
<?php if ($designs): ?>
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Cake designs</h2>
            <p>A few cakes we made recently</p>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($designs as $d): ?>
                <div class="col">
                    <a href="<?= url('gallery.php') ?>" class="gallery-item d-block">
                        <img src="<?= e(image_url('gallery', $d['image'])) ?>" alt="<?= e($d['title']) ?>" loading="lazy">
                        <div class="caption"><?= e($d['title']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= url('gallery.php') ?>" class="btn btn-outline-brand px-4">More designs</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reviews -->
<?php if ($reviews): ?>
<section class="section section-cream">
    <div class="container">
        <div class="section-title"><h2>What customers say</h2></div>
        <div class="row g-4">
            <?php foreach ($reviews as $r): ?>
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="stars"><?= str_repeat('&#9733;', (int) $r['rating']) . str_repeat('&#9734;', 5 - (int) $r['rating']) ?></div>
                        <p class="my-2">"<?= e($r['comment']) ?>"</p>
                        <strong class="small"><?= e($r['customer_name']) ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
