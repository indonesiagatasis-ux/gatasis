<?php
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Category not found";
    exit;
}

/* ================= LOAD CATEGORY ================= */
$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found");
}

/* ================= LOAD PARENTS ================= */
$parents = $db->prepare("
    SELECT id, name
    FROM categories
    WHERE parent_id IS NULL
    AND id != ?
    ORDER BY name
");
$parents->execute([$id]);
$parents = $parents->fetchAll(PDO::FETCH_ASSOC);

/* ================= UPDATE CATEGORY ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name      = $_POST['name'];
    $status    = $_POST['status'];
    $newParent = $_POST['parent_id'] ?: null;
    $newPos    = $_POST['position'] ?? null;

    $oldParent = $category['parent_id'];
    $oldPos    = $category['position'];
    $icon      = $category['icon'];

    // ================= ICON UPLOAD =================
    if (!empty($_FILES['icon']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext  = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $newIcon = uniqid('cat_') . '.' . $ext;

        if (move_uploaded_file($_FILES['icon']['tmp_name'], $uploadDir . $newIcon)) {

            if (!empty($icon) && file_exists($uploadDir . $icon)) {
                unlink($uploadDir . $icon);
            }

            $icon = $newIcon;
        }
    }

    $db->beginTransaction();

    try {

        /* ===== LOGIC POSITION & PARENT (TETAP) ===== */
        if ($newParent != $oldParent) {

            $stmt = $db->prepare("
                UPDATE categories
                SET position = position - 1
                WHERE parent_id " . ($oldParent ? "= ?" : "IS NULL") . "
                AND position > ?
            ");
            $oldParent
                ? $stmt->execute([$oldParent, $oldPos])
                : $stmt->execute([$oldPos]);

            $stmt = $db->prepare("
                SELECT COALESCE(MAX(position),0)+1
                FROM categories
                WHERE parent_id " . ($newParent ? "= ?" : "IS NULL")
            );
            $newParent ? $stmt->execute([$newParent]) : $stmt->execute();
            $newPos = $stmt->fetchColumn();

        } elseif ($newPos && $newPos != $oldPos) {

            if ($newPos > $oldPos) {
                $stmt = $db->prepare("
                    UPDATE categories
                    SET position = position - 1
                    WHERE parent_id " . ($oldParent ? "= ?" : "IS NULL") . "
                    AND position > ? AND position <= ?
                ");
                $oldParent
                    ? $stmt->execute([$oldParent, $oldPos, $newPos])
                    : $stmt->execute([$oldPos, $newPos]);
            } else {
                $stmt = $db->prepare("
                    UPDATE categories
                    SET position = position + 1
                    WHERE parent_id " . ($oldParent ? "= ?" : "IS NULL") . "
                    AND position >= ? AND position < ?
                ");
                $oldParent
                    ? $stmt->execute([$oldParent, $newPos, $oldPos])
                    : $stmt->execute([$newPos, $oldPos]);
            }
        }

        /* ===== UPDATE CATEGORY ===== */
        $stmt = $db->prepare("
            UPDATE categories
            SET name=:name,
                slug=:slug,
                icon=:icon,
                parent_id=:parent_id,
                position=:position,
                status=:status
            WHERE id=:id
        ");

        $stmt->execute([
            ':name'      => $name,
            ':slug'      => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name))),
            ':icon'      => $icon,
            ':parent_id' => $newParent,
            ':position'  => $newPos ?? $oldPos,
            ':status'    => $status,
            ':id'        => $id
        ]);

        $db->commit();
        header("Location: index.php?page=categories");
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
?>

<!-- ================= PAGE HEADER ================= -->
<div class="page-header">
    <div>
        <h1>Edit Category</h1>
        <p class="page-desc">Update category information</p>
    </div>
</div>

<!-- ================= CARD ================= -->
<div class="card" style="max-width:1300px;">
    <div class="card-body" style="padding-bottom:32px;">

        <form method="post" class="product-form" enctype="multipart/form-data">

            <div class="form-grid">

                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($category['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="parent_id">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                <?= $category['parent_id']==$p['id']?'selected':'' ?>>
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
                            src="<?= $category['icon']
                                ? 'uploads/categories/'.$category['icon']
                                : 'assets/img/no-image.png' ?>"
                            style="width:80px;height:80px;object-fit:contain;
                                   border:1px solid #e5e7eb;border-radius:10px;
                                   background:#f9fafb;">
                        <input type="file" name="icon" accept="image/*"
                               onchange="previewIcon(this)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?= $category['status']=='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= $category['status']=='inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Order</label>
                    <input type="number" name="position" min="1"
                           value="<?= $category['position'] ?>">
                </div>

            </div>

            <div class="form-actions">
                <a href="index.php?page=categories" class="btn btn-light">Cancel</a>
                <button class="btn btn-primary">Update Category</button>
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
