<?php
// ============================
// VALIDASI ID
// ============================
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php?page=brands");
    exit;
}

// ============================
// AMBIL DATA BRAND
// ============================
$stmt = $db->prepare("SELECT * FROM brands WHERE id=?");
$stmt->execute([$id]);
$brand = $stmt->fetch();

if (!$brand) {
    header("Location: index.php?page=brands");
    exit;
}

// ============================
// CEK PRODUK TERKAIT
// ============================
$check = $db->prepare("SELECT COUNT(*) FROM products WHERE brand_id=?");
$check->execute([$id]);

if ($check->fetchColumn() > 0) {
    header("Location: index.php?page=brands&error=brand_used");
    exit;
}

// ============================
// HAPUS LOGO
// ============================
$uploadDir = __DIR__ . '/../uploads/brands/';

if (!empty($brand['logo']) && file_exists($uploadDir . $brand['logo'])) {
    unlink($uploadDir . $brand['logo']);
}

// ============================
// HAPUS DATA BRAND
// ============================
$stmt = $db->prepare("DELETE FROM brands WHERE id=?");
$stmt->execute([$id]);

// ============================
// REDIRECT
// ============================
header("Location: index.php?page=brands");
exit;
