<?php
$id = $_GET['id'] ?? 0;

$stmt = $db->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?page=products");
exit;
