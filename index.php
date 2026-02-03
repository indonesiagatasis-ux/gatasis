<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil semua brands
$brands = $db->query("SELECT id, name, logo FROM brands ORDER BY name ASC")->fetchAll();

// Ambil semua categories (hanya 4 untuk homepage)
$categories = $db->query("SELECT id, name, icon FROM categories WHERE status='active' ORDER BY name ASC LIMIT 4")->fetchAll();

// Ambil beberapa produk terbaru (hanya 4 untuk homepage)
$products = $db->query("
    SELECT p.*, b.name AS brand_name, pi.image_path
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE p.status='active'
    ORDER BY p.id DESC
    LIMIT 4
    ")->fetchAll();

include 'includes/header.php';
include 'includes/hero.php';
?>

<style>
/* General Styles */
.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
    text-align: center;
}

.section-subtitle {
    color: #666;
    text-align: center;
    margin-bottom: 3rem;
    font-size: 0.95rem;
}

/* Technology Partners Section */
.partners-section {
    background: #fff;
    padding: 60px 0;
}

.partners-slider {
    position: relative;
    overflow: hidden;
    padding: 40px 0;
}

.partners-track {
    display: flex;
    gap: 60px;
    align-items: center;
    animation: scroll 30s linear infinite;
}

.partner-logo {
    flex-shrink: 0;
    width: 180px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    filter: grayscale(100%);
    opacity: 0.6;
    transition: all 0.3s ease;
}

.partner-logo:hover {
    filter: grayscale(0%);
    opacity: 1;
}

.partner-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.partners-track:hover {
    animation-play-state: paused;
}

/* Categories Section */
.categories-section {
    background: #f8f9fa;
    padding: 60px 0;
}

.category-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 200px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.category-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-card:hover img {
    transform: scale(1.05);
}

.category-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: 20px;
    color: white;
}

.category-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

/* Industries Section */
.industries-section {
    background: #fff;
    padding: 60px 0;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 24px;
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.product-image {
    position: relative;
    padding-top: 100%;
    background: #f8f9fa;
    overflow: hidden;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    padding: 16px;
}

.product-brand {
    color: #007bff;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.product-name {
    color: #1a1a1a;
    font-size: 1rem;
    font-weight: 500;
    line-height: 1.4;
    margin: 0;
}

.product-name a {
    color: inherit;
    text-decoration: none;
}

.product-name a:hover {
    color: #007bff;
}

/* Navigation Arrows */
.nav-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 10;
    transition: all 0.3s ease;
}

.nav-arrow:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.nav-arrow.left {
    left: 20px;
}

.nav-arrow.right {
    right: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .section-title {
        font-size: 1.5rem;
    }
    
    .category-card {
        height: 150px;
    }
    
    .nav-arrow {
        display: none;
    }
    
    .partners-track {
        gap: 40px;
    }
    
    .partner-logo {
        width: 120px;
    }
}

@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }
}
</style>

<div class="container-fluid px-0">
    
    <!-- Technology Partners Section -->
    <section class="partners-section">
        <div class="container">
            <h2 class="section-title">Our Technology Partners</h2>
            <p class="section-subtitle">Working with leading global brands to provide best-in-class packaging machinery and support</p>
            
            <div class="partners-slider">
                <button class="nav-arrow left" onclick="scrollPartners(-1)">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <div class="partners-track" id="partnersTrack">
                    <?php 
                    // Duplikasi brands untuk efek infinite scroll
                    $duplicatedBrands = array_merge($brands, $brands);
                    foreach ($duplicatedBrands as $brand): 
                        $logo = (!empty($brand['logo']) && file_exists("admin/uploads/brands/" . $brand['logo']))
                        ? "admin/uploads/brands/" . $brand['logo']
                        : "https://via.placeholder.com/180x80?text=" . urlencode($brand['name']);
                        ?>
                        <!-- Di bagian Brands Section di index.php, ubah link brand menjadi: -->
                        <div class="partner-logo">
                            <a href="brands.php?id=<?= $brand['id'] ?>">
                                <img src="<?= $logo ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button class="nav-arrow right" onclick="scrollPartners(1)">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Find the right equipment for your needs</p>
            
            <div class="row g-4">
                <?php foreach ($categories as $cat):
                    $cat_img = (!empty($cat['icon']) && file_exists("admin/uploads/categories/" . $cat['icon']))
                    ? "admin/uploads/categories/" . $cat['icon']
                    : "https://via.placeholder.com/400x300?text=" . urlencode($cat['name']);
                    ?>
<!-- Di bagian Categories Section di index.php, ubah menjadi: -->
<div class="col-6 col-md-3">
    <a href="categories.php?id=<?= $cat['id'] ?>" class="text-decoration-none">
        <div class="category-card">
            <img src="<?= $cat_img ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
            <div class="category-overlay">
                <h3 class="category-name"><?= htmlspecialchars($cat['name']) ?></h3>
            </div>
        </div>
    </a>
</div>
<?php endforeach; ?>
</div>

<div class="text-center mt-4">
    <a href="categories.php" class="btn btn-outline-primary btn-lg rounded-pill px-5">View All Categories</a>
</div>
</div>
</section>

<!-- Industries Section -->
<!--     <section class="industries-section">
        <div class="container">
            <h2 class="section-title">Industries We Serve</h2>
            <p class="section-subtitle">Solutions for every sector</p>
-->            
<!-- Anda bisa menambahkan konten industries di sini jika diperlukan -->
<!--         </div>
    </section> -->

    <!-- Latest Products Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Latest Products</h2>
            <p class="section-subtitle">Discover our newest packaging solutions</p>
            
            <div class="products-grid">
                <?php foreach ($products as $prod): 
                    $image = (!empty($prod['image_path']) && file_exists("admin/uploads/products/" . $prod['image_path']))
                    ? "admin/uploads/products/" . $prod['image_path']
                    : "https://via.placeholder.com/400x400?text=" . urlencode($prod['name']);
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= $image ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?= htmlspecialchars($prod['brand_name'] ?? 'GATASIS') ?></div>
                            <h3 class="product-name">
                                <a href="products/<?= $prod['slug'] ?>">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                            </h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="products.php" class="btn btn-primary btn-lg rounded-pill px-5">View All Products</a>
            </div>
        </div>
    </section>

</div>

<script>
// Scroll partners functionality
    function scrollPartners(direction) {
        const track = document.getElementById('partnersTrack');
        const scrollAmount = 300;
        track.scrollLeft += direction * scrollAmount;
    }

// Pause animation on hover
    document.querySelector('.partners-track')?.addEventListener('mouseenter', function() {
        this.style.animationPlayState = 'paused';
    });

    document.querySelector('.partners-track')?.addEventListener('mouseleave', function() {
        this.style.animationPlayState = 'running';
    });
</script>

<?php include 'includes/footer.php'; ?>