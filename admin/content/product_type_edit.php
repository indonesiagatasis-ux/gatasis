<?php
$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM product_types WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if ($_POST) {
    $stmt = $db->prepare(
        "UPDATE product_types SET name = ?, slug = ?, description = ?, position = ?, status = ? WHERE id = ?"
    );
    $stmt->execute([
        $_POST['name'],
        strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $_POST['name']))),
        $_POST['description'],
        $_POST['position'], // Update posisi manual
        $_POST['status'],
        $id
    ]);
    header("Location: index.php?page=product_types"); 
    exit;
}
?>

<div class="page-header">
    <div>
        <h1>Edit Product Type</h1>
        <p class="page-desc">Update product type information</p>
    </div>
</div>

<div class="card" style="max-width:1300px;">
    <div class="card-body">
        <form method="post" class="product-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Type Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Order / Position</label>
                    <input type="number" name="position" value="<?= $data['position'] ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?= $data['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $data['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($data['description']) ?></textarea>
                </div>
            </div>
            <div class="form-actions" style="margin-top:24px;">
                <a href="index.php?page=product_types" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product Type</button>
            </div>
        </form>
    </div>
</div>