<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil semua brands
$brands = $db->query("
    SELECT b.*, COUNT(p.id) as product_count
    FROM brands b
    LEFT JOIN products p ON b.id = p.brand_id AND p.status = 'active'
    GROUP BY b.id
    ORDER BY b.name ASC
")->fetchAll();

include 'includes/header.php';
?>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0;
    color: white;
    text-align: center;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
}

.brands-section {
    padding: 60px 0;
}

.brand-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.brand-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.brand-logo {
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.brand-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.brand-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.brand-count {
    color: #667eea;
    font-size: 0.9rem;
    font-weight: 600;
}

.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
}
</style>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Brands</h1>
        <p class="page-subtitle">Partnering with the world's leading packaging technology brands</p>
    </div>
</section>

<section class="brands-section">
    <div class="container">
        <div class="brands-grid">
            <?php foreach ($brands as $brand): 
                $logo = (!empty($brand['logo']) && file_exists("admin/uploads/brands/" . $brand['logo']))
                    ? "admin/uploads/brands/" . $brand['logo']
                    : "https://via.placeholder.com/200x120?text=" . urlencode($brand['name']);
            ?>
                <a href="brands.php?id=<?= $brand['id'] ?>" class="text-decoration-none">
                    <div class="brand-card">
                        <div class="brand-logo">
                            <img src="<?= $logo ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                        </div>
                        <h3 class="brand-name"><?= htmlspecialchars($brand['name']) ?></h3>
                        <p class="brand-count"><?= $brand['product_count'] ?> Products</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>