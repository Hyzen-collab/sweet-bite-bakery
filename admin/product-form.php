<?php
require_once __DIR__ . '/includes/auth.php';

$adminActive = 'products';
$id          = (int) ($_GET['id'] ?? 0);
$editing     = $id > 0;

$categories = db()->query('SELECT * FROM categories ORDER BY display_order')->fetchAll();

// Start with empty values, or load the product being edited
$product = [
    'category_id' => $categories[0]['category_id'] ?? 0, 'name' => '', 'description' => '',
    'price_from' => '', 'price_unit' => '', 'sizes' => '', 'flavours' => '', 'lead_time' => '',
    'image' => null, 'is_available' => 1, 'is_featured' => 0,
];

if ($editing) {
    $stmt = db()->prepare('SELECT * FROM products WHERE product_id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        flash('danger', 'Product not found.');
        redirect('admin/products.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $data = [
        'category_id'  => (int) ($_POST['category_id'] ?? 0),
        'name'         => trim($_POST['name'] ?? ''),
        'description'  => trim($_POST['description'] ?? ''),
        'price_from'   => trim($_POST['price_from'] ?? ''),
        'price_unit'   => trim($_POST['price_unit'] ?? ''),
        'sizes'        => trim($_POST['sizes'] ?? ''),
        'flavours'     => trim($_POST['flavours'] ?? ''),
        'lead_time'    => trim($_POST['lead_time'] ?? ''),
        'is_available' => isset($_POST['is_available']) ? 1 : 0,
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
    ];

    if ($data['name'] === '')                              { $errors[] = 'Please enter the product name.'; }
    if ($data['description'] === '')                       { $errors[] = 'Please enter a short description.'; }
    if (!is_numeric($data['price_from']) || $data['price_from'] < 0) { $errors[] = 'Please enter the price as a number, for example 2500.'; }
    if ($data['category_id'] <= 0)                         { $errors[] = 'Please choose a category.'; }

    [$newImage, $uploadError] = save_upload($_FILES['image'] ?? [], 'products');
    if ($uploadError) { $errors[] = $uploadError; }

    if (!$errors) {
        $image = $newImage ?: $product['image'];

        if ($editing) {
            $sql = 'UPDATE products SET category_id = ?, name = ?, description = ?, price_from = ?, price_unit = ?,
                    sizes = ?, flavours = ?, lead_time = ?, is_available = ?, is_featured = ?, image = ?
                    WHERE product_id = ?';
            $values = [...array_values($data), $image, $id];
        } else {
            $sql = 'INSERT INTO products (category_id, name, description, price_from, price_unit, sizes, flavours,
                    lead_time, is_available, is_featured, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $values = [...array_values($data), $image, date('Y-m-d H:i:s')];
        }
        db()->prepare($sql)->execute($values);

        // Remove the old uploaded picture if a new one replaced it
        if ($editing && $newImage && $product['image']) {
            delete_upload('products', $product['image']);
        }

        flash('success', $editing ? 'Product updated.' : 'Product added.');
        redirect('admin/products.php');
    }

    // Show what the admin typed again
    $product = array_merge($product, $data);
}

$adminTitle = $editing ? 'Edit product' : 'Add product';
include __DIR__ . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="admin-card">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control" required value="<?= e($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-select">
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['category_id'] ?>" <?= (int) $product['category_id'] === (int) $c['category_id'] ? 'selected' : '' ?>>
                        <?= e($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control" required><?= e($product['description']) ?></textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="price_from">Price from (Rs.)</label>
            <input type="number" step="0.01" min="0" id="price_from" name="price_from" class="form-control" required value="<?= e($product['price_from']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="price_unit">Price unit</label>
            <input type="text" id="price_unit" name="price_unit" class="form-control" placeholder="per lb, each, per box of 6" value="<?= e($product['price_unit']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="lead_time">Lead time</label>
            <input type="text" id="lead_time" name="lead_time" class="form-control" placeholder="Order 2 days before" value="<?= e($product['lead_time']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="sizes">Sizes</label>
            <input type="text" id="sizes" name="sizes" class="form-control" placeholder="1 lb, 2 lb, 3 lb" value="<?= e($product['sizes']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="flavours">Flavours</label>
            <input type="text" id="flavours" name="flavours" class="form-control" placeholder="Chocolate, Vanilla" value="<?= e($product['flavours']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="image">Photo (JPG, PNG or WEBP, max 2 MB)</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="col-md-6">
            <?php if ($product['image']): ?>
                <span class="form-label d-block">Current photo</span>
                <img class="thumb-big" style="max-height:110px" src="<?= e(image_url('products', $product['image'])) ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="col-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" <?= $product['is_available'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_available">Show on website</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_featured">Show on home page (featured)</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-brand px-4">Save</button>
            <a href="<?= url('admin/products.php') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
