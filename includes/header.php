<!DOCTYPE html>
<html lang="id">
<base href="/gatasis/">
<!-- <title><?= isset($page_title) ? $page_title : 'PT Gatasis Indonesia' ?></title> -->
<link rel="stylesheet" href="assets/css/style.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : "PT GATASIS INDONESIA" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-grad: linear-gradient(135deg, #0062ff 0%, #003180 100%); }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .navbar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; }
        .navbar-brand { font-weight: 700; color: #0062ff !important; }
        <style>
        .breadcrumb-item + .breadcrumb-item::before {
            content: "•"; /* Mengganti garis miring (/) dengan titik agar lebih modern */
            color: #cbd5e1;
        }
        .breadcrumb-item a:hover {
            color: #0062ff !important;
        }
        /* Animasi halus saat halaman dimuat */
        section {
            animation: fadeInDown 0.8s ease-out;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">PT GATASIS INDONESIA</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Home</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="admin/index.php">Portal Admin</a></li> -->
                </ul>
            </div>
        </div>
    </nav>