<?php
require_once __DIR__ . '/includes/auth.php';

$adminTitle  = 'Shop Settings';
$adminActive = 'settings';

// Fields on the shop details form: key => label
$fields = [
    'shop_name'      => 'Shop name',
    'tagline'        => 'Tagline',
    'address'        => 'Address',
    'phone'          => 'Phone number',
    'whatsapp'       => 'WhatsApp number (e.g. 0771234567)',
    'email'          => 'Email',
    'facebook'       => 'Facebook page link',
    'hours_weekdays' => 'Opening hours (weekdays)',
    'hours_sunday'   => 'Opening hours (Sunday)',
    'hours_note'     => 'Note about holidays / Poya days',
    'delivery_areas' => 'Delivery areas',
    'map_query'      => 'Map search text (address shown on the map)',
    'banner_text'    => 'Announcement banner text',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'shop') {
        foreach ($fields as $key => $label) {
            set_setting($key, trim($_POST[$key] ?? ''));
        }
        set_setting('banner_on', isset($_POST['banner_on']) ? '1' : '0');

        if (!is_valid_phone(setting('whatsapp')) && trim($_POST['whatsapp'] ?? '') !== '') {
            flash('warning', 'Saved, but the WhatsApp number does not look like a Sri Lankan number.');
        } else {
            flash('success', 'Settings saved.');
        }
    }

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = db()->prepare('SELECT password_hash FROM admins WHERE admin_id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($current, (string) $hash)) {
            flash('danger', 'Your current password is wrong.');
        } elseif (strlen($new) < 8) {
            flash('danger', 'The new password must be at least 8 characters.');
        } elseif ($new !== $confirm) {
            flash('danger', 'The two new passwords are not the same.');
        } else {
            db()->prepare('UPDATE admins SET password_hash = ? WHERE admin_id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
            flash('success', 'Password changed.');
        }
    }
    redirect('admin/settings.php');
}

include __DIR__ . '/includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="post" class="admin-card">
            <?= csrf_field() ?><input type="hidden" name="action" value="shop">
            <h2 class="h5 mb-3">Shop details</h2>
            <div class="row g-3">
                <?php foreach ($fields as $key => $label): ?>
                    <div class="col-md-6">
                        <label class="form-label" for="<?= e($key) ?>"><?= e($label) ?></label>
                        <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" class="form-control" value="<?= e(setting($key)) ?>">
                    </div>
                <?php endforeach; ?>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="banner_on" name="banner_on" <?= setting('banner_on') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="banner_on">Show the announcement banner at the top of the website</label>
                    </div>
                </div>
                <div class="col-12"><button class="btn btn-brand px-4">Save settings</button></div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <form method="post" class="admin-card">
            <?= csrf_field() ?><input type="hidden" name="action" value="password">
            <h2 class="h5 mb-3">Change password</h2>
            <div class="mb-3">
                <label class="form-label" for="current_password">Current password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="new_password">New password (8+ characters)</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm_password">Repeat new password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button class="btn btn-brand">Change password</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
