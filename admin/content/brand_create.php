<?php
// ==============================
// SAVE BRAND
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $status = $_POST['status'];
    $position = (int)($_POST['position'] ?? 0);

    // ==============================
    // UPLOAD LOGO
    // ==============================
    $logo = null;
    if (!empty($_FILES['logo']['name'])) {
        $ext  = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = uniqid('brand_') . '.' . $ext;
        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            'uploads/brands/' . $logo
        );
    }

    $stmt = $db->prepare(
        "INSERT INTO brands (name, slug, logo, status, position)
        VALUES (:name, :slug, :logo, :status, :position)"
    );

    $stmt->execute([
        ':name'     => $name,
        ':slug'     => $slug,
        ':logo'     => $logo,
        ':status'   => $status,
        ':position' => (int)($_POST['position'] ?? 0)
    ]);

    header("Location: index.php?page=brands");
    exit;
}
?>


<div class="page-header">
    <div>
        <h1>Add Brand</h1>
        <p class="page-desc">Create new product brand</p>
    </div>
</div>

<div class="card" style="max-width:930px;">
    <div class="card-body">

        <form method="post" enctype="multipart/form-data" class="product-form">

            <div class="form-grid">

                <!-- NAME -->
                <div class="form-group">
                    <label>Brand Name</label>
                    <input
                    type="text"
                    name="name"
                    placeholder="e.g. Samsung"
                    required
                    >
                </div>

                <!-- POSITION -->
                <div class="form-group">
                    <label>Position</label>
                    <input
                    type="number"
                    name="position"
                    min="0"
                    placeholder="Order number"
                    >
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- LOGO -->
                <div class="form-group" style="grid-column: span 2;">
                    <label>Brand Logo</label>

                    <div style="display:flex; align-items:center; gap:16px;">
                        <img
                        id="logoPreview"
                        src="assets/img/no-image.png"
                        style="
                        width:80px;
                        height:80px;
                        object-fit:contain;
                        border:1px solid #e5e7eb;
                        border-radius:8px;
                        background:#fff;
                        padding:6px;
                        "
                        >
                        <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        onchange="previewLogo(this)"
                        >
                    </div>
                </div>

            </div>

            <!-- 🔽 ACTIONS (INI YANG DIRAPIKAN) -->
            <div class="form-actions" style="margin-top:24px;">
                <a href="index.php?page=brands" class="btn btn-light">Cancel</a>
                <button class="btn btn-primary">Save Brand</button>
            </div>

        </form>

    </div>
</div>

