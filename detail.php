<?php
require_once __DIR__ . '/admin/layouts/database.php';

// 1. Ambil SLUG dari URL yang diproses .htaccess
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
try {
    // Query dengan JOIN ke categories (c) dan parent categories (pc)
    $stmt = $db->prepare("SELECT p.*, 
       b.name as brand_name, 
       c.name as category_name,
       pc.name as parent_category_name,
       pt.name as type_name
       FROM products p 
       LEFT JOIN brands b ON p.brand_id = b.id 
       LEFT JOIN categories c ON p.category_id = c.id 
       LEFT JOIN categories pc ON c.parent_id = pc.id 
       LEFT JOIN product_types pt ON p.product_type_id = pt.id 
       WHERE p.slug = ? AND p.status = 'active'");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: ../index.php");
        exit;
    }

    // Ambil ID produk yang asli untuk query relasi lainnya
    $product_id = $product['id'];

    // 3. Ambil Galeri Gambar
    $stmt_img = $db->prepare("SELECT image_path, is_primary 
      FROM product_images 
      WHERE product_id = ? AND status = 'active' 
      ORDER BY is_primary DESC, position ASC");
    $stmt_img->execute([$product_id]);
    $images = $stmt_img->fetchAll();

    // 4. Ambil Spesifikasi
    $stmt_attr = $db->prepare("SELECT a.name, pav.value 
     FROM product_attribute_values pav
     JOIN attributes a ON pav.attribute_id = a.id
     WHERE pav.product_id = ?");
    $stmt_attr->execute([$product_id]);
    $attributes = $stmt_attr->fetchAll();



    $parent_name = !empty($product['parent_category_name']) ? $product['parent_category_name'] : ($product['category_name'] ?? '');
// Definisikan judul halaman sebelum include header
    $page_title = htmlspecialchars($product['name']) . ($parent_name ? " " . $parent_name : "") . " | PT GATASIS INDONESIA";

    include 'includes/header.php';

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<div class="container mt-5">
    <nav aria-label="breadcrumb" class="d-flex justify-content-center">
        <ol class="breadcrumb mb-3 bg-white px-4 py-2 shadow-sm" style="border-radius: 20px; font-size: 0.85rem;">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-decoration-none text-muted">
                    <i class="bi bi-house-door me-1"></i> Beranda
                </a>
            </li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">
                <?= htmlspecialchars($product['name']) ?>
            </li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-md-6">
            <div id="productCarousel" class="carousel slide card border-0 shadow-sm p-4" data-bs-ride="carousel" style="border-radius: 20px; background: #fff;">
                <div class="carousel-inner">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $key => $img): ?>
                            <div class="carousel-item <?= $img['is_primary'] == 1 ? 'active' : '' ?>">
                                <img src="admin/<?= $img['image_path'] ?>" class="d-block mx-auto img-fluid" style="max-height: 400px; object-fit: contain;">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="carousel-item active">
                            <img src="https://via.placeholder.com/500x500?text=No+Image" class="d-block mx-auto img-fluid">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="ps-md-5">
                <div class="mb-2">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                        <?= htmlspecialchars($product['brand_name'] ?? 'Original Product') ?>
                    </span>
                </div>

                <h1 class="fw-bold mb-3 product-title-gradient" style="font-size: 2.5rem; line-height: 1.2;">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

<!--                 <div class="d-flex align-items-center mb-4">
                    <h2 class="text-primary fw-bold mb-0" style="font-size: 1.8rem;">
                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                    </h2>
                    <span class="ms-2 text-muted small mt-2">/ Unit</span>
                </div> -->

                <!-- <hr class="my-4" style="opacity: 0.1;"> -->

                <div class="mb-5">
                    <div class="product-info-mini border-top pt-2">
                        <div class="d-flex align-items-center py-2 border-bottom border-light">
                            <div class="text-muted" style="width: 130px; font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="bi bi-grid-fill me-2 text-primary opacity-50"></i>KATEGORI
                            </div>
                            <div style="font-size: 0.85rem;">
                                <a href="index.php?categories=<?= $product['category_id'] ?>" class="text-decoration-none fw-bold text-dark hover-primary">
                                    <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center py-2 border-bottom border-light">
                            <div class="text-muted" style="width: 130px; font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="bi bi-layers-fill me-2 text-primary opacity-50"></i>JENIS PRODUK
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                <?= htmlspecialchars($product['type_name'] ?? 'Standard Edition') ?>
                            </div>
                        </div>
                    </div>
                    
                  <!--   <div class="mt-3">
                        <a href="#productTab" class="text-primary text-decoration-none fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            PELAJARI SPESIFIKASI <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div> -->
                </div>

                <style>
                    .hover-primary:hover {
                        color: #0062ff !important;
                        text-decoration: underline !important;
                    }
                    .product-info-mini .text-muted {
                        text-transform: uppercase;
                    }
                </style>
                
<!--                 <div class="mt-3">
                    <a href="#productTab" class="text-primary text-decoration-none fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        LIHAT DETAIL SPESIFIKASI <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                </div> -->
            </div>

            <style>
                .hover-primary:hover {
                    color: #0062ff !important;
                }
                .product-info-mini i {
                    font-size: 0.9rem;
                }
            </style>

            <div class="d-grid gap-3">
                <div class="mt-4">
                    <a href="https://wa.me/628123456789?text=Halo, saya tertarik dengan produk <?= urlencode($product['name']) ?>. Bisa bantu jelaskan lebih lanjut?" 
                     target="_blank" 
                     class="btn btn-whatsapp px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center mb-3">
                     <i class="bi bi-whatsapp me-2 fs-5"></i>
                     Tanya Spesifikasi & Harga
                 </a>

                 <div class="d-flex align-items-center gap-3 pt-2 border-top border-light">
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Bagikan:</span>

                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                     target="_blank" class="text-secondary share-icon" title="Bagikan ke Facebook">
                     <i class="bi bi-facebook"></i>
                 </a>

                 <a href="https://twitter.com/intent/tweet?url=<?= urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&text=Cek produk ini: <?= urlencode($product['name']) ?>" 
                     target="_blank" class="text-secondary share-icon" title="Bagikan ke X">
                     <i class="bi bi-twitter-x"></i>
                 </a>

                 <a href="javascript:void(0)" onclick="copyToClipboard()" class="text-secondary share-icon" title="Salin Link">
                    <i class="bi bi-link-45deg fs-5"></i>
                </a>

                <div class="ms-auto">
                    <a href="javascript:window.print()" class="btn btn-sm btn-light border text-muted px-3 rounded-pill fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-printer me-1"></i> CETAK
                    </a>
                </div>
            </div>
        </div>

        <script>
            function copyToClipboard() {
                navigator.clipboard.writeText(window.location.href);
                alert("Link produk berhasil disalin!");
            }
        </script>
        <p class="text-center text-muted small">
            <i class="bi bi-shield-check me-1"></i> Produk Original & Garansi Resmi
        </p>
    </div>
</div>
</div>


</div>
</div>

<div class="container mt-5 pt-4 border-top">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <ul class="nav nav-pills justify-content-center mb-4" id="productTab" role="tablist">
                <?php if ($product['show_overview'] ?? true): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-4" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">OVERVIEW</button>
                    </li>
                <?php endif; ?>

                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">SPESIFIKASI</button>
                </li>

                <?php if ($product['show_features'] ?? true): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-4" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">FITUR</button>
                    </li>
                <?php endif; ?>

                <?php if (!empty($product['download_link'])): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-4" id="download-tab" data-bs-toggle="tab" data-bs-target="#download" type="button" role="tab">DOWNLOAD</button>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="tab-content card border-0 shadow-sm p-4 p-md-5" id="productTabContent" style="border-radius: 20px; background: #fff;">

                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <h4 class="fw-bold mb-3">Deskripsi Produk</h4>
                    <div class="lh-lg text-muted">
                        <?= $product['description'] ?? 'Deskripsi produk belum tersedia.' ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="specs" role="tabpanel">
                    <h4 class="fw-bold mb-4">Spesifikasi Teknis</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <?php foreach ($attributes as $attr): ?>
                                    <tr>
                                        <td class="fw-bold py-3" style="width: 30%; background: #f8fafc;"><?= htmlspecialchars($attr['name']) ?></td>
                                        <td class="py-3"><?= htmlspecialchars($attr['value']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="features" role="tabpanel">
                    <h4 class="fw-bold mb-3">Fitur Unggulan</h4>
                    <div class="lh-lg text-muted">
                        <?= $product['features'] ?? 'Daftar fitur belum tersedia.' ?>
                    </div>
                </div>

                <div class="tab-pane fade text-center py-4" id="download" role="tabpanel">
                    <i class="bi bi-file-earmark-pdf text-danger display-1 mb-3"></i>
                    <h4 class="fw-bold">Datasheet & Brosur</h4>
                    <p class="text-muted">Dapatkan informasi detail mengenai produk ini dalam format PDF.</p>
                    <a href="admin/uploads/pdf/<?= $product['download_link'] ?>" class="btn btn-primary btn-lg px-5 mt-3 rounded-pill" download>
                        Download Sekarang (PDF)
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Tab Navigasi agar lebih Modern */
    .nav-pills .nav-link {
        color: #64748b;
        background: transparent;
        border: 2px solid transparent;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link.active {
        color: #0062ff !important;
        background-color: transparent !important;
        border-bottom: 2px solid #0062ff;
        border-radius: 0;
    }
    .tab-content {
        min-height: 300px;
    }
</style>

<?php include 'includes/footer.php'; ?>