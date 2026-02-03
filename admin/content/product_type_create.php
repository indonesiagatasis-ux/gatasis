<?php
if ($_POST) {
    // AUTO POSITION: Mengambil posisi tertinggi + 1
    $stmtPos = $db->query("SELECT COALESCE(MAX(position), 0) + 1 FROM product_types");
    $nextPosition = $stmtPos->fetchColumn();

    $stmt = $db->prepare(
        "INSERT INTO product_types (name, slug, description, position, status)
         VALUES (:name, :slug, :description, :position, :status)"
    );

    $stmt->execute([
        ':name'        => $_POST['name'],
        ':slug'        => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $_POST['name']))),
        ':description' => $_POST['description'],
        ':position'    => $nextPosition, // Otomatis
        ':status'      => $_POST['status']
    ]);

    header("Location: index.php?page=product_types"); 
    exit;
}
?>

<div class="page-header">
    <div>
        <h1>Add Product Type</h1>
        <p class="page-desc">Create product type information</p>
    </div>
</div>

<div class="card" style="max-width:1300px;">
    <div class="card-body">
        <form method="post" class="product-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Type Name</label>
                    <input type="text" name="name" placeholder="e.g. Spareparts" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Brief description..."></textarea>
                </div>
            </div>
            <div class="form-actions" style="margin-top:24px;">
                <a href="index.php?page=product_types" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Product Type</button>
            </div>
        </form>
    </div>
</div>