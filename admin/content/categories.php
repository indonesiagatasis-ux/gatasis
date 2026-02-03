<?php
// =====================================================
// GET CATEGORIES + PARENT
// =====================================================
$rows = $db->query("
    SELECT c.*, p.name AS parent_name
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    ORDER BY c.parent_id, c.position, c.name
    ")->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// BUILD TREE (PARENT -> CHILD)
// =====================================================
$tree = [];
$refs = [];

foreach ($rows as $row) {
    $row['children'] = [];
    $refs[$row['id']] = $row;
}

foreach ($refs as $id => &$cat) {
    if ($cat['parent_id']) {
        $refs[$cat['parent_id']]['children'][] = &$cat;
    } else {
        $tree[] = &$cat;
    }
}
unset($cat);


// =====================================================
// PRODUCT COUNT PER CATEGORY
// =====================================================
$productCount = $db->query("
    SELECT category_id, COUNT(*) total
    FROM products
    GROUP BY category_id
    ")->fetchAll(PDO::FETCH_KEY_PAIR);


// =====================================================
// RENDER TREE FUNCTION WITH DUPLICATE WARNING
// =====================================================
function renderCategoryTree($categories, $productCount, $level = 0, &$seenNames = [])
{
    foreach ($categories as $cat):

        $count  = $productCount[$cat['id']] ?? 0;
        $status = $cat['status'] === 'active'
        ? 'badge-success'
        : 'badge-secondary';

        // duplicate detection
        $isDuplicate = false;
        $key = strtolower($cat['name']);
        if (in_array($key, $seenNames)) {
            $isDuplicate = true;
        } else {
            $seenNames[] = $key;
        }

        // parent or child
        $isChild = $level > 0;
        ?>

        <div class="category-row <?= $isChild ? 'is-child' : 'is-parent' ?>">

            <div class="tree-symbol">
                <?= $isChild ? '└' : '' ?>
            </div>

            <div class="category-icon">
                <img
                src="<?= !empty($cat['icon'])
                ? 'uploads/categories/'.$cat['icon']
                : 'assets/img/no-image.png' ?>"
                style="
                width:62px;
                height:62px;
                object-fit:contain;
                "
                >
            </div>


            <div class="item-content">
                <div class="item-header">
                    <h3 class="item-title">
                        <?= htmlspecialchars($cat['name']) ?>
                        <?php if ($isDuplicate): ?>
                            <span class="badge badge-warning">Duplicate</span>
                        <?php endif; ?>
                    </h3>

                    <span class="badge <?= $status ?>">
                        <?= ucfirst($cat['status']) ?>
                    </span>
                </div>

                <div class="item-meta">
                    <span><strong>Products:</strong> <?= $count ?></span>
                    <span><strong>Slug:</strong> <?= $cat['slug'] ?></span>
                </div>
            </div>

            <div class="item-actions">
                <a href="index.php?page=category_edit&id=<?= $cat['id'] ?>"
                   class="btn btn-sm btn-primary">Edit</a>

                   <a href="index.php?page=category_delete&id=<?= $cat['id'] ?>"
                       class="btn btn-sm btn-danger btn-delete"
                       onclick="return confirm('Delete this category?')">
                       Delete
                   </a>
               </div>
           </div>


           <?php
           if (!empty($cat['children'])) {
            renderCategoryTree($cat['children'], $productCount, $level + 1, $seenNames);
        }

    endforeach;
}
?>

<!-- ================= PAGE HEADER ================= -->

<div class="page-header">
    <div>
        <h1>Categories</h1>
        <p class="page-desc">Manage product categories & hierarchy</p>
    </div>

    <div class="page-actions">
        <a href="index.php?page=category_create" class="btn btn-primary">+ Add Category
        </a>
    </div>
</div>

<!-- ================= CARD ================= -->
<div class="card">
    <div class="card-header"
    style="background: linear-gradient(135deg,#667eea,#764ba2); color:#fff">
    <h2 style="margin:0">All Categories</h2>
</div>

<div class="card-body">
    <?php if (empty($tree)): ?>
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <h3>No Categories</h3>
            <p>Create your first product category</p>
            <a href="index.php?page=category_create" class="btn btn-primary">
                Add Category
            </a>
        </div>
    <?php else: ?>
        <div class="sortable-list">
            <?php renderCategoryTree($tree, $productCount); ?>
        </div>
    <?php endif; ?>
</div>
</div>