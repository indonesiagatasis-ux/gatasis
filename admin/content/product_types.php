<?php
// Logika tetap sama sesuai permintaan
$data = $db->query("SELECT * FROM product_types ORDER BY position ASC")->fetchAll();

?>

<div class="page-header">
    <div>
        <h1>🏷️ Product Types</h1>
        <p class="page-desc">Manage your specific product classifications</p>
    </div>

    <div class="page-actions">
        <a href="index.php?page=product_type_create" class="btn btn-primary">
            + Add Product Type
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="margin: 0;">All Product Types</h2>
    </div>

    <div class="card-body">
        <?php if (empty($data)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                <h3>No Product Types</h3>
                <p>Create your first product type classification</p>
                <a href="index.php?page=product_type_create" class="btn btn-primary">
                    Add Product Type
                </a>
            </div>
        <?php else: ?>
            <div class="sortable-list">
                <?php foreach ($data as $r): 
                    $statusClass = ($r['status'] === 'active') ? 'badge-success' : 'badge-secondary';
                ?>
                    <div class="category-row is-parent">
                        <div class="category-icon">
                            <div style="width:62px; height:62px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:8px; font-size:24px;">
                                🏷️
                            </div>
                        </div>

                        <div class="item-content">
                            <div class="item-header">
                                <h3 class="item-title">
                                    <?= htmlspecialchars($r['name']) ?>
                                </h3>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst($r['status']) ?>
                                </span>
                            </div>

                            <div class="item-meta">
                                <span><strong>Desc:</strong> <?= $r['description'] ?></span>
                                <span><strong>Slug:</strong> <?= $r['slug'] ?></span>
                                </div>
                        </div>

                        <div class="item-actions">
                            <a href="index.php?page=product_type_edit&id=<?= $r['id'] ?>" 
                               class="btn btn-sm btn-primary">
                               Edit
                            </a>
                            <a href="index.php?page=product_type_delete&id=<?= $r['id'] ?>" 
                               class="btn btn-sm btn-danger btn-delete"
                               onclick="return confirm('Delete this product type?')">
                               Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>