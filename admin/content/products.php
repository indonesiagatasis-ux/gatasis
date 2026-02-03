<?php
// =====================================================
// LOAD MASTER DATA (FILTER OPTIONS)
// =====================================================
$productTypes = $db->query("
    SELECT id, name 
    FROM product_types 
    WHERE status = 'active'
    ORDER BY name
    ")->fetchAll();

$categories = $db->query("
    SELECT id, name 
    FROM categories 
    WHERE status = 'active'
    ORDER BY name
    ")->fetchAll();

$brands = $db->query("
    SELECT id, name 
    FROM brands 
    WHERE status = 'active'
    ORDER BY name
    ")->fetchAll();


// =====================================================
// BUILD FILTER & SEARCH
// =====================================================
$where  = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[]  = 'p.name LIKE ?';
    $params[] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['type'])) {
    $where[]  = 'p.product_type_id = ?';
    $params[] = $_GET['type'];
}
if (!empty($_GET['category'])) {
    $where[]  = 'p.category_id = ?';
    $params[] = $_GET['category'];
}
if (!empty($_GET['brand'])) {
    $where[]  = 'p.brand_id = ?';
    $params[] = $_GET['brand'];
}

$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';


// =====================================================
// PAGINATION
// =====================================================
$limit   = 10;
$pageNum = max(1, (int)($_GET['p'] ?? 1));
$offset  = ($pageNum - 1) * $limit;


// =====================================================
// COUNT
// =====================================================
$countSql = "
SELECT COUNT(*)
FROM products p
LEFT JOIN product_types pt ON p.product_type_id = pt.id
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
$whereSql
";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalRows  = $stmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);


// =====================================================
// DATA
// =====================================================
$sql = "
SELECT
p.*,
pt.name AS product_type,
c.name  AS category_name,
b.name  AS brand_name,
pi.image_path AS thumbnail
FROM products p
LEFT JOIN product_types pt ON p.product_type_id = pt.id
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN product_images pi 
ON pi.product_id = p.id AND pi.is_primary = 1
$whereSql
ORDER BY p.id DESC
LIMIT $limit OFFSET $offset
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h1>📦 Products</h1>
        <p class="page-desc">Manage all products in your catalog</p>
    </div>
    <div class="page-actions">
        <a href="index.php?page=product_create" class="btn btn-primary">
            + Add Product
        </a>
    </div>
</div>

<!-- ================= FILTER ================= -->
<form method="get" class="filter-bar">
    <input type="hidden" name="page" value="products">

    <input
    type="text"
    name="q"
    class="filter-search"
    placeholder="Search product name..."
    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
    >

    <select name="brand" class="filter-select">
        <option value="">All Brands</option>
        <?php foreach ($brands as $b): ?>
            <option value="<?= $b['id'] ?>" <?= ($_GET['brand'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['name']) ?>
            </option>
        <?php endforeach ?>
    </select>

    <select name="category" class="filter-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($_GET['category'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach ?>
    </select>

    <select name="type" class="filter-select">
        <option value="">All Types</option>
        <?php foreach ($productTypes as $pt): ?>
            <option value="<?= $pt['id'] ?>" <?= ($_GET['type'] ?? '') == $pt['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($pt['name']) ?>
            </option>
        <?php endforeach ?>
    </select>

    <button class="btn btn-primary">Filter</button>
    <a href="index.php?page=products" class="btn btn-light">Reset</a>
</form>

<!-- ================= TABLE ================= -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th width="70">IMG</th>
                <th>Product</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Category</th>
                <th width="120">Price</th>
                <th width="100">Status</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:30px;">
                        No products found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $row): ?>
                    <?php
                    $thumb = !empty($row['thumbnail'])
                    ? '/gatasis/admin/' . ltrim($row['thumbnail'], '/')
                    : 'assets/img/no-image.png';
                    ?>
                    <tr>
                        <td>
                            <img 
                            src="<?= htmlspecialchars($thumb) ?>"
                            class="product-thumb"
                            onerror="this.onerror=null;this.src='assets/img/no-image.png';"
                            >

                        </td>

                        <td>
                            <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                            <small class="muted"><?= htmlspecialchars($row['slug'] ?? '-') ?></small>
                        </td>

                        <td><?= htmlspecialchars($row['product_type'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['brand_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>

                        <td>Rp <?= number_format($row['price']) ?></td>

                        <td>
                            <span class="status-badge <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>

                        <td class="actions">
                            <a href="index.php?page=product_edit&id=<?= $row['id'] ?>" class="action-btn edit">✏️</a>
                            <a
                            href="index.php?page=product_delete&id=<?= $row['id'] ?>"
                            class="action-btn delete"
                            onclick="return confirm('Delete this product?')"
                            >🗑️</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>

<!-- ================= FOOTER ================= -->
<div class="table-footer">
    <div class="info">
        Showing <?= count($products) ?> of <?= $totalRows ?> products
    </div>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a
            href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>"
            class="<?= $i == $pageNum ? 'active' : '' ?>"
            ><?= $i ?></a>
        <?php endfor ?>
    </div>
</div>
