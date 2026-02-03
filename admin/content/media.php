<?php
/**
 * Media Gallery
 * Path: C:\wamp64\www\gatasis\admin\content\media.php
 */

// Ambil data gambar + nama produk
$query = "
    SELECT 
        pi.*, 
        p.name AS product_name
    FROM product_images pi
    LEFT JOIN products p ON pi.product_id = p.id
    ORDER BY pi.id DESC
";
$images = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/media-gallery.css">

<div class="page-header">
    <div>
        <h1>🖼️ Media Library</h1>
        <p class="page-desc">Manage all product gallery images</p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
        <h2 style="margin:0;">Product Gallery Images</h2>
    </div>

    <div class="card-body">
        <?php if (empty($images)): ?>
            <div class="empty-state">
                <div class="empty-icon">📂</div>
                <h3>No Media Found</h3>
            </div>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach ($images as $img): ?>

                    <?php
                    // image_path di DB contoh: uploads/products/namafile.png
                    $filePath = ltrim($img['image_path'] ?? '', '/');
                    $imageUrl = '/gatasis/admin/' . $filePath;
                    ?>

                    <div class="media-item">
                        <div class="media-preview">
                            <?php if (!empty($filePath)): ?>
                                <img 
                                    src="<?= htmlspecialchars($imageUrl) ?>"
                                    alt="<?= htmlspecialchars($img['product_name'] ?? 'Product Image') ?>"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='assets/img/image-broken.png';"
                                >
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="media-info">
                            <span class="product-ref">
                                <strong>Product:</strong><br>
                                <?= htmlspecialchars($img['product_name'] ?? 'Unassigned Product') ?>
                            </span>

                            <div class="media-actions">
                                <?php if (!empty($filePath)): ?>
                                    <a 
                                        href="<?= htmlspecialchars($imageUrl) ?>" 
                                        target="_blank" 
                                        class="btn-view"
                                    >
                                        View
                                    </a>
                                <?php endif; ?>

                                <a 
                                    href="index.php?page=product_image_delete&id=<?= (int)$img['id'] ?>" 
                                    class="btn-delete-link"
                                    onclick="return confirm('Delete image from database?')"
                                >
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
