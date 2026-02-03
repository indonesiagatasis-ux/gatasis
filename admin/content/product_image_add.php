<?php
// ==================================================
// REQUIRE DB & VALIDATION
// ==================================================
if (!isset($db)) {
    require_once __DIR__ . '/../layouts/database.php';
}

$productId = (int)($_GET['product_id'] ?? 0);
if ($productId <= 0) {
    echo '<div class="info-box error">Invalid product</div>';
    return;
}

// ==================================================
// AJAX ACTIONS
// ==================================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    in_array($_POST['action'], ['set_primary', 'delete_image', 'reorder'])
) {

    header('Content-Type: application/json');

    // ---------- SET PRIMARY ----------
    if ($_POST['action'] === 'set_primary') {
        $imageId = (int)$_POST['image_id'];

        $img = $db->prepare("SELECT image_path FROM product_images WHERE id=? AND product_id=?");
        $img->execute([$imageId, $productId]);
        $img = $img->fetch();

        if ($img) {
            $db->prepare("UPDATE product_images SET is_primary=0 WHERE product_id=?")
            ->execute([$productId]);

            $db->prepare("UPDATE product_images SET is_primary=1 WHERE id=?")
            ->execute([$imageId]);

            $db->prepare("UPDATE products SET main_image=? WHERE id=?")
            ->execute([$img['image_path'], $productId]);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    // ---------- DELETE ----------
    if ($_POST['action'] === 'delete_image') {
        $imageId = (int)$_POST['image_id'];

        $img = $db->prepare("SELECT image_path FROM product_images WHERE id=? AND product_id=?");
        $img->execute([$imageId, $productId]);
        $img = $img->fetch();

        if ($img) {
            $file = __DIR__ . '/../' . $img['image_path'];
            if (file_exists($file)) unlink($file);

            $db->prepare("DELETE FROM product_images WHERE id=?")
            ->execute([$imageId]);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    // ---------- REORDER ----------
    if ($_POST['action'] === 'reorder') {

        $order = $_POST['order'] ?? [];

        foreach ($order as $pos => $id) {
            $db->prepare("
                UPDATE product_images 
                    SET position = ?
                    WHERE id = ? AND product_id = ?
                    ")->execute([$pos, $id, $productId]);
        }

        echo json_encode(['success' => true]);
        exit;
    }

}

// ==================================================
// IMAGE UPLOAD (NORMAL POST)
// ==================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {

    $uploadDir = __DIR__ . '/../uploads/products/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {

            $ext  = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $name = uniqid('product_') . '.' . $ext;
            move_uploaded_file($tmp, $uploadDir . $name);

            $path = 'uploads/products/' . $name;

            $db->prepare("
                INSERT INTO product_images
                (product_id, image_path, is_primary, status, created_at)
                VALUES (?, ?, 0, 'active', NOW())
                ")->execute([$productId, $path]);
        }
    }
    echo "<script>location.href='?page=product_create&product_id={$productId}'</script>";
    exit;


}


// ==================================================
// LOAD IMAGES
// ==================================================
$images = $db->prepare("
    SELECT * FROM product_images
    WHERE product_id=?
    ORDER BY is_primary DESC, position ASC, id ASC
    ");

$images->execute([$productId]);
$images = $images->fetchAll();


?>

<!-- ================= UI ================= -->
<div class="card" style="margin-top:30px;">
    <div class="card-header">
        <h3>Product Images</h3>
    </div>
    <div class="card-body">

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="images[]" multiple accept="image/*" onchange="preview(this)">
            <div id="preview" class="preview-grid"></div>
            <button class="btn btn-primary" style="margin-top:10px;">Upload Images</button>
        </form>

        <hr>

        <div class="image-grid">
            <?php foreach ($images as $img): ?>
             <div class="image-item" draggable="true" data-id="<?= $img['id'] ?>">
                <span class="drag-handle">☰</span>
                <img src="/gatasis/admin/<?= $img['image_path'] ?>">

                <?php if ($img['is_primary']): ?>
                    <span class="badge">MAIN</span>
                <?php endif ?>

                <div class="actions">
                    <button type="button" onclick="setPrimary(<?= $img['id'] ?>)">
                        ⭐ Set Main
                    </button>
                    <button type="button" class="delete"
                    onclick="deleteImage(<?= $img['id'] ?>)">
                    🗑 Delete
                </button>
            </div>

        </div>
    <?php endforeach ?>
</div>

</div>
</div>

<!-- ================= STYLE ================= -->
<style>
    .preview-grid,.image-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
        gap:14px;
        margin-top:12px
    }
    .image-item{
        position:relative;
        border:2px solid #e5e7eb;
        padding:8px;
        border-radius:10px;
        background:#fff
    }
    .image-item.primary{border-color:#3b82f6}
    .image-item img{
        width:100%;
        height:120px;
        object-fit:contain
    }
    .badge{
        position:absolute;
        top:6px;
        left:6px;
        background:#3b82f6;
        color:#fff;
        font-size:10px;
        padding:4px 8px;
        border-radius:6px
    }
    .actions{
        display:flex;
        gap:6px;
        margin-top:6px
    }
    .actions button{
        flex:1;
        font-size:11px;
        padding:6px;
        border-radius:6px;
        border:1px solid #3b82f6;
        background:#fff;
        color:#3b82f6;
        cursor:pointer
    }
    .actions .delete{
        border-color:#ef4444;
        color:#ef4444
    }
    .image-item.dragging {
        opacity: 0.4;
        border-style: dashed;
    }
    .drag-handle{
        position:absolute;
        top:6px;
        right:6px;
        cursor:grab;
        opacity:0;
        font-size:16px;
    }
    .image-item:hover .drag-handle{
        opacity:1;
    }

</style>

<!-- ================= SCRIPT ================= -->
<script>
    function preview(input){
        const box=document.getElementById('preview');
        box.innerHTML='';
        [...input.files].forEach(f=>{
            const r=new FileReader();
            r.onload=e=>{
                const d=document.createElement('div');
                d.className='image-item';
                d.innerHTML=`<img src="${e.target.result}">`;
                box.appendChild(d);
            };
            r.readAsDataURL(f);
        });
    }

    function setPrimary(id) {
        fetch(location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_primary&image_id=${id}`
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;

            document.querySelectorAll('.image-item').forEach(el => {
                el.classList.remove('primary');
                const badge = el.querySelector('.badge');
                if (badge) badge.remove();
            });

            const item = document.querySelector(`.image-item[data-id="${id}"]`);
            if (item) {
                item.classList.add('primary');

                const badge = document.createElement('span');
                badge.className = 'badge';
                badge.innerText = 'MAIN';
                item.appendChild(badge);
            }
        });
    }


    function deleteImage(id) {
        if (!confirm('Delete this image?')) return;

        fetch(location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_image&image_id=${id}`
        })
        .then(r => r.json())
        .then(() => location.reload());
    }


    let dragged = null;

    document.querySelectorAll('.image-item').forEach(item => {

        item.addEventListener('dragstart', () => {
            dragged = item;
            item.style.opacity = .4;
        });

        item.addEventListener('dragend', () => {
            dragged = null;
            item.style.opacity = 1;
            saveOrder();
        });

        item.addEventListener('dragover', e => e.preventDefault());

        item.addEventListener('drop', () => {
            if (dragged && dragged !== item) {
                const grid = item.parentNode;
                grid.insertBefore(dragged, item);
            }
        });
    });

    function saveOrder() {
        const order = [];
        document.querySelectorAll('.image-item').forEach(el => {
            order.push(el.dataset.id);
        });

        fetch(location.href, {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=reorder&' +
            order.map((id,i)=>`order[${i}]=${id}`).join('&')
        });
    }

</script>
