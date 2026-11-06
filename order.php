<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Custom Cake Order';
$activePage      = 'order';
$metaDescription = 'Send a custom cake or order request to Sweet Bite Bakery, Colombo.';

$products = db()->query('SELECT product_id, name FROM products WHERE is_available = 1 ORDER BY name')->fetchAll();

// Form values (kept on screen if something is wrong)
$form = [
    'customer_name' => '', 'phone' => '', 'email' => '',
    'product_id' => (int) ($_GET['product'] ?? 0),
    'size' => '', 'flavour' => '', 'cake_message' => '', 'design_notes' => '',
    'required_date' => '', 'required_time' => '', 'delivery_type' => 'pickup', 'address' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // Hidden "honeypot" field: real people never fill it, spam bots do
    if (!empty($_POST['website'])) {
        redirect('index.php');
    }

    foreach ($form as $key => $default) {
        $form[$key] = is_int($default) ? (int) ($_POST[$key] ?? 0) : trim($_POST[$key] ?? '');
    }

    // Server side checks (the browser checks too, but we never trust only the browser)
    if ($form['customer_name'] === '') {
        $errors[] = 'Please enter your name.';
    }
    if (!is_valid_phone($form['phone'])) {
        $errors[] = 'Please enter a valid Sri Lankan phone number, for example 077 123 4567.';
    }
    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The email address does not look right.';
    }
    $dateOk = DateTime::createFromFormat('Y-m-d', $form['required_date']);
    if (!$dateOk || $dateOk->format('Y-m-d') !== $form['required_date']) {
        $errors[] = 'Please choose the date you need the order.';
    } elseif ($form['required_date'] < date('Y-m-d')) {
        $errors[] = 'The date cannot be in the past.';
    }
    if (!in_array($form['delivery_type'], ['pickup', 'delivery'], true)) {
        $form['delivery_type'] = 'pickup';
    }
    if ($form['delivery_type'] === 'delivery' && $form['address'] === '') {
        $errors[] = 'Please enter the delivery address.';
    }

    // Name of the item (a listed product, or a custom cake)
    $itemName = 'Custom cake';
    $productId = null;
    if ($form['product_id'] > 0) {
        $find = db()->prepare('SELECT name FROM products WHERE product_id = ?');
        $find->execute([$form['product_id']]);
        if ($found = $find->fetchColumn()) {
            $itemName  = $found;
            $productId = $form['product_id'];
        }
    }

    // Optional reference photo
    [$photo, $uploadError] = save_upload($_FILES['reference_image'] ?? [], 'orders');
    if ($uploadError) {
        $errors[] = $uploadError;
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO orders (customer_name, phone, email, product_id, item_name, size, flavour, cake_message,
                                 design_notes, reference_image, required_date, required_time, delivery_type, address,
                                 status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $form['customer_name'], $form['phone'], $form['email'] ?: null, $productId, $itemName,
            $form['size'], $form['flavour'], $form['cake_message'], $form['design_notes'], $photo,
            $form['required_date'], $form['required_time'], $form['delivery_type'], $form['address'],
            'New', date('Y-m-d H:i:s'),
        ]);
        $orderId = (int) db()->lastInsertId();

        // Tell the owner by email (works only if the hosting has mail enabled)
        if (setting('email') !== '') {
            @mail(
                setting('email'),
                'New order request #' . $orderId . ' - ' . $itemName,
                "Name: {$form['customer_name']}\nPhone: {$form['phone']}\nItem: $itemName\nDate needed: {$form['required_date']}\n"
            );
        }

        redirect('thank-you.php?ref=' . $orderId);
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Custom Cake Order</h1>
        <p>Fill in the form and we will contact you to confirm the price and details.</p>
    </div>
</div>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <strong>Please fix these:</strong>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="orderForm" method="post" enctype="multipart/form-data" class="form-card" novalidate>
                <?= csrf_field() ?>
                <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">

                <h2 class="h5 mb-3">Your details</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="customer_name" class="form-label required">Your name</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" required
                               value="<?= e($form['customer_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label required">Phone number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required
                               placeholder="077 123 4567" value="<?= e($form['phone']) ?>">
                        <div class="invalid-feedback">Please enter a valid Sri Lankan phone number.</div>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Email (optional)</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= e($form['email']) ?>">
                    </div>
                </div>

                <h2 class="h5 mb-3">Your cake</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="product_id" class="form-label">Item</label>
                        <select id="product_id" name="product_id" class="form-select">
                            <option value="0">Custom cake (my own design)</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= (int) $p['product_id'] ?>" <?= $form['product_id'] === (int) $p['product_id'] ? 'selected' : '' ?>>
                                    <?= e($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="size" class="form-label">Size</label>
                        <input type="text" id="size" name="size" class="form-control" placeholder="e.g. 2 lb" value="<?= e($form['size']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="flavour" class="form-label">Flavour</label>
                        <input type="text" id="flavour" name="flavour" class="form-control" placeholder="e.g. Chocolate" value="<?= e($form['flavour']) ?>">
                    </div>
                    <div class="col-12">
                        <label for="cake_message" class="form-label">Message on the cake</label>
                        <input type="text" id="cake_message" name="cake_message" class="form-control" maxlength="100"
                               placeholder="e.g. Happy Birthday Nimali" value="<?= e($form['cake_message']) ?>">
                    </div>
                    <div class="col-12">
                        <label for="design_notes" class="form-label">Design idea</label>
                        <textarea id="design_notes" name="design_notes" rows="3" class="form-control"
                                  placeholder="Colours, theme, characters, anything you like"><?= e($form['design_notes']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label for="reference_image" class="form-label">Reference photo (optional)</label>
                        <input type="file" id="reference_image" name="reference_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">JPG, PNG or WEBP, up to 2 MB.</div>
                    </div>
                </div>

                <h2 class="h5 mb-3">When and where</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="required_date" class="form-label required">Date needed</label>
                        <input type="date" id="required_date" name="required_date" class="form-control" required
                               value="<?= e($form['required_date']) ?>">
                        <div id="dateWarning" class="form-text text-danger d-none">
                            This date is very close. Custom cakes need about 2 days, so we may not be able to make it. Please call us.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="required_time" class="form-label">Time needed</label>
                        <input type="time" id="required_time" name="required_time" class="form-control" value="<?= e($form['required_time']) ?>">
                    </div>
                    <div class="col-12">
                        <span class="form-label d-block">Pickup or delivery</span>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="delivery_type" id="pickup" value="pickup"
                                   <?= $form['delivery_type'] === 'pickup' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pickup">I will pick it up</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="delivery_type" id="delivery" value="delivery"
                                   <?= $form['delivery_type'] === 'delivery' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="delivery">Please deliver</label>
                        </div>
                    </div>
                    <div class="col-12" id="addressBox">
                        <label for="address" class="form-label required">Delivery address</label>
                        <input type="text" id="address" name="address" class="form-control" value="<?= e($form['address']) ?>">
                        <div class="form-text">We deliver to: <?= e(setting('delivery_areas')) ?>. Delivery charges depend on the distance.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-pink btn-lg w-100">Send my request</button>
                <p class="small text-muted text-center mt-3 mb-0">
                    Prefer WhatsApp?
                    <a href="<?= e(whatsapp_link('Hi, I would like to order a cake.')) ?>" target="_blank" rel="noopener">Chat with us</a>.
                </p>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
