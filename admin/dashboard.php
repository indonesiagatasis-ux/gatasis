<?php
// ======================
// TOTAL COUNTS
// ======================
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();

$totalParentCategories = $db->query("
    SELECT COUNT(*) 
    FROM categories 
    WHERE parent_id IS NULL
    ")->fetchColumn();

$totalChildCategories = $db->query("
    SELECT COUNT(*) 
    FROM categories 
    WHERE parent_id IS NOT NULL
    ")->fetchColumn();

$totalBrands = $db->query("SELECT COUNT(*) FROM brands")->fetchColumn();

// ======================
// LAST UPDATED / ADDED PRODUCTS
// ======================
$recentProducts = $db->query("
    SELECT id, name, updated_at, created_at
    FROM products
    ORDER BY COALESCE(updated_at, created_at) DESC
    LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    ?>



    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p style="color: #6b7280; margin: 0;">Selamat datang kembali di Admin Panel.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-light">View Site</button>
            <button class="btn btn-primary">Download Report</button>
        </div>
    </div>

    <div class="form-grid" style="margin-bottom: 24px; grid-template-columns: repeat(3, 1fr);">

        <div class="card" style="padding: 20px; border-left: 4px solid #2563eb;">
            <p class="stat-label">Products</p>
            <h2 class="stat-value"><?= number_format($totalProducts) ?></h2>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #7c3aed;">
            <p class="stat-label">Categories</p>

            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:12px;">
                <div>
                    <p class="mini-label">Parent</p>
                    <strong><?= $totalParentCategories ?></strong>
                </div>
                <div>
                    <p class="mini-label">Child</p>
                    <strong><?= $totalChildCategories ?></strong>
                </div>
                <div>
                    <p class="mini-label">Total</p>
                    <strong><?= $totalCategories ?></strong>
                </div>
            </div>
        </div>


        <div class="card" style="padding: 20px; border-left: 4px solid #10b981;">
            <p class="stat-label">Brands</p>
            <h2 class="stat-value"><?= number_format($totalBrands) ?></h2>
        </div>

    </div>


    <div class="form-grid">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg,#667eea,#764ba2); color:#fff">
                <h3 style="margin:0; font-size: 16px;">Quick Actions</h3>
            </div>
            <div class="card-body">
                <p>Halo Admin, apa yang ingin Anda lakukan hari ini?</p>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <a href="index.php?page=product_create" class="btn btn-outline" style="flex:1; justify-content:center;">
                        + Add Product
                    </a>
                    <a href="index.php?page=category_create" class="btn btn-outline" style="flex:1; justify-content:center;">
                        + Add Category
                    </a>
                    <a href="index.php?page=brand_create" class="btn btn-outline" style="flex:1; justify-content:center;">
                        + Add Brand
                    </a>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 style="margin:0; font-size: 16px;">System Information</h3>
            </div>
            <div class="card-body">
                <table class="data-table">
                    <tr>
                        <td style="border:none; color: #6b7280;">Server Status</td>
                        <td style="border:none; text-align: right;"><span class="badge badge-success">Online</span></td>
                    </tr>
                    <tr>
                        <td style="border:none; color: #6b7280;">PHP Version</td>
                        <td style="border:none; text-align: right;">8.2.0</td>
                    </tr>
                    <tr>
                        <td style="border:none; color: #6b7280;">Last Backup</td>
                        <td style="border:none; text-align: right;">Today, 04:00 AM</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0; font-size: 16px;">Recent Product Activity</h3>
            </div>
            <div class="card-body">

                <?php if (empty($recentProducts)): ?>
                    <p style="color:#6b7280;">No recent activity</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align:right;">Last Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $p): ?>
                                <?php
                                $date = $p['updated_at'] ?: $p['created_at'];
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($p['name']) ?></strong>
                                    </td>
                                    <td style="text-align:right; color:#6b7280;">
                                        <?= date('d M Y H:i', strtotime($date)) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>


    </div>
