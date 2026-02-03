<?php
require_once __DIR__ . '/layouts/config.php';
require_once __DIR__ . '/layouts/database.php';

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin CMS</title>

    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <?php include 'layouts/sidebar.php'; ?>

        <!-- MAIN -->
        <div class="main">

            <!-- TOPBAR (WAJIB ADA) -->
            <?php include 'layouts/header.php'; ?>

            <!-- CONTENT -->
            <main class="content">
                <div class="page-container">

                    <?php
                    switch ($page) {

                        case 'products':
                        include 'content/products.php'; break;

                        case 'product_create':
                        include 'content/product_create.php'; break;
                        case 'product_edit':
                        include 'content/product_edit.php'; break;
                        case 'product_delete':
                        include 'content/product_delete.php'; break;
                        case 'product_image_add':
                        include 'content/product_image_add.php'; break;

                        case 'product_types':
                        include 'content/product_types.php'; break;
                        case 'product_type_create':
                        include 'content/product_type_create.php'; break;
                        case 'product_type_edit':
                        include 'content/product_type_edit.php'; break;

                        case 'categories':
                        include 'content/categories.php'; break;
                        case 'category_create':
                        include 'content/category_create.php'; break;
                        case 'category_edit':
                        include 'content/category_edit.php'; break;
                        case 'category_delete':
                        include 'content/category_delete.php'; break;

                        case 'brands':
                        include 'content/brands.php'; break;
                        case 'brand_create':
                        include 'content/brand_create.php'; break;
                        case 'brand_edit':
                        include 'content/brand_edit.php'; break;
                        case 'brand_delete':
                        include 'content/brand_delete.php'; break;

                        case 'industries':
                        include 'content/industries.php'; break;

                        case 'media':
                        include 'content/media.php'; break;

                        // case 'media':
                        // include 'content/media.php'; break;

                        // case 'media':
                        // include 'content/media.php'; break;

                        // case 'settings':
                        // include 'content/settings.php'; break;

                        default:
                        include 'dashboard.php'; break;
                    }
                    ?>
                </div>
                </main>

                <?php include 'layouts/footer.php'; ?>

            </div>
        </div>

    </body>
    </html>
