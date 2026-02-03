<?php
// ==============================
// INIT
// ==============================
$productId = (int)($_GET['product_id'] ?? 0);
$isEdit = $productId > 0;

// ==============================
// LOAD MASTER DATA
// ==============================
$categories = $db->query("
    SELECT c.id, c.name, c.parent_id
    FROM categories c
    WHERE c.status='active'
    ORDER BY COALESCE(c.parent_id, c.id), c.position ASC
    ")->fetchAll();

$brands = $db->query("
    SELECT id, name FROM brands 
    WHERE status='active' ORDER BY name
    ")->fetchAll();

$productTypes = $db->query("
    SELECT id, name FROM product_types 
    WHERE status='active' ORDER BY name
    ")->fetchAll();

// ==============================
// LOAD PRODUCT (EDIT)
// ==============================
$product = null;
$description = '';

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo '<div class="info-box error">Product not found</div>';
        return;
    }

    // description
    $stmt = $db->prepare("
        SELECT content FROM product_descriptions 
        WHERE product_id=?
        ");
    $stmt->execute([$productId]);
    $description = $stmt->fetchColumn() ?: '';
}

    // ==============================
// LOAD PRODUCT EXTRA DATA
// ==============================

$productDescription = null;
$productSpecs = [];
$productDownloads = [];

if ($isEdit) {

    // Description
    $stmt = $db->prepare("SELECT * FROM product_descriptions WHERE product_id=?");
    $stmt->execute([$productId]);
    $productDescription = $stmt->fetch();

    // Specs
    $stmt = $db->prepare("
        SELECT * FROM product_specifications
        WHERE product_id=?
        ORDER BY sort_order ASC
        ");
    $stmt->execute([$productId]);
    $productSpecs = $stmt->fetchAll();

    // Downloads
    $stmt = $db->prepare("
        SELECT * FROM product_downloads
        WHERE product_id=?
        ORDER BY sort_order ASC
        ");
    $stmt->execute([$productId]);
    $productDownloads = $stmt->fetchAll();
}


// ==============================
// SAVE PRODUCT
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {

    try {

        $db->beginTransaction();

        // ==========================
        // PRODUCT (INSERT / UPDATE)
        // ==========================
        if ($isEdit) {

            $stmt = $db->prepare("
                UPDATE products SET
                    product_type_id=?,
                    name=?,
                    slug=?,
                    category_id=?,
                    brand_id=?,
                    price=?,
                    status=?
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
                $productId
            ]);

        } else {

            $stmt = $db->prepare("
                INSERT INTO products
                (product_type_id, name, slug, category_id, brand_id, price, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");

            $stmt->execute([
                $_POST['product_type_id'],
                $_POST['name'],
                strtolower(str_replace(' ', '-', $_POST['name'])),
                $_POST['category_id'] ?: null,
                $_POST['brand_id'] ?: null,
                $_POST['price'] ?: 0,
                $_POST['status']
            ]);

            $productId = $db->lastInsertId();
        }

        // ==========================
        // DESCRIPTION (WYSIWYG)
        // ==========================
        $stmt = $db->prepare("
            INSERT INTO product_descriptions (product_id, content)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE content = VALUES(content)
            ");

        $stmt->execute([
            $productId,
            $_POST['description'] ?? ''
        ]);

        // ==========================
        // SPECIFICATIONS
        // ==========================
        $db->prepare("DELETE FROM product_specifications WHERE product_id=?")
        ->execute([$productId]);

        if (!empty($_POST['spec_key'])) {

            $stmt = $db->prepare("
                INSERT INTO product_specifications
                (product_id, spec_key, spec_value, sort_order)
                VALUES (?, ?, ?, ?)
                ");

            foreach ($_POST['spec_key'] as $i => $key) {

                $value = $_POST['spec_value'][$i] ?? '';

                if (trim($key) === '' && trim($value) === '') continue;

                $stmt->execute([
                    $productId,
                    trim($key),
                    trim($value),
                    $i
                ]);
            }
        }

        // ==========================
        // DOWNLOADS
        // ==========================
        $uploadDir = __DIR__ . '/../uploads/product_downloads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($_FILES['download_file']['name'][0])) {

            $stmt = $db->prepare("
                INSERT INTO product_downloads
                (product_id, title, file_path, file_size, sort_order)
                VALUES (?, ?, ?, ?, ?)
                ");

            foreach ($_FILES['download_file']['name'] as $i => $name) {

                if ($_FILES['download_file']['error'][$i] !== UPLOAD_ERR_OK) continue;

                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $safeName = uniqid('dl_') . '.' . $ext;

                move_uploaded_file(
                    $_FILES['download_file']['tmp_name'][$i],
                    $uploadDir . $safeName
                );

                $stmt->execute([
                    $productId,
                    $_POST['download_label'][$i] ?? '',
                    'uploads/product_downloads/' . $safeName,
                    filesize($uploadDir . $safeName),
                    $i
                ]);
            }
        }

        // ==========================
        // COMMIT
        // ==========================
        $db->commit();

        header("Location: index.php?page=product_create&product_id=" . $productId);
        exit;

    } catch (Exception $e) {

        $db->rollBack();

        echo '<div class="info-box error">
        Failed to save product:<br>' .
        htmlspecialchars($e->getMessage()) .
        '</div>';
    }
}

?>


<!-- ================= UI ================= -->
<div class="page-header">
    <h1><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h1>
    <p class="page-desc">
        <?= $isEdit ? 'Edit product details & images' : 'Create product, desc, spec, downloads first, then add images' ?>
    </p>
</div>


<div class="card">
    <div class="card-body">

        <form method="post" enctype="multipart/form-data">

            <input type="hidden" name="save_product" value="1">

            <div class="form-grid">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required
                    value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Product Type</label>
                    <select name="product_type_id" required>
                        <option value="">Select</option>
                        <?php foreach ($productTypes as $pt): ?>
                            <option value="<?= $pt['id'] ?>"
                                <?= ($product['product_type_id'] ?? '') == $pt['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pt['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <!-- CATEGORY -->
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?php if ($cat['parent_id']): ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;└─ <?= htmlspecialchars($cat['name']) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($cat['name']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>


                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id">
                        <option value="">Select</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>"
                                <?= ($product['brand_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price"
                    value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?= ($product['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>

            </div>

    <!--                 <div class="form-actions">
                        <button class="btn btn-primary">
                            <?= $isEdit ? 'Update Product' : 'Save Product' ?>
                        </button>
                    </div> -->

                    <!-- ================= EXTRA PRODUCT DATA ================= -->
                    <div class="card product-extra-card">
                        <div class="card-header">
                            <h3>Product Details</h3>
                        </div>

                        <div class="card-body">

                            <!-- Tabs -->
                            <div class="product-tabs">
                                <button type="button" class="tab active" data-tab="desc">Description</button>
                                <button type="button" class="tab" data-tab="spec">Specification</button>
                                <button type="button" class="tab" data-tab="download">Downloads</button>
                            </div>


                            <!-- Description -->
                            <div class="tab-content active" id="tab-desc">

                                <!-- <label>Product Description</label> -->

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

                                <!-- SOURCE OF TRUTH -->
                                <textarea name="description" id="description" hidden><?= htmlspecialchars($description) ?></textarea>
                            </div>



                            <!-- Specification -->
                            <div class="tab-content" id="tab-spec">

                                <div class="spec-wrapper" id="specWrapper">

                                    <?php if ($productSpecs): foreach ($productSpecs as $s): ?>
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

                                <?php endif; ?>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSpec()">
                                + Add Specification
                            </button>
                        </div>





                        <!-- Downloads -->
                        <div class="tab-content" id="tab-download">

                            <div class="download-wrapper" id="downloadWrapper">

                                <?php if ($productDownloads): foreach ($productDownloads as $d): ?>
                                    <div class="download-row">
                                        <input type="text" name="download_label[]" value="<?= htmlspecialchars($d['title']) ?>">
                                        <input type="file" name="download_file[]">
                                        <button type="button" onclick="removeRow(this)">✕</button>
                                    </div>
                                <?php endforeach; else: ?>

                                <div class="download-row">
                                    <input type="text" name="download_label[]" placeholder="File title">
                                    <input type="file" name="download_file[]">
                                    <button type="button" onclick="removeRow(this)">✕</button>
                                </div>

                            <?php endif; ?>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDownload()">
                            + Add File
                        </button>
                    </div>




                    <div class="form-actions">
                        <button class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // ==============================
        // IMAGE MANAGER (ONLY AFTER SAVE)
        // ==============================
        if ($isEdit) {
            include __DIR__ . '/product_image_add.php';
        }
        ?>



        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const tabs = document.querySelectorAll('.product-tabs .tab');
                const contents = document.querySelectorAll('.tab-content');
                const editor = document.getElementById('editor');
                const textarea = document.getElementById('description');

                function syncEditorToTextarea() {
                    if (editor && textarea) {
                        textarea.value = editor.innerHTML;
                    }
                }

                function syncTextareaToEditor() {
                    if (editor && textarea) {
                        editor.innerHTML = textarea.value;
                    }
                }

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {

                        syncEditorToTextarea();

                        tabs.forEach(t => t.classList.remove('active'));
                        contents.forEach(c => c.classList.remove('active'));

                        tab.classList.add('active');
                        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');

                        if (tab.dataset.tab === 'desc') {
                            syncTextareaToEditor();
                        }
                    });
                });

                document.querySelector('form').addEventListener('submit', () => {
                    syncEditorToTextarea();
                });
            });

            function exec(cmd){
                document.execCommand(cmd,false,null);
            }

            function addSpec(){
                document.getElementById('specWrapper').insertAdjacentHTML('beforeend', `
        <div class="spec-row">
            <input type="text" name="spec_key[]" placeholder="Specification">
            <input type="text" name="spec_value[]" placeholder="Value">
            <button type="button" onclick="removeRow(this)">✕</button>
        </div>
                `);
            }

            function addDownload(){
                document.getElementById('downloadWrapper').insertAdjacentHTML('beforeend', `
        <div class="download-row">
            <input type="text" name="download_label[]" placeholder="File title">
            <input type="file" name="download_file[]">
            <button type="button" onclick="removeRow(this)">✕</button>
        </div>
                `);
            }

            function removeRow(btn){
                btn.parentElement.remove();
            }
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