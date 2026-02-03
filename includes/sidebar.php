<?php
// 1. Ambil Kategori (Data yang sudah ada sebelumnya)
$stmt_cat = $db->query("SELECT * FROM categories WHERE status='active' ORDER BY position ASC");
$all_categories = $stmt_cat->fetchAll();

$parents = array_filter($all_categories, fn($c) => is_null($c['parent_id']));
$children = array_filter($all_categories, fn($c) => !is_null($c['parent_id']));

// 2. Ambil Brands dari database
$stmt_brand = $db->query("SELECT * FROM brands ORDER BY name ASC");
$brands = $stmt_brand->fetchAll();

$current_cat = isset($_GET['categories']) ? (int)$_GET['categories'] : 0;
$current_brand = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
?>

<aside class="col-lg-9 mb-4">
    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 12px;">
        <h5 class="fw-bold mb-3 px-2" style="letter-spacing: 0.5px; color: #334155;">KATALOG</h5>
        <div class="accordion accordion-flush" id="sidebarMenu">
            <div class="accordion-item border-0 mb-1">
                <a href="index.php" class="nav-link px-2 py-2 fw-semibold text-uppercase <?= ($current_cat == 0 && $current_brand == 0) ? 'text-primary' : 'text-secondary' ?>" style="font-size: 0.85rem;">
                    SEMUA PRODUK
                </a>
            </div>

            <?php foreach ($parents as $parent): 
                $my_children = array_filter($children, fn($child) => $child['parent_id'] == $parent['id']);
                $has_child = !empty($my_children);
                $is_active = ($current_cat == $parent['id']);
                $is_child_active = in_array($current_cat, array_column($my_children, 'id'));
                $collapse_id = "collapse_" . $parent['id'];
            ?>
                <div class="accordion-item border-0 mb-1">
                    <div class="accordion-header d-flex align-items-center justify-content-between">
                        <a href="?categories=<?= $parent['id'] ?>" class="nav-link px-2 py-2 fw-semibold text-uppercase <?= ($is_active || $is_child_active) ? 'text-primary' : 'text-secondary' ?>" style="font-size: 0.85rem; flex-grow: 1;">
                            <?= htmlspecialchars($parent['name']) ?>
                        </a>
                        <?php if ($has_child): ?>
                            <button class="btn btn-sm p-0 border-0 shadow-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapse_id ?>">
                                <span class="small"><?= ($is_child_active) ? '▲' : '▼' ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($has_child): ?>
                        <div id="<?= $collapse_id ?>" class="accordion-collapse collapse <?= $is_child_active ? 'show' : '' ?>">
                            <div class="accordion-body p-0 ps-3 mt-1">
                                <?php foreach ($my_children as $child): ?>
                                    <a href="?categories=<?= $child['id'] ?>" class="nav-link py-1 px-2 mb-1 rounded <?= ($current_cat == $child['id']) ? 'bg-primary text-white shadow-sm' : 'text-muted' ?>" style="font-size: 0.8rem;">
                                        <?= htmlspecialchars($child['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
        <h5 class="fw-bold mb-3 px-2" style="letter-spacing: 0.5px; color: #334155;">BRANDS</h5>
        <div class="list-group list-group-flush">
            <?php foreach ($brands as $brand): ?>
                <a href="?brand=<?= $brand['id'] ?>" class="list-group-item list-group-item-action border-0 px-2 py-2 d-flex align-items-center justify-content-between <?= ($current_brand == $brand['id']) ? 'text-primary fw-bold' : 'text-secondary' ?>" style="font-size: 0.85rem; border-radius: 6px;">
                    <?= htmlspecialchars($brand['name']) ?>
                    <?php if ($current_brand == $brand['id']): ?>
                        <span class="badge bg-primary rounded-pill">✓</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</aside>