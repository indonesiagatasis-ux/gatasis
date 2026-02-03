<?php
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM brands WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    echo "<p>Brand not found</p>";
    exit;
}

if ($_POST) {

    $name   = trim($_POST['name']);
    $slug   = strtolower(str_replace(' ','-',$name));
    $status = $_POST['status'];
    $logo   = $data['logo']; // default: logo lama

    // ======================
    // UPLOAD LOGO BARU
    // ======================
    if (!empty($_FILES['logo']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/brands/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext  = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $newLogo = uniqid('brand_') . '.' . $ext;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $newLogo)) {

            // hapus logo lama jika ada
            if (!empty($data['logo']) && file_exists($uploadDir . $data['logo'])) {
                unlink($uploadDir . $data['logo']);
            }

            $logo = $newLogo;
        }
    }

    // ======================
    // UPDATE DATABASE
    // ======================
    $stmt = $db->prepare(
        "UPDATE brands 
        SET name=?, slug=?, logo=?, status=?, position=? 
        WHERE id=?"
    );

    $stmt->execute([
        $name,
        $slug,
        $logo,
        $status,
        (int)$_POST['position'],
        $id
    ]);

    header("Location: index.php?page=brands");
    exit;
}
?>

<!-- ================= PAGE HEADER ================= -->
<!-- ================= PAGE HEADER ================= -->
<div class="page-header">
    <div>
        <h1>Edit Brand</h1>
        <p class="page-desc">Update brand information & logo</p>
    </div>
</div>

<!-- ================= CARD ================= -->
<div class="card" style="max-width:930px;">
    <div class="card-body">

        <form method="post" enctype="multipart/form-data" class="product-form">

            <div class="form-grid">

                <!-- BRAND NAME -->
                <div class="form-group">
                    <label>Brand Name</label>
                    <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($data['name']) ?>"
                    placeholder="e.g. Panasonic"
                    required
                    >
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?= $data['status']=='active'?'selected':'' ?>>
                            Active
                        </option>
                        <option value="inactive" <?= $data['status']=='inactive'?'selected':'' ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Position</label>
                    <input
                    type="number"
                    name="position"
                    value="<?= (int)$data['position'] ?>"
                    min="0"
                    placeholder="Order number"
                    >
                </div>


                <!-- LOGO -->
                <div class="form-group" style="grid-column: span 2;">
                    <label>Brand Logo</label>

                    <div style="display:flex; align-items:center; gap:16px;">

                        <img
                        id="logoPreview"
                        src="<?= !empty($data['logo'])
                        ? 'uploads/brands/'.$data['logo']
                        : 'assets/img/no-image.png' ?>"
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

                    <small style="color:#6b7280;">
                        Allowed: JPG, PNG, WEBP
                    </small>
                </div>

            </div>

            <!-- ACTIONS -->
            <div class="form-actions">
                <a href="index.php?page=brands" class="btn btn-light">Cancel</a>
                <button class="btn btn-primary">Update Brand</button>
            </div>

        </form>

    </div>
</div>


<!-- ================= PREVIEW SCRIPT ================= -->
<script>
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        if (input.files && input.files[0]) {
            preview.src = URL.createObjectURL(input.files[0]);
        }
    }
</script>
