<?php
$data = $db->query("
    SELECT 
    b.id,
    b.name,
    b.slug,
    b.logo,
    b.status,
    b.position,
    COUNT(p.id) AS total_products
    FROM brands b
    LEFT JOIN products p ON p.brand_id = b.id
    GROUP BY b.id
    ORDER BY b.position ASC, b.name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (($_GET['error'] ?? '') === 'brand_used'): ?>
        <div class="info-box" style="background:#fee2e2;border-left-color:#dc2626;">
            Brand cannot be deleted because it is still used by products.
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h1>Brands</h1>
            <p class="page-desc">Manage product brands</p>
        </div>

        <div class="page-actions">
            <a href="index.php?page=brand_create" class="btn btn-primary">
                + Add Brand
            </a>
        </div>
    </div>

    <div class="card">

        <div class="card-header"
        style="background: linear-gradient(135deg,#667eea,#764ba2); color:#fff">
        <h2 style="margin:0; font-size:16px;">Brand List</h2>
    </div>

    <div class="card-body">

        <?php if (empty($data)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                <h3>No Brands</h3>
                <p>Create your first brand</p>
                <a href="index.php?page=brand_create" class="btn btn-primary">
                    Add Brand
                </a>
            </div>
        <?php else: ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="60">Order</th>
                        <th>Brand</th>
                        <th width="140">Products</th>
                        <th width="140">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $r): ?>
                        <tr>
                            <td><strong><?= (int)$r['position'] ?></strong></td>

                            <td>
                                <div style="display:flex; align-items:center; gap:14px;">
                                    <img
                                    src="<?= !empty($r['logo'])
                                    ? 'uploads/brands/'.$r['logo']
                                    : 'assets/img/no-image.png' ?>"
                                    alt=""
                                    style="
                                    width:56px;
                                    height:56px;
                                    object-fit:contain;
                                    border-radius:8px;
                                    background:#f9fafb;
                                    border:1px solid #e5e7eb;
                                    padding:4px;
                                    "
                                    >

                                    <div>
                                        <div style="font-weight:600;">
                                            <?= htmlspecialchars($r['name']) ?>
                                        </div>
                                        <div style="font-size:12px; color:#6b7280;">
                                            <?= htmlspecialchars($r['slug'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>


                            <td><?= (int)$r['total_products'] ?> products</td>

                            <td>
                                <span class="badge <?= $r['status'] === 'active'
                                ? 'badge-success'
                                : 'badge-secondary' ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>

                        <td class="actions">
                            <a href="index.php?page=brand_edit&id=<?= $r['id'] ?>"
                               class="action-btn edit" title="Edit">✏️</a>

                               <a href="index.php?page=brand_delete&id=<?= $r['id'] ?>"
                                   class="action-btn delete"
                                   title="Delete"
                                   onclick="return confirm('Delete this brand?')">🗑️</a>
                               </td>
                           </tr>
                       <?php endforeach ?>
                   </tbody>
               </table>

           <?php endif; ?>

       </div>
   </div>
