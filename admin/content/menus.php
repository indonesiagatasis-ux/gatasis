<?php
// includes/functions.php
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($type = null, $message = null) {
    if ($type && $message) {
        // set flash message
        $_SESSION['flash'][$type] = $message;
    } elseif (!empty($_SESSION['flash'])) {
        // tampilkan semua flash
        foreach ($_SESSION['flash'] as $t => $msg) {
            $class = ($t === 'success') ? 'alert-success' : 'alert-danger';
            echo "<div class='alert {$class}'>$msg</div>";
        }
        unset($_SESSION['flash']);
    }
}

function getUserNameById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: '-';
}

function getUserById($pdo, $id) {
    if (!$id) return '-';
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetchColumn();
    return $user ?: '-';
}
