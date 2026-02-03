<?php
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?page=categories");
exit;
