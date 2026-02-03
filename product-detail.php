<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil slug dari URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: products.php');
    exit;
}

// Ambil data produk berdasarkan slug
$stmt = $db->prepare("
    SELECT p.*, b.name AS brand_name, b.logo AS brand_logo, c.name AS category_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.slug = ? AND p.status = 'active'
    LIMIT 1
    ");
$stmt->execute([$slug]);
$product = $stmt->fetch();

// Jika produk tidak ditemukan
if (!$product) {
    header('Location: products.php');
    exit;
}

// Ambil semua gambar produk
$stmt = $db->prepare("
    SELECT * FROM product_images 
    WHERE product_id = ? 
    ORDER BY is_primary DESC, id ASC
    ");
$stmt->execute([$product['id']]);
$images = $stmt->fetchAll();

// Ambil produk terkait (dari brand atau category yang sama)
$stmt = $db->prepare("
    SELECT p.*, b.name AS brand_name, pi.image_path
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE p.id != ? 
    AND (p.brand_id = ? OR p.category_id = ?)
    AND p.status = 'active'
    ORDER BY RAND()
    LIMIT 4
    ");
$stmt->execute([$product['id'], $product['brand_id'], $product['category_id']]);
$related_products = $stmt->fetchAll();

// Ambil spesifikasi produk (jika ada tabel specifications)
$specifications = [];
try {
    $stmt = $db->prepare("SELECT * FROM product_specifications WHERE product_id = ? ORDER BY display_order ASC");
    $stmt->execute([$product['id']]);
    $specifications = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist
}

include 'includes/header.php';
?>

<style>
/* Breadcrumb */
.breadcrumb-section {
    background: #f8f9fa;
    padding: 20px 0;
}

.breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0;
}

.breadcrumb-item a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

/* Product Detail Section */
.product-detail-section {
    padding: 50px 0;
}

/* Product Gallery */
.product-gallery {
    position: sticky;
    top: 100px;
}

.main-image-container {
    background: #f8f9fa;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
    position: relative;
    padding-top: 100%;
}

.main-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.thumbnail {
    aspect-ratio: 1;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail:hover, .thumbnail.active {
    border-color: #667eea;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Product Info */
.product-info-container {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.product-category-badge {
    display: inline-block;
    background: #e8eeff;
    color: #667eea;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 15px;
}

.product-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
    line-height: 1.2;
}

.product-brand {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.brand-logo-small {
    width: 80px;
    height: 50px;
    object-fit: contain;
    background: #f8f9fa;
    padding: 8px;
    border-radius: 8px;
}

.brand-name {
    font-size: 1.1rem;
    color: #666;
}

.brand-name strong {
    color: #1a1a1a;
    font-weight: 600;
}

/* Product Description */
.product-description {
    margin: 30px 0;
}

.section-heading {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-heading svg {
    color: #667eea;
}

.description-text {
    font-size: 1rem;
    line-height: 1.8;
    color: #555;
}

/* Specifications */
.specifications-table {
    width: 100%;
    margin: 30px 0;
}

.spec-row {
    display: flex;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.spec-row:last-child {
    border-bottom: none;
}

.spec-label {
    flex: 0 0 200px;
    font-weight: 600;
    color: #1a1a1a;
}

.spec-value {
    flex: 1;
    color: #666;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
}

.btn-contact {
    flex: 1;
    background: #667eea;
    color: white;
    border: none;
    padding: 16px 30px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
}

.btn-contact:hover {
    background: #764ba2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-whatsapp {
    flex: 1;
    background: #25D366;
    color: white;
    border: none;
    padding: 16px 30px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
}

.btn-whatsapp:hover {
    background: #1fb855;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
}

/* Related Products */
.related-products-section {
    padding: 60px 0;
    background: #f8f9fa;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 40px;
    text-align: center;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.related-product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-decoration: none;
    display: block;
}

.related-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.related-product-image {
    position: relative;
    padding-top: 100%;
    background: #f8f9fa;
}

.related-product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-product-info {
    padding: 20px;
}

.related-product-brand {
    color: #667eea;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.related-product-name {
    color: #1a1a1a;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 992px) {
    .product-gallery {
        position: static;
        margin-bottom: 30px;
    }
    
    .product-title {
        font-size: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .product-info-container {
        padding: 25px;
    }
    
    .product-title {
        font-size: 1.75rem;
    }
    
    .spec-row {
        flex-direction: column;
        gap: 5px;
    }
    
    .spec-label {
        flex: none;
    }
    
    .related-products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
}

@media (max-width: 576px) {
    .thumbnail-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

.tab-content {
    transition: all 0.3s ease-in-out;
}

.table td {
    border-color: #f1f5f9;
}

/* Animasi fade in yang lebih lembut */
.tab-pane.fade {
    transition: opacity 0.15s linear;
}

.nav-pills .nav-link i {
    font-size: 1.1rem;
}
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <?php if (!empty($product['category_name'])): ?>
                    <li class="breadcrumb-item"><a href="categories.php?id=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Detail -->
<section class="product-detail-section">
    <div class="container">
        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-5 col-md-6">
                <div class="product-gallery">
                    <div class="main-image-container">
                        <?php 
                        $main_image = !empty($images[0]['image_path']) && file_exists("admin/uploads/products/" . $images[0]['image_path'])
                        ? "admin/uploads/products/" . $images[0]['image_path']
                        : "https://via.placeholder.com/600x600?text=" . urlencode($product['name']);
                        ?>
                        <img src="<?= $main_image ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image" id="mainImage">
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                        <div class="thumbnail-container">
                            <?php foreach ($images as $index => $img): 
                                $thumb_path = (!empty($img['image_path']) && file_exists("admin/uploads/products/" . $img['image_path']))
                                ? "admin/uploads/products/" . $img['image_path']
                                : "https://via.placeholder.com/150x150?text=Image";
                                ?>
                                <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage('<?= $thumb_path ?>', this)">
                                    <img src="<?= $thumb_path ?>" alt="<?= htmlspecialchars($product['name']) ?> - Image <?= $index + 1 ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-7 col-md-6">
                <div class="product-info-container">
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="product-category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                    <?php endif; ?>
                    
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <?php if (!empty($product['brand_name'])): ?>
                        <div class="product-brand">
                            <?php if (!empty($product['brand_logo']) && file_exists("admin/uploads/brands/" . $product['brand_logo'])): ?>
                            <img src="admin/uploads/brands/<?= $product['brand_logo'] ?>" alt="<?= htmlspecialchars($product['brand_name']) ?>" class="brand-logo-small">
                        <?php endif; ?>
                        <div class="brand-name">
                            Brand: <strong><?= htmlspecialchars($product['brand_name']) ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <h2 class="section-heading">
                            <svg width="24" height="24" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Product Description
                        </h2>
                        <div class="description-text">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Specifications -->
                <?php if (!empty($specifications)): ?>
                    <div class="product-specifications">
                        <h2 class="section-heading">
                            <svg width="24" height="24" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            Specifications
                        </h2>
                        <div class="specifications-table">
                            <?php foreach ($specifications as $spec): ?>
                                <div class="spec-row">
                                    <div class="spec-label"><?= htmlspecialchars($spec['spec_name']) ?></div>
                                    <div class="spec-value"><?= htmlspecialchars($spec['spec_value']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="contact.php?product=<?= urlencode($product['name']) ?>" class="btn-contact">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        Contact Us
                    </a>
                    <a href="https://wa.me/6281234567890?text=Hi, I'm interested in <?= urlencode($product['name']) ?>" target="_blank" class="btn-whatsapp">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<div class="row justify-content-center mt-5 pt-4 border-top">
    <div class="col-lg-10">
        <ul class="nav nav-pills justify-content-center mb-4" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>DESKRIPSI
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                    <i class="bi bi-list-check me-2"></i>SPESIFIKASI
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4" id="download-tab" data-bs-toggle="tab" data-bs-target="#download" type="button" role="tab">
                    <i class="bi bi-download me-2"></i>DOWNLOAD
                </button>
            </li>
        </ul>

        <div class="tab-content card border-0 shadow-sm p-4 p-md-5" id="productTabContent" style="border-radius: 20px; background: #fff; min-height: 350px;">
            
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <h4 class="fw-bold mb-4 text-dark">Deskripsi Produk</h4>
                <div class="lh-lg text-muted" style="text-align: justify;">
                    <?php if (!empty($product['description'])): ?>
                        <?= $product['description'] ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-left-dots display-4 text-light"></i>
                            <p class="mt-3">Belum ada deskripsi mendalam untuk produk ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                <h4 class="fw-bold mb-4 text-dark">Spesifikasi Teknis</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody class="border-top-0">
                            <?php if(!empty($attributes)): ?>
                                <?php foreach ($attributes as $attr): ?>
                                    <tr>
                                        <td class="fw-bold py-3 text-secondary" style="width: 35%; background: #fcfcfc;">
                                            <?= htmlspecialchars($attr['name']) ?>
                                        </td>
                                        <td class="py-3 ps-4 fw-medium">
                                            <?= htmlspecialchars($attr['value']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center py-5">
                                        <i class="bi bi-clipboard-x display-4 text-light"></i>
                                        <p class="mt-3 text-muted">Data spesifikasi belum diinput.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade text-center py-5" id="download" role="tabpanel" aria-labelledby="download-tab">
                <?php if (!empty($product['download_link'])): ?>
                    <div class="mx-auto" style="max-width: 500px;">
                        <i class="bi bi-file-earmark-pdf-fill text-danger display-2 mb-3"></i>
                        <h4 class="fw-bold">Dokumen Teknis</h4>
                        <p class="text-muted mb-4">Silakan unduh brosur atau datasheet resmi untuk detail teknis lebih lanjut mengenai <?= htmlspecialchars($product['name']) ?>.</p>
                        <a href="admin/uploads/pdf/<?= $product['download_link'] ?>" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm" download>
                            <i class="bi bi-cloud-arrow-down me-2"></i> Unduh PDF (Datasheet)
                        </a>
                    </div>
                <?php else: ?>
                    <div class="py-4">
                        <i class="bi bi-file-earmark-x display-4 text-light"></i>
                        <p class="mt-3 text-muted">Maaf, brosur PDF belum tersedia untuk saat ini.</p>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill mt-2" disabled>Dokumen Tidak Tersedia</button>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>




<!-- Related Products -->
<?php if (count($related_products) > 0): ?>
    <section class="related-products-section">
        <div class="container">
            <h2 class="section-title">Related Products</h2>
            <div class="related-products-grid">
                <?php foreach ($related_products as $related): 
                    $related_image = (!empty($related['image_path']) && file_exists("admin/uploads/products/" . $related['image_path']))
                    ? "admin/uploads/products/" . $related['image_path']
                    : "https://via.placeholder.com/300x300?text=" . urlencode($related['name']);
                    ?>
                    <a href="products/<?= $related['slug'] ?>" class="related-product-card">
                        <div class="related-product-image">
                            <img src="<?= $related_image ?>" alt="<?= htmlspecialchars($related['name']) ?>" loading="lazy">
                        </div>
                        <div class="related-product-info">
                            <div class="related-product-brand"><?= htmlspecialchars($related['brand_name'] ?? 'GATASIS') ?></div>
                            <h3 class="related-product-name"><?= htmlspecialchars($related['name']) ?></h3>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
// Change main image when clicking thumbnail
    function changeImage(imageSrc, thumbnail) {
        document.getElementById('mainImage').src = imageSrc;
        
    // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        
    // Add active class to clicked thumbnail
        thumbnail.classList.add('active');
    }

// Image zoom on hover (optional enhancement)
    const mainImage = document.getElementById('mainImage');
    const container = document.querySelector('.main-image-container');

    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        mainImage.style.transformOrigin = `${x}% ${y}%`;
    });

    container.addEventListener('mouseenter', () => {
        mainImage.style.transform = 'scale(1.5)';
        mainImage.style.transition = 'transform 0.3s ease';
    });

    container.addEventListener('mouseleave', () => {
        mainImage.style.transform = 'scale(1)';
        mainImage.style.transformOrigin = 'center';
    });
</script>

<?php include 'includes/footer.php'; ?>