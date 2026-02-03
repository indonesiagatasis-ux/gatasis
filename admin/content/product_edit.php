<?php
// =====================================================
// INIT
// =====================================================
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo '<div class="info-box error">Invalid product</div>';
    return;
}
$isEdit = true;


// =====================================================
// LOAD MASTER DATA
// =====================================================
$categories = $db->query("
    SELECT id,name,parent_id
    FROM categories
    WHERE status='active'
    ORDER BY COALESCE(parent_id,id), position ASC
    ")->fetchAll();

$brands = $db->query("
    SELECT id,name FROM brands
    WHERE status='active'
    ORDER BY name
    ")->fetchAll();

$productTypes = $db->query("
    SELECT id,name FROM product_types
    WHERE status='active'
    ORDER BY name
    ")->fetchAll();


// =====================================================
// LOAD PRODUCT
// =====================================================
$stmt = $db->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<div class="info-box error">Product not found</div>';
    return;
}


// =====================================================
// LOAD EXTRA DATA
// =====================================================
$description = '';
$productSpecs = [];
$productDownloads = [];

// Description
$stmt = $db->prepare("
    SELECT content FROM product_descriptions
    WHERE product_id=?
    ");
$stmt->execute([$id]);
$description = $stmt->fetchColumn() ?: '';

// Specs
$stmt = $db->prepare("
    SELECT * FROM product_specifications
    WHERE product_id=?
    ORDER BY sort_order ASC
    ");
$stmt->execute([$id]);
$productSpecs = $stmt->fetchAll();

// Downloads
$stmt = $db->prepare("
    SELECT * FROM product_downloads
    WHERE product_id=?
    ORDER BY sort_order ASC
    ");
$stmt->execute([$id]);
$productDownloads = $stmt->fetchAll();


// =====================================================
// SAVE PRODUCT
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {

    try {
        $db->beginTransaction();

        // -------------------------------
        // UPDATE PRODUCT
        // -------------------------------
        $stmt = $db->prepare("
            UPDATE products SET
                product_type_id=?,
                name=?,
                slug=?,
                category_id=?,
                brand_id=?,
                price=?,
                status=?,
                updated_at=NOW()
                WHERE id=?
                ");

        $stmt->execute([
            $_POST['product_type_id'],
            $_POST['name'],
            strtolower(str_replace(' ', '-', $_POST['name'])),
            $_POST['category_id'] ?: null,
            $_POST['brand_id'] ?: null,
            $_POST['price'] ?: 0,
            $_POST['status'],
            $id
        ]);

        // -------------------------------
        // DESCRIPTION
        // -------------------------------
        $stmt = $db->prepare("
            INSERT INTO product_descriptions (product_id, content)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE content=VALUES(content)
            ");
        $stmt->execute([
            $id,
            $_POST['description'] ?? ''
        ]);

        // -------------------------------
        // SPECIFICATIONS
        // -------------------------------
        $db->prepare("DELETE FROM product_specifications WHERE product_id=?")
        ->execute([$id]);

        if (!empty($_POST['spec_key'])) {
            $stmt = $db->prepare("
                INSERT INTO product_specifications
                (product_id, spec_key, spec_value, sort_order)
                VALUES (?, ?, ?, ?)
                ");

            foreach ($_POST['spec_key'] as $i => $key) {
                $val = $_POST['spec_value'][$i] ?? '';
                if (trim($key)==='' && trim($val)==='') continue;

                $stmt->execute([$id, trim($key), trim($val), $i]);
            }
        }

        // -------------------------------
        // DOWNLOADS
        // -------------------------------
        $uploadDir = __DIR__ . '/../uploads/product_downloads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (!empty($_FILES['download_file']['name'][0])) {

            $stmt = $db->prepare("
                INSERT INTO product_downloads
                (product_id, title, file_path, file_size, sort_order)
                VALUES (?, ?, ?, ?, ?)
                ");

            foreach ($_FILES['download_file']['name'] as $i => $name) {

                if ($_FILES['download_file']['error'][$i] !== UPLOAD_ERR_OK) continue;

                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $safe = uniqid('dl_') . '.' . $ext;

                move_uploaded_file(
                    $_FILES['download_file']['tmp_name'][$i],
                    $uploadDir.$safe
                );

                $stmt->execute([
                    $id,
                    $_POST['download_label'][$i] ?? '',
                    'uploads/product_downloads/'.$safe,
                    filesize($uploadDir.$safe),
                    $i
                ]);
            }
        }

        $db->commit();

        header("Location: index.php?page=product_edit&id=".$id."&success=1");
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        echo '<div class="info-box error">'.$e->getMessage().'</div>';
    }
}
?>

<!-- =====================================================
 UI
===================================================== -->
<div class="page-header">
    <h1>Edit Product</h1>
    <p class="page-desc">Edit product details, description, specification & downloads</p>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="info-box success">✓ Product updated</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="save_product" value="1">

            <div class="form-grid">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Product Type</label>
                    <select name="product_type_id" required>
                        <?php foreach($productTypes as $pt): ?>
                            <option value="<?= $pt['id'] ?>" <?= $product['product_type_id']==$pt['id']?'selected':'' ?>>
                                <?= htmlspecialchars($pt['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">Select</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>>
                                <?= $c['parent_id']?'— ':'' ?><?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id">
                        <option value="">Select</option>
                        <?php foreach($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $product['brand_id']==$b['id']?'selected':'' ?>>
                                <?= htmlspecialchars($b['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?= $product['status']=='active'?'selected':'' ?>>Active</option>
                        <option value="draft" <?= $product['status']=='draft'?'selected':'' ?>>Draft</option>
                    </select>
                </div>

            </div>


<!-- ================= PRODUCT DETAILS ================= -->
<div class="card product-extra-card">
    <div class="card-header"><h3>Product Details</h3></div>
    <div class="card-body">

        <div class="product-tabs">
            <button type="button" class="tab active" data-tab="desc">Description</button>
            <button type="button" class="tab" data-tab="spec">Specification</button>
            <button type="button" class="tab" data-tab="download">Downloads</button>
        </div>

<!-- DESCRIPTION -->
<div class="tab-content active" id="tab-desc">
    <div class="wysiwyg-toolbar">
        <button type="button" onclick="exec('bold')"><b>B</b></button>
        <button type="button" onclick="exec('italic')"><i>I</i></button>
        <button type="button" onclick="exec('underline')"><u>U</u></button>
        <button type="button" onclick="exec('justifyLeft')">⯇</button>
        <button type="button" onclick="exec('justifyCenter')">≡</button>
        <button type="button" onclick="exec('justifyRight')">⯈</button>
        <button type="button" onclick="exec('insertUnorderedList')">• List</button>
    </div>

    <div id="editor" class="wysiwyg-editor" contenteditable="true">
        <?= $description ?>
    </div>

    <textarea name="description" id="description" hidden></textarea>
</div>

<!-- SPEC -->
<div class="tab-content" id="tab-spec">
    <div class="spec-wrapper" id="specWrapper">
        <?php if($productSpecs): foreach($productSpecs as $s): ?>
            <div class="spec-row">
                <input type="text" name="spec_key[]" value="<?= htmlspecialchars($s['spec_key']) ?>">
                <input type="text" name="spec_value[]" value="<?= htmlspecialchars($s['spec_value']) ?>">
                <button type="button" onclick="removeRow(this)">✕</button>
            </div>
        <?php endforeach; else: ?>
        <div class="spec-row">
            <input type="text" name="spec_key[]" placeholder="Specification">
            <input type="text" name="spec_value[]" placeholder="Value">
            <button type="button" onclick="removeRow(this)">✕</button>
        </div>
    <?php endif ?>
</div>

<button type="button" class="btn btn-outline-primary btn-sm" onclick="addSpec()">+ Add Specification</button>
</div>

<!-- DOWNLOAD -->
<div class="tab-content" id="tab-download">
    <div class="download-wrapper" id="downloadWrapper">

        <?php foreach($productDownloads as $d): ?>
            <div class="download-row">
                <input type="text" value="<?= htmlspecialchars($d['title']) ?>" disabled>
                <input type="text" value="<?= basename($d['file_path']) ?>" disabled>
                <button type="button" disabled>✓</button>
            </div>
        <?php endforeach ?>

        <div class="download-row">
            <input type="text" name="download_label[]" placeholder="File title">
            <input type="file" name="download_file[]">
            <button type="button" onclick="removeRow(this)">✕</button>
        </div>

    </div>

    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDownload()">+ Add File</button>
</div>

</div>
</div>

<div class="form-actions">
    <button class="btn btn-primary">Save Product</button>
</div>

</form>
</div>
</div>

<?php
$_GET['product_id'] = $id;
include __DIR__ . '/product_image_add.php';
?>

<script>
    function exec(cmd){document.execCommand(cmd,false,null);}
    function sync(){document.getElementById('description').value=document.getElementById('editor').innerHTML;}
    document.querySelector('form').addEventListener('submit',sync);

    document.querySelectorAll('.product-tabs .tab').forEach(t=>{
        t.onclick=()=>{
            document.querySelectorAll('.tab,.tab-content').forEach(e=>e.classList.remove('active'));
            t.classList.add('active');
            document.getElementById('tab-'+t.dataset.tab).classList.add('active');
            sync();
        }
    });

    function addSpec(){
        specWrapper.insertAdjacentHTML('beforeend',`
    <div class="spec-row">
        <input type="text" name="spec_key[]" placeholder="Specification">
        <input type="text" name="spec_value[]" placeholder="Value">
        <button type="button" onclick="removeRow(this)">✕</button>
        </div>`);
    }

    function addDownload(){
        downloadWrapper.insertAdjacentHTML('beforeend',`
    <div class="download-row">
        <input type="text" name="download_label[]" placeholder="File title">
        <input type="file" name="download_file[]">
        <button type="button" onclick="removeRow(this)">✕</button>
        </div>`);
    }

    function removeRow(btn){btn.parentElement.remove();}
</script>

<style>
/* ======================================================
   PRODUCT EXTRA CARD (WRAPPER UTAMA)
====================================================== */
.product-extra-card {
    margin-top: 30px;
}

.product-extra-card .card-body {
    background: #fff;
}


/* ======================================================
   TABS HEADER
====================================================== */
.product-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
    flex-wrap: wrap;
}

.product-tabs .tab {
    padding: 8px 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.product-tabs .tab.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #fff;
}


/* ======================================================
   TAB CONTENT VISIBILITY
====================================================== */
.tab-content {
    visibility: hidden;
    height: 0;
    overflow: hidden;
    opacity: 0;
}

.tab-content.active {
    visibility: visible;
    height: auto;
    opacity: 1;
}


/* ======================================================
   TAB BOX (SPEC & DOWNLOAD)
====================================================== */
#tab-spec,
#tab-download {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 18px;
}


/* ======================================================
   ROW WRAPPER (SPEC & DOWNLOAD)
====================================================== */
.spec-wrapper,
.download-wrapper {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 16px;
}


/* ======================================================
   ROW ITEM (SATU BARIS SPEC / DOWNLOAD)
====================================================== */
.spec-row,
.download-row {
    display: grid;
    grid-template-columns: 1fr 1fr 44px;
    gap: 14px;
    padding: 14px;
    align-items: center;

    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}


/* ======================================================
   INPUT FIELD STYLE
====================================================== */
.spec-row input,
.download-row input[type="text"],
.download-row input[type="file"] {
    width: 100%;
    padding: 12px 14px;
    font-size: 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: #ffffff;
}

/* file input biar sejajar */
.download-row input[type="file"] {
    padding: 9px 10px;
}


/* ======================================================
   REMOVE BUTTON (X)
====================================================== */
.spec-row button,
.download-row button {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 8px;
    background: #ef4444;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
}

.spec-row button:hover,
.download-row button:hover {
    background: #dc2626;
}


/* ======================================================
   ADD BUTTON (+ Add ...)
====================================================== */
#tab-spec .btn,
#tab-download .btn {
    margin-top: 10px;
}


/* ======================================================
   WYSIWYG (BIAR KONSISTEN DENGAN TAB LAIN)
====================================================== */
.wysiwyg-toolbar {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
}

.wysiwyg-toolbar button {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    background: #fff;
    border-radius: 6px;
    cursor: pointer;
}

.wysiwyg-editor {
    width: 100%;
    min-height: 260px;
    padding: 12px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}
</style>