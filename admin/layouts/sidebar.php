<?php
$current = $_GET['page'] ?? 'dashboard';
?>

<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">⚡</span> CMS ADMIN
    </div>

    <nav class="sidebar-menu">
        <a href="index.php" class="menu-item <?= $current == 'dashboard' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>

        <div class="menu-group">
            <div class="menu-title">Content Management</div>

            <a href="index.php?page=products" class="menu-item <?= $current == 'products' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">inventory_2</span>
                Products
            </a>
            
            <a href="index.php?page=categories" class="menu-item <?= $current == 'categories' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">category</span>
                Categories
            </a>
            
            <a href="index.php?page=brands" class="menu-item <?= $current == 'brands' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">loyalty</span>
                Brands
            </a>

            <a href="index.php?page=product_types" class="menu-item <?= $current == 'product_types' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">loyalty</span>
                Products Types
            </a>

            <a href="index.php?page=media" class="menu-item <?= $current == 'media' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">photo_library</span>
                Media Library
            </a>
        </div>

        <div class="menu-group">
            <div class="menu-title">System</div>
            <a href="index.php?page=settings" class="menu-item <?= $current == 'settings' ? 'active' : '' ?>">
                <span class="material-symbols-outlined">settings</span>
                Settings
            </a>
            <a href="logout.php" class="menu-item logout">
                <span class="material-symbols-outlined">logout</span>
                Logout
            </a>
        </div>
    </nav>
</aside>