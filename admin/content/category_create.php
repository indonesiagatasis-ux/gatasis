<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $parentId = $_POST['parent_id'] ?? null;

    // AUTO POSITION PER PARENT
    $stmt = $db->prepare("
        SELECT COALESCE(MAX(position), 0) + 1
        FROM categories
        WHERE parent_id " . ($parentId ? "= :parent_id" : "IS NULL")
    );

    $params = [];
    if ($parentId) {
        $params[':parent_id'] = $parentId;
    }

    $stmt->execute($params);
    $position = $stmt->fetchColumn();

    $icon = null;

    if (!empty($_FILES['icon']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $icon = uniqid('cat_') . '.' . $ext;

        move_uploaded_file(
            $_FILES['icon']['tmp_name'],
            $uploadDir . $icon
        );
    }


    $stmt = $db->prepare("
        INSERT INTO categories (name, slug, icon, parent_id, position, status)
        VALUES (:name, :slug, :icon, :parent_id, :position, :status)
        ");

    $stmt->execute([
        ':name'      => $_POST['name'],
        ':slug'      => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $_POST['name']))),
        ':icon'      => $icon,
        ':parent_id' => $parentId ?: null,
    ':position'  => $position,   // ✅ WAJIB PAKAI INI
    ':status'    => $_POST['status']
]);



    header("Location: index.php?page=categories");
    exit;
}

// ================================
// LOAD PARENT CATEGORIES
// ================================
$parents = $db->query("
    SELECT id, name
    FROM categories
    WHERE parent_id IS NULL
    ORDER BY position ASC, name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);


    ?>

    <div class="page-header">
        <div>
            <h1>Add Category</h1>
            <p class="page-desc">Create category information</p>
        </div>
    </div>

    <div class="card" style="max-width:1300px;">
        <div class="card-body">
            <form method="post" class="product-form" enctype="multipart/form-data">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" placeholder="e.g. Machinery" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Category Icon / Logo</label>

                        <div style="display:flex; gap:16px; align-items:center;">
                            <img
                            id="iconPreview"
                            src="assets/img/no-image.png"
                            style="
                            width:80px;
                            height:80px;
                            object-fit:contain;
                            border:1px solid #e5e7eb;
                            border-radius:10px;
                            background:#f9fafb;
                            "
                            >
                            <input type="file" name="icon" accept="image/*" onchange="previewIcon(this)">
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="position" min="1" value="<?= $position ?? 1 ?>">
                    </div>


                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <a href="index.php?page=categories" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Category</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        function previewIcon(input) {
            const img = document.getElementById('iconPreview');
            if (input.files && input.files[0]) {
                img.src = URL.createObjectURL(input.files[0]);
            }
        }
    </script>
