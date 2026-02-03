<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil category_id dari URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data category
$category = null;
if ($category_id > 0) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ? AND status = 'active'");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
}

// Jika category tidak ditemukan, redirect ke halaman categories
if (!$category) {
    header('Location: all-categories.php');
    exit;
}

// Ambil produk dari category ini
$stmt = $db->prepare("
    SELECT p.*, b.name as brand_name, pi.image_path
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE p.category_id = ? AND p.status = 'active'
    ORDER BY p.created_at DESC
");
$stmt->execute([$category_id]);
$products = $stmt->fetchAll();

// Ambil jumlah produk
$product_count = count($products);

include 'includes/header.php';
?>

<style>
/* Category Header Section */
.category-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0 60px;
    color: white;
    position: relative;
    overflow: hidden;
}

.category-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
    background-size: 100px 100px;
}

.category-icon-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    max-width: 200px;
    max-height: 200px;
    margin: 0 auto 30px;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-icon-container img {
    max-width: 100%;
    max-height: 120px;
    object-fit: contain;
}

.category-info {
    text-align: center;
    position: relative;
    z-index: 1;
}

.category-name {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.category-description {
    font-size: 1.1rem;
    opacity: 0.95;
    max-width: 800px;
    margin: 0 auto 20px;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    justify-content: center;
    gap: 50px;
    margin-top: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

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

/* Products Section */
.products-section {
    padding: 60px 0;
}

.section-header {
    margin-bottom: 40px;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.section-subtitle {
    color: #666;
    font-size: 1rem;
}

/* Filter & Sort */
.filter-bar {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-label {
    font-weight: 600;
    color: #333;
    margin-right: 10px;
}

.form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 8px 15px;
    min-width: 200px;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    position: relative;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
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
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #667eea;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    z-index: 1;
}

.product-info {
    padding: 20px;
}

.product-brand {
    color: #667eea;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-name {
    color: #1a1a1a;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 12px;
    min-height: 50px;
}

.product-name a {
    color: inherit;
    text-decoration: none;
    display: block;
}

.product-name a:hover {
    color: #667eea;
}

.product-footer {
    padding: 15px 20px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-view {
    background: #667eea;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #764ba2;
    color: white;
    transform: translateX(5px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state img {
    max-width: 300px;
    margin-bottom: 30px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: #666;
    margin-bottom: 15px;
}

.empty-state p {
    color: #999;
    margin-bottom: 30px;
}

/* Responsive */
@media (max-width: 768px) {
    .category-name {
        font-size: 2rem;
    }
    
    .category-stats {
        flex-direction: column;
        gap: 20px;
    }
    
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-select {
        width: 100%;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 576px) {
    .category-header {
        padding: 50px 0 40px;
    }
    
    .category-icon-container {
        padding: 30px;
        max-width: 150px;
        max-height: 150px;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Category Header -->
<section class="category-header">
    <div class="container">
        <?php 
        $icon = (!empty($category['icon']) && file_exists("admin/uploads/categories/" . $category['icon']))
            ? "admin/uploads/categories/" . $category['icon']
            : "https://via.placeholder.com/200x200?text=" . urlencode($category['name']);
        ?>
        
        <div class="category-icon-container">
            <img src="<?= $icon ?>" alt="<?= htmlspecialchars($category['name']) ?>">
        </div>
        
        <div class="category-info">
            <h1 class="category-name"><?= htmlspecialchars($category['name']) ?></h1>
            
            <?php if (!empty($category['description'])): ?>
            <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
            
            <div class="category-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= $product_count ?></span>
                    <span class="stat-label">Products</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        <?php 
                        $brands = array_unique(array_column($products, 'brand_name'));
                        echo count(array_filter($brands));
                        ?>
                    </span>
                    <span class="stat-label">Brands</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all-categories.php">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title"><?= htmlspecialchars($category['name']) ?> Products</h2>
            <p class="section-subtitle">Browse our selection of <?= htmlspecialchars($category['name']) ?> packaging equipment</p>
        </div>
        
        <?php if (count($products) > 0): ?>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">Brand:</span>
                <select class="form-select" id="brandFilter" onchange="filterProducts()">
                    <option value="">All Brands</option>
                    <?php 
                    $brands = array_unique(array_filter(array_column($products, 'brand_name')));
                    sort($brands);
                    foreach ($brands as $brand): 
                    ?>
                    <option value="<?= htmlspecialchars($brand) ?>"><?= htmlspecialchars($brand) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Sort by:</span>
                <select class="form-select" id="sortFilter" onchange="sortProducts()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name-asc">Name (A-Z)</option>
                    <option value="name-desc">Name (Z-A)</option>
                </select>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $prod): 
                $image = (!empty($prod['image_path']) && file_exists("admin/uploads/products/" . $prod['image_path']))
                    ? "admin/uploads/products/" . $prod['image_path']
                    : "https://via.placeholder.com/400x400?text=" . urlencode($prod['name']);
            ?>
                <div class="product-card" data-brand="<?= htmlspecialchars($prod['brand_name'] ?? '') ?>" data-name="<?= htmlspecialchars($prod['name']) ?>" data-date="<?= $prod['created_at'] ?? '' ?>">
                    <div class="product-image">
                        <img src="<?= $image ?>" alt="<?= htmlspecialchars($prod['name']) ?>" loading="lazy">
                        <span class="product-badge"><?= htmlspecialchars($category['name']) ?></span>
                    </div>
                    <div class="product-info">
                        <div class="product-brand"><?= htmlspecialchars($prod['brand_name'] ?? 'GATASIS') ?></div>
                        <h3 class="product-name">
                            <a href="products/<?= $prod['slug'] ?>">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                        </h3>
                    </div>
                    <div class="product-footer">
                        <a href="products/<?= $prod['slug'] ?>" class="btn-view">
                            View Details →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="empty-state">
            <img src="https://via.placeholder.com/300x300?text=No+Products" alt="No Products">
            <h3>No Products Available</h3>
            <p>There are currently no products in this category.</p>
            <a href="all-categories.php" class="btn btn-primary btn-lg rounded-pill px-5">Browse Other Categories</a>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<script>
// Filter products by brand
function filterProducts() {
    const brandFilter = document.getElementById('brandFilter').value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const brand = card.getAttribute('data-brand').toLowerCase();
        
        if (brandFilter === '' || brand === brandFilter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Sort products
function sortProducts() {
    const sortValue = document.getElementById('sortFilter').value;
    const grid = document.getElementById('productsGrid');
    const cards = Array.from(grid.querySelectorAll('.product-card'));
    
    cards.sort((a, b) => {
        switch(sortValue) {
            case 'newest':
                return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
            case 'oldest':
                return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
            case 'name-asc':
                return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
            case 'name-desc':
                return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
            default:
                return 0;
        }
    });
    
    // Re-append sorted cards
    cards.forEach(card => grid.appendChild(card));
}
</script>

<?php include 'includes/footer.php'; ?>