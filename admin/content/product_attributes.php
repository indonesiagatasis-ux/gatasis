<?php
// includes/auth.php
require_once __DIR__ . '/config.php';

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// simple function to create admin (run once if needed)
function create_admin($username, $password, $name='Admin') {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username,password,name) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hash, $name]);
}
