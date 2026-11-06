<?php
/**
 * Small helper functions used by both the public site and the admin panel.
 */
require_once __DIR__ . '/db.php';

const ORDER_STATUSES = ['New', 'Contacted', 'Confirmed', 'Completed', 'Cancelled'];
const OCCASIONS      = ['Birthday', 'Wedding', 'Kids', 'Anniversary', 'Festival', 'Cupcakes'];

/* ---------- Output and links ---------- */

// Escape text before printing it in HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

// Rs. 3,200
function money($amount): string
{
    return 'Rs. ' . number_format((float) $amount, 0);
}

/* ---------- Shop settings (stored in the database) ---------- */

function setting(string $key, string $default = ''): string
{
    static $all = null;

    if ($all === null) {
        $all = [];
        foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $all[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $all[$key] ?? $default;
}

function set_setting(string $key, string $value): void
{
    $check = db()->prepare('SELECT COUNT(*) FROM settings WHERE setting_key = ?');
    $check->execute([$key]);

    if ($check->fetchColumn() > 0) {
        $stmt = db()->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?');
        $stmt->execute([$value, $key]);
    } else {
        $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
        $stmt->execute([$key, $value]);
    }
}

/* ---------- Phone numbers and WhatsApp ---------- */

// Accepts 077 123 4567, 0112345678 or +94771234567
function is_valid_phone(string $phone): bool
{
    $digits = preg_replace('/[\s\-()]/', '', $phone);
    return (bool) preg_match('/^(?:\+94|0)\d{9}$/', $digits);
}

// 0771234567 -> 94771234567 (the format WhatsApp links need)
function to_international(string $phone): string
{
    $digits = preg_replace('/\D/', '', $phone);
    if (strpos($digits, '0') === 0) {
        $digits = '94' . substr($digits, 1);
    }
    return $digits;
}

function whatsapp_link(string $message = '', string $number = ''): string
{
    $number = to_international($number !== '' ? $number : setting('whatsapp'));
    $link   = 'https://wa.me/' . $number;
    return $message !== '' ? $link . '?text=' . rawurlencode($message) : $link;
}

/* ---------- Flash messages ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function show_flash(): void
{
    foreach ($_SESSION['flash'] ?? [] as $item) {
        echo '<div class="alert alert-' . e($item['type']) . ' alert-dismissible fade show" role="alert">'
            . e($item['message'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    unset($_SESSION['flash']);
}

/* ---------- Form safety (CSRF) ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $saved = $_SESSION['csrf'] ?? '';
    $sent  = $_POST['csrf'] ?? '';
    if ($saved === '' || !hash_equals($saved, $sent)) {
        http_response_code(400);
        exit('Your form has expired. Please go back, refresh the page and try again.');
    }
}

/* ---------- Image upload and image paths ---------- */

/**
 * Saves an uploaded picture into uploads/<folder>/.
 * Returns [file name or null, error message or null].
 */
function save_upload(array $file, string $folder): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null]; // nothing was uploaded, which is fine
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'The picture could not be uploaded. Please try again.'];
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        return [null, 'The picture is too big. Maximum size is 2 MB.'];
    }

    $info    = @getimagesize($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!$info || !isset($allowed[$info['mime']])) {
        return [null, 'Only JPG, PNG or WEBP pictures are allowed.'];
    }

    $name   = bin2hex(random_bytes(8)) . '.' . $allowed[$info['mime']];
    $folder = trim($folder, '/');
    $target = UPLOAD_DIR . $folder . '/' . $name;

    if (!is_dir(dirname($target))) {
        mkdir(dirname($target), 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return [null, 'Could not save the picture on the server.'];
    }

    return [$name, null];
}

// Pictures added by the admin live in uploads/, the starter pictures live in assets/images/
function image_url(string $folder, ?string $file): string
{
    if ($file && is_file(UPLOAD_DIR . $folder . '/' . $file)) {
        return url('uploads/' . $folder . '/' . $file);
    }
    if ($file && is_file(ROOT_PATH . '/assets/images/' . $folder . '/' . $file)) {
        return asset('images/' . $folder . '/' . $file);
    }
    return asset('images/logo.png');
}

function delete_upload(string $folder, ?string $file): void
{
    if ($file && is_file(UPLOAD_DIR . $folder . '/' . $file)) {
        unlink(UPLOAD_DIR . $folder . '/' . $file);
    }
}

/* ---------- Reusable pieces ---------- */

function render_product_card(array $p): void
{
    ?>
    <div class="col">
        <div class="card product-card h-100">
            <a href="<?= url('product.php?id=' . (int) $p['product_id']) ?>">
                <img src="<?= e(image_url('products', $p['image'])) ?>" class="card-img-top"
                     alt="<?= e($p['name']) ?>" loading="lazy">
            </a>
            <div class="card-body d-flex flex-column">
                <span class="badge badge-soft mb-2 align-self-start"><?= e($p['category_name']) ?></span>
                <h3 class="h5 card-title"><?= e($p['name']) ?></h3>
                <p class="card-text text-muted small flex-grow-1"><?= e($p['description']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="price">From <?= money($p['price_from']) ?>
                        <small class="text-muted"><?= e($p['price_unit']) ?></small></span>
                    <a href="<?= url('product.php?id=' . (int) $p['product_id']) ?>"
                       class="btn btn-sm btn-brand">View</a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
