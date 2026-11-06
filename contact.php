<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Contact & Location';
$activePage      = 'contact';
$metaDescription = 'Contact Sweet Bite Bakery in Colombo. Address, phone, WhatsApp, opening hours and map.';

$form   = ['name' => '', 'phone' => '', 'email' => '', 'message' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (!empty($_POST['website'])) { // spam bots fill this hidden field
        redirect('index.php');
    }

    foreach ($form as $key => $unused) {
        $form[$key] = trim($_POST[$key] ?? '');
    }

    if ($form['name'] === '') {
        $errors[] = 'Please enter your name.';
    }
    if ($form['phone'] !== '' && !is_valid_phone($form['phone'])) {
        $errors[] = 'The phone number does not look right.';
    }
    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The email address does not look right.';
    }
    if ($form['phone'] === '' && $form['email'] === '') {
        $errors[] = 'Please give a phone number or an email so we can reply.';
    }
    if (strlen($form['message']) < 5) {
        $errors[] = 'Please write your message.';
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO messages (name, phone, email, message, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$form['name'], $form['phone'], $form['email'], $form['message'], date('Y-m-d H:i:s')]);

        flash('success', 'Thank you! We received your message and will reply soon.');
        redirect('contact.php');
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Contact &amp; Location</h1>
        <p>We would love to hear from you</p>
    </div>
</div>

<section class="container">
    <?php show_flash(); ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <h2 class="h4">Visit or call us</h2>
            <p class="mb-1"><strong>Address:</strong> <?= e(setting('address')) ?></p>
            <p class="mb-1"><strong>Phone:</strong> <a href="tel:<?= e(preg_replace('/\s/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
            <p class="mb-1"><strong>WhatsApp:</strong> <a href="<?= e(whatsapp_link()) ?>" target="_blank" rel="noopener"><?= e(setting('whatsapp')) ?></a></p>
            <p class="mb-3"><strong>Email:</strong> <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>

            <div class="hours-box mb-3">
                <h3 class="h6">Opening hours</h3>
                <p class="mb-1"><?= e(setting('hours_weekdays')) ?></p>
                <p class="mb-2"><?= e(setting('hours_sunday')) ?></p>
                <p class="small text-muted mb-0"><?= e(setting('hours_note')) ?></p>
            </div>

            <a href="<?= e(whatsapp_link('Hi, I would like to ask about a cake.')) ?>" target="_blank" rel="noopener"
               class="btn btn-success me-2 mb-2">WhatsApp us</a>
            <a href="<?= e(setting('facebook')) ?>" target="_blank" rel="noopener" class="btn btn-outline-brand mb-2">Facebook</a>
        </div>

        <div class="col-lg-7">
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" class="form-card">
                <?= csrf_field() ?>
                <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
                <h2 class="h4 mb-3">Send us a message</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?= e($form['name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($form['phone']) ?>">
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= e($form['email']) ?>">
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label required">Message</label>
                        <textarea id="message" name="message" rows="4" class="form-control" required><?= e($form['message']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-pink px-4" type="submit">Send message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Map -->
    <div class="ratio ratio-21x9 mt-5 rounded-4 overflow-hidden shadow-sm">
        <iframe title="Map showing Sweet Bite Bakery" loading="lazy" allowfullscreen
                src="https://maps.google.com/maps?q=<?= rawurlencode(setting('map_query', 'Colombo, Sri Lanka')) ?>&amp;output=embed"></iframe>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
