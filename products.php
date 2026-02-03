<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil filter dari URL
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$where_conditions = ["p.status='active'"];
$params = [];

if ($brand_filter > 0) {
    $where_conditions[] = "p.brand_id = ?";
    $params[] = $brand_filter;
}

if ($category_filter > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($search_query)) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR b.name LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(" AND ", $where_conditions);

// Sorting
$order_by = "p.created_at DESC";
switch ($sort_by) {
    case 'oldest':
    $order_by = "p.created_at ASC";
    break;
    case 'name-asc':
    $order_by = "p.name ASC";
    break;
    case 'name-desc':
    $order_by = "p.name DESC";
    break;
    case 'newest':
    default:
    $order_by = "p.created_at DESC";
    break;
}

// Get products with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Count total products
$count_sql = "SELECT COUNT(DISTINCT p.id) as total FROM products p 
LEFT JOIN brands b ON p.brand_id = b.id 
WHERE {$where_clause}";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_products = $stmt->fetch()['total'];
$total_pages = ceil($total_products / $per_page);

// Get products
$sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name, pi.image_path
FROM products p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
WHERE {$where_clause}
GROUP BY p.id
ORDER BY {$order_by}
LIMIT {$per_page} OFFSET {$offset}";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all brands for filter
$brands = $db->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();

// Get all categories for filter
$categories = $db->query("SELECT id, name FROM categories WHERE status='active' ORDER BY name ASC")->fetchAll();

include 'includes/header.php';
?>

<style>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
    background-size: 100px 100px;
}

.page-header > * {
    position: relative;
    z-index: 1;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.95;
}

.breadcrumb-section {
    background: #f8f9fa;
    padding: 15px 0;
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
    padding: 50px 0;
}

/* Filter Bar */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.filter-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.results-count {
    color: #667eea;
    font-weight: 600;
    font-size: 0.95rem;
}

.filter-controls {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    padding: 10px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-reset {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* Active Filters */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #667eea;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.filter-tag .remove {
    cursor: pointer;
    font-size: 1.2rem;
    line-height: 1;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
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

.product-category {
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
    margin-bottom: 10px;
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

.product-brand {
    color: #999;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.product-footer {
    padding: 15px 20px;
    border-top: 1px solid #f0f0f0;
}

.btn-view {
    background: #667eea;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #764ba2;
    color: white;
    transform: translateX(3px);
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 50px;
}

.pagination {
    display: flex;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item .page-link {
    padding: 10px 16px;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    color: #666;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-item.active .page-link {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.page-item .page-link:hover:not(.active) {
    background: #f8f9fa;
    border-color: #667eea;
    color: #667eea;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
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
    font-size: 1.8rem;
    color: #666;
    margin-bottom: 15px;
}

.empty-state p {
    color: #999;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

/* Responsive */
@media (max-width: 992px) {
    .filter-controls {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .filter-controls {
        grid-template-columns: 1fr;
    }
    
    .filter-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Products</h1>
        <p class="page-subtitle">Discover our complete range of packaging solutions</p>
    </div>
</section>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <h2 class="filter-title">
                    <svg width="24" height="24" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"/>
                    </svg>
                    Filter Products
                </h2>
                <span class="results-count"><?= $total_products ?> Products Found</span>
            </div>
            
            <form method="GET" action="products.php" id="filterForm">
                <div class="filter-controls">
                    <!-- Search -->
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search_query) ?>">
                    </div>
                    
                    <!-- Brand Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Brand</label>
                        <select name="brand" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>" <?= $brand_filter == $brand['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select name="category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="filter-group">
                        <label class="filter-label">Sort By</label>
                        <select name="sort" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="newest" <?= $sort_by == 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="oldest" <?= $sort_by == 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                            <option value="name-asc" <?= $sort_by == 'name-asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                            <option value="name-desc" <?= $sort_by == 'name-desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Active Filters -->
                <?php if ($brand_filter > 0 || $category_filter > 0 || !empty($search_query)): ?>
                    <div class="active-filters">
                        <?php if (!empty($search_query)): ?>
                            <span class="filter-tag">
                                Search: "<?= htmlspecialchars($search_query) ?>"
                                <a href="?<?= http_build_query(array_merge($_GET, ['search' => ''])) ?>" class="remove text-white">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($brand_filter > 0): 
                            $brand_name = array_filter($brands, fn($b) => $b['id'] == $brand_filter);
                            $brand_name = reset($brand_name)['name'] ?? 'Unknown';
                            ?>
                            <span class="filter-tag">
                                Brand: <?= htmlspecialchars($brand_name) ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['brand' => ''])) ?>" class="remove text-white">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($category_filter > 0): 
                            $cat_name = array_filter($categories, fn($c) => $c['id'] == $category_filter);
                            $cat_name = reset($cat_name)['name'] ?? 'Unknown';
                            ?>
                            <span class="filter-tag">
                                Category: <?= htmlspecialchars($cat_name) ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>" class="remove text-white">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <button type="button" class="btn-reset" onclick="window.location.href='products.php'">
                            Clear All Filters
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (count($products) > 0): ?>
            
            <!-- Products Grid -->
            <div class="products-grid">
                <?php foreach ($products as $prod): 
                    $image = (!empty($prod['image_path']) && file_exists("admin/uploads/products/" . $prod['image_path']))
                    ? "admin/uploads/products/" . $prod['image_path']
                    : "https://via.placeholder.com/400x400?text=" . urlencode($prod['name']);
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= $image ?>" alt="<?= htmlspecialchars($prod['name']) ?>" loading="lazy">
                            <?php if (!empty($prod['category_name'])): ?>
                                <span class="product-badge"><?= htmlspecialchars($prod['category_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars($prod['brand_name'] ?? 'GATASIS') ?></div>
                            <h3 class="product-name">
                                <a href="products/<?= $prod['slug'] ?>">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                            </h3>
                            <?php if (!empty($prod['category_name'])): ?>
                                <div class="product-brand">
                                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                    </svg>
                                    <?= htmlspecialchars($prod['category_name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-footer">
                            <a href="products/<?= $prod['slug'] ?>" class="btn-view">
                                View Details
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-wrapper">
                    <ul class="pagination">
                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="products.php?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                ‹ Previous
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="products.php?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">
                                    <?= $total_pages ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next -->
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="products.php?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                Next ›
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            
            <!-- Empty State -->
            <div class="empty-state">
                <img src="https://via.placeholder.com/300x300?text=No+Products" alt="No Products">
                <h3>No Products Found</h3>
                <p>Try adjusting your filters or search terms</p>
                <a href="products.php" class="btn btn-primary btn-lg rounded-pill px-5">View All Products</a>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<script>
// Auto-submit search on Enter
    document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filterForm').submit();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>